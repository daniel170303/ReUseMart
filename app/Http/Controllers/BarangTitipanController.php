<?php

namespace App\Http\Controllers;

use App\Models\BarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // validasi gambar
        ]);

        // Simpan gambar jika ada
        if ($request->hasFile('gambar_barang')) {
            $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
            $validatedData['gambar_barang'] = $path;
        }

        $barang = BarangTitipan::create($validatedData);
        return response()->json(['message' => 'Barang Titipan berhasil ditambahkan', 'data' => $barang], 201);
    }

    public function search($keyword)
    {
        $results = BarangTitipan::where('nama_barang_titipan', 'like', "%$keyword%")
            ->orWhere('harga_barang', 'like', "%$keyword%")
            ->orWhere('deskripsi_barang', 'like', "%$keyword%")
            ->orWhere('jenis_barang', 'like', "%$keyword%")
            ->orWhere('garansi_barang', 'like', "%$keyword%")
            ->orWhere('berat_barang', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }

    public function show($nama)
    {
        $barang = BarangTitipan::where('nama_barang_titipan', 'like', '%' . $nama . '%')->get();
        if ($barang->isEmpty()) {
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
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Jika ada file baru, hapus lama dan simpan baru
        if ($request->hasFile('gambar_barang')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar_barang) {
                Storage::disk('public')->delete($barang->gambar_barang);
            }
            $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
            $validatedData['gambar_barang'] = $path;
        }

        $barang->update($validatedData);
        return response()->json(['message' => 'Barang berhasil diperbarui', 'data' => $barang]);
    }

    public function destroy($id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        // Hapus file gambar jika ada
        if ($barang->gambar_barang) {
            Storage::disk('public')->delete($barang->gambar_barang);
        }

        $barang->delete();
        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
