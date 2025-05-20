<?php

namespace App\Http\Controllers;

use App\Models\AlamatPembeli;
use Illuminate\Http\Request;

class AlamatPembeliController extends Controller
{
    public function index()
    {
        $alamat = AlamatPembeli::all();
        return view('Admin.alamat_pembeli.index', compact('alamat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pembeli' => 'required|exists:pembeli,id_pembeli',
            'alamat_lengkap' => 'required|string|max:255',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
        ]);

        AlamatPembeli::create($validated);
        return redirect()->route('Admin.alamat_pembeli.index')->with('success', 'Alamat berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $alamat = AlamatPembeli::findOrFail($id);
        $validated = $request->validate([
            'alamat_lengkap' => 'required|string|max:255',
            'kota' => 'required|string|max:100',
            'provinsi' => 'required|string|max:100',
            'kode_pos' => 'required|string|max:10',
        ]);

        $alamat->update($validated);
        return redirect()->route('Admin.alamat_pembeli.index')->with('success', 'Alamat berhasil diperbarui.');
    }

    public function destroy($id)
    {
        AlamatPembeli::findOrFail($id)->delete();
        return redirect()->route('Admin.alamat_pembeli.index')->with('success', 'Alamat berhasil dihapus.');
    }
}
