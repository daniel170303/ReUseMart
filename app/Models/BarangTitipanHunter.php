<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangTitipanHunter extends Model
{
    use HasFactory;

    // Nama tabel penghubung
    protected $table = 'barang_titipan_hunter';

    // Kolom yang bisa diisi
    protected $fillable = [
        'id_barang',
        'id_pegawai',
    ];

    // Disable timestamps jika tabel tidak memiliki created_at dan updated_at
    public $timestamps = false;

    // Tentukan relasi ke tabel barang_titipan
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    // Tentukan relasi ke tabel pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
