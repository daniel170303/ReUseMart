<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index()
    {
        return response()->json(Pegawai::all());
    }

    public function create()
    {
        return view('pegawai.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_role' => 'required|integer',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai', // Memastikan email unik
            'password_pegawai' => 'required|string|min:8|max:50', // Pastikan password minimal 8 karakter
        ]);

        $pegawai = Pegawai::create($validated);

        return response()->json(['message' => 'Pegawai berhasil ditambahkan', 'data' => $pegawai], 201);
    }

    public function show($nama)
    {
        // Mencari pegawai berdasarkan nama
        $pegawai = Pegawai::where('nama_pegawai', 'like', '%' . $nama . '%')->get();
        
        if ($pegawai->isEmpty()) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        return response()->json($pegawai);
    }

    public function edit($id)
    {
        $pegawai = Pegawai::find($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_role' => 'required|integer',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai', // Memastikan email unik
            'password_pegawai' => 'required|string|min:8|max:50', // Pastikan password minimal 8 karakter
        ]);

        $pegawai->update($validated);
        return response()->json(['message' => 'Pegawai berhasil diperbarui', 'data' => $pegawai]);
    }

    public function destroy($id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        $pegawai->delete();
        return response()->json(['message' => 'Pegawai berhasil dihapus']);
    }
}
