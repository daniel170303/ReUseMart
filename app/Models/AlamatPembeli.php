<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlamatPembeli extends Model
{
    protected $table = 'alamat_pembeli';
    protected $primaryKey = 'id_alamat';
    public $timestamps = true;
    
    protected $fillable = [
        'id_pembeli',
        'nama_penerima',
        'nomor_telepon',
        'alamat_lengkap',
        'kota',
        'provinsi',
        'kode_pos',
        'is_utama'
    ];
    
    protected $casts = [
        'is_utama' => 'boolean',
    ];
    
    /**
     * Relasi ke model Pembeli
     */
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
}