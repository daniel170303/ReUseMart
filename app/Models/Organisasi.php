<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Laravel\Sanctum\HasApiTokens;

class Organisasi extends Model implements Authenticatable
{
    use AuthenticatableTrait, HasApiTokens;
    
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

    public function setPasswordOrganisasiAttribute($value)
    {
        $this->attributes['password_organisasi'] = Hash::make($value);
    }

    // Override methods untuk authentication
    public function getAuthIdentifierName()
    {
        return 'id_organisasi';
    }

    public function getAuthIdentifier()
    {
        return $this->id_organisasi;
    }

    public function getAuthPassword()
    {
        return $this->password_organisasi;
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
}
