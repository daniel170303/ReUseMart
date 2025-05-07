<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penitip extends Model
{
    protected $table = 'penitip';

    protected $primaryKey = 'id_penitip';

    public $incrementing = true;

    public $timestamps = false;

    protected $fillable = [
        'nama_penitip',
        'nik_penitip',
        'nomor_telepon_penitip',
        'email_penitip',
        'password_penitip'
    ];
}
