<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pegawai extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $timestamps = false;

    protected $fillable = [
        'id_role',
        'nama_pegawai',
        'nomor_telepon_pegawai',
        'email_pegawai',
        'password_pegawai',
    ];

    public function role()
    {
        return $this->belongsTo(RolePegawai::class, 'id_role', 'id_role');
    }
}