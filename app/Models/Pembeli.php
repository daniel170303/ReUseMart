<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Pembeli extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'pembeli';
    protected $primaryKey = 'id_pembeli';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'nomor_telepon_pembeli',
        'email_pembeli',
        'password_pembeli',
    ];

    protected $hidden = [
        'password_pembeli',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_pembeli;
    }

    public function getAuthIdentifierName()
    {
        return 'id_pembeli';
    }

    public function rewards()
    {
        return $this->hasMany(RewardPembeli::class, 'id_pembeli', 'id_pembeli');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pembeli');
    }
}