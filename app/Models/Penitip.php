<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penitip extends Model
{
    use HasFactory;

    protected $table = 'penitip';
    protected $primaryKey = 'id_penitip';
    public $timestamps = false;

    protected $fillable = [
        'nama_penitip',
        'nik_penitip',
        'nomor_telepon_penitip',
        'email_penitip',
        'password_penitip',
    ];

    protected $hidden = [
        'password_penitip',
    ];

    // Accessor untuk nama
    public function getNameAttribute()
    {
        return $this->nama_penitip;
    }

    // Accessor untuk email
    public function getEmailAttribute()
    {
        return $this->email_penitip;
    }
}