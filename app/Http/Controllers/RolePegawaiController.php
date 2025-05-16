<?php

namespace App\Http\Controllers;

use App\Models\RolePegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;

class RolePegawaiController extends Controller
{
    public function index()
    {
        $roles = RolePegawai::with('pegawai')->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|max:50|unique:role_pegawai,nama_role',
        ]);

        $rolePegawai = RolePegawai::create($validated);

        return response()->json(['message' => 'Role Pegawai berhasil ditambahkan', 'data' => $rolePegawai], 201);
    }

    public function show($id)
    {
        $rolePegawai = RolePegawai::with('pegawai')->find($id);

        if (!$rolePegawai) {
            return response()->json(['message' => 'Role Pegawai tidak ditemukan'], 404);
        }

        return response()->json($rolePegawai);
    }

    public function update(Request $request, $id)
    {
        $rolePegawai = RolePegawai::find($id);

        if (!$rolePegawai) {
            return response()->json(['message' => 'Role Pegawai tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_role' => 'required|string|max:50|unique:role_pegawai,nama_role,' . $rolePegawai->id_role . ',id_role',
        ]);

        $rolePegawai->update($validated);

        return response()->json(['message' => 'Role Pegawai berhasil diperbarui', 'data' => $rolePegawai]);
    }

    public function destroy($id)
    {
        $rolePegawai = RolePegawai::find($id);

        if (!$rolePegawai) {
            return response()->json(['message' => 'Role Pegawai tidak ditemukan'], 404);
        }

        $rolePegawai->delete();

        return response()->json(['message' => 'Role Pegawai berhasil dihapus']);
    }

    public function search($keyword)
    {
        $results = RolePegawai::with('pegawai')
            ->where('nama_role', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Role Pegawai tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }

    public function pegawaiByRole($id)
    {
        $rolePegawai = RolePegawai::with('pegawai')->find($id);

        if (!$rolePegawai) {
            return response()->json(['message' => 'Role Pegawai tidak ditemukan'], 404);
        }

        return response()->json($rolePegawai->pegawai);
    }
}