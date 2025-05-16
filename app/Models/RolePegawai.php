<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePegawai extends Model
{
    protected $table = 'role_pegawai';  // nama tabel di database

    protected $primaryKey = 'id_role';  // primary key tabel

    public $timestamps = false;  // jika tabel tidak pakai created_at dan updated_at

    protected $fillable = [
        'nama_role',
    ];

    // Relasi: satu role memiliki banyak pegawai
    public function pegawai()
    {
        return $this->hasMany(Pegawai::class, 'id_role', 'id_role');
    }
}
