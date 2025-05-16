<?php

namespace App\Http\Controllers;

use App\Models\RewardPenitip;
use Illuminate\Http\Request;

class RewardPenitipController extends Controller
{
    public function index()
    {
        $rewards = RewardPenitip::with('penitip')->get();
        return view('reward.index', compact('rewards'));
    }

    public function show($id_penitip)
    {
        $reward = RewardPenitip::with('penitip')->findOrFail($id_penitip);
        return view('reward.show', compact('reward'));
    }

    public function reset($id_penitip)
    {
        $reward = RewardPenitip::findOrFail($id_penitip);
        $reward->jumlah_poin_penitip = 0;
        $reward->komisi_penitip = 0;
        $reward->save();

        return redirect()->back()->with('success', 'Reward berhasil direset.');
    }
}
