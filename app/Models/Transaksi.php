<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    
    protected $fillable = [
        'nomor_nota',
        'id_pembeli',
        'id_barang',
        'tanggal_pemesanan',
        'tanggal_pelunasan',
        'jenis_pengiriman',
        'subtotal_barang',
        'ongkir',
        'poin_ditebus',
        'diskon_poin',
        'total_pembayaran',
        'metode_pengiriman',
        'alamat_pengiriman',
        'status_transaksi',
        'bukti_pembayaran',
        'tanggal_pembayaran',

    ];

    protected $casts = [
        'tanggal_pemesanan' => 'datetime',
        'tanggal_pembayaran' => 'datetime',
        'alamat_pengiriman' => 'array',
    ];

    // Relasi ke Barang
    public function barangTitipan()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    // Relasi ke Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    // Relasi ke Detail Transaksi
    public function detailTransaksis()
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }

    // Accessor untuk format tanggal
    public function getTanggalPemesananFormatAttribute()
    {
        return $this->tanggal_pemesanan ? $this->tanggal_pemesanan->format('d/m/Y H:i') : '-';
    }

    public function getTanggalPembayaranFormatAttribute()
    {
        return $this->tanggal_pembayaran ? $this->tanggal_pembayaran->format('d/m/Y H:i') : '-';
    }
}