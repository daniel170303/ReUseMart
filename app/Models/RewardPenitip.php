<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardPenitip extends Model
{
    protected $table = 'reward_penitip';
    protected $primaryKey = 'id_penitip';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_penitip',
        'jumlah_poin_penitip',
        'komisi_penitip',
    ];

    public function penitip()
    {
        return $this->belongsTo(Penitip::class, 'id_penitip');
    }
}
