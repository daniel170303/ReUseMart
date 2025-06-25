<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    // Sesuaikan nama tabel dengan yang ada di database
    protected $table = 'donasi'; // atau 'donasis'
    protected $primaryKey = 'id_donasi'; // sesuaikan dengan primary key
    public $timestamps = false; // set true jika ada created_at, updated_at

    protected $fillable = [
        'id_barang',
        'id_request',
        'tanggal_donasi',
        'penerima_donasi'
    ];

    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    public function requestDonasi()
    {
        return $this->belongsTo(Request::class, 'id_request', 'id_request');
    }
}
