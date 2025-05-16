<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';

    protected $fillable = [
        'nama_pegawai',
        'email_pegawai',
        'nomor_telepon_pegawai',
        'password_pegawai',
        'id_role'
    ];

    protected $hidden = [
        'password_pegawai',
    ];

    public function getAuthPassword()
    {
        return $this->password_pegawai;
    }
}
