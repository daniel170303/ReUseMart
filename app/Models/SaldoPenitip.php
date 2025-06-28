<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoPenitip extends Model
{
    protected $table = 'saldo_penitip';
    protected $primaryKey = 'id_penitip';
    public $timestamps = false;

    protected $fillable = ['id_penitip', 'saldo_penitip'];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }
}