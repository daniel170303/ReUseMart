<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardPembeli extends Model
{
    protected $table = 'reward_pembeli';
    protected $primaryKey = 'id_poin_reward';
    public $timestamps = false;

    protected $fillable = [
        'id_pembeli',
        'id_merch',
        'jumlah_poin_pembeli',
    ];

    // Relasi ke pembeli (optional)
    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }
}