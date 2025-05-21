<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi'; 
    protected $primaryKey = 'id_transaksi'; 
    public $timestamps = false; 
    protected $fillable = [
        'id_transaksi', 'id_barang', 'id_pembeli', 'nama_barang', 'tanggal_pemesanan', 'tanggal_pelunasan', 'jenis_pengiriman', 'tanggal_pengiriman', 'tanggal_pengambilan'
    ];
}
