<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index()
    {
        return response()->json(Transaksi::all(), 200);
    }

    // Menyimpan transaksi baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang'          => 'required|integer',
            'id_pembeli'         => 'required|integer',
            'nama_barang'        => 'required|string|max:255',
            'tanggal_pemesanan'  => 'required|date',
            'tanggal_pelunasan'  => 'nullable|date',
            'jenis_pengiriman'   => 'required|string|max:50',
            'tanggal_pengiriman' => 'nullable|date',
            'tanggal_pengambilan'=> 'nullable|date',
            'ongkir'             => 'required|integer',
            'status_transaksi'   => 'nullable|string|max:255',
        ]);

        $transaksi = Transaksi::create($validated);

        return response()->json([
            'message' => 'Transaksi berhasil ditambahkan',
            'data'    => $transaksi
        ], 201);
    }

    // Menampilkan transaksi tertentu
    public function show($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaksi);
    }

    // Memperbarui transaksi
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_barang'          => 'required|integer',
            'id_pembeli'         => 'required|integer',
            'nama_barang'        => 'required|string|max:255',
            'tanggal_pemesanan'  => 'required|date',
            'tanggal_pelunasan'  => 'nullable|date',
            'jenis_pengiriman'   => 'required|string|max:50',
            'tanggal_pengiriman' => 'nullable|date',
            'tanggal_pengambilan'=> 'nullable|date',
            'ongkir'             => 'required|integer',
            'status_transaksi'   => 'nullable|string|max:255',
        ]);

        $transaksi->update($validated);

        return response()->json([
            'message' => 'Transaksi berhasil diperbarui',
            'data'    => $transaksi
        ]);
    }

    // Menghapus transaksi
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    // Pencarian transaksi berdasarkan keyword
    public function search($keyword)
    {
        $results = Transaksi::where('nama_barang', 'like', "%{$keyword}%")
            ->orWhere('jenis_pengiriman', 'like', "%{$keyword}%")
            ->orWhere('status_transaksi', 'like', "%{$keyword}%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($results);
    }
}
