<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Pegawai extends Authenticatable
{
    use HasFactory, HasApiTokens;
    
    protected $table = 'pegawai';
    protected $primaryKey = 'id_pegawai';
    public $timestamps = false;
    
    protected $fillable = [
        'id_role',
        'nama_pegawai',
        'nomor_telepon_pegawai',
        'email_pegawai',
        'password_pegawai',
        'tanggal_lahir_pegawai',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    // protected $casts = [
    //     'tanggal_lahir_pegawai' => 'date', // Casting ke tipe date Carbon
    // ];

    protected $hidden = [
        'password_pegawai',
    ];

    // // Override method untuk autentikasi Laravel
    // public function getAuthIdentifierName()
    // {
    //     return 'email_pegawai';
    // }

    // public function getAuthIdentifier()
    // {
    //     return $this->getAttribute($this->primaryKey);
    // }

    // public function getAuthPassword()
    // {
    //     return $this->password_pegawai;
    // }

    // public function getRememberToken()
    // {
    //     return $this->remember_token ?? null;
    // }

    // public function setRememberToken($value)
    // {
    //     $this->remember_token = $value;
    // }

    // public function getRememberTokenName()
    // {
    //     return 'remember_token';
    // }

    // Relationship dengan role_pegawai
    public function rolePegawai()
    {
        return $this->belongsTo(RolePegawai::class, 'id_role', 'id_role');
    }

    // Accessor untuk mendapatkan nama role
    public function getRoleNameAttribute()
    {
        $role = $this->rolePegawai;
        return $role ? $role->nama_role : 'Unknown';
    }
    
    // Accessor untuk role_pegawai
    public function getRolePegawaiAttribute()
    {
        return $this->rolePegawai()->first();
    }

    // Method untuk mengecek role specific
    public function isOwner()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Owner';
    }

    public function isAdmin()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Admin';
    }

    public function isCS()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Customer Service';
    }

    public function isGudang()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Gudang';
    }

    public function isHunter()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Hunter';
    }

    public function isKurir()
    {
        $role = $this->rolePegawai;
        return $role && $role->nama_role === 'Kurir';
    }
}