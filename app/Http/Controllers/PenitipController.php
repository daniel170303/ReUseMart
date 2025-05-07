<?php

namespace App\Http\Controllers;

use App\Models\Penitip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenitipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penitips = Penitip::all();
        return response()->json($penitips);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Jika pakai API, bagian ini tidak diperlukan.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_penitip' => 'required|string|max:50',
            'nik_penitip' => 'required|string|size:16|unique:penitip,nik_penitip',
            'nomor_telepon_penitip' => 'required|string|max:50',
            'email_penitip' => 'required|email|max:50|unique:penitip,email_penitip',
            'password_penitip' => 'required|string|min:6',
        ]);

        $validated['password_penitip'] = Hash::make($validated['password_penitip']);

        $penitip = Penitip::create($validated);

        return response()->json($penitip, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Penitip $penitip)
    {
        return response()->json($penitip);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Penitip $penitip)
    {
        // Jika pakai API, bagian ini tidak diperlukan.
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Penitip $penitip)
    {
        $validated = $request->validate([
            'nama_penitip' => 'sometimes|required|string|max:50',
            'nik_penitip' => 'sometimes|required|string|size:16|unique:penitip,nik_penitip,' . $penitip->id_penitip . ',id_penitip',
            'nomor_telepon_penitip' => 'sometimes|required|string|max:50',
            'email_penitip' => 'sometimes|required|email|max:50|unique:penitip,email_penitip,' . $penitip->id_penitip . ',id_penitip',
            'password_penitip' => 'sometimes|nullable|string|min:6',
        ]);

        if (!empty($validated['password_penitip'])) {
            $validated['password_penitip'] = Hash::make($validated['password_penitip']);
        } else {
            unset($validated['password_penitip']);
        }

        $penitip->update($validated);

        return response()->json($penitip);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Penitip $penitip)
    {
        $penitip->delete();
        return response()->json(['message' => 'Penitip deleted successfully']);
    }
}
