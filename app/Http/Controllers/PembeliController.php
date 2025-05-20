<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
   
class PembeliController extends Controller
{
    public function index()
{
    $pembelis = Pembeli::all();
    return view('pembelis.index', compact('pembelis'));
}

public function create()
{
    return view('pembelis.create');
}

public function store(Request $request)
{
    $validated = $request->validate([
        'nama_pembeli' => 'required|string|max:50',
        'alamat_pembeli' => 'required|string|max:50',
        'nomor_telepon_pembeli' => 'required|string|max:50',
        'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
        'password_pembeli' => 'required|string|max:255',
    ]);

    $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
    Pembeli::create($validated);

    return redirect()->route('pembelis.index');
}

public function edit(Pembeli $pembeli)
{
    return view('pembelis.edit', compact('pembeli'));
}

public function update(Request $request, Pembeli $pembeli)
{
    $validated = $request->validate([
        'nama_pembeli' => 'required|string|max:50',
        'alamat_pembeli' => 'required|string|max:50',
        'nomor_telepon_pembeli' => 'required|string|max:50',
        'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli,' . $pembeli->id_pembeli . ',id_pembeli',
        'password_pembeli' => 'nullable|string|max:255',
    ]);

    if ($validated['password_pembeli']) {
        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
    } else {
        unset($validated['password_pembeli']);
    }

    $pembeli->update($validated);
    return redirect()->route('pembelis.index');
}

public function destroy(Pembeli $pembeli)
{
    $pembeli->delete();
    return redirect()->route('pembelis.index');
}

    

    
}