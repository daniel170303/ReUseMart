<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    // Nama tabel di database
    protected $table = 'transaksi';

    // Primary key tabel
    protected $primaryKey = 'id_transaksi';

    // Tidak menggunakan timestamps default (created_at, updated_at)
    public $timestamps = false;

    // Kolom yang bisa diisi
    protected $fillable = [
        'id_barang',
        'id_pembeli',
        'nama_barang',
        'tanggal_pemesanan',
        'tanggal_pelunasan',
        'jenis_pengiriman',
        'tanggal_pengiriman',
        'tanggal_pengambilan',
        'ongkir',
        'status_transaksi',
    ];

    // Relasi ke barang titipan
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    // (Opsional) Tambahkan relasi ke user/pembeli jika ada model User
    public function pembeli()
    {
        return $this->belongsTo(User::class, 'id_pembeli');
    }

    // Relasi ke barang titipan
    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }
}
