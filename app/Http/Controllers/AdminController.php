<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\BarangTitipan;
use App\Models\Transaksi;
use App\Models\Request as DonasiRequest;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'jumlahPegawai' => Pegawai::count(),
            'jumlahPenitip' => Penitip::count(),
            'jumlahPembeli' => Pembeli::count(),
            'jumlahOrganisasi' => Organisasi::count(),
            'jumlahBarang' => BarangTitipan::count(),
            'jumlahTransaksi' => Transaksi::count(),
            'jumlahRequest' => DonasiRequest::count(),
            'barangTerbaru' => BarangTitipan::latest('id_barang')->take(5)->get(),
        ]);
    }
}