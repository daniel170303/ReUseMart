<?php

namespace App\Http\Controllers;

use App\Models\Donasi;
use Illuminate\Http\Request;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $donasis = Donasi::all();
        return response()->json($donasis);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_barang' => 'required|integer',
            'id_request' => 'required|integer',
            'tanggal_donasi' => 'required|date',
            'penerima_donasi' => 'required|string|max:50',
        ]);

        $donasi = Donasi::create($validated);

        return response()->json($donasi, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $donasi = Donasi::findOrFail($id);
        return response()->json($donasi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $donasi = Donasi::findOrFail($id);

        $validated = $request->validate([
            'id_barang' => 'sometimes|integer',
            'id_request' => 'sometimes|integer',
            'tanggal_donasi' => 'sometimes|date',
            'penerima_donasi' => 'sometimes|string|max:50',
        ]);

        $donasi->update($validated);

        return response()->json($donasi);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $donasi = Donasi::findOrFail($id);
        $donasi->delete();

        return response()->json(['message' => 'Donasi deleted successfully']);
    }
}
