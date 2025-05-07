<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangTitipan extends Model
{
    // Nama tabel di database
    protected $table = 'barang_titipan';

    // Primary key custom (bukan 'id')
    protected $primaryKey = 'id_barang';

    // Tidak menggunakan timestamps (created_at dan updated_at)
    public $timestamps = false;

    // Kolom yang boleh diisi melalui mass assignment (POST/PUT)
    protected $fillable = [
        'nama_barang_titipan',
        'harga_barang',
        'deskripsi_barang',
        'jenis_barang',
        'garansi_barang',
        'berat_barang',
    ];
}
