<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PembeliController extends Controller
{
    // Menampilkan semua data pembeli
    public function index()
    {
        return response()->json(Pembeli::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);

        $pembeli = Pembeli::create($validated);

        return response()->json(['message' => 'Pembeli berhasil ditambahkan', 'data' => $pembeli], 201);
    }

    public function show($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        return response()->json($pembeli);
    }

    public function update(Request $request, $id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli,' . $pembeli->id_pembeli . ',id_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);

        $pembeli->update($validated);

        return response()->json(['message' => 'Pembeli berhasil diperbarui', 'data' => $pembeli]);
    }

    public function destroy($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        $pembeli->delete();

        return response()->json(['message' => 'Pembeli berhasil dihapus']);
    }

    public function search($keyword)
    {
        $results = Pembeli::where('nama_pembeli', 'like', "%$keyword%")
            ->orWhere('alamat_pembeli', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_pembeli', 'like', "%$keyword%")
            ->orWhere('email_pembeli', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }
}