<?php

namespace App\Http\Controllers;

use App\Models\Penitipan;
use App\Models\DetailPenitipan;
use App\Models\Penitip;
use App\Models\BarangTitipan;
use App\Models\BarangTitipanHunter;
use App\Models\Pegawai;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PenitipanController extends Controller
{
    public function index()
    {
        $this->updateStatusBarangMelewatiBatas();

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
        $this->updateStatusBarangMelewatiBatas();

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
        $this->updateStatusBarangMelewatiBatas();
        
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

    public function halamanJadwalPengembalian()
    {
        $jadwalPengambilan = DetailPenitipan::with(['barangTitipan', 'penitipan.penitip'])
            ->whereHas('penitipan', function ($query) {
                $query->whereNotNull('tanggal_pengambilan')
                    ->where('status_barang', 'akan diambil penitip');
            })
            ->get();

        return view('pegawai.gudang.jadwalPengembalian', compact('jadwalPengambilan'));
    }

    public function konfirmasiPengembalian(Request $request)
    {
        $request->validate([
            'id_penitipan' => 'required|exists:penitipan,id_penitipan',
        ]);

        $penitipan = Penitipan::findOrFail($request->id_penitipan);
        $tanggalBatas = \Carbon\Carbon::parse($penitipan->tanggal_batas_pengambilan);

        // Cek apakah hari ini sudah lewat dari tanggal batas pengambilan
        if (now()->gt($tanggalBatas)) {
            // Barang otomatis jadi donasi
            $penitipan->update([
                'status_barang' => 'barang untuk donasi',
            ]);

            $detailBarang = DetailPenitipan::where('id_penitipan', $penitipan->id_penitipan)->get();
            foreach ($detailBarang as $detail) {
                $barang = BarangTitipan::find($detail->id_barang);
                if ($barang) {
                    $barang->status_barang = 'barang untuk donasi';
                    $barang->save();
                }
            }

            return back()->withErrors(['Batas waktu pengambilan telah lewat. Barang otomatis menjadi donasi.']);
        }

        // Masih dalam waktu, boleh dikonfirmasi
        $penitipan->update([
            'tanggal_pengambilan' => now(),
            'status_barang' => 'sudah diambil penitip',
        ]);

        $detailBarang = DetailPenitipan::where('id_penitipan', $penitipan->id_penitipan)->get();
        foreach ($detailBarang as $detail) {
            $barang = BarangTitipan::find($detail->id_barang);
            if ($barang) {
                $barang->status_barang = 'sudah diambil penitip';
                $barang->save();
            }
        }

        return back()->with('success', 'Pengambilan barang berhasil dikonfirmasi.');
    }

    public function jadwalPengiriman()
    {
        // Ambil data transaksi
        $transaksi = \DB::table('transaksi')
            ->leftJoin('transaksi_pengiriman', 'transaksi.id_transaksi', '=', 'transaksi_pengiriman.id_transaksi')
            ->leftJoin('pegawai', function ($join) {
                $join->on('transaksi_pengiriman.id_pegawai', '=', 'pegawai.id_pegawai')
                    ->where('pegawai.id_role', 6); // pastikan hanya kurir
            })
            ->whereIn('transaksi.status_transaksi', ['dikirim', 'diambil pembeli'])
            ->select(
                'transaksi.*',
                'pegawai.nama_pegawai as nama_kurir'
            )
            ->get();

        // Ambil semua pegawai dengan role kurir
        $kurirs = \App\Models\Pegawai::where('id_role', 6)->get();

        return view('pegawai.gudang.jadwalPengiriman', compact('transaksi', 'kurirs'));
    }

    public function prosesJadwalkanPengiriman(Request $request)
    {
        // Validasi awal
        $request->validate([
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'tanggal_pengiriman' => 'nullable|date',
            'waktu_pengiriman' => 'nullable|date_format:H:i',
        ]);

        $transaksi = \DB::table('transaksi')->where('id_transaksi', $request->id_transaksi)->first();

        if (!$transaksi) {
            return back()->withErrors(['id_transaksi' => 'Transaksi tidak ditemukan'])->withInput();
        }

        $status = strtolower($transaksi->status_transaksi);

        if ($status === 'dikirim') {
            $request->validate([
                'id_kurir' => 'required|exists:pegawai,id_pegawai',
            ]);
        }

        // Gabungkan tanggal dan waktu menjadi objek Carbon
        if ($request->tanggal_pengiriman && $request->waktu_pengiriman) {
            $tanggalWaktu = Carbon::parse($request->tanggal_pengiriman . ' ' . $request->waktu_pengiriman);
        } else {
            return back()->withErrors(['waktu_pengiriman' => 'Tanggal dan waktu pengiriman harus diisi'])->withInput();
        }

        // Cek jika tanggal pengiriman adalah hari ini
        $hariIni = Carbon::today();
        if ($tanggalWaktu->isSameDay($hariIni)) {
            // Maksimal jam 16:00:00
            $batasJam = $tanggalWaktu->copy()->setTime(16, 0, 0);
            if ($tanggalWaktu->greaterThan($batasJam)) {
                return back()->withErrors(['waktu_pengiriman' => 'Waktu maksimal pukul 16:00 jika tanggal hari ini'])->withInput();
            }
        }

        // Simpan jadwal pengiriman atau pengambilan sesuai status transaksi
        if ($status === 'dikirim') {
            \DB::table('transaksi')->where('id_transaksi', $request->id_transaksi)->update([
                'tanggal_pengiriman' => $tanggalWaktu,
            ]);

            \DB::table('transaksi_pengiriman')->updateOrInsert(
                ['id_transaksi' => $request->id_transaksi],
                ['id_pegawai' => $request->id_kurir]
            );
        } elseif ($status === 'diambil pembeli') {
            \DB::table('transaksi')->where('id_transaksi', $request->id_transaksi)->update([
                'tanggal_pengambilan' => $tanggalWaktu,
            ]);
        } else {
            return back()->withErrors(['status_transaksi' => 'Status transaksi tidak valid untuk penjadwalan'])->withInput();
        }

        // Tambahkan notifikasi HANYA jika jenis pengiriman adalah 'Pengantaran'
        if (in_array($status, ['dikirim']) && strtolower($transaksi->jenis_pengiriman) === 'pengantaran') {
            // Ambil relasi pembeli dan penitip
            $barang = \DB::table('barang_titipan')->where('id_barang', $transaksi->id_barang)->first();
            $penitipan = \DB::table('detail_penitipan')->where('id_barang', $barang->id_barang)->first();
            $penitipanHeader = \DB::table('penitipan')->where('id_penitipan', $penitipan->id_penitipan)->first();
            $penitip = \DB::table('penitip')->where('id_penitip', $penitipanHeader->id_penitip)->first();
            $pembeli = \DB::table('pembeli')->where('id_pembeli', $transaksi->id_pembeli)->first();
            $kurir = \DB::table('pegawai')->where('id_pegawai', $request->id_kurir)->first();

            $waktuFormat = $tanggalWaktu->format('d M Y H:i');
        }
        return redirect()->route('pegawai.gudang.jadwalPengiriman')->with('success', 'Jadwal berhasil disimpan.');
    }

    public function indexKonfirmasiPengambilan()
    {
        $transaksi = Transaksi::where('status_transaksi', 'Diambil Pembeli')->get();

        return view('pegawai.gudang.konfirmasiPengambilan', compact('transaksi'));
    }


    // public function konfirmasiPengambilan(Request $request, $id_transaksi)
    // {
    //     $transaksi = Transaksi::findOrFail($id_transaksi);

    //     // Cek dulu status_transaksi valid
    //     if ($transaksi->status_transaksi !== 'Diambil Pembeli') {
    //         return back()->withErrors(['Status transaksi tidak valid untuk konfirmasi pengambilan.']);
    //     }

    //     // Update status_transaksi jadi 'Selesai'
    //     $transaksi->update([
    //         'status_transaksi' => 'Selesai',
    //         'tanggal_pengambilan' => now(),
    //     ]);

    //     return back()->with('success', 'Status transaksi berhasil diperbarui menjadi selesai.');
    // }

    public function viewTransaksiSederhana()
    {
        $transaksi = Transaksi::whereIn('status_transaksi', ['Siap Diambil'])->get();
        return view('pegawai.gudang.listTransaksi', compact('transaksi'));
    }

    public function konfirmasiPengambilan(Request $request, $id_transaksi)
    {
        $transaksi = Transaksi::findOrFail($id_transaksi);

        // Cek dulu status_transaksi valid
        if ($transaksi->status_transaksi !== 'Siap Diambil') {
            return back()->withErrors(['Status transaksi tidak valid untuk konfirmasi pengambilan.']);
        }

        // Update status_transaksi jadi 'Selesai'
        $transaksi->update([
            'status_transaksi' => 'Selesai',
        ]);

        return back()->with('success', 'Status transaksi berhasil diperbarui menjadi selesai.');
    }

    private function updateStatusBarangMelewatiBatas()
    {
        try {
            $hariIni = Carbon::now()->format('Y-m-d');
            
            // Cari penitipan yang melewati batas pengambilan
            $penitipanMelewatiBatas = Penitipan::where('tanggal_batas_pengambilan', '<', $hariIni)
                ->whereNull('tanggal_pengambilan') // Belum diambil
                ->whereNotIn('status_barang', ['barang untuk donasi', 'sudah didonasikan', 'sudah diambil penitip'])
                ->get();

            if ($penitipanMelewatiBatas->count() > 0) {
                $jumlahDiupdate = 0;
                
                foreach ($penitipanMelewatiBatas as $penitipan) {
                    // Update status penitipan
                    $penitipan->update([
                        'status_barang' => 'barang untuk donasi'
                    ]);

                    // Update status semua barang terkait
                    $detailBarang = DetailPenitipan::where('id_penitipan', $penitipan->id_penitipan)->get();
                    
                    foreach ($detailBarang as $detail) {
                        $barang = BarangTitipan::find($detail->id_barang);
                        if ($barang && !in_array($barang->status_barang, ['barang untuk donasi', 'sudah didonasikan', 'terjual', 'sudah diambil penitip'])) {
                            $barang->update([
                                'status_barang' => 'barang untuk donasi'
                            ]);
                            $jumlahDiupdate++;
                        }
                    }
                }

                if ($jumlahDiupdate > 0) {
                    Log::info("Status barang diupdate otomatis di PenitipanController: {$jumlahDiupdate} barang diubah menjadi 'barang untuk donasi' karena melewati batas pengambilan.");
                    
                    // Set flash message
                    session()->flash('info', "Terdapat {$jumlahDiupdate} barang yang telah diubah statusnya menjadi 'barang untuk donasi' karena melewati batas pengambilan.");
                }
            }

        } catch (\Exception $e) {
            Log::error('Error saat mengupdate status barang melewati batas di PenitipanController: ' . $e->getMessage());
        }
    }

}
