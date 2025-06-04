<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Penitip extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'penitip';
    protected $primaryKey = 'id_penitip';
    public $timestamps = false;
    
    protected $fillable = [
        'nama_penitip',
        'email_penitip',
        'password_penitip',
        'nomor_telepon_penitip',
        'alamat_penitip',
        'tanggal_lahir_penitip',
    ];

    protected $hidden = [
        'password_penitip',
    ];

    // Override method untuk autentikasi Laravel
    public function getAuthIdentifierName()
    {
        return 'email_penitip';
    }

    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->primaryKey);
    }

    public function getAuthPassword()
    {
        return $this->password_penitip;
    }

    public function getRememberToken()
    {
        return $this->remember_token ?? null;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }
    
    // JANGAN gunakan mutator untuk password karena bisa menyebabkan masalah
    // Gunakan DB::table() untuk update password langsung
}