<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PenitipController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = Penitip::query();

        if ($keyword) {
            $query->where('nama_penitip', 'like', "%$keyword%")
                ->orWhere('nik_penitip', 'like', "%$keyword%")
                ->orWhere('email_penitip', 'like', "%$keyword%")
                ->orWhere('nomor_telepon_penitip', 'like', "%$keyword%");
        }

        $penitip = $query->get();

        return view('penitip.penitip', compact('penitip'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|max:16|unique:penitip,nik_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50|unique:penitip,email_penitip',
            'password_penitip' => 'required|string|min:8|max:50',
        ]);

        $validated['password_penitip'] = Hash::make($validated['password_penitip']); // Hash password

        $penitip = Penitip::create($validated);

        return response()->json(['message' => 'Penitip berhasil ditambahkan', 'data' => $penitip], 201);
    }

    public function show($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        return response()->json($penitip);
    }

    public function update(Request $request, $id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|max:16|unique:penitip,nik_penitip,' . $penitip->id_penitip . ',id_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50|unique:penitip,email_penitip,' . $penitip->id_penitip . ',id_penitip',
            'password_penitip' => 'required|string|min:8|max:50',
        ]);

        $validated['password_penitip'] = Hash::make($validated['password_penitip']);

        $penitip->update($validated);

        return response()->json(['message' => 'Penitip berhasil diperbarui', 'data' => $penitip]);
    }

    public function destroy($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        $penitip->delete();

        return response()->json(['message' => 'Penitip berhasil dihapus']);
    }

    public function search($keyword)
    {
        $results = Penitip::where('nama_penitip', 'like', "%$keyword%")
            ->orWhere('nik_penitip', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_penitip', 'like', "%$keyword%")
            ->orWhere('email_penitip', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }

    public function profileById($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return abort(404, 'Penitip tidak ditemukan.');
        }

        $barangTitipan = $penitip->barangTitipan ?? collect();
        $riwayatPenitipan = $penitip->riwayatPenitipan ?? collect();

        return view('penitip.profilePenitip', compact('penitip', 'barangTitipan', 'riwayatPenitipan'));
    }
}