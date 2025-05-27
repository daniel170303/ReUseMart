<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPenitipan;
use App\Models\Barang;
use App\Models\Penitipan;

class DetailPenitipanController extends Controller
{
    public function index()
    {
        $details = DetailPenitipan::with(['barang', 'penitipan'])->get();
        return view('detail-penitipan.index', compact('details'));
    }

    public function create()
    {
        $barang = Barang::all();
        $penitipan = Penitipan::all();
        return view('detail-penitipan.create', compact('barang', 'penitipan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_penitipan' => 'required|exists:penitipan,id_penitipan',
        ]);

        DetailPenitipan::create($request->only(['id_barang', 'id_penitipan']));

        return redirect()->route('detail-penitipan.index')->with('success', 'Detail penitipan berhasil ditambahkan.');
    }

    public function destroy($id_barang, $id_penitipan)
    {
        DetailPenitipan::where('id_barang', $id_barang)
            ->where('id_penitipan', $id_penitipan)
            ->delete();

        return redirect()->route('detail-penitipan.index')->with('success', 'Detail penitipan berhasil dihapus.');
    }
}
