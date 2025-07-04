<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $table = 'rating';
    protected $primaryKey = 'id_rating';
    protected $fillable = ['id_barang', 'id_pembeli', 'rating'];
    public $timestamps = false;

    // Relasi ke Barang
    public function barang()
    {
        return $this->belongsTo(BarangTitipan::class, 'id_barang', 'id_barang');
    }

    // Relasi ke Pembeli
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
}
