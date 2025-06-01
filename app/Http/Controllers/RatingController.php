<?php

namespace App\Http\Controllers;

use App\Models\BarangTitipan;
use App\Models\Rating;
use App\Models\Pembeli;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|exists:barang_titipan,id_barang',  // Barang yang dirating
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',  // Pembeli yang memberikan rating
            'rating' => 'required|integer|between:1,5',  // Rating antara 1 sampai 5
        ]);

        // Menyimpan rating baru
        $rating = Rating::create($validated);

        // Menghubungkan rating dengan barang
        $barang = BarangTitipan::find($validated['id_barang']);

        // Jika barang sudah memiliki rating, kita bisa menghentikan proses atau update rating yang ada
        // Untuk kasus ini, kita asumsikan hanya bisa memberikan satu rating per barang
        if ($barang->rating()->exists()) {
            // Rating sudah ada, update rating jika diperlukan
            $barang->rating()->update(['rating' => $validated['rating']]);
        } else {
            // Jika belum ada rating, simpan rating baru
            $barang->rating()->create([
                'id_pembeli' => $validated['id_pembeli'],
                'rating' => $validated['rating'],
            ]);
        }

        return redirect()->route('barang.show', $request->id_barang)->with('success', 'Rating berhasil diberikan!');
    }
}
