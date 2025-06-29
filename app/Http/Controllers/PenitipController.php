<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use App\Models\DetailPenitipan;
use App\Models\Penitipan;
use App\Models\BarangTitipan;
use App\Models\SaldoPenitip;
use App\Models\RewardPenitip;
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

        $penitips = $query->get();

        if ($request->ajax()) {
            return response()->json($penitips);
        }

        return view('customerService.customerService', compact('penitips'));
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

        $validated['password_penitip'] = Hash::make($validated['password_penitip']);

        // Simpan penitip terlebih dahulu
        $penitip = Penitip::create($validated);

        // Buat entri saldo dengan nilai awal 0
        SaldoPenitip::create([
            'id_penitip' => $penitip->id_penitip,
            'saldo_penitip' => 0
        ]);

        // Buat entri reward dengan nilai awal 0
        RewardPenitip::create([
            'id_penitip' => $penitip->id_penitip,
            'jumlah_poin_penitip' => 0,
            'komisi_penitip' => 0
        ]);

        if ($request->ajax()) {
            return response()->json(['message' => 'Penitip berhasil ditambahkan', 'data' => $penitip], 201);
        }

        return redirect()->back()->with('success', 'Penitip berhasil ditambahkan');
    }

    public function show(Request $request, $id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Penitip tidak ditemukan'], 404);
            }
            return abort(404, 'Penitip tidak ditemukan');
        }

        if ($request->ajax()) {
            return response()->json($penitip);
        }

        return view('customerService.detailPenitip', compact('penitip'));
    }

    public function update(Request $request, $id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return $request->ajax()
                ? response()->json(['message' => 'Penitip tidak ditemukan'], 404)
                : redirect()->back()->with('error', 'Penitip tidak ditemukan');
        }

        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|max:16|unique:penitip,nik_penitip,' . $penitip->id_penitip . ',id_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50|unique:penitip,email_penitip,' . $penitip->id_penitip . ',id_penitip',
            'password_penitip' => 'nullable|string|min:8|max:50',
        ]);

        $validated['password_penitip'] = Hash::make($validated['password_penitip']);

        $penitip->update($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Penitip berhasil diperbarui', 'data' => $penitip]);
        }

        return redirect()->back()->with('success', 'Penitip berhasil diperbarui');
    }

    public function destroy(Request $request, $id)
    {
        $penitip = Penitip::find($id);

        if (!$penitip) {
            return $request->ajax()
                ? response()->json(['message' => 'Penitip tidak ditemukan'], 404)
                : redirect()->back()->with('error', 'Penitip tidak ditemukan');
        }

        $penitip->delete();

        if ($request->ajax()) {
            return response()->json(['message' => 'Penitip berhasil dihapus']);
        }

        return redirect()->back()->with('success', 'Penitip berhasil dihapus');
    }

    public function search(Request $request, $keyword)
    {
         $searchKeyword = $keyword ?? $request->input('keyword') ?? $request->input('search') ?? '';
        
        \Log::info('Final search keyword: ' . $searchKeyword);

        $query = Penitip::query();

        if ($searchKeyword && trim($searchKeyword) !== '') {
            $searchKeyword = trim($searchKeyword);
            
            $query->where(function($q) use ($searchKeyword) {
                $q->where('nama_penitip', 'like', "%{$searchKeyword}%")
                  ->orWhere('nik_penitip', 'like', "%{$searchKeyword}%")
                  ->orWhere('email_penitip', 'like', "%{$searchKeyword}%")
                  ->orWhere('nomor_telepon_penitip', 'like', "%{$searchKeyword}%")
                  ->orWhere('id_penitip', 'like', "%{$searchKeyword}%");
            });
        }

        $penitips = $query->orderBy('id_penitip', 'desc')->get();
        
        \Log::info('Search results count: ' . $penitips->count());

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $penitips,
                'count' => $penitips->count(),
                'keyword' => $searchKeyword
            ]);
        }

        if ($penitips->isEmpty()) {
            return redirect()->route('cs.penitip')
                ->with('warning', "Tidak ada penitip yang ditemukan dengan kata kunci: '{$searchKeyword}'");
        }

        return view('customerService.customerService', [
            'penitips' => $penitips,
            'keyword' => $searchKeyword
        ]);
    }

    public function profileById($id)
    {
        $penitip = Penitip::with(['saldo', 'reward'])->find($id);

        if (!$penitip) {
            return abort(404, 'Penitip tidak ditemukan.');
        }

        $barangTitipan = BarangTitipan::whereHas('detailPenitipan.penitipan', function ($query) use ($id) {
            $query->where('id_penitip', $id);
        })
        ->with(['transaksiTerakhir', 'gambarBarang'])
        ->get();
        $riwayatPenitipan = $penitip->riwayatPenitipan ?? collect();

        return view('penitip.profilePenitip', compact('penitip', 'barangTitipan', 'riwayatPenitipan'));
    }

    public function barangTitipanPenitip($id_penitip, Request $request)
    {
        $barangTitipan = BarangTitipan::whereHas('detailPenitipan', function ($query) use ($id_penitip) {
            $query->whereHas('penitipan', function ($subQuery) use ($id_penitip) {
                $subQuery->where('id_penitip', $id_penitip);
            });
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

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data barang titipan berhasil diambil',
                'data' => $barangTitipan,
                'count' => $barangTitipan->count(),
                'id_penitip' => $id_penitip
            ]);
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

        $tanggalSelesai = \Carbon\Carbon::parse($penitipan->tanggal_selesai_penitipan);
        $tanggalBatas = \Carbon\Carbon::parse($penitipan->tanggal_batas_pengambilan);
        $tanggalPengambilan = \Carbon\Carbon::parse($request->tanggal_pengambilan);

        // Validasi harus di antara tanggal selesai + 1 s/d tanggal batas
        if ($tanggalPengambilan->lt($tanggalSelesai->copy()->addDay()) || $tanggalPengambilan->gt($tanggalBatas)) {
            return back()->with('error', 'Tanggal pengambilan harus antara 1 hingga 7 hari setelah tanggal selesai penitipan.');
        }

        // Simpan tanggal pengambilan
        $penitipan->tanggal_pengambilan = $tanggalPengambilan;
        $penitipan->status_barang = 'akan diambil penitip'; // opsional, update status
        $penitipan->save();

        return redirect()->back()->with('success', 'Jadwal tanggal pengambilan berhasil disimpan.');
    }
}