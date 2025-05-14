<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Proses registrasi user baru
     */
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // Validasi password dan konfirmasi
            'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi',
        ]);

        // Buat user baru
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password), // Gunakan Hash::make untuk keamanan
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }

    /**
     * Proses login user
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $credentials['email'])->first();

        // Cek password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            // Generate token jika login berhasil (dengan Laravel Sanctum)
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
                'token' => $token, // Token untuk autentikasi lebih lanjut
            ], 200);
        }

        // Jika login gagal
        return response()->json([
            'message' => 'Email atau password salah',
        ], 401);
    }
}
