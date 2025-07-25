<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Penitipan;
use App\Models\BarangTitipan;
use App\Models\Penitip;
use App\Models\Request;
use App\Models\Donasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request as HttpRequest;

class OwnerController extends Controller
{
    public function profile($id)
    {
        // Cek apakah user yang login adalah owner
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        // Pastikan user yang login adalah owner yang benar
        if (!$owner || $owner->id_pegawai != $id) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Akses ditolak.']);
        }

        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        $processedBarangIds = [];

        // Ambil semua penitipan yang punya barang
        $penitipanList = Penitipan::with('detailPenitipan.barang')
            ->get();

        $komisi20 = collect();
        $komisi30 = collect();
        $totalKomisi20 = 0;
        $totalKomisi30 = 0;

        foreach ($penitipanList as $penitipan) {
            $statusPerpanjang = strtolower(trim($penitipan->status_perpanjangan));

            foreach ($penitipan->detailPenitipan as $detail) {
                $barang = $detail->barang;

                if (
                    !$barang ||
                    strtolower($barang->status_barang) === 'barang untuk donasi' ||
                    in_array($barang->id_barang, $processedBarangIds)
                ) {
                    continue;
                }

                $processedBarangIds[] = $barang->id_barang;
                $komisi = $barang->harga_barang * ($statusPerpanjang === 'ya' ? 0.30 : 0.20);

                if ($statusPerpanjang === 'ya') {
                    $komisi30->push([
                        'id_barang' => $barang->id_barang,
                        'nama' => $barang->nama_barang_titipan,
                        'harga' => $barang->harga_barang,
                        'komisi' => $komisi,
                    ]);
                    $totalKomisi30 += $komisi;
                } else {
                    $komisi20->push([
                        'id_barang' => $barang->id_barang,
                        'nama' => $barang->nama_barang_titipan,
                        'harga' => $barang->harga_barang,
                        'komisi' => $komisi,
                    ]);
                    $totalKomisi20 += $komisi;
                }
            }
        }

        $totalKomisi = $totalKomisi20 + $totalKomisi30;

