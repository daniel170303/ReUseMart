<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donasi;
use App\Models\RewardPenitip;
use App\Models\DetailPenitipan;
use App\Models\Penitipan;
use App\Models\BarangTitipan;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donasis = Donasi::all();
        return response()->json($donasis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|integer',
            'id_request' => 'required|integer',
            'tanggal_donasi' => 'required|date',
            'penerima_donasi' => 'required|string|max:50',
        ]);

        // Simpan donasi
        $donasi = Donasi::create($validated);

        // ========== LOGIKA POIN REWARD ==========
        $barangId = $request->id_barang;

        // Cari id_penitipan dari detail_penitipan
        $detail = DetailPenitipan::where('id_barang', $barangId)->first();

        if ($detail) {
            $penitipan = Penitipan::find($detail->id_penitipan);

            if ($penitipan) {
                $idPenitip = $penitipan->id_penitip;

                // Coba ambil harga dari tabel barang_titipan jika ada
                $barang = BarangTitipan::find($barangId);
                $harga = $barang ? $barang->harga_barang : 0;

                // Hitung poin berdasarkan harga (1 poin per Rp10.000)
                $poin = floor($harga / 10000);
                $komisi = $poin * 10000;

                // Update atau buat reward penitip
                $reward = RewardPenitip::firstOrNew(['id_penitip' => $idPenitip]);
                $reward->jumlah_poin_penitip += $poin;
                $reward->komisi_penitip += $komisi;
                $reward->save();
            }
        }
        // ========== END LOGIKA POIN REWARD ==========

        return response()->json($donasi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $donasi = Donasi::findOrFail($id);
        return response()->json($donasi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);

        $validated = $request->validate([
            'id_barang' => 'required|integer',
            'id_request' => 'required|integer',
            'tanggal_donasi' => 'required|date',
            'penerima_donasi' => 'required|string|max:255',
        ]);

        $donasi->update($validated);

        return redirect()->back()->with('success', 'Donasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);
        $donasi->delete();

        return response()->json(['message' => 'Donasi deleted successfully']);
    }

    /**
     * Search donasi berdasarkan kolom tertentu.
     */
    public function search($keyword)
    {
        $results = Donasi::where('id_barang', 'like', "%$keyword%")
            ->orWhere('id_request', 'like', "%$keyword%")
            ->orWhere('tanggal_donasi', 'like', "%$keyword%")
            ->orWhere('penerima_donasi', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Donasi tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }
}
