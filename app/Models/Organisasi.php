<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organisasi extends Model
{
    use HasFactory;

    protected $table = 'organisasi';
    protected $primaryKey = 'id_organisasi';
    public $timestamps = false;

    protected $fillable = [
        'nama_organisasi',
        'alamat_organisasi',
        'nomor_telepon_organisasi',
        'email_organisasi',
        'password_organisasi',
    ];

    protected $hidden = [
        'password_organisasi',
    ];

    // Accessor untuk nama
    public function getNameAttribute()
    {
        return $this->nama_organisasi;
    }

    // Accessor untuk email
    public function getEmailAttribute()
    {
        return $this->email_organisasi;
    }
}