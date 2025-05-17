<?php

namespace App\Http\Controllers;

use App\Models\RolePegawai;
use Illuminate\Http\Request;

class RolePegawaiController extends Controller
{
    /**
     * Display a listing of the resource, with optional search.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');

        if ($keyword) {
            $roles = $this->searchByKeyword($keyword);
        } else {
            $roles = RolePegawai::all();
        }

        return response()->json($roles);
    }

    /**
     * Fungsi untuk mencari role pegawai berdasarkan keyword.
     */
    protected function search(string $keyword)
    {
        return RolePegawai::where('nama_role', 'LIKE', '%' . $keyword . '%')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_role' => 'required|string|max:50',
        ]);

        $role = RolePegawai::create([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json($role, 201);
    }

    public function show(RolePegawai $rolePegawai)
    {
        return response()->json($rolePegawai);
    }

    public function update(Request $request, RolePegawai $rolePegawai)
    {
        $request->validate([
            'nama_role' => 'required|string|max:50',
        ]);

        $rolePegawai->update([
            'nama_role' => $request->nama_role,
        ]);

        return response()->json($rolePegawai);
    }

    public function destroy(RolePegawai $rolePegawai)
    {
        $rolePegawai->delete();

        return response()->json(['message' => 'Role pegawai berhasil dihapus.']);
    }
}
