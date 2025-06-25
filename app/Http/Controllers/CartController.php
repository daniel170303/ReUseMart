<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangTitipan;
use App\Models\RewardPembeli;

class CartController extends Controller
{
    // Menampilkan isi keranjang
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('landingPage.Keranjang.keranjang', compact('cart'));

    }

    // Menambahkan barang ke keranjang
    public function add($id)
    {
        $barang = BarangTitipan::findOrFail($id);

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $cart[$id]['jumlah']++;
        } else {
            $cart[$id] = [
                'nama' => $barang->nama_barang_titipan,
                'harga' => $barang->harga_barang,
                'jumlah' => 1,
                'gambar' => $barang->gambar_barang,
                'berat' => $barang->berat_barang
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Barang berhasil ditambahkan ke keranjang!');
    }

    // Menghapus 1 item dari keranjang
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('keranjang')->with('success', 'Barang berhasil dihapus dari keranjang.');
    }

    // Mengosongkan seluruh keranjang
    public function clear()
    {
        session()->forget('cart');
        return redirect()->route('keranjang')->with('success', 'Keranjang berhasil dikosongkan.');
    }

    public function cancelTransaction(Request $request)
    {
        try {
            $cart = Session::get('cart', []);
            $user = Auth::user();
            
            // Kembalikan stok barang
            foreach ($cart as $idBarang => $item) {
                $barang = BarangTitipan::find($idBarang);
                if ($barang) {
                    $barang->increment('stok_barang', $item['jumlah']);
                }
            }
            
            // Kembalikan poin jika ada yang ditebus
            if ($request->poin_ditebus > 0 && $user instanceof \App\Models\Pembeli) {
                $rewardPembeli = RewardPembeli::where('id_pembeli', $user->id_pembeli)->first();
                if ($rewardPembeli) {
                    $rewardPembeli->increment('jumlah_poin_pembeli', $request->poin_ditebus);
                }
            }
            
            // Hapus keranjang dari session
            Session::forget('cart');
            
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan. Stok dan poin telah dikembalikan.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membatalkan transaksi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}