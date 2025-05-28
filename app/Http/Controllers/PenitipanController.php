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
            'id_penitip' => 'required|exists:penitip,id_penitip',
            'id_barang' => 'required|array',
            'id_barang.*' => 'exists:barang_titipan,id_barang',
        ]);

        $tanggalPenitipan = now();
        $tanggalSelesai = $tanggalPenitipan->copy()->addDays(30);
        $tanggalBatasPengambilan = $tanggalSelesai->copy()->addDays(3);

        $penitipan = Penitipan::create([
            'id_penitip' => $request->id_penitip,
            'tanggal_penitipan' => $tanggalPenitipan,
            'tanggal_selesai_penitipan' => $tanggalSelesai,
            'tanggal_batas_pengambilan' => $tanggalBatasPengambilan,
            'status_perpanjangan' => 'ya',
        ]);

        foreach ($request->id_barang as $id_barang) {
            DetailPenitipan::create([
                'id_penitipan' => $penitipan->id_penitipan,
                'id_barang' => $id_barang,
            ]);
        }

        return redirect()->route('penitipan.index')->with('success', 'Data penitipan berhasil disimpan.');
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
