<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\Pembeli;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Proses registrasi user baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'email_penitip' => 'required|email',
            'password_penitip' => 'required|string|min:3',
        ]);

        // Buat user baru
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }

    /**
     * Proses login user via API
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $credentials['email'])->first();

        // Cek password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            // Buat token untuk API
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Email atau password salah',
            ], 401);
        }

        // Gagal login
        return response()->json([
            'message' => 'Login berhasil',
            'penitip' => $penitip,
            'token' => $token,
        ]);
    }

    /**
     * Logout user (hapus token)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}