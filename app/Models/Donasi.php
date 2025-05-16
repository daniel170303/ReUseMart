<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donasi extends Model
{
    // Nama tabel jika tidak pakai konvensi jamak (plural)
    protected $table = 'donasi';

    // Primary key kustom
    protected $primaryKey = 'id_donasi';

    // Tidak menggunakan auto timestamps (created_at & updated_at)
    public $timestamps = true; // ubah ke false jika tidak pakai timestamps

    // Kolom-kolom yang bisa diisi secara massal
    protected $fillable = [
        'id_barang',
        'id_request',
        'tanggal_donasi',
        'penerima_donasi',
    ];

    // Relasi ke model Barang (jika ada)
    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }

    // Relasi ke model Request (jika ada)
    public function request()
    {
        return $this->belongsTo(RequestDonasi::class, 'id_request');
    }
}
