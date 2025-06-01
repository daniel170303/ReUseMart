<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\BarangTitipan;
use App\Models\BarangTitipanHunter;
use App\Models\Pegawai;

class HunterController extends Controller
{
    public function profile($id)
    {
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai || $pegawai->id_role != 5 || $pegawai->id_pegawai != $id) {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        // Ambil data hunter dengan relasi rolePegawai yang sudah ada
        $hunter = Pegawai::with('rolePegawai')->findOrFail($id);

        // Ambil barang yang ditangani hunter ini melalui relasi barang_titipan_hunter
        $barangHunter = BarangTitipanHunter::where('id_pegawai', $id)
            ->with(['barangTitipan.gambarBarangTitipan', 'barangTitipan.transaksiTerakhir'])
            ->get();

        // Hitung statistik
        $totalBarang = $barangHunter->count();
        
        $barangTerjual = $barangHunter->filter(function($item) {
            return $item->barangTitipan && $item->barangTitipan->status_barang === 'terjual';
        })->count();

        // Hitung total komisi (20% dari harga barang yang terjual)
        $totalKomisi = $barangHunter->filter(function($item) {
            return $item->barangTitipan && $item->barangTitipan->status_barang === 'terjual';
        })->sum(function($item) {
            return $item->barangTitipan->harga_barang * 0.2; // 20% komisi
        });

        // Komisi bulan ini
        $komisiBulanIni = $barangHunter->filter(function($item) {
            if (!$item->barangTitipan || $item->barangTitipan->status_barang !== 'terjual') {
                return false;
            }
            
            // Cek apakah ada transaksi terakhir dan tanggal pelunasan bulan ini
            $transaksi = $item->barangTitipan->transaksiTerakhir;
            if ($transaksi && $transaksi->tanggal_pelunasan) {
                $tanggalPelunasan = \Carbon\Carbon::parse($transaksi->tanggal_pelunasan);
                return $tanggalPelunasan->month === now()->month && 
                       $tanggalPelunasan->year === now()->year;
            }
            
            return false;
        })->sum(function($item) {
            return $item->barangTitipan->harga_barang * 0.2;
        });

        // Barang terjual bulan ini
        $barangTerjualBulanIni = $barangHunter->filter(function($item) {
            if (!$item->barangTitipan || $item->barangTitipan->status_barang !== 'terjual') {
                return false;
            }
            
            $transaksi = $item->barangTitipan->transaksiTerakhir;
            if ($transaksi && $transaksi->tanggal_pelunasan) {
                $tanggalPelunasan = \Carbon\Carbon::parse($transaksi->tanggal_pelunasan);
                return $tanggalPelunasan->month === now()->month && 
                       $tanggalPelunasan->year === now()->year;
            }
            
            return false;
        })->count();

        // Rata-rata komisi per barang
        $rataRataKomisi = $barangTerjual > 0 ? $totalKomisi / $barangTerjual : 0;

        // Barang dengan status berbeda
        $barangDijual = $barangHunter->filter(function($item) {
            return $item->barangTitipan && $item->barangTitipan->status_barang === 'dijual';
        })->count();

        $barangDiambil = $barangHunter->filter(function($item) {
            return $item->barangTitipan && $item->barangTitipan->status_barang === 'sudah diambil penitip';
        })->count();

        $barangDonasi = $barangHunter->filter(function($item) {
            return $item->barangTitipan && in_array($item->barangTitipan->status_barang, ['barang untuk donasi', 'sudah didonasikan']);
        })->count();

        return view('pegawai.hunter.profile', compact(
            'hunter',
            'barangHunter',
            'totalBarang',
            'barangTerjual',
            'totalKomisi',
            'komisiBulanIni',
            'barangTerjualBulanIni',
            'rataRataKomisi',
            'barangDijual',
            'barangDiambil',
            'barangDonasi'
        ));
    }
}
