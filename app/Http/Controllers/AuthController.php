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
            'role' => 'required|in:pembeli,organisasi',
        ]);

        // Buat user
        $user = User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
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
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'message' => 'Login berhasil',
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer'
            ], 200);
        }

        return response()->json([
            'message' => 'Email atau password salah',
        ], 401);
    }

    // Proses logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }

    // Mengambil informasi user yang sedang login
    public function userInfo(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }
}