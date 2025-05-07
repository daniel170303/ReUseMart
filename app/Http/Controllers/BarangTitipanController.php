<?php

namespace App\Http\Controllers;

use App\Models\BarangTitipan;
use Illuminate\Http\Request;

class BarangTitipanController extends Controller
{
    public function index()
    {
        $barangTitipan = BarangTitipan::all();
        return response()->json($barangTitipan);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'garansi_barang' => 'required|string|max:50',
            'berat_barang' => 'required|integer',
        ]);

        $barang = BarangTitipan::create($validatedData);
        return response()->json(['message' => 'Barang Titipan berhasil ditambahkan', 'data' => $barang], 201);
    }

    public function show($id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'garansi_barang' => 'required|string|max:50',
            'berat_barang' => 'required|integer',
        ]);

        $barang->update($validatedData);
        return response()->json(['message' => 'Barang berhasil diperbarui', 'data' => $barang]);
    }

    public function destroy($id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $barang->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
