<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Penitip extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'penitip';
    protected $primaryKey = 'id_penitip';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'nama_penitip',
        'nik_penitip',
        'nomor_telepon_penitip',
        'email_penitip',
        'password_penitip',
        'alamat_penitip',
        'tanggal_lahir_penitip',
    ];

    protected $hidden = [
        'password_penitip',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir_penitip' => 'date',
    ];

    // Override method untuk authentication
    public function getAuthPassword()
    {
        return $this->password_penitip;
    }

    public function getEmailForPasswordReset()
    {
        return $this->email_penitip;
    }

    // Relasi
    public function barangTitipan()
    {
        return $this->hasMany(BarangTitipan::class, 'id_penitip', 'id_penitip');
    }
}