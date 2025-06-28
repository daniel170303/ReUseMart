<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\BarangTitipan;
use App\Models\DetailTransaksi;
use App\Models\Pembeli;
use App\Models\RewardPembeli;
use App\Jobs\CancelUnpaidTransactionJob; // Import the job class
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiController extends Controller
{
    // Fungsi bantuan untuk menghasilkan nomor transaksi
    private function generateTransactionNumber()
    {
        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('m');
        
        // Dapatkan nomor transaksi terakhir untuk bulan dan tahun ini untuk menentukan urutan berikutnya
        $lastTransaction = Transaksi::where('nomor_nota', 'LIKE', "{$year}.{$month}.%")
                                    ->orderBy('nomor_nota', 'desc')
                                    ->first();
        
        $sequence = 1;
        if ($lastTransaction) {
            $parts = explode('.', $lastTransaction->nomor_nota);
            $lastSequence = intval(end($parts));
            $sequence = $lastSequence + 1;
        }
        
        return sprintf('%s.%s.%05d', $year, $month, $sequence);
    }

    // CATATAN: Metode CRUD berikut (index, store, show, update, destroy, search)
    // tampaknya berasal dari sistem transaksi yang lebih lama atau berbasis API tunggal.
    // Metode ini mungkin perlu ditinjau ulang atau dihapus jika tidak lagi relevan
    // dengan alur checkout berbasis keranjang saat ini untuk menghindari kebingungan.

    // Menampilkan semua transaksi (Metode Lama - Perlu Tinjauan)
    public function index()
    {
        return response()->json(Transaksi::all(), 200);
    }

    // Menyimpan transaksi baru (Metode Lama - Perlu Tinjauan)
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validasi field lama, sesuaikan jika masih dipakai
            'id_barang'          => 'required|integer',
            'id_pembeli'         => 'required|integer',
            // 'nama_barang'        => 'required|string|max:255',
            'tanggal_pemesanan'  => 'required|date',
            'tanggal_pelunasan'  => 'nullable|date',
            'jenis_pengiriman'   => 'required|string|max:50', // Mungkin tidak relevan lagi
            'tanggal_pengiriman' => 'nullable|date',
            'tanggal_pengambilan'=> 'nullable|date',
            'ongkir'             => 'required|integer',
            'status_transaksi'   => 'nullable|string|max:255',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan (Metode Lama)',
            'data'    => $transaksi
        ], 201);
    }

    // Menampilkan transaksi tertentu (Metode Lama - Perlu Tinjauan)
    public function show($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaksi);
    }

    // Memperbarui transaksi (Metode Lama - Perlu Tinjauan)
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            // Validasi field lama, sesuaikan jika masih dipakai
        ]);

        $transaksi->update($validated);

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui (Metode Lama)',
            'data'    => $transaksi
        ]);
    }

    // Menghapus transaksi (Metode Lama - Perlu Tinjauan)
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus (Metode Lama)']);
    }

    // Pencarian transaksi berdasarkan keyword (Metode Lama - Perlu Tinjauan)
    public function search($keyword)
    {
        // Sesuaikan field pencarian jika metode ini masih relevan
        $results = Transaksi::where('nomor_nota', 'like', "%{$keyword}%")
            ->orWhere('status_transaksi', 'like', "%{$keyword}%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($results);
    }

// Menampilkan riwayat transaksi untuk pembeli yang sedang login
public function history()
{
    $pembeli = Auth::user();
    if (!$pembeli || !$pembeli instanceof Pembeli) {
        return redirect()->route('login')->with('error', 'Silakan login untuk melihat riwayat transaksi.');
    }

    $transaksis = Transaksi::where('id_pembeli', $pembeli->id_pembeli)
                            ->orderBy('tanggal_pemesanan', 'desc')
                            ->paginate(10); // Tambahkan paginasi untuk tampilan yang lebih baik
    return view('historyTransaksi.historyTransaksi', compact('transaksis'));
}

// Menampilkan detail transaksi
public function showDetail($id)
{
    $pembeli = Auth::user();
    if (!$pembeli || !$pembeli instanceof Pembeli) {
        return redirect()->route('login')->with('error', 'Silakan login untuk melihat detail transaksi.');
    }

    $transaksi = Transaksi::with(['detailTransaksis.barangTitipan'])
                          ->where('id_pembeli', $pembeli->id_pembeli)
                          ->findOrFail($id);

    // Hitung countdown untuk transaksi yang masih menunggu pembayaran
    $countdown = 0;
    $batasWaktuHabis = false;
    
    if ($transaksi->status_transaksi === 'menunggu_pembayaran') {
        $batasWaktu = $transaksi->tanggal_pemesanan->copy()->addMinutes(1);
        $sekarang = Carbon::now();
        
        if ($sekarang->lt($batasWaktu)) {
            $countdown = $batasWaktu->diffInSeconds($sekarang);
        } else {
            $batasWaktuHabis = true;
            
            // Jika waktu sudah habis tapi status masih menunggu pembayaran,
            // mungkin job belum dijalankan, jadi kita perlu memperbarui UI
            // untuk menunjukkan bahwa waktu sudah habis
            \Illuminate\Support\Facades\Log::info("Batas waktu pembayaran untuk Transaksi ID {$transaksi->id_transaksi} telah habis saat melihat detail");
        }
    }

    return view('historyTransaksi.detailTransaksi', compact('transaksi', 'countdown', 'batasWaktuHabis'));
}

// Upload bukti pembayaran
public function uploadBuktiPembayaran(Request $request, $id)
{
    $pembeli = Auth::user();
    if (!$pembeli || !$pembeli instanceof Pembeli) {
        return redirect()->route('login')->with('error', 'Silakan login untuk mengupload bukti pembayaran.');
    }

    $transaksi = Transaksi::where('id_pembeli', $pembeli->id_pembeli)
                          ->where('status_transaksi', 'menunggu_pembayaran')
                          ->findOrFail($id);

    $request->validate([
        'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ], [
        'bukti_pembayaran.required' => 'Bukti pembayaran wajib diunggah.',
        'bukti_pembayaran.image' => 'File harus berupa gambar.',
        'bukti_pembayaran.mimes' => 'Format gambar harus jpeg, png, atau jpg.',
        'bukti_pembayaran.max' => 'Ukuran gambar maksimal 2MB.',
    ]);

    try {
        // Simpan gambar bukti pembayaran
        $file = $request->file('bukti_pembayaran');
        $fileName = time() . '_' . $transaksi->nomor_nota . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_pembayaran', $fileName, 'public');
        
        // Update status transaksi dan simpan path bukti pembayaran
        $transaksi->bukti_pembayaran_path = $path;
        $transaksi->status_transaksi = 'menunggu_verifikasi';
        $transaksi->save();
        
        // Log aktivitas upload bukti pembayaran
        \Illuminate\Support\Facades\Log::info("Bukti pembayaran berhasil diunggah untuk Transaksi ID {$transaksi->id_transaksi} oleh Pembeli ID {$pembeli->id_pembeli}");

        return redirect()->route('transaksi.detail', $transaksi->id_transaksi)
                         ->with('success', 'Bukti pembayaran berhasil diunggah. Pembayaran Anda sedang diverifikasi.');
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error("Kesalahan saat mengunggah bukti pembayaran untuk Transaksi ID {$transaksi->id_transaksi}: " . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan saat mengunggah bukti pembayaran: ' . $e->getMessage());
    }
}

// Verifikasi bukti pembayaran (untuk admin/pegawai)
public function verifikasiPembayaran(Request $request, $id)
{
    // Cek apakah user adalah admin/pegawai
    $pegawai = Auth::guard('pegawai')->user();
    if (!$pegawai) {
        return redirect()->route('login')->with('error', 'Anda tidak memiliki akses untuk melakukan verifikasi pembayaran.');
    }

    $transaksi = Transaksi::with('detailTransaksis.barangTitipan', 'pembeli.rewardPembeli')->findOrFail($id);
    
    if ($transaksi->status_transaksi !== 'menunggu_verifikasi') {
        return redirect()->back()->with('error', 'Status transaksi tidak valid untuk diverifikasi.');
    }

    $isValid = $request->input('is_valid', false);

    try {
        DB::transaction(function () use ($transaksi, $isValid, $request) {
            if ($isValid) {
                // Jika pembayaran valid
                $transaksi->status_transaksi = 'disiapkan';
                $transaksi->tanggal_pelunasan = Carbon::now();
                
                // Update status barang menjadi "sold out"
                foreach ($transaksi->detailTransaksis as $detail) {
                    if ($detail->barangTitipan) {
                        $detail->barangTitipan->status_barang = 'sold_out';
                        $detail->barangTitipan->save();
                        
                        // Log perubahan status barang
                        \Illuminate\Support\Facades\Log::info("Barang ID {$detail->barangTitipan->id_barang} status diubah menjadi 'sold_out'");
                    }
                }
                
                // Kirim notifikasi ke penitip
                $this->notifikasiPenitip($transaksi);
            } else {
                // Jika pembayaran tidak valid
                $transaksi->status_transaksi = 'menunggu_pembayaran';
                
                // Tambahkan catatan mengapa pembayaran tidak valid
                $transaksi->catatan_admin = $request->input('catatan', 'Bukti pembayaran tidak valid.');
                
                // Kembalikan status barang menjadi "tersedia"
                foreach ($transaksi->detailTransaksis as $detail) {
                    if ($detail->barangTitipan) {
                        $detail->barangTitipan->status_barang = 'tersedia';
                        $detail->barangTitipan->save();
                        
                        // Log perubahan status barang
                        \Illuminate\Support\Facades\Log::info("Barang ID {$detail->barangTitipan->id_barang} status dikembalikan menjadi 'tersedia'");
                    }
                }
            }
            
            $transaksi->save();
        });

        $message = $isValid ? 
            'Pembayaran berhasil diverifikasi. Status transaksi diubah menjadi "Sedang Disiapkan" dan barang ditandai sebagai "Sold Out".' : 
            'Pembayaran ditolak. Status transaksi dikembalikan ke "Menunggu Pembayaran".';
        
        return redirect()->back()->with('success', $message);
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan saat memverifikasi pembayaran: ' . $e->getMessage());
    }
}

// Fungsi untuk mengirim notifikasi ke penitip
private function notifikasiPenitip($transaksi)
{
    // Dapatkan semua penitip yang terkait dengan barang dalam transaksi
    $penitipIds = [];
    foreach ($transaksi->detailTransaksis as $detail) {
        if ($detail->barangTitipan && $detail->barangTitipan->id_penitip) {
            $penitipIds[] = $detail->barangTitipan->id_penitip;
        }
    }
    
    // Hapus duplikat
    $penitipIds = array_unique($penitipIds);
    
    // Implementasi notifikasi ke database
    foreach ($penitipIds as $penitipId) {
        // Cek apakah tabel notifikasi_penitip ada
        if (!\Schema::hasTable('notifikasi_penitip')) {
            // Jika tidak ada, buat tabel
            \Schema::create('notifikasi_penitip', function ($table) {
                $table->id();
                $table->unsignedBigInteger('id_penitip');
                $table->unsignedBigInteger('id_transaksi');
                $table->text('pesan');
                $table->boolean('dibaca')->default(false);
                $table->timestamps();
            });
        }
        
        // Simpan notifikasi ke database
        \DB::table('notifikasi_penitip')->insert([
            'id_penitip' => $penitipId,
            'id_transaksi' => $transaksi->id_transaksi,
            'pesan' => "Barang Anda telah terjual dengan nomor transaksi {$transaksi->nomor_nota}. Status transaksi telah diubah menjadi 'Sedang Disiapkan'.",
            'dibaca' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        // Log notifikasi
        \Illuminate\Support\Facades\Log::info("Notifikasi dikirim ke Penitip ID {$penitipId} untuk Transaksi ID {$transaksi->id_transaksi}");
        
        // Implementasi notifikasi email (jika diperlukan)
        // Contoh: Mail::to($penitip->email)->send(new BarangTerjualNotification($transaksi));
    }
}

    // Memproses checkout dari keranjang belanja
    public function processCheckout(Request $request)
    {
        $pembeli = Auth::user();
        if (!$pembeli || !$pembeli instanceof Pembeli) {
            return redirect()->route('login')->with('error', 'Silakan login sebagai pembeli untuk melanjutkan.');
        }

        $cart = Session::get('cart', []);
        if (empty($cart)) {
            return redirect()->route('keranjang')->with('error', 'Keranjang belanja Anda kosong.');
        }

        // --- 1. VALIDASI INPUT ---
        $validator = Validator::make($request->all(), [
            'metode_pengiriman' => 'required|in:kurir,ambil_sendiri',
            'nama_penerima' => 'required_if:metode_pengiriman,kurir|string|max:255|nullable',
            'no_telepon' => 'required_if:metode_pengiriman,kurir|string|max:20|nullable',
            'alamat_lengkap' => 'required_if:metode_pengiriman,kurir|string|nullable',
            'kota' => 'required_if:metode_pengiriman,kurir|string|in:Yogyakarta|nullable',
            'kode_pos' => 'nullable|string|max:10',
            'poin_ditebus' => 'integer|min:0|nullable',
        ], [
            'metode_pengiriman.required' => 'Metode pengiriman wajib dipilih.',
            'nama_penerima.required_if' => 'Nama penerima wajib diisi untuk pengiriman kurir.',
            'no_telepon.required_if' => 'Nomor telepon wajib diisi untuk pengiriman kurir.',
            'alamat_lengkap.required_if' => 'Alamat lengkap wajib diisi untuk pengiriman kurir.',
            'kota.required_if' => 'Kota wajib diisi (Yogyakarta) untuk pengiriman kurir.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // --- 2. KALKULASI SERVER-SIDE ---
        $subtotalBarang = 0;
        $firstBarangId = null;
        $namaBarangList = [];

        // Debug cart
        \Log::info('Cart contents:', ['cart' => $cart]);

        foreach ($cart as $id => $item) {
            $barang = BarangTitipan::find($id);
            if (!$barang || !in_array($barang->status_barang, ['tersedia', 'promo'])) {
                Session::forget("cart.{$id}");
                return redirect()->route('keranjang')->with('error', "Barang '{$item['nama']}' tidak lagi tersedia atau sudah habis dan telah dihapus dari keranjang Anda.");
            }
            $subtotalBarang += $barang->harga_barang * $item['jumlah'];
            
            // Ambil ID barang pertama untuk kompatibilitas
            if ($firstBarangId === null) {
                $firstBarangId = $barang->id_barang;
            }
            
            // Kumpulkan nama barang
            $namaBarangList[] = $barang->nama_barang_titipan;
        }

        // Pastikan firstBarangId tidak null
        if ($firstBarangId === null) {
            return redirect()->route('keranjang')->with('error', 'Tidak ada barang valid dalam keranjang.');
        }

        // Buat nama barang gabungan
        $namaBarangGabungan = count($namaBarangList) > 1 
            ? $namaBarangList[0] . ' (+' . (count($namaBarangList) - 1) . ' item lainnya)'
            : $namaBarangList[0];

        \Log::info('Barang info:', [
            'first_barang_id' => $firstBarangId,
            'nama_barang_gabungan' => $namaBarangGabungan,
            'total_items' => count($cart)
        ]);

        $ongkir = 0;
        if ($request->input('metode_pengiriman') === 'kurir') {
            $ongkir = ($subtotalBarang >= 1500000) ? 0 : 100000;
        }

        $poinDitebus = (int) $request->input('poin_ditebus', 0);
        $rewardPembeli = $pembeli->rewardPembeli()->firstOrCreate(
            ['id_pembeli' => $pembeli->id_pembeli],
            ['jumlah_poin_pembeli' => 0]
        );
        $poinSaatIni = $rewardPembeli->jumlah_poin_pembeli ?? 0;

        if ($poinDitebus > $poinSaatIni) {
            return redirect()->back()->with('error', 'Jumlah poin yang ingin Anda tukarkan melebihi poin yang Anda miliki saat ini.')->withInput();
        }

        $NILAI_TUKAR_POIN = 100;
        $diskonPoin = $poinDitebus * $NILAI_TUKAR_POIN;

        $totalPembayaran = $subtotalBarang + $ongkir - $diskonPoin;
        if ($totalPembayaran < 0) {
            $totalPembayaran = 0;
        }

        // Hitung poin yang akan diperoleh
        $poinDiperoleh = 0;
        if ($subtotalBarang > 0) {
            $poinDasar = floor($subtotalBarang / 10000);
            $poinBonus = 0;
            if ($subtotalBarang > 500000) {
                $poinBonus = floor($poinDasar * 0.20);
            }
            $poinDiperoleh = $poinDasar + $poinBonus;
        }
        
        // --- 3. BUAT NOMOR TRANSAKSI UNIK ---
        $nomorNota = $this->generateTransactionNumber();

        // --- 4. TRANSAKSI DATABASE ---
        $createdTransaction = null;
        try {
            DB::transaction(function () use (
                $request, $pembeli, $cart, $nomorNota, $subtotalBarang, $ongkir, $diskonPoin, 
                $poinDitebus, $totalPembayaran, $poinDiperoleh, $rewardPembeli, 
                $firstBarangId, $namaBarangGabungan, &$createdTransaction
            ) {
                // PERBAIKAN: Data transaksi dengan semua field yang diperlukan
                $transaksiData = [
                    'nomor_nota' => $nomorNota,
                    'id_pembeli' => $pembeli->id_pembeli,
                    'id_barang' => $firstBarangId, // WAJIB: ID barang pertama
                    'nama_barang' => $namaBarangGabungan, // WAJIB: Nama barang
                    'tanggal_pemesanan' => Carbon::now(),
                    'tanggal_pelunasan' => null,
                    'subtotal_barang' => $subtotalBarang,
                    'ongkir' => $ongkir,
                    'diskon_poin' => $diskonPoin,
                    'poin_ditebus' => $poinDitebus,
                    'total_pembayaran' => $totalPembayaran,
                    'metode_pengiriman' => $request->input('metode_pengiriman'),
                    'jenis_pengiriman' => $request->input('metode_pengiriman'),
                    'alamat_pengiriman_lengkap' => $request->input('metode_pengiriman') === 'kurir' ? $request->input('alamat_lengkap') : null,
                    'alamat_pengiriman' => $request->input('metode_pengiriman') === 'kurir' ? $request->input('alamat_lengkap') : null,
                    'nama_penerima' => $request->input('metode_pengiriman') === 'kurir' ? $request->input('nama_penerima') : null,
                    'telepon_penerima' => $request->input('metode_pengiriman') === 'kurir' ? $request->input('no_telepon') : null,
                    'kode_pos_pengiriman' => $request->input('metode_pengiriman') === 'kurir' ? $request->input('kode_pos') : null,
                    'status_transaksi' => 'menunggu_pembayaran',
                    'poin_diperoleh' => $poinDiperoleh,
                    'catatan_pembeli' => $request->input('catatan_pembeli'),
                ];

                \Log::info('Data transaksi yang akan disimpan:', $transaksiData);

                // Buat transaksi
                $createdTransaction = Transaksi::create($transaksiData);

                \Log::info('Transaksi berhasil dibuat:', [
                    'id_transaksi' => $createdTransaction->id_transaksi,
                    'nomor_nota' => $nomorNota,
                    'id_barang' => $firstBarangId,
                    'nama_barang' => $namaBarangGabungan
                ]);

                // Buat DetailTransaksi untuk setiap item
                foreach ($cart as $id => $itemDetails) {
                    $barang = BarangTitipan::find($id);
                    if (!$barang) {
                        throw new \Exception("Barang dengan ID {$id} tidak ditemukan saat proses checkout.");
                    }

                    DetailTransaksi::create([
                        'id_transaksi' => $createdTransaction->id_transaksi,
                        'id_barang_titipan' => $id,
                        'nama_barang' => $barang->nama_barang_titipan,
                        'harga_satuan' => $barang->harga_barang,
                        'jumlah_barang' => $itemDetails['jumlah'],
                        'subtotal_item' => $barang->harga_barang * $itemDetails['jumlah'],
                    ]);

                    // Perbarui status barang titipan
                    $barang->status_barang = 'menunggu_pembayaran';
                    $barang->save();

                    \Log::info('Detail transaksi dibuat:', [
                        'id_barang' => $id,
                        'nama_barang' => $barang->nama_barang_titipan,
                        'jumlah' => $itemDetails['jumlah']
                    ]);
                }

                // Perbarui Poin Pembeli
                $rewardPembeli->jumlah_poin_pembeli -= $poinDitebus;
                $rewardPembeli->jumlah_poin_pembeli += $poinDiperoleh;
                $rewardPembeli->save();

                // Kosongkan keranjang belanja
                Session::forget('cart');
            });

            // Dispatch job untuk membatalkan transaksi jika tidak dibayar dalam 1 menit
            if ($createdTransaction && $createdTransaction->status_transaksi === 'menunggu_pembayaran') {
                CancelUnpaidTransactionJob::dispatch($createdTransaction->id_transaksi)->delay(now()->addMinutes(1));
            }

            return redirect()->route('pembeli.history')->with('success', "Checkout berhasil! Nomor Transaksi Anda: {$nomorNota}. Segera lakukan pembayaran dalam 1 menit.");

        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database Query Error:', [
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'sql' => $e->getSql() ?? 'N/A',
                'bindings' => $e->getBindings() ?? [],
                'cart' => $cart,
                'first_barang_id' => $firstBarangId ?? 'NULL'
            ]);
            
            return redirect()->route('keranjang')->with('error', 
                'Terjadi kesalahan database: Field id_barang tidak memiliki nilai default. Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            \Log::error('General Error saat proses checkout:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'cart' => $cart,
                'pembeli_id' => $pembeli->id_pembeli ?? 'unknown',
                'first_barang_id' => $firstBarangId ?? 'NULL'
            ]);
            
            return redirect()->route('keranjang')->with('error', 
                'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cetakPDF($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $nomorUrut = str_pad($transaksi->id_transaksi, 5, '0', STR_PAD_LEFT);
        $tanggalTransaksi = Carbon::parse($transaksi->tanggal_pemesanan)->format('y.m');
        $noNota = $tanggalTransaksi . '.' . $nomorUrut;

        $pembeli = \DB::table('pembeli')
        ->select('nama_pembeli', 'alamat_pembeli', 'email_pembeli')
        ->where('id_pembeli', $transaksi->id_pembeli)
        ->first();


        // Ambil data barang titipan
        $barangTitipan = \DB::table('barang_titipan')->where('id_barang', $transaksi->id_barang)->first();

        // Ambil nama kurir
        $kurir = \DB::table('transaksi_pengiriman')
            ->join('pegawai', function ($join) {
                $join->on('transaksi_pengiriman.id_pegawai', '=', 'pegawai.id_pegawai')
                    ->where('pegawai.id_role', 6);
            })
            ->where('transaksi_pengiriman.id_transaksi', $transaksi->id_transaksi)
            ->select('pegawai.nama_pegawai as nama_kurir')
            ->first();

        // Format tanggal supaya tampil cantik
        $tanggal_pemesanan = Carbon::parse($transaksi->tanggal_pemesanan)->format('d-m-Y');
        $tanggal_pelunasan = $transaksi->tanggal_pelunasan ? Carbon::parse($transaksi->tanggal_pelunasan)->format('d-m-Y') : null;
        $tanggal_pengiriman = $transaksi->tanggal_pengiriman ? Carbon::parse($transaksi->tanggal_pengiriman)->format('d-m-Y') : null;

        $data = [
            'no_nota' => $noNota,
            'tanggal_pemesanan' => $tanggal_pemesanan,
            'tanggal_pelunasan' => $tanggal_pelunasan,
            'tanggal_pengiriman' => $tanggal_pengiriman,
            'nama_pembeli' => $pembeli->nama_pembeli ?? '-',
            'email_pembeli' => $pembeli->email_pembeli ?? '-',
            'alamat_pembeli' => $pembeli->alamat_pembeli ?? '-',
            'nama_kurir' => $kurir->nama_kurir ?? '-',
            'barang' => $barangTitipan->nama_barang_titipan ?? '-',
            'ongkir' => $transaksi->ongkir ?? 0,
        ];

        $pdf = Pdf::loadView('pdf.notaPenjualan', $data);

        return $pdf->download('nota_penjualan_' . $data['no_nota'] . '.pdf');
    }

    public function cetakPDFAmbil($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $nomorUrut = str_pad($transaksi->id_transaksi, 5, '0', STR_PAD_LEFT);
        $tanggalTransaksi = Carbon::parse($transaksi->tanggal_pemesanan)->format('y.m');
        $noNota = $tanggalTransaksi . '.' . $nomorUrut;

        $pembeli = \DB::table('pembeli')
            ->select('nama_pembeli', 'alamat_pembeli', 'email_pembeli')
            ->where('id_pembeli', $transaksi->id_pembeli)
            ->first();

        $barangTitipan = \DB::table('barang_titipan')->where('id_barang', $transaksi->id_barang)->first();

        // Format tanggal
        $tanggal_pemesanan = Carbon::parse($transaksi->tanggal_pemesanan)->format('d-m-Y');
        $tanggal_pelunasan = $transaksi->tanggal_pelunasan ? Carbon::parse($transaksi->tanggal_pelunasan)->format('d-m-Y') : null;
        $tanggal_pengambilan = $transaksi->tanggal_pengambilan ? Carbon::parse($transaksi->tanggal_pengambilan)->format('d-m-Y') : null;

        $data = [
            'no_nota' => $noNota,
            'tanggal_pemesanan' => $tanggal_pemesanan,
            'tanggal_pelunasan' => $tanggal_pelunasan,
            'tanggal_pengiriman' => $tanggal_pengambilan,
            'nama_pembeli' => $pembeli->nama_pembeli ?? '-',
            'email_pembeli' => $pembeli->email_pembeli ?? '-',
            'alamat_pembeli' => $pembeli->alamat_pembeli ?? '-',
            'nama_kurir' => '- (Diambil Pembeli)', 
            'barang' => $barangTitipan->nama_barang_titipan ?? '-',
            'ongkir' => $transaksi->ongkir ?? 0,
        ];

        $pdf = Pdf::loadView('pdf.notaPengambilan', $data);

        return $pdf->download('nota_penjualan_' . $data['no_nota'] . '.pdf');
    }
}