        return view('owner.profile', compact(
            'owner',
            'komisi20',
            'komisi30',
            'totalKomisi20',
            'totalKomisi30',
            'totalKomisi'
        ));
    }

    public function barangDonasi()
    {
        try {
            // Debug: Log semua data request_donasi
            $allRequests = DB::table('request')->get();
            Log::info('All requests in database: ' . $allRequests->count());
            Log::info('All requests data: ' . json_encode($allRequests->toArray()));

            // Debug: Log requests dengan status pending
            $pendingRequests = DB::table('request')
                ->where('status_request', 'pending')
                ->get();
            Log::info('Pending requests: ' . $pendingRequests->count());
            Log::info('Pending requests data: ' . json_encode($pendingRequests->toArray()));

            // Ambil request donasi dengan status pending
            $requests = DB::table('request as rd')
                ->leftJoin('organisasi as o', 'rd.id_organisasi', '=', 'o.id_organisasi')
                ->select(
                    'rd.*',
                    'o.nama_organisasi',
                    'o.email_organisasi'
                )
                ->where('rd.status_request', 'pending')
                ->get();

            // Ambil semua request untuk riwayat (termasuk yang ditolak dan diterima)
            $allRequestsHistory = DB::table('request as rd')
                ->leftJoin('organisasi as o', 'rd.id_organisasi', '=', 'o.id_organisasi')
                ->select(
                    'rd.*',
                    'o.nama_organisasi',
                    'o.email_organisasi'
                )
                ->whereIn('rd.status_request', ['ditolak', 'diterima'])
                ->orderBy('rd.tanggal_request', 'desc')
                ->get();

            Log::info('Final requests with join: ' . $requests->count());
            Log::info('Final requests data: ' . json_encode($requests->toArray()));

            // Ambil data donasi dengan join ke tabel terkait
            $donasis = DB::table('donasi as d')
                ->leftJoin('barang_titipan as bt', 'd.id_barang', '=', 'bt.id_barang')
                ->leftJoin('request as rd', 'd.id_request', '=', 'rd.id_request')
                ->leftJoin('organisasi as o', 'rd.id_organisasi', '=', 'o.id_organisasi')
                ->select(
                    'd.*',
                    'bt.nama_barang_titipan',
                    'bt.jenis_barang',
                    'rd.nama_request_barang',
                    'o.nama_organisasi'
                )
                ->get();

            Log::info('Barang Donasi - Requests: ' . $requests->count() . ', Donasis: ' . $donasis->count());

            return view('owner.barangDonasi', compact('requests', 'donasis', 'allRequestsHistory'));
        } catch (\Exception $e) {
            Log::error('Error in barangDonasi: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return view('owner.barangDonasi', [
                'requests' => collect(),
                'donasis' => collect(),
                'allRequestsHistory' => collect()
            ])->with('error', 'Terjadi kesalahan saat memuat data: ' . $e->getMessage());
        }
    }

    public function terimaRequest($id)
    {
        try {
            // Ambil data request
            $request = DB::table('request')->where('id_request', $id)->first();
            
            if (!$request) {
                return redirect()->back()->with('error', 'Request tidak ditemukan.');
            }

            // Ambil barang yang tersedia untuk donasi
            $barangTersedia = BarangTitipan::where('status_barang', 'barang untuk donasi')
                ->get();

            return response()->json([
                'success' => true,
                'request' => $request,
                'barang_tersedia' => $barangTersedia
            ]);

        } catch (\Exception $e) {
            Log::error('Error in terimaRequest: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function tolakRequest($id)
    {
        try {
            DB::beginTransaction();

            // Update status request menjadi 'ditolak' tanpa menghapus data
            $updated = DB::table('request')
                ->where('id_request', $id)
                ->update(['status_request' => 'ditolak']);

            if ($updated) {
                DB::commit();
                return redirect()->back()->with('success', 'Request donasi berhasil ditolak.');
            } else {
                DB::rollback();
                return redirect()->back()->with('error', 'Request tidak ditemukan.');
            }

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in tolakRequest: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak request.');
        }
    }

    public function konfirmasiDonasi(HttpRequest $request)
    {
        $validated = $request->validate([
            'id_request' => 'required|integer',
            'id_barang' => 'required|integer',
            'penerima_donasi' => 'required|string|max:255',
            'tanggal_donasi' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            // Buat data donasi
            $donasi = Donasi::create([
                'id_barang' => $validated['id_barang'],
                'id_request' => $validated['id_request'],
                'tanggal_donasi' => $validated['tanggal_donasi'],
                'penerima_donasi' => $validated['penerima_donasi']
            ]);

            // Update status request menjadi 'diterima'
            DB::table('request')
                ->where('id_request', $validated['id_request'])
                ->update(['status_request' => 'diterima']);

            // Update status barang (opsional)
            BarangTitipan::where('id_barang', $validated['id_barang'])
                ->update(['status_barang' => 'barang sudah didonasikan']);

            DB::commit();

            return redirect()->back()->with('success', 'Donasi berhasil dikonfirmasi!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in konfirmasiDonasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat konfirmasi donasi: ' . $e->getMessage());
        }
    }

    public function editDonasi(HttpRequest $request, $id)
    {
        $validated = $request->validate([
            'id_barang' => 'required|integer',
            'id_request' => 'required|integer',
            'tanggal_donasi' => 'required|date',
            'penerima_donasi' => 'required|string|max:255',
        ]);

        try {
            $donasi = Donasi::findOrFail($id);
            $donasi->update($validated);

            return redirect()->back()->with('success', 'Donasi berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error in editDonasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui donasi.');
        }
    }

    public function hapusDonasi($id)
    {
        try {
            $donasi = Donasi::findOrFail($id);
            $donasi->delete();

            return redirect()->back()->with('success', 'Donasi berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Error in hapusDonasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus donasi.');
        }
    }

    public function laporanPenjualan(HttpRequest $request)
    {
        // Cek apakah user yang login adalah owner
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        // Get filter parameters - hanya tahun
        $tahun = (int) $request->get('tahun', date('Y'));

        // Data laporan penjualan tahunan (per bulan)
        $laporanTahunan = $this->getLaporanPenjualanTahunan($tahun);
        
        // Data untuk grafik (12 bulan)
        $dataGrafik = $this->getDataGrafikPenjualan($tahun);

        // Summary data
        $totalPenjualan = $laporanTahunan->sum('total_penjualan');
        $totalBarang = $laporanTahunan->sum('total_barang');
        $totalKomisi = $laporanTahunan->sum('total_komisi');

        // Nama bulan untuk tampilan
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('owner.laporanPenjualan', compact(
            'laporanTahunan',
            'dataGrafik',
            'totalPenjualan',
            'totalBarang',
            'totalKomisi',
            'tahun',
            'namaBulan'
        ));
    }

    public function laporanPenjualanPDF(HttpRequest $request)
    {
        $tahun = $request->tahun ?? date('Y');

        // Panggil data yang sama seperti laporanPenjualan()
        $laporanTahunan = $this->getLaporanPenjualanTahunan($tahun);
        $dataGrafik = $this->getDataGrafikPenjualan($tahun);
        $totalPenjualan = $laporanTahunan->sum('total_penjualan');
        $totalBarang = $laporanTahunan->sum('total_barang');
        $totalKomisi = $laporanTahunan->sum('total_komisi');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        // Generate Progress Chart
        $htmlChart = $this->generateProgressChart($dataGrafik, $tahun);

        $pdf = Pdf::loadView('owner.laporanPenjualanPDF', compact(
            'laporanTahunan', 
            'dataGrafik',
            'totalPenjualan', 
            'totalBarang', 
            'totalKomisi', 
            'tahun', 
            'namaBulan',
            'htmlChart'
        ));

        return $pdf->download('laporan-penjualan-tahunan-' . $tahun . '.pdf');
    }

    private function generateProgressChart($dataGrafik, $tahun)
    {
        $labels = $dataGrafik->pluck('bulan')->toArray();
        $data = $dataGrafik->pluck('penjualan')->toArray();
        
        if (empty($data) || array_sum($data) == 0) {
            return '<div style="text-align: center; padding: 40px; border: 2px solid #ddd; background: #f9f9f9; margin: 20px 0;">
                    <h4 style="color: #666; margin-bottom: 10px;">Tidak Ada Data Penjualan untuk Tahun ' . $tahun . '</h4>
                    <p style="color: #888; font-size: 12px;">Belum ada transaksi yang tercatat untuk tahun ini.</p>
                </div>';
        }
        
        $maxValue = max($data);
        $totalValue = array_sum($data);
        
        $chartHtml = '
    <div style="border: 2px solid #333; padding: 20px; background: white; margin: 20px 0;">
        <h4 style="text-align: center; margin-bottom: 25px; color: #333; font-size: 16px;">
            📊 Grafik Penjualan Tahunan ' . $tahun . '
        </h4>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="border: 1px solid #333; padding: 10px; width: 12%; font-size: 12px; font-weight: bold;">Bulan</th>
                    <th style="border: 1px solid #333; padding: 10px; width: 48%; font-size: 12px; font-weight: bold;">Grafik Penjualan</th>
                    <th style="border: 1px solid #333; padding: 10px; width: 25%; font-size: 12px; font-weight: bold;">Nilai Penjualan</th>
                    <th style="border: 1px solid #333; padding: 10px; width: 15%; font-size: 12px; font-weight: bold;">Persentase</th>
                </tr>
            </thead>
            <tbody>';
        
        foreach ($data as $index => $value) {
            $percentage = $maxValue > 0 ? ($value / $maxValue) * 100 : 0;
            $percentageOfTotal = $totalValue > 0 ? ($value / $totalValue) * 100 : 0;
            
            // Tentukan warna berdasarkan nilai
            $barColor = '#4285F4'; // Default blue
            if ($percentage >= 80) {
                $barColor = '#34A853'; // Green untuk nilai tinggi
            } elseif ($percentage >= 50) {
                $barColor = '#FBBC04'; // Yellow untuk nilai sedang
            } elseif ($percentage >= 25) {
                $barColor = '#FF9800'; // Orange untuk nilai rendah
            } else {
                $barColor = '#EA4335'; // Red untuk nilai sangat rendah
            }
            
            $chartHtml .= '
        <tr style="' . ($index % 2 == 0 ? 'background: #fafafa;' : '') . '">
            <td style="border: 1px solid #ccc; padding: 10px; text-align: center; font-weight: bold; font-size: 11px;">
                ' . $labels[$index] . '
            </td>
            <td style="border: 1px solid #ccc; padding: 8px;">
                <div style="width: 100%; height: 24px; background: #e9ecef; border: 1px solid #ced4da; position: relative;">
                    <div style="
                        width: ' . $percentage . '%; 
                        height: 100%; 
                        background: ' . $barColor . ';
                        position: relative;
                        transition: all 0.3s ease;
                    ">
                        <div style="
                            position: absolute;
                            right: 5px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: ' . ($percentage > 30 ? 'white' : '#333') . ';
                            font-size: 9px;
                            font-weight: bold;
                        ">
                            ' . ($percentage > 15 ? round($percentage, 1) . '%' : '') . '
                        </div>
                    </div>
                    ' . ($percentage <= 15 && $percentage > 0 ? 
                        '<div style="position: absolute; left: ' . ($percentage + 2) . '%; top: 50%; transform: translateY(-50%); font-size: 9px; color: #666;">' . round($percentage, 1) . '%</div>' 
                        : '') . '
                </div>
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; text-align: right; font-size: 11px;">
                <strong>Rp' . number_format($value, 0, ',', '.') . '</strong>
            </td>
            <td style="border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 10px;">
                ' . round($percentageOfTotal, 1) . '%
            </td>
        </tr>';
        }
        
        $chartHtml .= '
            </tbody>
            <tfoot>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td style="border: 2px solid #333; padding: 12px; text-align: center; font-size: 12px;">
                        <strong>TOTAL</strong>
                    </td>
                    <td style="border: 2px solid #333; padding: 12px; text-align: center; font-size: 11px;">
                        <div style="width: 100%; height: 24px; background: #28a745; border: 1px solid #1e7e34; color: white; line-height: 24px; text-align: center;">
                            <strong>100% (Total Penjualan)</strong>
                        </div>
                    </td>
                    <td style="border: 2px solid #333; padding: 12px; text-align: right; font-size: 12px;">
                        <strong>Rp' . number_format($totalValue, 0, ',', '.') . '</strong>
                    </td>
                    <td style="border: 2px solid #333; padding: 12px; text-align: center; font-size: 11px;">
                        <strong>100%</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Keterangan dan Analisis -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6;">
            <div style="display: table; width: 100%;">
                <div style="display: table-row;">
                    <div style="display: table-cell; width: 50%; padding-right: 15px; vertical-align: top;">
                        <h5 style="margin: 0 0 10px 0; color: #495057; font-size: 13px;">📈 Statistik Penjualan:</h5>
                        <div style="font-size: 11px; line-height: 1.4;">
                            <strong>• Rata-rata per Bulan:</strong><br>
                            &nbsp;&nbsp;Rp' . number_format($totalValue / 12, 0, ',', '.') . '<br><br>
                            
                            <strong>• Penjualan Tertinggi:</strong><br>
                            &nbsp;&nbsp;' . $labels[array_search($maxValue, $data)] . ' - Rp' . number_format($maxValue, 0, ',', '.') . '<br><br>
                            
                            <strong>• Penjualan Terendah:</strong><br>
                            &nbsp;&nbsp;' . $labels[array_search(min(array_filter($data, function($v) { return $v > 0; })), $data)] . ' - Rp' . number_format(min(array_filter($data, function($v) { return $v > 0; })) ?: 0, 0, ',', '.') . '
                        </div>
                    </div>
                    <div style="display: table-cell; width: 50%; padding-left: 15px; vertical-align: top; border-left: 1px solid #dee2e6;">
                        <h5 style="margin: 0 0 10px 0; color: #495057; font-size: 13px;">🎯 Keterangan Grafik:</h5>
                        <div style="font-size: 11px; line-height: 1.4;">
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; width: 15px; height: 12px; background: #34A853; margin-right: 8px; vertical-align: middle;"></span>
                                <strong>Hijau:</strong> Penjualan Sangat Tinggi (≥80%)
                            </div>
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; width: 15px; height: 12px; background: #FBBC04; margin-right: 8px; vertical-align: middle;"></span>
                                <strong>Kuning:</strong> Penjualan Tinggi (50-79%)
                            </div>
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; width: 15px; height: 12px; background: #FF9800; margin-right: 8px; vertical-align: middle;"></span>
                                <strong>Orange:</strong> Penjualan Sedang (25-49%)
                            </div>
                            <div style="margin-bottom: 8px;">
                                <span style="display: inline-block; width: 15px; height: 12px; background: #4285F4; margin-right: 8px; vertical-align: middle;"></span>
                                <strong>Biru:</strong> Penjualan Rendah (1-24%)
                            </div>
                            <div>
                                <span style="display: inline-block; width: 15px; height: 12px; background: #EA4335; margin-right: 8px; vertical-align: middle;"></span>
                                <strong>Merah:</strong> Tidak Ada Penjualan (0%)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>';
        
        return $chartHtml;
    }

    private function getLaporanPenjualanTahunan($tahun)
    {
        $laporanPerBulan = [];
        
        // Nama bulan
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            // Query yang lebih sederhana tanpa join ke penitipan dulu
            $dataBulan = DB::table('transaksi as t')
                ->join('barang_titipan as bt', 't.id_barang', '=', 'bt.id_barang')
                ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->leftJoin('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->select(
                    DB::raw('SUM(bt.harga_barang) as total_penjualan'),
                    DB::raw('COUNT(t.id_transaksi) as total_barang'),
                    DB::raw('SUM(CASE 
                        WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.30 
                        ELSE bt.harga_barang * 0.20 
                    END) as total_komisi')
                )
                ->whereMonth('t.tanggal_pelunasan', $bulan)
                ->whereYear('t.tanggal_pelunasan', $tahun)
                ->where('t.status_transaksi', 'Selesai')
                ->whereNotNull('t.tanggal_pelunasan')
                ->first();

            $laporanPerBulan[] = (object) [
                'bulan' => $bulan,
                'nama_bulan' => $namaBulan[$bulan],
                'total_penjualan' => $dataBulan->total_penjualan ?? 0,
                'total_barang' => $dataBulan->total_barang ?? 0,
                'total_komisi' => $dataBulan->total_komisi ?? 0,
            ];
        }

        return collect($laporanPerBulan);
    }

    private function getDataGrafikPenjualan($tahun)
    {
        $dataGrafik = [];
        
        for ($i = 1; $i <= 12; $i++) {
            // Query untuk grafik menggunakan harga_barang
            $penjualan = DB::table('transaksi as t')
                ->join('barang_titipan as bt', 't.id_barang', '=', 'bt.id_barang')
                ->whereMonth('t.tanggal_pelunasan', $i)
                ->whereYear('t.tanggal_pelunasan', $tahun)
                ->where('t.status_transaksi', 'Selesai')
                ->whereNotNull('t.tanggal_pelunasan')
                ->sum('bt.harga_barang');

            $dataGrafik[] = [
                'bulan' => date('M', mktime(0, 0, 0, $i, 1)),
                'bulan_angka' => $i,
                'penjualan' => $penjualan ?: 0
            ];
        }

        return collect($dataGrafik);
    }

    public function laporanKomisi(HttpRequest $request)
    {
        try {
            // Cek apakah user yang login adalah owner
            if (!Auth::guard('pegawai')->check()) {
                return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
            }

            $owner = Auth::guard('pegawai')->user();
            
            if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
                return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
            }

            // Get filter parameters
            $bulan = (int) $request->get('bulan', date('m'));
            $tahun = (int) $request->get('tahun', date('Y'));

            // Data laporan komisi bulanan per produk
            $laporanKomisi = $this->getLaporanKomisiBulanan($bulan, $tahun);
            
            // Data untuk grafik komisi
            $dataGrafikKomisi = $this->getDataGrafikKomisi($tahun);

            // Summary data
            $totalKomisi = $laporanKomisi->sum('komisi');
            $totalProduk = $laporanKomisi->count();
            $totalPenjualan = $laporanKomisi->sum('harga_jual');
            $rataRataKomisi = $totalProduk > 0 ? $totalKomisi / $totalProduk : 0;

            // Nama bulan untuk tampilan
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $namaBulanTerpilih = isset($namaBulan[$bulan]) ? $namaBulan[$bulan] : 'Tidak Diketahui';

            return view('owner.laporanKomisi', compact(
                'laporanKomisi',
                'dataGrafikKomisi',
                'totalKomisi',
                'totalProduk',
                'totalPenjualan',
                'rataRataKomisi',
                'bulan',
                'tahun',
                'namaBulan',
                'namaBulanTerpilih'
            ));
        } catch (\Exception $e) {
            // Handle exception
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function laporanKomisiPDF(HttpRequest $request)
    {
        try {
            $bulan = (int) ($request->bulan ?? date('m'));
            $tahun = (int) ($request->tahun ?? date('Y'));

            // Panggil data yang sama seperti laporanKomisi()
            $laporanKomisi = $this->getLaporanKomisiBulanan($bulan, $tahun);
            
            // Hitung summary data
            $totalProduk = $laporanKomisi->count();
            $totalPenjualan = $laporanKomisi->sum('harga_jual');
            $totalKomisiHunter = $laporanKomisi->sum('komisi_hunter');
            $totalKomisiReUse = $laporanKomisi->sum('komisi_reuse_mart');
            $totalBonusPenitip = $laporanKomisi->sum('bonus_penitip');

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $namaBulanTerpilih = $namaBulan[$bulan];

            // Load view dengan data
            $pdf = Pdf::loadView('owner.laporanKomisiPDF', compact(
                'laporanKomisi',
                'totalProduk',
                'totalPenjualan', 
                'totalKomisiHunter',
                'totalKomisiReUse',
                'totalBonusPenitip',
                'bulan',
                'tahun',
                'namaBulanTerpilih'
            ));

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'landscape');
            
            // Set options untuk DomPDF
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

            return $pdf->download('laporan-komisi-' . strtolower($namaBulanTerpilih) . '-' . $tahun . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    private function getLaporanKomisiBulanan($bulan, $tahun)
    {
        return DB::table('transaksi as t')
            ->join('barang_titipan as bt', 't.id_barang', '=', 'bt.id_barang')
            ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
            ->leftJoin('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
            ->leftJoin('penitip as pen', 'p.id_penitip', '=', 'pen.id_penitip')
            ->select(
                'bt.id_barang',
                'bt.id_barang as kode_produk',
                'bt.nama_barang_titipan',
                'bt.harga_barang as harga_jual',
                'bt.created_at as tanggal_masuk',
                't.tanggal_pelunasan as tanggal_terjual',
                'pen.nama_penitip',
                'bt.deskripsi_barang',
                DB::raw('0 as komisi_hunter'),
                DB::raw('CASE 
                    WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.30 
                    ELSE bt.harga_barang * 0.20 
                END as komisi_reuse_mart'),
                DB::raw('0 as bonus_penitip'),
                DB::raw('CASE 
                    WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN "30%" 
                    ELSE "20%" 
                END as persentase_komisi'),
                DB::raw('CASE 
                    WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.30 
                    ELSE bt.harga_barang * 0.20 
                END as komisi')
            )
            ->whereMonth('t.tanggal_pelunasan', $bulan)
            ->whereYear('t.tanggal_pelunasan', $tahun)
            ->where('t.status_transaksi', 'Selesai')
            ->whereNotNull('t.tanggal_pelunasan')
            ->orderBy('komisi', 'desc')
            ->get();
    }

    private function getDataGrafikKomisi($tahun)
    {
        $dataGrafik = [];
        
        for ($i = 1; $i <= 12; $i++) {
            $komisi = DB::table('transaksi as t')
                ->join('barang_titipan as bt', 't.id_barang', '=', 'bt.id_barang')
                ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->leftJoin('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->whereMonth('t.tanggal_pelunasan', $i)
                ->whereYear('t.tanggal_pelunasan', $tahun)
                ->where('t.status_transaksi', 'Selesai')
                ->whereNotNull('t.tanggal_pelunasan')
                ->sum(DB::raw('CASE 
                    WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.30 
                    ELSE bt.harga_barang * 0.20 
                END'));

            $dataGrafik[] = [
                'bulan' => date('M', mktime(0, 0, 0, $i, 1)),
                'bulan_angka' => $i,
                'komisi' => $komisi ?: 0
            ];
        }

        return collect($dataGrafik);
    }

    private function generateKomisiChart($laporanKomisi, $namaBulan, $tahun)
    {
        if ($laporanKomisi->isEmpty()) {
            return '<div style="text-align: center; padding: 40px; border: 2px solid #ddd; background: #f9f9f9; margin: 20px 0;">
                    <h4 style="color: #666; margin-bottom: 10px;">Tidak Ada Data Komisi untuk ' . $namaBulan . ' ' . $tahun . '</h4>
                    <p style="color: #888; font-size: 12px;">Belum ada transaksi yang menghasilkan komisi pada periode ini.</p>
                </div>';
        }

        // Ambil top 10 produk dengan komisi tertinggi
        $topProduk = $laporanKomisi->take(10);
        $maxKomisi = $topProduk->max('komisi');
        
        $chartHtml = '
    <div style="border: 2px solid #333; padding: 20px; background: white; margin: 20px 0;">
        <h4 style="text-align: center; margin-bottom: 25px; color: #333; font-size: 16px;">
            💰 Top 10 Produk dengan Komisi Tertinggi - ' . $namaBulan . ' ' . $tahun . '
        </h4>
        
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="border: 1px solid #333; padding: 8px; width: 8%; font-size: 11px;">Rank</th>
                    <th style="border: 1px solid #333; padding: 8px; width: 25%; font-size: 11px;">Nama Produk</th>
                    <th style="border: 1px solid #333; padding: 8px; width: 37%; font-size: 11px;">Grafik Komisi</th>
                    <th style="border: 1px solid #333; padding: 8px; width: 15%; font-size: 11px;">Komisi</th>
                    <th style="border: 1px solid #333; padding: 8px; width: 15%; font-size: 11px;">Persentase</th>
                </tr>
            </thead>
            <tbody>';
    
        foreach ($topProduk as $index => $produk) {
            $percentage = $maxKomisi > 0 ? ($produk->komisi / $maxKomisi) * 100 : 0;
        
            // Tentukan warna berdasarkan ranking
            $colors = ['#FFD700', '#C0C0C0', '#CD7F32', '#4285F4', '#34A853', '#FBBC04', '#FF9800', '#9C27B0', '#E91E63', '#795548'];
            $barColor = $colors[$index] ?? '#6c757d';
        
            $chartHtml .= '
        <tr style="' . ($index % 2 == 0 ? 'background: #fafafa;' : '') . '">
            <td style="border: 1px solid #ccc; padding: 8px; text-align: center; font-weight: bold; font-size: 11px;">
                #' . ($index + 1) . '
            </td>
            <td style="border: 1px solid #ccc; padding: 8px; font-size: 10px;">
                <strong>' . substr($produk->nama_barang_titipan, 0, 30) . '</strong><br>
                <small style="color: #666;">ID: ' . $produk->id_barang . '</small>
            </td>
            <td style="border: 1px solid #ccc; padding: 6px;">
                <div style="width: 100%; height: 20px; background: #e9ecef; border: 1px solid #ced4da; position: relative;">
                    <div style="
                        width: ' . $percentage . '%; 
                        height: 100%; 
                        background: ' . $barColor . ';
                        position: relative;
                    ">
                        <div style="
                            position: absolute;
                            right: 3px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: white;
                            font-size: 8px;
                            font-weight: bold;
                            text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
                        ">
                            ' . ($percentage > 20 ? round($percentage, 1) . '%' : '') . '
                        </div>
                    </div>
                </div>
            </td>
            <td style="border: 1px solid #ccc; padding: 8px; text-align: right; font-size: 10px;">
                <strong>Rp' . number_format($produk->komisi, 0, ',', '.') . '</strong>
            </td>
            <td style="border: 1px solid #ccc; padding: 8px; text-align: center; font-size: 10px;">
                ' . $produk->persentase_komisi . '
            </td>
        </tr>';
        }
    
        $totalKomisiTop10 = $topProduk->sum('komisi');
    
        $chartHtml .= '
            </tbody>
            <tfoot>
                <tr style="background: #e9ecef; font-weight: bold;">
                    <td colspan="3" style="border: 2px solid #333; padding: 10px; text-align: center; font-size: 11px;">
                        <strong>TOTAL TOP 10</strong>
                    </td>
                    <td style="border: 2px solid #333; padding: 10px; text-align: right; font-size: 11px;">
                        <strong>Rp' . number_format($totalKomisiTop10, 0, ',', '.') . '</strong>
                    </td>
                    <td style="border: 2px solid #333; padding: 10px; text-align: center; font-size: 10px;">
                        <strong>Mix</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
        
        <div style="margin-top: 15px; font-size: 10px; color: #6c757d; text-align: center; font-style: italic;">
            * Grafik men</div>
    </div>';
        
        return $chartHtml;
    }

    public function laporanStokGudang(HttpRequest $request)
    {
        try {
            // Cek apakah user yang login adalah owner
            if (!Auth::guard('pegawai')->check()) {
                return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
            }

            $owner = Auth::guard('pegawai')->user();
            
            if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
                return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
            }

            // Ambil data stok gudang hari ini berdasarkan tanggal penitipan
            $stokGudang = $this->getStokGudangHariIni();
            
            // Summary data
            $totalStok = $stokGudang->count();
            $totalNilaiStok = $stokGudang->sum('harga');
            $stokPerpanjangan = $stokGudang->where('perpanjangan', 'Ya')->count();
            $stokNormal = $stokGudang->where('perpanjangan', 'Tidak')->count();

            // Tambamb informasi tanggal
            $tanggalHariIni = \Carbon\Carbon::now()->translatedFormat('d F Y');

            return view('owner.laporanStokGudang', compact(
                'stokGudang',
                'totalStok',
                'totalNilaiStok',
                'stokPerpanjangan',
                'stokNormal',
                'tanggalHariIni'
            ));

        } catch (\Exception $e) {
            \Log::error('Error di laporanStokGudang: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function laporanStokGudangPDF(Request $request)
    {
        try {
            // Ambil data stok gudang hari ini berdasarkan tanggal penitipan
            $stokGudang = $this->getStokGudangHariIni();
            
            // Summary data
            $totalStok = $stokGudang->count();
            $totalNilaiStok = $stokGudang->sum('harga');
            $tanggalHariIni = \Carbon\Carbon::now()->translatedFormat('d F Y');

            $pdf = Pdf::loadView('owner.laporanStokGudangPDF', compact(
                'stokGudang',
                'totalStok',
                'totalNilaiStok',
                'tanggalHariIni'
            ));

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'landscape');
            
            // Set options untuk DomPDF
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

            $tanggal = \Carbon\Carbon::now()->format('d-m-Y');
            return $pdf->download('laporan-stok-gudang-' . $tanggal . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating Stok Gudang PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    private function getStokGudangHariIni()
    {
        $tanggalHariIni = \Carbon\Carbon::now()->format('Y-m-d');
    
        return DB::table('barang_titipan as bt')
            ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
            ->leftJoin('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
            ->leftJoin('penitip as pen', 'p.id_penitip', '=', 'pen.id_penitip')
            ->leftJoin('transaksi as t', function($join) {
                $join->on('bt.id_barang', '=', 't.id_barang')
                     ->where('t.status_transaksi', '=', 'Selesai');
            })
            ->select(
                'bt.id_barang as kode_produk',
                'bt.nama_barang_titipan as nama_produk',
                'pen.id_penitip',
                'pen.nama_penitip',
                'p.tanggal_penitipan as tanggal_masuk',
                DB::raw('CASE 
                    WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN "Ya" 
                    ELSE "Tidak" 
                END as perpanjangan'),
                DB::raw('NULL as id_hunter'),
                DB::raw('NULL as nama_hunter'),
                'bt.harga_barang as harga'
            )
            ->where('bt.status_barang', '!=', 'barang untuk donasi')
            ->whereNull('t.id_transaksi')
            ->whereNotNull('p.tanggal_penitipan')
            ->orderBy('p.tanggal_penitipan', 'desc')
            ->orderBy('bt.created_at', 'desc')
            ->get();
    }

    public function laporanPenjualanPerKategori(HttpRequest $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Validasi tahun
        if (!is_numeric($tahun) || $tahun < 2020 || $tahun > date('Y')) {
            $tahun = date('Y');
        }

        // Daftar kategori/jenis barang
        $kategoriBarang = [
            'Elektronik & Gadget',
            'Pakaian & Aksesori', 
            'Perabotan Rumah Tangga',
            'Buku, Alat Tulis, & Peralatan Sekolah',
            'Hobi, Mainan, & Koleksi',
            'Perlengkapan Bayi & Anak',
            'Otomotif & Aksesori',
            'Perlengkapan Taman & Outdoor',
            'Peralatan Kantor & Industri',
            'Kosmetik & Perawatan Diri'
        ];

        $laporanKategori = [];
        $totalTerjual = 0;
        $totalGagalTerjual = 0;
        $totalBelumTerjual = 0;

        foreach ($kategoriBarang as $kategori) {
            // Hitung barang terjual: status 'dijual' DAN ada transaksi 'Selesai' yang sudah dilunasi
            $barangTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->join('transaksi as t', 'bt.id_barang', '=', 't.id_barang')
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'dijual')
                ->where('t.status_transaksi', 'Selesai')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->whereNotNull('t.tanggal_pelunasan')
                ->distinct('bt.id_barang')
                ->count('bt.id_barang');

            // Hitung barang gagal terjual: status 'sudah diambil penitip' (tidak terjual)
            $barangGagalTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'sudah diambil penitip')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->count();

            // Hitung barang belum terjual: status 'dijual' tapi BELUM ada transaksi 'Selesai'
            $barangBelumTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->leftJoin('transaksi as t', function($join) {
                    $join->on('bt.id_barang', '=', 't.id_barang')
                         ->where('t.status_transaksi', '=', 'Selesai')
                         ->whereNotNull('t.tanggal_pelunasan');
                })
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'dijual')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->whereNull('t.id_barang') // Tidak ada transaksi selesai
                ->count();

            $laporanKategori[] = [
                'kategori' => $kategori,
                'terjual' => $barangTerjual,
                'gagal_terjual' => $barangGagalTerjual,
                'belum_terjual' => $barangBelumTerjual
            ];

            $totalTerjual += $barangTerjual;
            $totalGagalTerjual += $barangGagalTerjual;
            $totalBelumTerjual += $barangBelumTerjual;
        }

        // Urutkan berdasarkan total barang (descending)
        usort($laporanKategori, function($a, $b) {
            $totalA = $a['terjual'] + $a['gagal_terjual'] + $a['belum_terjual'];
            $totalB = $b['terjual'] + $b['gagal_terjual'] + $b['belum_terjual'];
            return $totalB - $totalA;
        });

        return view('owner.laporanPenjualanPerKategori', compact(
            'laporanKategori',
            'tahun',
            'totalTerjual',
            'totalGagalTerjual',
            'totalBelumTerjual'
        ));
    }

    public function laporanPenjualanPerKategoriPDF(HttpRequest $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        
        // Validasi tahun
        if (!is_numeric($tahun) || $tahun < 2020 || $tahun > date('Y')) {
            $tahun = date('Y');
        }

        // Daftar kategori/jenis barang
        $kategoriBarang = [
            'Elektronik & Gadget',
            'Pakaian & Aksesori', 
            'Perabotan Rumah Tangga',
            'Buku, Alat Tulis, & Peralatan Sekolah',
            'Hobi, Mainan, & Koleksi',
            'Perlengkapan Bayi & Anak',
            'Otomotif & Aksesori',
            'Perlengkapan Taman & Outdoor',
            'Peralatan Kantor & Industri',
            'Kosmetik & Perawatan Diri'
        ];

        $laporanKategori = [];
        $totalTerjual = 0;
        $totalGagalTerjual = 0;
        $totalBelumTerjual = 0;

        foreach ($kategoriBarang as $kategori) {
            $barangTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->join('transaksi as t', 'bt.id_barang', '=', 't.id_barang')
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'dijual')
                ->where('t.status_transaksi', 'Selesai')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->whereNotNull('t.tanggal_pelunasan')
                ->distinct('bt.id_barang')
                ->count('bt.id_barang');

            $barangGagalTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'sudah diambil penitip')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->count();

            $barangBelumTerjual = \DB::table('barang_titipan as bt')
                ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
                ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
                ->leftJoin('transaksi as t', function($join) {
                    $join->on('bt.id_barang', '=', 't.id_barang')
                         ->where('t.status_transaksi', '=', 'Selesai')
                         ->whereNotNull('t.tanggal_pelunasan');
                })
                ->where('bt.jenis_barang', $kategori)
                ->where('bt.status_barang', 'dijual')
                ->whereYear('p.tanggal_penitipan', $tahun)
                ->whereNull('t.id_barang')
                ->count();

            $laporanKategori[] = [
                'kategori' => $kategori,
                'terjual' => $barangTerjual,
                'gagal_terjual' => $barangGagalTerjual,
                'belum_terjual' => $barangBelumTerjual
            ];

            $totalTerjual += $barangTerjual;
            $totalGagalTerjual += $barangGagalTerjual;
            $totalBelumTerjual += $barangBelumTerjual;
        }

        usort($laporanKategori, function($a, $b) {
            $totalA = $a['terjual'] + $a['gagal_terjual'] + $a['belum_terjual'];
            $totalB = $b['terjual'] + $b['gagal_terjual'] + $b['belum_terjual'];
            return $totalB - $totalA;
        });

        $tanggalCetak = now()->format('d F Y');

        // Generate PDF
        $pdf = Pdf::loadView('owner.laporanPenjualanPerKategoriPDF', compact(
            'laporanKategori',
            'tahun',
            'totalTerjual',
            'totalGagalTerjual',
            'totalBelumTerjual',
            'tanggalCetak'
        ));

        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download('laporan-penjualan-per-kategori-' . $tahun . '.pdf');
    }

    public function laporanMasaPenitipanHabis()
    {
        $penitipanHabis = \DB::table('penitipan as p')
            ->join('detail_penitipan as dp', 'p.id_penitipan', '=', 'dp.id_penitipan')
            ->join('barang_titipan as bt', 'dp.id_barang', '=', 'bt.id_barang')
            ->join('penitip as pt', 'p.id_penitip', '=', 'pt.id_penitip')
            ->select(
                'bt.id_barang as kode_produk',
                'bt.nama_barang_titipan as nama_produk',
                'pt.id_penitip',
                'pt.nama_penitip',
                'p.tanggal_penitipan as tanggal_masuk',
                'p.tanggal_selesai_penitipan as tanggal_akhir',
                'p.tanggal_batas_pengambilan as batas_ambil',
                'bt.status_barang',
                'p.id_penitipan'
            )
            ->where('p.tanggal_selesai_penitipan', '<', now())
            ->where('bt.status_barang', '!=', 'sudah diambil penitip')
            ->where('bt.status_barang', '!=', 'sudah didonasikan')
            ->orderBy('p.tanggal_selesai_penitipan', 'asc')
            ->get();

        $totalBarang = $penitipanHabis->count();
        $totalPenitip = $penitipanHabis->unique('id_penitip')->count();

        return view('owner.laporanMasaPenitipanHabis', compact(
            'penitipanHabis',
            'totalBarang',
            'totalPenitip'
        ));
    }

    public function laporanMasaPenitipanHabisPDF()
    {
        $penitipanHabis = \DB::table('penitipan as p')
            ->join('detail_penitipan as dp', 'p.id_penitipan', '=', 'dp.id_penitipan')
            ->join('barang_titipan as bt', 'dp.id_barang', '=', 'bt.id_barang')
            ->join('penitip as pt', 'p.id_penitip', '=', 'pt.id_penitip')
            ->select(
                'bt.id_barang as kode_produk',
                'bt.nama_barang_titipan as nama_produk',
                'pt.id_penitip',
                'pt.nama_penitip',
                'p.tanggal_penitipan as tanggal_masuk',
                'p.tanggal_selesai_penitipan as tanggal_akhir',
                'p.tanggal_batas_pengambilan as batas_ambil',
                'bt.status_barang',
                'p.id_penitipan'
            )
            ->where('p.tanggal_selesai_penitipan', '<', now())
            ->where('bt.status_barang', '!=', 'sudah diambil penitip')
            ->where('bt.status_barang', '!=', 'sudah didonasikan')
            ->orderBy('p.tanggal_selesai_penitipan', 'asc')
            ->get();

        $totalBarang = $penitipanHabis->count();
        $totalPenitip = $penitipanHabis->unique('id_penitip')->count();
        $tanggalCetak = now()->format('d F Y');

        // Generate PDF
        $pdf = Pdf::loadView('owner.laporanMasaPenitipanHabisPDF', compact(
            'penitipanHabis',
            'totalBarang',
            'totalPenitip',
            'tanggalCetak'
        ));

        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download('laporan-masa-penitipan-habis-' . now()->format('Y-m-d') . '.pdf');
    }

    public function laporanKomisiPerHunter(HttpRequest $request)
    {
        try {
            // Cek apakah user yang login adalah owner
            if (!Auth::guard('pegawai')->check()) {
                return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
            }

            $owner = Auth::guard('pegawai')->user();
            
            if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
                return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
            }

            // Get filter parameters
            $bulan = (int) $request->get('bulan', date('m'));
            $tahun = (int) $request->get('tahun', date('Y'));

            // Data laporan komisi per hunter
            $laporanKomisiHunter = $this->getLaporanKomisiPerHunter($bulan, $tahun);
            
            // Summary data
            $totalKomisiSemua = $laporanKomisiHunter->sum('total_komisi');
            $totalProdukSemua = $laporanKomisiHunter->sum('total_produk');
            $totalPenjualanSemua = $laporanKomisiHunter->sum('total_penjualan');
            $jumlahHunter = $laporanKomisiHunter->count();

            // Nama bulan untuk tampilan
            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $namaBulanTerpilih = isset($namaBulan[$bulan]) ? $namaBulan[$bulan] : 'Tidak Diketahui';

            return view('owner.laporanKomisiPerHunter', compact(
                'laporanKomisiHunter',
                'totalKomisiSemua',
                'totalProdukSemua',
                'totalPenjualanSemua',
                'jumlahHunter',
                'bulan',
                'tahun',
                'namaBulan',
                'namaBulanTerpilih'
            ));

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function laporanKomisiPerHunterPDF(HttpRequest $request)
    {
        try {
            $bulan = (int) ($request->bulan ?? date('m'));
            $tahun = (int) ($request->tahun ?? date('Y'));

            // Panggil data yang sama seperti laporanKomisiPerHunter()
            $laporanKomisiHunter = $this->getLaporanKomisiPerHunter($bulan, $tahun);
            
            // Hitung summary data
            $totalKomisiSemua = $laporanKomisiHunter->sum('total_komisi');
            $totalProdukSemua = $laporanKomisiHunter->sum('total_produk');
            $totalPenjualanSemua = $laporanKomisiHunter->sum('total_penjualan');
            $jumlahHunter = $laporanKomisiHunter->count();

            $namaBulan = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $namaBulanTerpilih = $namaBulan[$bulan];

            // Load view dengan data
            $pdf = Pdf::loadView('owner.laporanKomisiPerHunterPDF', compact(
                'laporanKomisiHunter',
                'totalKomisiSemua',
                'totalProdukSemua',
                'totalPenjualanSemua',
                'jumlahHunter',
                'bulan',
                'tahun',
                'namaBulanTerpilih'
            ));

            // Set paper size dan orientation
            $pdf->setPaper('A4', 'portrait');
            
            // Set options untuk DomPDF
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans'
            ]);

            return $pdf->download('laporan-komisi-per-hunter-' . strtolower($namaBulanTerpilih) . '-' . $tahun . '.pdf');

        } catch (\Exception $e) {
            \Log::error('Error generating PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
        }
    }

    private function getLaporanKomisiPerHunter($bulan, $tahun)
    {
        return DB::table('pegawai as p')
            ->join('barang_titipan_hunter as bth', 'p.id_pegawai', '=', 'bth.id_pegawai')
            ->join('barang_titipan as bt', 'bth.id_barang', '=', 'bt.id_barang')
            ->join('transaksi as t', 'bt.id_barang', '=', 't.id_barang')
            ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
            ->leftJoin('penitipan as pen', 'dp.id_penitipan', '=', 'pen.id_penitipan')
            ->select(
                'p.id_pegawai',
                'p.nama_pegawai as nama_hunter',
                DB::raw('COUNT(t.id_transaksi) as total_produk'),
                DB::raw('SUM(bt.harga_barang) as total_penjualan'),
                DB::raw('SUM(CASE 
                    WHEN LOWER(TRIM(COALESCE(pen.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.10 
                    ELSE bt.harga_barang * 0.05 
                END) as total_komisi'),
                DB::raw('ROUND(AVG(bt.harga_barang), 0) as rata_rata_harga')
            )
            ->where('p.id_role', 5) // Role hunter
            ->whereMonth('t.tanggal_pelunasan', $bulan)
            ->whereYear('t.tanggal_pelunasan', $tahun)
            ->where('t.status_transaksi', 'Selesai')
            ->whereNotNull('t.tanggal_pelunasan')
            ->groupBy('p.id_pegawai', 'p.nama_pegawai')
            ->orderBy('total_komisi', 'desc')
            ->get();
    }

    // Add this method to the existing OwnerController class

public function laporanBarangDonasi(HttpRequest $request)
{
    try {
        // Cek apakah user yang login adalah owner
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        // Get filter parameters
        $tahun = (int) $request->get('tahun', date('Y'));

        // Data laporan donasi barang
        $laporanDonasi = $this->getLaporanDonasiBarang($tahun);
        
        // Summary data
        $totalDonasi = $laporanDonasi->count();
        $totalOrganisasi = $laporanDonasi->unique('nama_organisasi')->count();

        return view('owner.laporanBarangDonasi', compact(
            'laporanDonasi',
            'totalDonasi',
            'totalOrganisasi',
            'tahun'
        ));

    } catch (\Exception $e) {
        Log::error('Error in laporanBarangDonasi: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function laporanBarangDonasiPDF(HttpRequest $request)
{
    try {
        $tahun = (int) ($request->tahun ?? date('Y'));

        // Panggil data yang sama seperti laporanBarangDonasi()
        $laporanDonasi = $this->getLaporanDonasiBarang($tahun);
        
        // Hitung summary data
        $totalDonasi = $laporanDonasi->count();
        $totalOrganisasi = $laporanDonasi->unique('nama_organisasi')->count();
        $tanggalCetak = now()->translatedFormat('d F Y');

        // Load view dengan data
        $pdf = Pdf::loadView('owner.laporanBarangDonasiPDF', compact(
            'laporanDonasi',
            'totalDonasi',
            'totalOrganisasi',
            'tahun',
            'tanggalCetak'
        ));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Set options untuk DomPDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        return $pdf->download('laporan-donasi-barang-' . $tahun . '.pdf');

    } catch (\Exception $e) {
        Log::error('Error generating Donasi PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
    }
}

private function getLaporanDonasiBarang($tahun)
{
    return DB::table('donasi as d')
        ->join('barang_titipan as bt', 'd.id_barang', '=', 'bt.id_barang')
        ->join('request as r', 'd.id_request', '=', 'r.id_request')
        ->leftJoin('organisasi as o', 'r.id_organisasi', '=', 'o.id_organisasi')
        ->leftJoin('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
        ->leftJoin('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
        ->leftJoin('penitip as pen', 'p.id_penitip', '=', 'pen.id_penitip')
        ->select(
            'bt.id_barang as kode_produk',
            'bt.nama_barang_titipan as nama_produk',
            'pen.id_penitip',
            'pen.nama_penitip',
            'd.tanggal_donasi',
            'o.nama_organisasi',
            'd.penerima_donasi as nama_penerima',
            'r.nama_request_barang',
            'd.id_donasi'
        )
        ->whereYear('d.tanggal_donasi', $tahun)
        ->orderBy('d.tanggal_donasi', 'desc')
        ->get();
}

// Add these methods to the existing OwnerController class

public function laporanRequestDonasi(HttpRequest $request)
{
    try {
        // Cek apakah user yang login adalah owner
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        // Get filter parameters
        $status = $request->get('status', 'pending'); // Default to pending requests
        $tahun = (int) $request->get('tahun', date('Y'));

        // Data laporan request donasi
        $laporanRequest = $this->getLaporanRequestDonasi($status, $tahun);
        
        // Summary data
        $totalRequest = $laporanRequest->count();
        $totalOrganisasi = $laporanRequest->unique('id_organisasi')->count();
        $requestPending = $laporanRequest->where('status_request', 'pending')->count();
        $requestDiterima = $laporanRequest->where('status_request', 'diterima')->count();
        $requestDitolak = $laporanRequest->where('status_request', 'ditolak')->count();

        return view('owner.laporanRequestDonasi', compact(
            'laporanRequest',
            'totalRequest',
            'totalOrganisasi',
            'requestPending',
            'requestDiterima',
            'requestDitolak',
            'status',
            'tahun'
        ));

    } catch (\Exception $e) {
        Log::error('Error in laporanRequestDonasi: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function laporanRequestDonasiPDF(HttpRequest $request)
{
    try {
        $status = $request->get('status', 'pending');
        $tahun = (int) ($request->tahun ?? date('Y'));

        // Panggil data yang sama seperti laporanRequestDonasi()
        $laporanRequest = $this->getLaporanRequestDonasi($status, $tahun);
        
        // Hitung summary data
        $totalRequest = $laporanRequest->count();
        $totalOrganisasi = $laporanRequest->unique('id_organisasi')->count();
        $tanggalCetak = now()->translatedFormat('d F Y');

        // Tentukan judul berdasarkan status
        $judulLaporan = 'LAPORAN REQUEST DONASI';
        if ($status === 'pending') {
            $judulLaporan = 'REKAP REQUEST DONASI (BELUM TERPENUHI)';
        } elseif ($status === 'diterima') {
            $judulLaporan = 'REKAP REQUEST DONASI (SUDAH TERPENUHI)';
        } elseif ($status === 'ditolak') {
            $judulLaporan = 'REKAP REQUEST DONASI (DITOLAK)';
        }

        // Load view dengan data
        $pdf = Pdf::loadView('owner.laporanRequestDonasiPDF', compact(
            'laporanRequest',
            'totalRequest',
            'totalOrganisasi',
            'status',
            'tahun',
            'tanggalCetak',
            'judulLaporan'
        ));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Set options untuk DomPDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        $filename = 'laporan-request-donasi-' . strtolower($status) . '-' . $tahun . '.pdf';
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error generating Request Donasi PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
    }
}

private function getLaporanRequestDonasi($status = 'all', $tahun = null)
{
    $query = DB::table('request as r')
        ->leftJoin('organisasi as o', 'r.id_organisasi', '=', 'o.id_organisasi')
        ->select(
            'r.id_request',
            'r.id_organisasi',
            'o.nama_organisasi',
            'o.alamat_organisasi',
            'o.email_organisasi',
            'o.nomor_telepon_organisasi',
            'r.nama_request_barang as request_barang',
            'r.tanggal_request',
            'r.status_request'
        );

    // Filter berdasarkan status
    if ($status !== 'all') {
        $query->where('r.status_request', $status);
    }

    // Filter berdasarkan tahun jika diberikan
    if ($tahun) {
        $query->whereYear('r.tanggal_request', $tahun);
    }

    return $query->orderBy('r.tanggal_request', 'desc')->get();
}

// Add these methods to the existing OwnerController class

public function laporanTransaksiPenitip(HttpRequest $request)
{
    try {
        // Cek apakah user yang login adalah owner
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        // Get filter parameters
        $idPenitip = $request->get('id_penitip');
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        // Get all penitip for dropdown
        $penitipList = \App\Models\Penitip::orderBy('nama_penitip')->get();

        // Data laporan transaksi penitip
        $laporanTransaksi = collect();
        $penitipData = null;
        $totalPendapatan = 0;
        $totalBonus = 0;
        $totalBersih = 0;

        if ($idPenitip) {
            $penitipData = \App\Models\Penitip::find($idPenitip);
            if ($penitipData) {
                $laporanTransaksi = $this->getLaporanTransaksiPenitip($idPenitip, $bulan, $tahun);
                $totalPendapatan = $laporanTransaksi->sum('harga_jual');
                $totalBonus = $laporanTransaksi->sum('bonus_terjual_cepat');
                $totalBersih = $laporanTransaksi->sum('pendapatan');
            }
        }

        // Nama bulan untuk tampilan
        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return view('owner.laporanTransaksiPenitip', compact(
            'laporanTransaksi',
            'penitipList',
            'penitipData',
            'totalPendapatan',
            'totalBonus',
            'totalBersih',
            'idPenitip',
            'bulan',
            'tahun',
            'namaBulan'
        ));

    } catch (\Exception $e) {
        Log::error('Error in laporanTransaksiPenitip: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function laporanTransaksiPenitipPDF(HttpRequest $request)
{
    try {
        $idPenitip = $request->get('id_penitip');
        $bulan = (int) ($request->bulan ?? date('m'));
        $tahun = (int) ($request->tahun ?? date('Y'));

        if (!$idPenitip) {
            return redirect()->back()->with('error', 'ID Penitip harus dipilih untuk generate PDF.');
        }

        // Get penitip data
        $penitipData = \App\Models\Penitip::find($idPenitip);
        if (!$penitipData) {
            return redirect()->back()->with('error', 'Data penitip tidak ditemukan.');
        }

        // Panggil data transaksi
        $laporanTransaksi = $this->getLaporanTransaksiPenitip($idPenitip, $bulan, $tahun);
        
        // Hitung summary data
        $totalProduk = $laporanTransaksi->count();
        $totalPendapatan = $laporanTransaksi->sum('harga_jual');
        $totalBonus = $laporanTransaksi->sum('bonus_terjual_cepat');
        $totalBersih = $laporanTransaksi->sum('pendapatan');

        $namaBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $namaBulanTerpilih = $namaBulan[$bulan];
        $tanggalCetak = now()->translatedFormat('d F Y');

        // Load view dengan data
        $pdf = Pdf::loadView('owner.laporanTransaksiPenitipPDF', compact(
            'laporanTransaksi',
            'penitipData',
            'totalProduk',
            'totalPendapatan',
            'totalBonus',
            'totalBersih',
            'bulan',
            'tahun',
            'namaBulanTerpilih',
            'tanggalCetak'
        ));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'landscape');
        
        // Set options untuk DomPDF
        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);

        $filename = 'laporan-transaksi-penitip-T' . $idPenitip . '-' . strtolower($namaBulanTerpilih) . '-' . $tahun . '.pdf';
        return $pdf->download($filename);

    } catch (\Exception $e) {
        Log::error('Error generating Transaksi Penitip PDF: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Gagal membuat PDF: ' . $e->getMessage());
    }
}

private function getLaporanTransaksiPenitip($idPenitip, $bulan, $tahun)
{
    return DB::table('transaksi as t')
        ->join('barang_titipan as bt', 't.id_barang', '=', 'bt.id_barang')
        ->join('detail_penitipan as dp', 'bt.id_barang', '=', 'dp.id_barang')
        ->join('penitipan as p', 'dp.id_penitipan', '=', 'p.id_penitipan')
        ->join('penitip as pen', 'p.id_penitip', '=', 'pen.id_penitip')
        ->select(
            'bt.id_barang as kode_produk',
            'bt.nama_barang_titipan as nama_produk',
            'bt.created_at as tanggal_masuk',
            't.tanggal_pelunasan as tanggal_laku',
            'bt.harga_barang as harga_jual',
            DB::raw('CASE 
                WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.70 
                ELSE bt.harga_barang * 0.80 
            END as harga_bersih'),
            DB::raw('CASE 
                WHEN DATEDIFF(t.tanggal_pelunasan, bt.created_at) <= 7 THEN 30000
                ELSE 0 
            END as bonus_terjual_cepat'),
            DB::raw('(CASE 
                WHEN LOWER(TRIM(COALESCE(p.status_perpanjangan, "tidak"))) = "ya" THEN bt.harga_barang * 0.70 
                ELSE bt.harga_barang * 0.80 
            END) + (CASE 
                WHEN DATEDIFF(t.tanggal_pelunasan, bt.created_at) <= 7 THEN 30000
                ELSE 0 
            END) as pendapatan'),
            'p.status_perpanjangan'
        )
        ->where('pen.id_penitip', $idPenitip)
        ->whereMonth('t.tanggal_pelunasan', $bulan)
        ->whereYear('t.tanggal_pelunasan', $tahun)
        ->where('t.status_transaksi', 'Selesai')
        ->whereNotNull('t.tanggal_pelunasan')
        ->orderBy('t.tanggal_pelunasan', 'desc')
        ->get();
}




}
