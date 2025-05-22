<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

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

    protected $hidden = [
        'password_pegawai',
    ];

    // Relationship dengan role_pegawai
    public function rolePegawai()
    {
        return $this->belongsTo(RolePegawai::class, 'id_role', 'id_role');
    }

    // Accessor untuk mendapatkan nama role
    public function getRoleNameAttribute()
    {
        return $this->rolePegawai ? $this->rolePegawai->nama_role : 'Unknown';
    }

    // Method untuk mengecek role specific
    public function isOwner()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Owner';
    }

    public function isAdmin()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Admin';
    }

    public function isCS()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Customer Service';
    }

    public function isGudang()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Gudang';
    }

    public function isHunter()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Hunter';
    }

    public function isKurir()
    {
        return $this->rolePegawai && $this->rolePegawai->nama_role === 'Kurir';
    }
}