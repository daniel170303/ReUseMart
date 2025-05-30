<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penitipan extends Model
{
    use HasFactory;

    protected $table = 'penitipan';
    protected $primaryKey = 'id_penitipan';
    public $timestamps = true;

    protected $fillable = [
        'id_penitip',
        'tanggal_penitipan',
        'tanggal_selesai_penitipan',
        'tanggal_pengambilan',
        'tanggal_batas_pengambilan',
        'status_perpanjangan',
        'tanggal_terjual',
        'status_barang',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }

    public function detailPenitipan()
    {
        return $this->hasMany(DetailPenitipan::class, 'id_penitipan');
    }
    
    public function transaksi()
    {
        return $this->hasOne(Transaksi::class, 'id_penitipan');
    }

}
