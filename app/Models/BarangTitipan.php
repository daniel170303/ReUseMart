<?php

namespace App\Models;

use App\Models\DetailPenitipan;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BarangTitipan extends Model
{
    // Nama tabel di database
    protected $table = 'barang_titipan';

    // Primary key custom
    protected $primaryKey = 'id_barang';

    // Tidak menggunakan created_at dan updated_at
    public $timestamps = false;

    // Kolom yang bisa diisi massal
    protected $fillable = [
        'nama_barang_titipan',
        'harga_barang',
        'deskripsi_barang',
        'jenis_barang',
        'garansi_barang',
        'berat_barang',
        'status_barang',
        'gambar_barang',
    ];

    /**
     * Relasi ke tabel gambar_barang_titipan (gambar tambahan)
     */
    public function gambarBarangTitipan()
    {
        return $this->hasMany(GambarBarangTitipan::class, 'id_barang', 'id_barang');
    }

    // Alias untuk backward compatibility
    public function gambarBarang()
    {
        return $this->gambarBarangTitipan();
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_barang', 'id_barang');
    }

    public function rating()
    {
        return $this->hasOne(Rating::class, 'id_barang', 'id_barang');  // Setiap barang hanya memiliki satu rating
    }

    /**
     * Ambil transaksi terakhir (berdasarkan tanggal pelunasan)
     */
    public function transaksiTerakhir()
    {
        return $this->hasOne(Transaksi::class, 'id_barang', 'id_barang')
                    ->orderByDesc('tanggal_pelunasan');
    }

    /**
     * Ambil nilai garansi dalam bentuk bulan (int)
     */
    public function getGaransiBulanAttribute()
    {
        $value = strtolower($this->garansi_barang);

        if (str_contains($value, 'tahun')) {
            preg_match('/\d+/', $value, $match);
            return isset($match[0]) ? ((int)$match[0] * 12) : 0;
        }

        if (str_contains($value, 'bulan')) {
            preg_match('/\d+/', $value, $match);
            return isset($match[0]) ? (int)$match[0] : 0;
        }

        return 0;
    }

    /**
     * Ambil sisa garansi dalam bulan (int atau null)
     */
    public function getSisaGaransiAttribute()
    {
        if (!$this->transaksiTerakhir || !$this->transaksiTerakhir->tanggal_pelunasan) {
            return null; // belum pernah terjual
        }

        $garansiBulan = $this->garansi_bulan;
        $tanggalPelunasan = Carbon::parse($this->transaksiTerakhir->tanggal_pelunasan);
        $garansiHabis = $tanggalPelunasan->copy()->addMonths($garansiBulan);
        $sisa = now()->diffInMonths($garansiHabis, false);

        return $sisa;
    }

    /**
     * Cek apakah garansi masih berlaku
     */
    public function getGaransiMasihBerlakuAttribute()
    {
        $sisa = $this->sisa_garansi;

        if ($sisa === null) {
            return null;
        }

        return $sisa > 0;
    }

    public function detailPenitipan()
    {
        return $this->hasOne(DetailPenitipan::class, 'id_barang', 'id_barang');
    }

    public function penitipan()
    {
        return $this->hasOneThrough(
            \App\Models\Penitip::class,
            \App\Models\DetailPenitipan::class,
            'id_barang',   // Foreign key di detail_penitipan
            'id_penitip',  // Foreign key di penitipan
            'id_barang',   // Local key di barang_titipan
            'id_penitipan' // Local key di detail_penitipan
        );
    }
}