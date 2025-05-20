<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;

class OrganisasiController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $organisasi = Organisasi::query()
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_organisasi', 'like', "%$keyword%")
                             ->orWhere('alamat_organisasi', 'like', "%$keyword%")
                             ->orWhere('nomor_telepon_organisasi', 'like', "%$keyword%")
                             ->orWhere('email_organisasi', 'like', "%$keyword%");
            })
            ->get();

        return view('admin.organisasi.index', compact('organisasi', 'keyword'));
    }

    public function create()
    {
        return view('admin.organisasi.create');
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

        Organisasi::create($validated);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        return view('admin.organisasi.edit', compact('organisasi'));
    }

    public function update(Request $request, $id)
    {
        $organisasi = Organisasi::findOrFail($id);

        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi,' . $id . ',id_organisasi',
            'password_organisasi' => 'required|string|max:50',
        ]);

        $organisasi->update($validated);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->delete();

        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil dihapus.');
    }
}
