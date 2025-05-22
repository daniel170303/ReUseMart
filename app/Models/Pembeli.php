<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembeli extends Model
{
    use HasFactory;

    protected $table = 'pembeli';
    protected $primaryKey = 'id_pembeli';
    public $timestamps = false;

    protected $fillable = [
        'nama_pembeli',
        'alamat_pembeli',
        'nomor_telepon_pembeli',
        'email_pembeli',
        'password_pembeli',
    ];

    protected $hidden = [
        'password_pembeli',
    ];

    // Accessor untuk nama
    public function getNameAttribute()
    {
        return $this->nama_pembeli;
    }

    // Accessor untuk email
    public function getEmailAttribute()
    {
        return $this->email_pembeli;
    }
}