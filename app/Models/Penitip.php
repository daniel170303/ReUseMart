<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Penitip extends Model
{
    use HasApiTokens;

    protected $table = 'penitip';
    protected $primaryKey = 'id_penitip';
    public $timestamps = false;

    protected $fillable = [
        'nama_penitip',
        'nik_penitip',
        'nomor_telepon_penitip',
        'email_penitip',
        'password_penitip'
    ];

    protected $hidden = [
        'password_penitip',
    ];

    public function penitipan()
    {
        return $this->hasMany(Penitipan::class, 'id_penitip', 'id_penitip');
    }

}
