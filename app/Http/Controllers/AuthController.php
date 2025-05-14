<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Proses registrasi user baru
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi',
        ]);

        // Buat user
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password), // atau Hash::make
            'role' => $request->role,
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi',
        ]);

        // Cari user berdasarkan email & role
        $user = User::where('email', $credentials['email'])
                    ->where('role', $credentials['role'])
                    ->first();

        // Cek password
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
            ], 200);
        }

        // Gagal login
        return response()->json([
            'message' => 'Email, password, atau role salah',
        ], 401);
    }
}
