<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePegawai extends Model
{
    use HasFactory;

    protected $table = 'role_pegawai';
    protected $primaryKey = 'id_role';
    public $timestamps = false;

    protected $fillable = [
        'nama_role',
    ];

    // Relationship dengan pegawai
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_role', 'id_role');
    }

    // Static method untuk mendapatkan semua role
    public static function getAllRoles()
    {
        return self::all()->pluck('nama_role', 'id_role');
    }
}