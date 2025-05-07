<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    // Nama tabel
    protected $table = 'pembeli';

    // Primary key
    protected $primaryKey = 'id_pembeli';

    // Jika tidak menggunakan timestamps
    public $timestamps = false;

    // Kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'nomor_telepon_pembeli',
        'email_pembeli',
        'password_pembeli',
    ];
}
