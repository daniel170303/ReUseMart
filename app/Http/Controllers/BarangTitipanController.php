<?php

namespace App\Http\Controllers;

use App\Models\BarangTitipan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarangTitipanController extends Controller
{
    // Menampilkan semua barang
    public function index()
    {
        $barangTitipan = BarangTitipan::whereDoesntHave('transaksi')->with(['transaksiTerakhir'])->get();

        foreach ($barangTitipan as $barang) {
            if ($barang->sisa_garansi === null) {
                $barang->status_garansi = 'Tanpa Garansi';
            } elseif ($barang->garansi_masih_berlaku) {
                $tanggalHabis = now()->addMonths($barang->sisa_garansi);
                $barang->status_garansi = 'Masih Bergaransi sampai ' . $tanggalHabis->translatedFormat('d M Y');
            } else {
                $tanggalHabis = now()->subMonths(abs($barang->sisa_garansi));
                $barang->status_garansi = 'Garansi Habis pada ' . $tanggalHabis->translatedFormat('d M Y');
            }
        }

        return response()->json($barangTitipan);
    }

    // Menyimpan barang baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'garansi_barang' => 'required|string|max:50',
            'berat_barang' => 'required|integer',
            'status_barang' => 'required|in:ready,terjual',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar_barang')) {
            $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
            $validatedData['gambar_barang'] = $path;
        }

        $barang = BarangTitipan::create($validatedData);

        return response()->json([
            'message' => 'Barang Titipan berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    // Fitur pencarian
    public function search($keyword)
    {
        $results = BarangTitipan::where('nama_barang_titipan', 'like', "%$keyword%")
            ->orWhere('harga_barang', 'like', "%$keyword%")
            ->orWhere('deskripsi_barang', 'like', "%$keyword%")
            ->orWhere('jenis_barang', 'like', "%$keyword%")
            ->orWhere('garansi_barang', 'like', "%$keyword%")
            ->orWhere('berat_barang', 'like', "%$keyword%")
            ->orWhere('status_barang', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }

    // Menampilkan barang berdasarkan nama
    public function show($nama)
    {
        $barang = BarangTitipan::where('nama_barang_titipan', 'like', '%' . $nama . '%')->get();
        if ($barang->isEmpty()) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
        return response()->json($barang);
    }

    // Menampilkan detail barang + gambar + diskusi
    public function showDetail($id)
    {
        $barang = BarangTitipan::with(['gambarBarang', 'transaksiTerakhir'])->findOrFail($id);

        // Hitung tanggal habis garansi jika ada transaksi terakhir
        if ($barang->transaksiTerakhir && $barang->garansi_bulan !== null) {
            $tanggalPelunasan = Carbon::parse($barang->transaksiTerakhir->tanggal_pelunasan);
            $tanggalHabisGaransi = $tanggalPelunasan->addMonths($barang->garansi_bulan);
            $barang->tanggal_habis_garansi = $tanggalHabisGaransi->translatedFormat('d F Y');
        } else {
            $barang->tanggal_habis_garansi = 'Tidak ada garansi atau belum terjual';
        }

        $diskusi = DB::table('diskusi_produk')
            ->where('id_barang', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('detailBarang', compact('barang', 'diskusi'));
    }

    // Update barang
    public function update(Request $request, $id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $validatedData = $request->validate([
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'jenis_barang' => 'required|string',
            'garansi_barang' => 'required|string|max:50',
            'berat_barang' => 'required|integer',
            'status_barang' => 'required|in:ready,terjual',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar_barang')) {
            if ($barang->gambar_barang) {
                Storage::disk('public')->delete($barang->gambar_barang);
            }
            $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
            $validatedData['gambar_barang'] = $path;
        }

        $barang->update($validatedData);

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => $barang
        ]);
    }

    // Hapus barang
    public function destroy($id)
    {
        $barang = BarangTitipan::find($id);
        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        if ($barang->gambar_barang) {
            Storage::disk('public')->delete($barang->gambar_barang);
        }

        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }

    public function cekGaransi(Request $request)
    {
        $keyword = $request->input('keyword');

        // Cari barang berdasarkan id atau nama, yang sudah pernah transaksi
        $barang = BarangTitipan::where('id_barang', $keyword)
            ->orWhere('nama_barang_titipan', 'like', "%$keyword%")
            ->whereHas('transaksi') // pastikan barang sudah pernah transaksi
            ->with('transaksiTerakhir')
            ->first();

        if (!$barang) {
            return redirect()->back()->with('error', 'Barang tidak ditemukan atau belum pernah transaksi.');
        }

        // Hitung sisa garansi dari accessor dan bulatkan
        $sisaGaransi = round($barang->sisa_garansi);

        // Format status garansi
        if ($sisaGaransi === null) {
            $statusGaransi = 'Barang tanpa garansi atau belum terjual.';
        } elseif ($barang->garansi_masih_berlaku) {
            $tanggalHabis = \Carbon\Carbon::parse($barang->transaksiTerakhir->tanggal_pelunasan)->addMonths($barang->garansi_bulan);
            $statusGaransi = "Garansi masih berlaku, habis pada " . $tanggalHabis->translatedFormat('d M Y') . " (sisa $sisaGaransi bulan).";
        } else {
            $tanggalHabis = \Carbon\Carbon::parse($barang->transaksiTerakhir->tanggal_pelunasan)->addMonths($barang->garansi_bulan);
            $statusGaransi = "Garansi sudah habis sejak " . $tanggalHabis->translatedFormat('d M Y') . ".";
        }

        // Kirim ke view baru, atau ke halaman sama dengan session flash message
        return view('landingPage.cekGaransi', compact('barang', 'statusGaransi'));
    }

}
