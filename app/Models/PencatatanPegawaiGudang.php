<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanPegawaiGudang extends Model
{
    // Tentukan nama tabel jika tidak mengikuti konvensi
    protected $table = 'pencatatan_pegawai_gudang';

    protected $timestamps = false;
    // Tentukan kolom yang dapat diisi (fillable)
    protected $fillable = [
        'id_barang',
        'id_pegawai',
    ];

    // Relasi ke BarangTitipan
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang');
    }

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai');
    }
}
