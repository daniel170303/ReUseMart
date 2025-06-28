<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPenitipan extends Model
{
    use HasFactory;

    protected $table = 'detail_penitipan';
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false; // karena menggunakan composite key

    protected $fillable = [
        'id_barang',
        'id_penitipan',
    ];

    public function penitipan()
    {
        return $this->belongsTo(Penitipan::class, 'id_penitipan', 'id_penitipan');
    }

    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }
}
