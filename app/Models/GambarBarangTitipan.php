<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GambarBarangTitipan extends Model
{
    use HasFactory;

    protected $table = 'gambar_barang_titipan';

    protected $primaryKey = 'id_gambar';

    protected $fillable = [
        'barang_titipan_id',
        'nama_file_gambar',
    ];

    // Relasi ke BarangTitipan
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'barang_titipan_id', 'id_barang');
    }
}
