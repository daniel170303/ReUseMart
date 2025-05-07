<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PembeliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pembelis = Pembeli::all();
        return response()->json($pembelis);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Jika menggunakan API, fungsi ini bisa dikosongkan atau digunakan untuk keperluan lain
        return response()->json(['message' => 'Form create pembeli']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
            'password_pembeli' => 'required|string|min:6',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);

        $pembeli = Pembeli::create($validated);

        return response()->json($pembeli, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembeli $pembeli)
    {
        return response()->json($pembeli);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembeli $pembeli)
    {
        return response()->json($pembeli);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembeli $pembeli)
    {
        $validated = $request->validate([
            'nama_pembeli' => 'sometimes|required|string|max:50',
            'alamat_pembeli' => 'sometimes|required|string|max:50',
            'nomor_telepon_pembeli' => 'sometimes|required|string|max:50',
            'email_pembeli' => 'sometimes|required|email|max:50|unique:pembeli,email_pembeli,' . $pembeli->id_pembeli . ',id_pembeli',
            'password_pembeli' => 'sometimes|nullable|string|min:6',
        ]);

        if (isset($validated['password_pembeli'])) {
            $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
        }

        $pembeli->update($validated);

        return response()->json($pembeli);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembeli $pembeli)
    {
        $pembeli->delete();

        return response()->json(['message' => 'Pembeli deleted successfully']);
    }
}
