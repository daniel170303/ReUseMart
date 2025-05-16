<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    //Login penitip menggunakan API
    public function loginPenitip(Request $request)
    {
        $request->validate([
            'email_penitip' => 'required|email',
            'password_penitip' => 'required|string|min:3',
        ]);

        $penitip = Penitip::where('email_penitip', $request->email_penitip)->first();

        if (!$penitip || !Hash::check($request->password_penitip, $penitip->password_penitip)) {
            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        $token = $penitip->createToken('penitip-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'penitip' => $penitip,
            'token' => $token,
        ]);
    }

    //Logout penitip (hapus token)
    public function logoutPenitip(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    //Register pembeli
    public function registerPembeli(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
        $pembeli = Pembeli::create($validated);
        $token = $pembeli->createToken('pembeli-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'pembeli' => $pembeli,
            'token' => $token,
        ], 201);
    }

    //Login pembeli menggunakan API
    public function loginPembeli(Request $request)
    {
        $request->validate([
            'email_pembeli' => 'required|email',
            'password_pembeli' => 'required|string|min:3',
        ]);

        $pembeli = Pembeli::where('email_pembeli', $request->email_pembeli)->first();

        if (!$pembeli || !Hash::check($request->password_pembeli, $pembeli->password_pembeli)) {
            return response()->json(['message' => 'Email atau password salah'], 401);
        }

        $token = $pembeli->createToken('pembeli-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'pembeli' => $pembeli,
            'token' => $token,
        ]);
    }

    //Logout pembeli (hapus token)
    public function logoutPembeli(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}
