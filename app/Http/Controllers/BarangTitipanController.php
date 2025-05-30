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

        return view('pegawai.gudang.manajemenBarangTitipan', compact('barangTitipan'));
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
            'status_barang' => 'required|in:dijual,terjual,sudah diambil penitip, sudah didonasikan, barang untuk donasi',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('gambar_barang')) {
            $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
            $validatedData['gambar_barang'] = $path;
        }

        $barang = BarangTitipan::create($validatedData);

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil ditambahkan');
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
        $barang = BarangTitipan::findOrFail($id);

        $request->validate([
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'jenis_barang' => 'required|string',
            'garansi_barang' => 'nullable|string',
            'berat_barang' => 'required|numeric',
            'status_barang' => 'required|string|in:dijual,terjual,sudah diambil penitip, sudah didonasikan, barang untuk donasi',
            'deskripsi_barang' => 'required|string',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Update field utama
        $barang->update($request->only([
            'nama_barang_titipan',
            'harga_barang',
            'jenis_barang',
            'garansi_barang',
            'berat_barang',
            'status_barang',
            'deskripsi_barang',
        ]));

        // Ganti gambar utama jika ada
        if ($request->hasFile('gambar_barang')) {
            // Hapus gambar lama
            if ($barang->gambar_barang && file_exists(storage_path('app/public/' . $barang->gambar_barang))) {
                \Storage::delete('public/' . $barang->gambar_barang);
            }

            // Upload baru
            $path = $request->file('gambar_barang')->store('gambar_barang_titipan', 'public');
            $barang->gambar_barang = $path;
            $barang->save();
        }

        // Simpan gambar tambahan baru jika ada
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('gambar_barang_titipan', 'public');

                GambarBarangTitipan::create([
                    'id_barang' => $barang->id_barang,
                    'nama_file_gambar' => basename($path),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
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

        return redirect()->route('gudang.index')->with('success', 'Barang berhasil dihapus');
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
