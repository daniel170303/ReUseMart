<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenitipController extends Controller
{
    // Tampilkan semua data penitip
    public function index(Request $request)
    {
        $search = $request->input('search');

        $penitips = Penitip::query()
            ->when($search, function($query, $search) {
                $query->where('nama_penitip', 'like', "%{$search}%")
                    ->orWhere('nik_penitip', 'like', "%{$search}%")
                    ->orWhere('nomor_telepon_penitip', 'like', "%{$search}%")
                    ->orWhere('email_penitip', 'like', "%{$search}%");
            })
            ->get();

        return view('customerService.customerService', compact('penitips', 'search'));
    }

    // Simpan data penitip baru dengan validasi unik nik_penitip
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|max:16|unique:penitip,nik_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50',
            'password_penitip' => 'required|string|min:8|max:50',
        ]);

        $validated['password_penitip'] = Hash::make($validated['password_penitip']);

        Penitip::create($validated);

        return redirect()->route('penitip.index')->with('success', 'Penitip berhasil ditambahkan');
    }


    // Tampilkan detail penitip berdasarkan id (tidak wajib untuk web CRUD biasa)
    public function show($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return redirect()->route('penitip.index')->with('error', 'Penitip tidak ditemukan');
        }

        return view('customerService.detail', compact('penitip')); // misal ada view detail
    }

    // Update data penitip
    public function update(Request $request, $id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return redirect()->route('penitip.index')->with('error', 'Penitip tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|max:16|unique:penitip,nik_penitip,' . $id . ',id_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50',
            'password_penitip' => 'nullable|string|min:8|max:50',
        ]);

        if (!empty($validated['password_penitip'])) {
            $validated['password_penitip'] = Hash::make($validated['password_penitip']);
        } else {
            unset($validated['password_penitip']);
        }

        $penitip->update($validated);

        return redirect()->route('penitip.index')->with('success', 'Penitip berhasil diperbarui');
    }

    // Hapus data penitip
    public function destroy($id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return redirect()->route('penitip.index')->with('error', 'Penitip tidak ditemukan');
        }

        $penitip->delete();

        return redirect()->route('penitip.index')->with('success', 'Penitip berhasil dihapus');
    }
}
