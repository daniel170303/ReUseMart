<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Proses login penitip via API
     */
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

        // Buat token API menggunakan Sanctum
        $token = $penitip->createToken('penitip-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'penitip' => $penitip,
            'token' => $token,
        ]);
    }

    /**
     * Logout penitip (hapus token)
     */
    public function logoutPenitip(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
