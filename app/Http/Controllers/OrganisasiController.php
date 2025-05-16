<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;
use Illuminate\Support\Facades\Hash;

class OrganisasiController extends Controller
{
    public function index()
    {
        return response()->json(Organisasi::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi',
            'password_organisasi' => 'required|string|min:6|max:50',
        ]);

        $validated['password_organisasi'] = Hash::make($validated['password_organisasi']);

        $organisasi = Organisasi::create($validated);

        return response()->json([
            'message' => 'Organisasi berhasil ditambahkan',
            'data' => $organisasi,
        ], 201);
    }

    public function show($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }
        return response()->json($organisasi, 200);
    }

    public function update(Request $request, $id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi,' . $id . ',id_organisasi',
            'password_organisasi' => 'required|string|min:6|max:50',
        ]);

        $validated['password_organisasi'] = Hash::make($validated['password_organisasi']);

        $organisasi->update($validated);

        return response()->json([
            'message' => 'Data organisasi berhasil diperbarui',
            'data' => $organisasi,
        ], 200);
    }


    public function destroy($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }

        $organisasi->delete();
        return response()->json(['message' => 'Data organisasi berhasil dihapus'], 200);
    }

    public function search($keyword)
    {
        $results = Organisasi::where('nama_organisasi', 'like', "%$keyword%")
            ->orWhere('alamat_organisasi', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_organisasi', 'like', "%$keyword%")
            ->orWhere('email_organisasi', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'total_results' => $results->count(),
            'data' => $results
        ], 200);
    }
}
