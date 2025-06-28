<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Pembeli extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'pembeli';
    protected $primaryKey = 'id_pembeli';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_pembeli',
        'email_pembeli',
        'password_pembeli',
        'nomor_telepon_pembeli',
        'alamat_pembeli',
        'tanggal_lahir_pembeli',
    ];

    protected $hidden = [
        'password_pembeli',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir_pembeli' => 'date',
    ];

    // Override method untuk authentication
    public function getAuthPassword()
    {
        return $this->password_pembeli;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_pembeli;
    }

    // Relasi
    public function rewardPembeli()
    {
        return $this->hasOne(RewardPembeli::class, 'id_pembeli', 'id_pembeli');
    }

    public function transaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pembeli', 'id_pembeli');
    }
}