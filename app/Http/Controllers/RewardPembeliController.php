<?php

namespace App\Http\Controllers;

use App\Models\RewardPembeli;
use Illuminate\Http\Request;

class RewardPembeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_pembeli' => 'required|integer',
            'jumlah_poin_pembeli' => 'required|integer',
            'id_merch' => 'nullable|integer', // Add validation for id_merch
        ]);

        RewardPembeli::create([
            'id_pembeli' => $request->id_pembeli,
            'jumlah_poin_pembeli' => $request->jumlah_poin_pembeli,
            'id_merch' => $request->id_merch, // Include id_merch in the creation
        ]);

        return redirect()->back()->with('success', 'Reward pembeli berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(RewardPembeli $rewardPembeli)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RewardPembeli $rewardPembeli)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RewardPembeli $rewardPembeli)
    {
        $request->validate([
            'jumlah_poin_pembeli' => 'required|integer',
            'id_merch' => 'required|integer',
        ]);

        $rewardPembeli->update([
            'jumlah_poin_pembeli' => $request->jumlah_poin_pembeli,
            'id_merch' => $request->id_merch,
        ]);

        return redirect()->back()->with('success', 'Reward pembeli berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RewardPembeli $rewardPembeli)
    {
        $rewardPembeli->delete();
        return redirect()->back()->with('success', 'Reward pembeli berhasil dihapus.');
    }
}