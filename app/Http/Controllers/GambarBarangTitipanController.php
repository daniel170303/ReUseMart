<?php

namespace App\Http\Controllers;

use App\Models\GambarBarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GambarBarangTitipanController extends Controller
{
    // Menampilkan semua gambar untuk satu barang titipan
    public function index($barangId)
    {
        $gambar = GambarBarangTitipan::where('id_barang', $barangId)->get();
        return response()->json($gambar);
    }

    // Upload dan simpan banyak gambar untuk satu barang titipan
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_barang' => 'required|exists:barang_titipan,id_barang',
            'gambar.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // 2MB max per file
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $uploadedFiles = [];

        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                // Simpan di storage/app/public/gambar_barang_titipan
                $path = $file->store('gambar_barang_titipan', 'public');

                $gambar = GambarBarangTitipan::create([
                    'id_barang' => $request->id_barang,
                    'nama_file_gambar' => basename($path),
                ]);

                $uploadedFiles[] = $gambar;
            }
        }

        return response()->json([
            'message' => 'Gambar berhasil diupload',
            'data' => $uploadedFiles,
        ], 201);
    }

    // Hapus gambar berdasarkan ID
    public function destroy($id)
    {
        $gambar = GambarBarangTitipan::findOrFail($id);

        // Hapus file fisik dari penyimpanan
        Storage::disk('public')->delete('gambar_barang_titipan/' . $gambar->nama_file_gambar);

        // Hapus dari database
        $gambar->delete();

        return response()->json(['message' => 'Gambar berhasil dihapus']);
    }
}
