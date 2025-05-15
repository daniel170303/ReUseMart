<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

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
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi',
        ]);

        $user = User::where('email', $credentials['email'])
                    ->where('role', $credentials['role'])
                    ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // Login berhasil, buat token Sanctum
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        }

        return response()->json([
            'message' => 'Email, password, atau role salah',
        ], 401);
    }
}
