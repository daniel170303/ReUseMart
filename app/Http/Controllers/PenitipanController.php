<?php

namespace App\Http\Controllers;

use App\Models\Penitipan;
use App\Models\DetailPenitipan;
use App\Models\Penitip;
use App\Models\BarangTitipan;
use App\Models\BarangTitipanHunter;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PenitipanController extends Controller
{
    public function index()
    {
        $penitipanList = Penitipan::all();
        $detailPenitipan = DetailPenitipan::with('barang')->get();
        $penitipList = Penitip::all();

        // Ambil ID barang yang sudah dititipkan
        $barangSudahDititipkan = DetailPenitipan::pluck('id_barang')->toArray();

        // Ambil barang yang TIDAK ada di list barang yang sudah dititipkan DAN TIDAK terhubung dengan hunter
        $barangList = BarangTitipan::whereNotIn('id_barang', $barangSudahDititipkan)
            ->where('status_barang', '!=', 'sudah diambil penitip') // Tambahan filter status
            ->where('status_barang', '!=', 'sudah didonasikan')     // Tambahan filter status
            ->get();

        $kurirs = Pegawai::where('id_role', 6)->get();

        return view('pegawai.gudang.penitipan', compact(
            'penitipanList', 
            'detailPenitipan', 
            'barangList',
            'penitipList',
            'kurirs'
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
        $tanggalBatasPengambilan = $tanggalSelesai->copy()->addDays(7);

        $barangPertama = BarangTitipan::find($request->id_barang[0]);
        $statusBarang = $barangPertama ? $barangPertama->status_barang : 'dijual';

        $penitipan = Penitipan::create([
            'id_penitip' => $request->id_penitip,
            'tanggal_penitipan' => $tanggalPenitipan,
            'tanggal_selesai_penitipan' => $tanggalSelesai,
            'tanggal_batas_pengambilan' => $tanggalBatasPengambilan,
            'status_perpanjangan' => 'tidak',
            'tanggal_pengambilan' => null,
            'status_barang' => $statusBarang,
        ]);

        foreach ($request->id_barang as $id_barang) {
            DetailPenitipan::create([
                'id_penitipan' => $penitipan->id_penitipan,
                'id_barang' => $id_barang,
            ]);
        }

        // Muat relasi penitip dan barang
        $penitipan->load(['penitip', 'detailPenitipan.barang']);

        // Generate PDF dengan format nota yang baru
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.nota_penitipan', [
            'penitipan' => $penitipan,
        ]);

        // Buat nama file dengan format: nota_penitipan_YYYY.MM.ID.pdf
        $fileName = 'nota_penitipan_' . date('Y.m', strtotime($penitipan->tanggal_penitipan)) . '.' . $penitipan->id_penitipan . '.pdf';
        
        Storage::put('public/nota/' . $fileName, $pdf->output());

        return redirect()->route('penitipan.index')->with([
            'success' => 'Data penitipan berhasil disimpan.',
            'nota_path' => $fileName,
            'last_penitipan_id' => $penitipan->id_penitipan
        ]);
    }

    public function downloadNota($id_penitipan)
    {
        $penitipan = Penitipan::with(['penitip', 'detailPenitipan.barang'])->findOrFail($id_penitipan);

        $pdf = Pdf::loadView('pdf.nota_penitipan', [
            'penitipan' => $penitipan,
        ]);

        // Buat nama file dengan format: nota_penitipan_YYYY.MM.ID.pdf
        $fileName = 'nota_penitipan_' . date('Y.m', strtotime($penitipan->tanggal_penitipan)) . '.' . $penitipan->id_penitipan . '.pdf';

        return $pdf->download($fileName);
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
            'tanggal_pengambilan' => 'nullable|date',
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

    public function perpanjang($id_penitipan)
    {
        $penitipan = Penitipan::findOrFail($id_penitipan);

        if ($penitipan->status_perpanjangan === 'tidak') {
            $tanggalSelesai = \Carbon\Carbon::parse($penitipan->tanggal_selesai_penitipan)->addDays(30);
            $tanggalBatasPengambilan = $tanggalSelesai->copy()->addDays(7);

            $penitipan->update([
                'tanggal_selesai_penitipan' => $tanggalSelesai,
                'tanggal_batas_pengambilan' => $tanggalBatasPengambilan,
                'status_perpanjangan' => 'ya',
            ]);

            return redirect()->back()->with('success', 'Masa penitipan berhasil diperpanjang 30 hari.');
        }

        return redirect()->back()->with('error', 'Penitipan tidak bisa diperpanjang.');
    }

    public function konfirmasiPengambilan($id)
    {
        $penitipan = Penitipan::findOrFail($id);

        // Update penitipan: status pengambilan, tanggal pengambilan, dan status_barang
        $penitipan->update([
            'tanggal_pengambilan' => now(),
            'status_barang' => 'sudah diambil penitip', // sekarang sudah valid
        ]);

        // Update semua barang terkait menjadi "sudah diambil penitip"
        $detailBarang = DetailPenitipan::where('id_penitipan', $id)->get();

        foreach ($detailBarang as $detail) {
            $barang = BarangTitipan::find($detail->id_barang);
            if ($barang) {
                $barang->status_barang = 'sudah diambil penitip';
                $barang->save();
            }
        }

        return back()->with('success', 'Pengambilan barang berhasil dikonfirmasi dan status diperbarui.');
    }

    public function jadwalkanPengiriman(Request $request)
    {
        $request->validate([
            'id_penitipan' => 'required|exists:penitipan,id',
            'tanggal_pengiriman' => 'required|date',
            'kurir_id' => 'required|exists:pegawai,id_pegawai',
        ]);

        $penitipan = Penitipan::with('transaksi')->findOrFail($request->id_penitipan); // pastikan ini id_penitipan

        // Validasi status transaksi dulu
        if ($penitipan->transaksi && $penitipan->transaksi->status_transaksi !== 'Dikirim') {
            return redirect()->back()->withErrors(['Hanya transaksi dengan status Dikirim yang bisa dijadwalkan.']);
        }

        // Baru update penitipan
        $penitipan->tanggal_pengiriman = $request->tanggal_pengiriman;
        $penitipan->kurir_id = $request->kurir_id;
        $penitipan->save();

        return redirect()->back()->with('success', 'Pengiriman berhasil dijadwalkan.');
    }

}
