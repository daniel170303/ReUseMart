<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Pembeli extends Model
{
    use HasApiTokens, Notifiable;

    protected $table = 'pembeli';
    protected $primaryKey = 'id_pembeli';
    public $timestamps = false;

    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'nomor_telepon_pembeli',
        'email_pembeli',
        'password_pembeli',
    ];

    protected $hidden = [
        'password_pembeli',
    ];
}
