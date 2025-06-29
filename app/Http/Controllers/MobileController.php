<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BarangTitipan;
use App\Models\Pembeli;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;

class MobileController extends Controller
{
    public function getBarangTitipan(Request $request)
    {
        try {
            // Query barang titipan yang tersedia untuk publik (status 'dijual' - lowercase seperti di BarangTitipanController)
            $query = BarangTitipan::with(['gambarBarangTitipan'])
                ->where('status_barang', 'dijual');

            // Jika ada parameter pencarian
            if ($request->has('search') && $request->search != '') {
                $keyword = $request->search;
                $query->where(function($q) use ($keyword) {
                    $q->where('nama_barang_titipan', 'like', "%$keyword%")
                      ->orWhere('jenis_barang', 'like', "%$keyword%")
                      ->orWhere('deskripsi_barang', 'like', "%$keyword%")
                      ->orWhere('harga_barang', 'like', "%$keyword%")
                      ->orWhere('berat_barang', 'like', "%$keyword%");
                });
            }

            $barangTitipan = $query->get();

            // Format data untuk mobile
            $data = $barangTitipan->map(function($barang) {
                // Proses status garansi - tanpa sisa hari
                $statusGaransi = 'Tanpa Garansi';
                $garansiMasihBerlaku = false;
                
                if ($barang->garansi_barang !== null) {
                    $tanggalGaransi = Carbon::parse($barang->garansi_barang);
                    $hariIni = Carbon::now();
                    
                    if ($tanggalGaransi->gte($hariIni)) {
                        $statusGaransi = 'Masih Bergaransi sampai ' . $tanggalGaransi->translatedFormat('d M Y');
                        $garansiMasihBerlaku = true;
                    } else {
                        $statusGaransi = 'Garansi Habis pada ' . $tanggalGaransi->translatedFormat('d M Y');
                        $garansiMasihBerlaku = false;
                    }
                }

                // Kumpulkan semua URL gambar
                $gambarUrls = [];
                
                // Gambar utama (gambar_barang)
                if ($barang->gambar_barang) {
                    $gambarUrls[] = [
                        'type' => 'utama',
                        'url' => asset('storage/' . $barang->gambar_barang),
                        'filename' => basename($barang->gambar_barang)
                    ];
                }

                // Gambar tambahan (gambar.*)
                if ($barang->gambarBarangTitipan && $barang->gambarBarangTitipan->count() > 0) {
                    foreach ($barang->gambarBarangTitipan as $gambar) {
                        $gambarUrls[] = [
                            'type' => 'tambahan',
                            'url' => asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar),
                            'filename' => $gambar->nama_file_gambar
                        ];
                    }
                }

                // URL gambar utama untuk backward compatibility
                $fotoUtamaUrl = null;
                if (!empty($gambarUrls)) {
                    // Prioritaskan gambar utama, jika tidak ada ambil yang pertama
                    $gambarUtama = collect($gambarUrls)->firstWhere('type', 'utama');
                    $fotoUtamaUrl = $gambarUtama ? $gambarUtama['url'] : $gambarUrls[0]['url'];
                }

                return [
                    'id' => $barang->id_barang,
                    'nama' => $barang->nama_barang_titipan,
                    'harga' => $barang->harga_barang,
                    'deskripsi' => $barang->deskripsi_barang,
                    'jenis_barang' => $barang->jenis_barang,
                    'berat' => $barang->berat_barang,
                    'status' => $barang->status_barang,
                    'status_garansi' => $statusGaransi,
                    'garansi_masih_berlaku' => $garansiMasihBerlaku,
                    'tanggal_garansi' => $barang->garansi_barang,
                    
                    // Gambar utama (untuk backward compatibility)
                    'foto_utama_url' => $fotoUtamaUrl,
                    
                    // Semua gambar (utama + tambahan)
                    'gambar_urls' => $gambarUrls,
                    'total_gambar' => count($gambarUrls),
                    
                    // Detail gambar terpisah
                    'gambar_utama' => $barang->gambar_barang ? [
                        'url' => asset('storage/' . $barang->gambar_barang),
                        'filename' => basename($barang->gambar_barang)
                    ] : null,
                    
                    'gambar_tambahan' => $barang->gambarBarangTitipan->map(function($gambar) {
                        return [
                            'id' => $gambar->id_gambar,
                            'url' => asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar),
                            'filename' => $gambar->nama_file_gambar
                        ];
                    })->toArray(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data barang titipan berhasil diambil',
                'data' => $data,
                'total' => $data->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data barang titipan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mendapatkan detail barang titipan beserta semua gambarnya
     */
    public function getDetailBarangTitipan($id)
    {
        try {
            $barang = BarangTitipan::with(['gambarBarangTitipan'])
                ->where('id_barang', $id)
                ->where('status_barang', 'dijual')
                ->first();

            if (!$barang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Barang tidak ditemukan atau tidak tersedia'
                ], 404);
            }

            // Proses status garansi - tanpa sisa hari
            $statusGaransi = 'Tanpa Garansi';
            $garansiMasihBerlaku = false;
            
            if ($barang->garansi_barang !== null) {
                $tanggalGaransi = Carbon::parse($barang->garansi_barang);
                $hariIni = Carbon::now();
                
                if ($tanggalGaransi->gte($hariIni)) {
                    $statusGaransi = 'Masih Bergaransi sampai ' . $tanggalGaransi->translatedFormat('d M Y');
                    $garansiMasihBerlaku = true;
                } else {
                    $statusGaransi = 'Garansi Habis pada ' . $tanggalGaransi->translatedFormat('d M Y');
                    $garansiMasihBerlaku = false;
                }
            }

            // Kumpulkan semua gambar
            $semuaGambar = [];
            
            // Gambar utama
            if ($barang->gambar_barang) {
                $semuaGambar[] = [
                    'type' => 'utama',
                    'url' => asset('storage/' . $barang->gambar_barang),
                    'filename' => basename($barang->gambar_barang),
                    'is_primary' => true
                ];
            }

            // Gambar tambahan
            foreach ($barang->gambarBarangTitipan as $gambar) {
                $semuaGambar[] = [
                    'type' => 'tambahan',
                    'id' => $gambar->id_gambar,
                    'url' => asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar),
                    'filename' => $gambar->nama_file_gambar,
                    'is_primary' => false
                ];
            }

            $data = [
                'id' => $barang->id_barang,
                'nama' => $barang->nama_barang_titipan,
                'harga' => $barang->harga_barang,
                'deskripsi' => $barang->deskripsi_barang,
                'jenis_barang' => $barang->jenis_barang,
                'berat' => $barang->berat_barang,
                'status' => $barang->status_barang,
                'status_garansi' => $statusGaransi,
                'garansi_masih_berlaku' => $garansiMasihBerlaku,
                'tanggal_garansi' => $barang->garansi_barang,
                'semua_gambar' => $semuaGambar,
                'total_gambar' => count($semuaGambar),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Detail barang berhasil diambil',
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail barang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getTopSeller(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            Log::info('Getting top seller data with limit: ' . $limit);
            
            // Cek struktur tabel transaksi terlebih dahulu
            $transaksiColumns = DB::select('DESCRIBE transaksi');
            Log::info('Transaksi table structure: ' . json_encode($transaksiColumns));
            
            // Query yang disesuaikan dengan struktur database yang ada
            $topSellers = DB::table('transaksi')
                ->join('barang_titipan', 'transaksi.id_barang', '=', 'barang_titipan.id_barang')
                ->join('detail_penitipan', 'barang_titipan.id_barang', '=', 'detail_penitipan.id_barang')
                ->join('penitipan', 'detail_penitipan.id_penitipan', '=', 'penitipan.id_penitipan')
                ->join('penitip', 'penitipan.id_penitip', '=', 'penitip.id_penitip')
                ->select(
                    'penitip.id_penitip',
                    'penitip.nama_penitip',
                    'penitip.email_penitip',
                    'penitip.nomor_telepon_penitip',
                    DB::raw('COUNT(transaksi.id_transaksi) as total_transaksi'),
                    // Gunakan kolom yang ada di tabel transaksi
                    DB::raw('SUM(CASE WHEN transaksi.status_transaksi = "Selesai" THEN barang_titipan.harga_barang END) as total_penjualan')
                )
                ->where('transaksi.status_transaksi', 'Selesai')
                ->groupBy(
                    'penitip.id_penitip',
                    'penitip.nama_penitip', 
                    'penitip.email_penitip',
                    'penitip.nomor_telepon_penitip'
                )
                ->orderBy('total_penjualan', 'desc')
                ->limit($limit)
                ->get();

            Log::info('Top sellers query result count: ' . $topSellers->count());

            // Format data untuk response
            $formattedSellers = $topSellers->map(function ($seller, $index) {
                return [
                    'ranking' => $index + 1,
                    'id_penitip' => $seller->id_penitip,
                    'nama_penitip' => $seller->nama_penitip,
                    'email_penitip' => $seller->email_penitip,
                    'nomor_telepon_penitip' => $seller->nomor_telepon_penitip ?? '',
                    'total_penjualan' => (float) ($seller->total_penjualan ?? 0),
                    'total_transaksi' => (int) ($seller->total_transaksi ?? 0),
                    'rata_rata_penjualan' => (float) ($seller->rata_rata_penjualan ?? 0),
                    'is_top_seller' => $index < 3, // Top 3 seller
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data top seller berhasil diambil',
                'data' => [
                    'top_sellers' => $formattedSellers,
                    'total_count' => $topSellers->count(),
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting top seller: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data top seller: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        try {
            // Validasi parameter
            $request->validate([
                'id_pembeli' => 'required|integer|exists:pembeli,id_pembeli'
            ]);

            $idPembeli = $request->query('id_pembeli');
            
            // Verifikasi pembeli exists
            $pembeli = Pembeli::find($idPembeli);
            if (!$pembeli) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pembeli tidak ditemukan',
                    'data' => null
                ], 404);
            }

            // Ambil transaksi dengan relasi barang
            $transaksis = Transaksi::with(['barang' => function($query) {
                    $query->select('id_barang', 'nama_barang_titipan', 'harga_barang', 'gambar_barang');
                }])
                ->where('id_pembeli', $idPembeli)
                ->orderBy('tanggal_pemesanan', 'desc')
                ->get();

            // Format response
            $formattedTransaksis = $transaksis->map(function ($transaksi) {
                return [
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_pembeli' => $transaksi->id_pembeli,
                    'id_barang_titipan' => $transaksi->id_barang_titipan,
                    'tanggal_pemesanan' => $transaksi->tanggal_pemesanan,
                    'status_transaksi' => $transaksi->status_transaksi,
                    'metode_pembayaran' => $transaksi->metode_pembayaran,
                    'total_harga' => $transaksi->total_harga,
                    'barang' => $transaksi->barang ? [
                        'id_barang_titipan' => $transaksi->barang->id_barang_titipan,
                        'nama_barang_titipan' => $transaksi->barang->nama_barang_titipan,
                        'harga_barang' => $transaksi->barang->harga_barang,
                        'foto_utama_url' => $transaksi->barang->foto_utama_url,
                    ] : null,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Riwayat transaksi berhasil diambil',
                'data' => $formattedTransaksis->toArray() // Langsung array, bukan nested
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'data' => [],
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error in history API: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
