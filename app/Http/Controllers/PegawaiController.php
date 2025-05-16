<?php

namespace App\Http\Controllers;

use App\Models\{Pegawai, RolePegawai};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::with('rolePegawai')->get();
        return response()->json($pegawai);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_role' => 'required|integer|exists:role_pegawai,id_role',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai',
            'password_pegawai' => 'required|string|min:8|max:50',
        ]);

        $validated['password_pegawai'] = Hash::make($validated['password_pegawai']);
        $pegawai = Pegawai::create($validated);

        return response()->json(['message' => 'Pegawai berhasil ditambahkan', 'data' => $pegawai], 201);
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('rolePegawai')->find($id);

        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        return response()->json($pegawai);
    }

    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_role' => 'required|integer|exists:role_pegawai,id_role',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'password_pegawai' => 'nullable|string|min:8|max:50',
        ]);

        if (isset($validated['password_pegawai'])) {
            $validated['password_pegawai'] = Hash::make($validated['password_pegawai']);
        }

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

    public function search($keyword)
    {
        $results = Pegawai::where('nama_pegawai', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_pegawai', 'like', "%$keyword%")
            ->orWhere('email_pegawai', 'like', "%$keyword%")
            ->orWhereHas('rolePegawai', function ($query) use ($keyword) {
                $query->where('nama_role', 'like', "%$keyword%");
            })
            ->get();

        return response()->json($results, 200);
    }
}