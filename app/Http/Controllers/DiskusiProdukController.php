<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DiskusiProdukController extends Controller
{
    public function store(Request $request, $id_barang)
    {
        $request->validate([
            'nama_pengirim' => 'required|string|max:100',
            'isi_pesan' => 'required|string',
        ]);

        DB::table('diskusi_produk')->insert([
            'id_barang' => $id_barang,
            'nama_pengirim' => $request->nama_pengirim,
            'isi_pesan' => $request->isi_pesan,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Pesan diskusi berhasil ditambahkan.');
    }
}

