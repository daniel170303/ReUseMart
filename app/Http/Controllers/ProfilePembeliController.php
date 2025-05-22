<?php

namespace App\Http\Controllers;

use App\Models\ProfilePembeli;
use Illuminate\Http\Request;

class ProfilePembeliController extends Controller
{
    public function index()
    {
        $pembelis = ProfilePembeli::all();
        return view('ProfilePembeli', compact('pembelis'));
    }

    public function show($id)
    {
        $pembeli = ProfilePembeli::findOrFail($id);
        return response()->json($pembeli);
    }
}
