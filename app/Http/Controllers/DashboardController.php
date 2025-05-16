<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'jumlahPegawai' => DB::table('pegawai')->count(),
            'jumlahBarang' => DB::table('barang_titipan')->count(),
            'jumlahPembeli' => DB::table('pembeli')->count(),
            'jumlahPenitip' => DB::table('penitip')->count(),
            'jumlahTransaksi' => DB::table('transaksi')->count(),
            'jumlahRequest' => DB::table('request')->count(),
            'jumlahReward' => DB::table('reward_pembeli')->count(),
            'jumlahOrganisasi' => DB::table('organisasi')->count(),
        ]);
    }
}
