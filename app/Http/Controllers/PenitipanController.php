<?php

namespace App\Http\Controllers;

use App\Models\Penitipan;
use App\Models\DetailPenitipan;
use App\Models\Penitip;
use App\Models\BarangTitipan;
use Illuminate\Http\Request;

class PenitipanController extends Controller
{
    public function index()
    {
        $penitipanList = Penitipan::all();
        $detailPenitipan = DetailPenitipan::with('barang')->get();
        $penitipList = Penitip::all();

        $barangSudahDititipkan = DetailPenitipan::pluck('id_barang')->toArray();

        $barangList = BarangTitipan::whereNotIn('id_barang', $barangSudahDititipkan)->get();

        return view('pegawai.gudang.penitipan', compact(
            'penitipanList', 
            'detailPenitipan', 
            'barangList',
            'penitipList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_penitip' => 'required|integer',
            'barang_ids' => 'required|array',
        ]);

        // Buat penitipan baru
        $penitipan = Penitipan::create([
            'id_penitip' => $request->id_penitip,
            'tanggal_penitipan' => now(),
            'tanggal_selesai_penitipan' => now()->addMonths(1),
            'tanggal_batas_pengambilan' => now()->addMonths(2),
            'status_perpanjangan' => 'belum',
            'status_barang' => 'ready',
        ]);

        // Tambahkan barang-barang ke detail_penitipan
        foreach ($request->barang_ids as $id_barang) {
            DetailPenitipan::create([
                'id_penitipan' => $penitipan->id_penitipan,
                'id_barang' => $id_barang,
            ]);
        }

        return redirect()->route('pegawai.penitipan')->with('success', 'Penitipan berhasil ditambahkan.');
    }

    public function create()
    {
        return view('penitipan.create');
    }

    public function edit($id)
    {
        $penitipan = Penitipan::findOrFail($id);
        return view('penitipan.edit', compact('penitipan'));
    }

    public function update(Request $request, $id)
    {
        $penitipan = Penitipan::findOrFail($id);

        $request->validate([
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'tanggal_penitipan' => 'required|date',
            'tanggal_selesai_penitipan' => 'required|date',
            'tanggal_batas_pengambilan' => 'required|date',
            'status_perpanjangan' => 'required|string|max:20',
            'tanggal_terjual' => 'nullable|date',
            'status_barang' => 'nullable|string|max:255',
        ]);

        $penitipan->update($request->all());

        return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $penitipan = Penitipan::findOrFail($id);
        $penitipan->delete();

        return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil dihapus.');
    }

    public function show($id)
    {
        $penitipan = Penitipan::with('penitip')->findOrFail($id);
        return view('penitipan.show', compact('penitipan'));
    }
}
