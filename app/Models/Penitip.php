<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penitip extends Model
{
    // Nama tabel jika tidak mengikuti konvensi Laravel
    protected $table = 'penitip';

    // Primary key kustom (bukan 'id')
    protected $primaryKey = 'id_penitip';

    // Tidak menggunakan timestamps (created_at & updated_at)
    public $timestamps = false;

    // Mass assignable fields
    protected $fillable = [
        'nama_penitip',
        'nik_penitip',
        'nomor_telepon_penitip',
        'email_penitip',
        'password_penitip',
    ];

    // Menyembunyikan password saat model diubah menjadi array/JSON
    protected $hidden = [
        'password_penitip',
    ];
}
