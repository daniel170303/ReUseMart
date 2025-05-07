<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Organisasi extends Model
{
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
    ];

    // Hash password otomatis saat diset
    public function setPasswordOrganisasiAttribute($value)
    {
        $this->attributes['password_organisasi'] = Hash::make($value);
    }
}
