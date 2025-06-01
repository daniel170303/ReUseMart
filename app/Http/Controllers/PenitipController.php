<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\DetailPenitipan;
use App\Models\Penitipan;
use App\Models\BarangTitipan;
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
        $penitip = Penitip::findOrFail($id);

        $barangTitipan = BarangTitipan::whereHas('penitipan', function ($query) use ($id) {
            $query->where('id_penitip', $id);
        })->with('rating')->get();

        $riwayatPenitipan = Penitipan::where('id_penitip', $id)
                                ->with(['detailPenitipan.barang'])
                                ->latest()->get();

        // Hitung rata-rata rating dari semua barang milik penitip
        $totalRating = 0;
        $jumlahRating = 0;

        foreach ($barangTitipan as $barang) {
            if ($barang->rating) {
                $totalRating += $barang->rating->rating;
                $jumlahRating++;
            }
        }

        $averageRating = $jumlahRating > 0 ? round($totalRating / $jumlahRating, 2) : null;

        return view('penitip.profilePenitip', compact('penitip', 'barangTitipan', 'riwayatPenitipan', 'averageRating'));
    }

    public function barangTitipanPenitip($id_penitip)
    {
        // Ambil semua barang titipan milik penitip tertentu
        $barangTitipan = BarangTitipan::whereHas('detailPenitipan.penitipan', function ($query) use ($id_penitip) {
            $query->where('id_penitip', $id_penitip);
        })->with(['transaksiTerakhir', 'gambarBarang'])->get();

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

        return view('penitip.barangTitipanPenitip', compact('barangTitipan'));
    }

    public function perpanjang($id_penitipan)
    {
        $penitipan = Penitipan::findOrFail($id_penitipan);

        if ($penitipan->status_perpanjangan == 'ya') {
            $tanggalSelesai = \Carbon\Carbon::parse($penitipan->tanggal_selesai_penitipan);
            $penitipan->tanggal_selesai_penitipan = $tanggalSelesai->addDays(30);
            $penitipan->save();

            return redirect()->back()->with('success', 'Masa penitipan berhasil diperpanjang 30 hari.');
        }

        return redirect()->back()->with('error', 'Penitipan tidak bisa diperpanjang.');
    }

    public function jadwalPengambilan(Request $request)
    {
        $request->validate([
            'id_penitipan' => 'required|exists:penitipan,id_penitipan',
            'tanggal_pengambilan' => 'required|date|after_or_equal:today',
        ]);

        $penitipan = Penitipan::findOrFail($request->id_penitipan);
        
        // Simpan tanggal pengambilan yang dijadwalkan penitip
        $penitipan->tanggal_pengambilan = $request->tanggal_pengambilan;
        $penitipan->save();

        return redirect()->back()->with('success', 'Jadwal pengambilan berhasil disimpan.');
    }

}