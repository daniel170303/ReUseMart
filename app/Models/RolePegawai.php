<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePegawai extends Model
{
    // Nama tabel jika tidak mengikuti konvensi Laravel (plural dari nama model)
    protected $table = 'role_pegawai';

    // Primary key tabel
    protected $primaryKey = 'id_role';

    // Jika primary key bukan integer atau bukan auto increment, atur di sini
    public $incrementing = true; // karena id_role auto increment

    // Jika kolom primary key bukan integer, ubah tipe key:
    protected $keyType = 'int';

    // Jika tabel tidak memiliki created_at dan updated_at
    public $timestamps = false;

    // Kolom yang boleh diisi mass assignment
    protected $fillable = [
        'nama_role',
    ];
}
