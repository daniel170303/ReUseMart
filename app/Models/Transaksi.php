<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BarangTitipan;


class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'id_barang',
        'id_pembeli',
        'nama_barang',
        'tanggal_pemesanan',
        'tanggal_pelunasan',
        'jenis_pengiriman',
        'tanggal_pengiriman',
        'tanggal_pengambilan',
        'ongkir',
        'status_transaksi',
    ];

    // Relasi ke Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli');
    }

    // Relasi ke BarangTitipan
    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang');
    }
}