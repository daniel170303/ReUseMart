<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\BarangTitipan;
use App\Models\Pembeli;
use App\Models\RewardPembeli;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        // Validasi input
        $request->validate([
            'metode_pengiriman' => 'required|in:kurir,ambil_sendiri',
            'subtotal_barang' => 'required|numeric|min:0',
            'ongkir' => 'required|numeric|min:0',
            'poin_ditebus' => 'required|numeric|min:0',
            'total_pembayaran' => 'required|numeric|min:0',
        ]);

        // Validasi alamat jika metode pengiriman kurir
        if ($request->metode_pengiriman === 'kurir') {
            $request->validate([
                'nama_penerima' => 'required|string|max:255',
                'no_telepon' => 'required|string|max:20',
                'alamat_lengkap' => 'required|string',
                'kota' => 'required|string|max:100',
                'kode_pos' => 'nullable|string|max:10',
            ]);
        }

        // Cek apakah user sudah login
        $pembeli = Auth::user();
        if (!$pembeli || !$pembeli instanceof Pembeli) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Ambil data keranjang dari session
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Validasi poin yang ditebus
        $poinDitebus = (int) $request->poin_ditebus;
        $rewardPembeli = RewardPembeli::where('id_pembeli', $pembeli->id_pembeli)->first();
        $poinTersedia = $rewardPembeli ? $rewardPembeli->jumlah_poin_pembeli : 0;

        if ($poinDitebus > $poinTersedia) {
            return redirect()->back()->with('error', 'Poin yang ingin ditebus melebihi poin yang Anda miliki.');
        }

        DB::beginTransaction();
        try {
            // Generate nomor nota
            $nomorNota = $this->generateNomorNota();

            // Siapkan data alamat pengiriman
            $alamatPengiriman = null;
            if ($request->metode_pengiriman === 'kurir') {
                $alamatPengiriman = [
                    'nama_penerima' => $request->nama_penerima,
                    'no_telepon' => $request->no_telepon,
                    'alamat_lengkap' => $request->alamat_lengkap,
                    'kota' => $request->kota,
                    'kode_pos' => $request->kode_pos,
                ];
            }

            // Buat transaksi baru
            $transaksi = Transaksi::create([
                'nomor_nota' => $nomorNota,
                'id_pembeli' => $pembeli->id_pembeli,
                'tanggal_pemesanan' => Carbon::now(),
                'tanggal_pelunasan' => now(), // Belum ada pembayaran
                'subtotal_barang' => $request->subtotal_barang,
                'ongkir' => $request->ongkir,
                'poin_ditebus' => $poinDitebus,
                'diskon_poin' => $poinDitebus * 100, // 1 poin = Rp 10.000
                'total_pembayaran' => $request->total_pembayaran,
                'metode_pengiriman' => $request->metode_pengiriman,
                'alamat_pengiriman' => $alamatPengiriman ? json_encode($alamatPengiriman) : null,
                'status_transaksi' => 'menunggu_pembayaran',
                'jenis_pengiriman' => $request->metode_pengiriman === 'kurir' ? 'kurir' : 'ambil_sendiri',

            ]);

            // Simpan detail transaksi untuk setiap item di keranjang
            foreach ($cart as $idBarang => $item) {
                $barang = BarangTitipan::find($idBarang);
                if (!$barang) {
                    throw new \Exception("Barang dengan ID {$idBarang} tidak ditemukan.");
                }

                // Cek stok barang
                //if ($barang->stok_barang < $item['jumlah']) {
                  //  throw new \Exception("Stok barang {$barang->nama_barang_titipan} tidak mencukupi.");
                //}

                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_barang' => $idBarang,
                    'nama_barang' => $barang->nama_barang_titipan,
                    'harga_satuan' => $barang->harga_barang,
                    'jumlah_barang' => $item['jumlah'],
                    'subtotal_item' => $barang->harga_barang * $item['jumlah'],
                ]);

                // Kurangi stok barang
              //  $barang->decrement('stok_barang', $item['jumlah']);            
              }

            // Kurangi poin pembeli jika ada poin yang ditebus
            if ($poinDitebus > 0 && $rewardPembeli) {
                $rewardPembeli->decrement('jumlah_poin_pembeli', $poinDitebus);
            }

            // Hitung poin yang akan diperoleh dari transaksi ini
            $poinDiperoleh = $this->hitungPoinDiperoleh($request->subtotal_barang);
            
            // Tambahkan poin baru ke pembeli
            if ($poinDiperoleh > 0) {
                if ($rewardPembeli) {
                    $rewardPembeli->increment('jumlah_poin_pembeli', $poinDiperoleh);
                } else {
                    RewardPembeli::create([
                        'id_pembeli' => $pembeli->id_pembeli,
                        'jumlah_poin_pembeli' => $poinDiperoleh,
                    ]);
                }
            }

            // Hapus keranjang dari session
            session()->forget('cart');

            DB::commit();

            return redirect()->route('transaksi.detail', $transaksi->id_transaksi)
                           ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function generateNomorNota()
    {
        $prefix = 'RM';
        $tanggal = Carbon::now()->format('Ymd');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        return $prefix . $tanggal . $random;
    }

    private function hitungPoinDiperoleh($totalBelanjaBarang)
    {
        if ($totalBelanjaBarang <= 0) return 0;
        
        $poinDasar = floor($totalBelanjaBarang / 10000); // 1 poin setiap Rp10.000
        $poinBonus = 0;
        
        if ($totalBelanjaBarang > 500000) { // Bonus 20% jika belanja > Rp500.000
            $poinBonus = floor($poinDasar * 0.20);
        }
        
        return $poinDasar + $poinBonus;
    }
}