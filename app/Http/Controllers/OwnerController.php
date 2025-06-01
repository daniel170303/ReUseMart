<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\Penitipan;
use App\Models\KomisiPegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnerController extends Controller
{
    // public function index()
    // {
    //     $owner = Pegawai::find(auth()->user()->id_pegawai);
    //     // Render tampilan dashboard owner
    //     return view('owner.dashboardOwner', compact('owner'));
    // }

    public function profile($id)
    {
        $owner = Auth::guard('pegawai')->user();
        
        // Pastikan user yang login adalah owner yang benar
        if (!$owner || $owner->id_pegawai != $id || !$owner->isOwner()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Akses ditolak.']);
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
                    continue; // skip jika barang sudah dihitung
                }

                // tandai barang sudah dihitung
                $processedBarangIds[] = $barang->id_barang;

                // hitung komisi
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

}
