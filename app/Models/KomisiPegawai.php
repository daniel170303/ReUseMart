<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomisiPegawai extends Model
{
    protected $table = 'komisi_pegawai';
    protected $primaryKey = 'id_komisi';
    public $timestamps = false;

    protected $fillable = [
        'id_pegawai',
        'jumlah_komisi',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }
}
