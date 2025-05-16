<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Organisasi extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'organisasi';
    protected $primaryKey = 'id_organisasi';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_organisasi',
        'alamat_organisasi',
        'nomor_telepon_organisasi',
        'email_organisasi',
        'password_organisasi'
    ];

    protected $hidden = [
        'password_organisasi',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_organisasi;
    }
}
