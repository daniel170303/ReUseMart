<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencatatanPegawaiGudang extends Model
{
    protected $table = 'pencatatan_pegawai_gudang';
    
    protected $fillable = [
        'id_barang',
        'id_pegawai',
    ];

    // Disable timestamps karena tabel tidak memiliki created_at dan updated_at
    public $timestamps = false;

    // Relasi ke Barang Titipan
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    // Relasi ke Pegawai
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}