<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;

class OrganisasiController extends Controller
{
    public function index()
    {
        return response()->json(Organisasi::all(), 200);
    }

    public function show($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }
        return response()->json($organisasi, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi',
            'password_organisasi' => 'required|string|max:50',
        ]);

        $organisasi = Organisasi::create($validated);
        return response()->json($organisasi, 201);
    }

    public function update(Request $request, $id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_organisasi' => 'sometimes|required|string|max:50',
            'alamat_organisasi' => 'sometimes|required|string|max:50',
            'nomor_telepon_organisasi' => 'sometimes|required|string|max:50',
            'email_organisasi' => 'sometimes|required|email|max:50|unique:organisasi,email_organisasi,' . $id . ',id_organisasi',
            'password_organisasi' => 'sometimes|required|string|max:50',
        ]);

        $organisasi->update($validated);
        return response()->json($organisasi, 200);
    }

    public function destroy($id)
    {
        $organisasi = Organisasi::find($id);
        if (!$organisasi) {
            return response()->json(['message' => 'Organisasi tidak ditemukan'], 404);
        }

        $organisasi->delete();
        return response()->json(['message' => 'Organisasi berhasil dihapus'], 200);
    }

    public function search($keyword)
    {
        $results = Organisasi::where('nama_organisasi', 'like', "%$keyword%")
            ->orWhere('alamat_organisasi', 'like', "%$keyword%")
            ->orWhere('email_organisasi', 'like', "%$keyword%")
            ->get();

        return response()->json($results, 200);
    }
}
