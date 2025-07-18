<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Request extends Model
{
    use HasFactory;
    protected $table = 'request'; // Nama tabel di database

    protected $primaryKey = 'id_request'; // Primary key

    public $timestamps = false; // Nonaktifkan timestamps jika tabel tidak pakai created_at & updated_at

    protected $fillable = [
        'id_organisasi',
        'nama_request_barang',
        'tanggal_request',
        'status_request',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'id_organisasi');
    }

    public function donasi()
    {
        return $this->hasMany(Donasi::class, 'id_request', 'id_request');
    }
}
