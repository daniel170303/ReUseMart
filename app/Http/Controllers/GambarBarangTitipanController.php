<?php

namespace App\Http\Controllers;

use App\Models\GambarBarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GambarBarangTitipanController extends Controller
{
    // Tampilkan semua gambar untuk satu barang titipan
    public function index($barangId)
    {
        $gambar = GambarBarangTitipan::where('barang_titipan_id', $barangId)->get();

        return response()->json($gambar);
    }

    // Upload dan simpan banyak gambar untuk satu barang titipan
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barang_titipan_id' => 'required|exists:barang_titipan,id_barang',
            'gambar.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // max 2MB per gambar
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $uploadedFiles = [];
        if($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('public/gambar_barang_titipan');

                $namaFile = basename($path);

                $gambar = GambarBarangTitipan::create([
                    'barang_titipan_id' => $request->barang_titipan_id,
                    'nama_file_gambar' => $namaFile,
                ]);

                $uploadedFiles[] = $gambar;
            }
        }

        return response()->json([
            'message' => 'Gambar berhasil diupload',
            'data' => $uploadedFiles
        ], 201);
    }

    // Hapus gambar berdasarkan id
    public function destroy($id)
    {
        $gambar = GambarBarangTitipan::findOrFail($id);

        // Hapus file fisik
        Storage::delete('public/gambar_barang_titipan/' . $gambar->nama_file_gambar);

        $gambar->delete();

        return response()->json(['message' => 'Gambar berhasil dihapus']);
    }
}
