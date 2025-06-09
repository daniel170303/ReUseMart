<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardPembeli extends Model
{
    use HasFactory;

    protected $table = 'reward_pembeli';
    protected $primaryKey = 'id_poin_reward';

        public $timestamps = false;


    protected $fillable = [
        'id_pembeli',
        'jumlah_poin_pembeli',
        'id_merch'
    ];

    // Add default values for missing fields
    protected $attributes = [
        'id_merch' => 1, // or whatever default value makes sense
    ];

    public function pembeli()
    {
        return $this->belongsTo(Pembeli::class, 'id_pembeli', 'id_pembeli');
    }

    public function merch()
    {
        return $this->belongsTo(Merch::class, 'id_merch', 'id_merch');
    }
}