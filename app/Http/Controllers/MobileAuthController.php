<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Pegawai;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->email;
        $password = $request->password;

        // Log untuk debugging
        Log::info('Mobile login attempt', ['email' => $email]);

        // Cek di tabel Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli) {
            Log::info('Found pembeli', ['id' => $pembeli->id_pembeli]);
            if (Hash::check($password, $pembeli->password_pembeli)) {
                $token = $pembeli->createToken('mobile_pembeli')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'user' => [
                        'id' => $pembeli->id_pembeli,
                        'nama' => $pembeli->nama_pembeli,
                        'email' => $pembeli->email_pembeli,
                        'nomor_telepon' => $pembeli->nomor_telepon_pembeli ?? null,
                        'role' => 'pembeli'
                    ]
                ]);
            } else {
                Log::warning('Password mismatch for pembeli', ['email' => $email]);
            }
        }

        // Cek di tabel Penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip) {
            Log::info('Found penitip', ['id' => $penitip->id_penitip]);
            Log::info('Password check', [
                'input_password' => $password,
                'stored_hash' => $penitip->password_penitip,
                'hash_check' => Hash::check($password, $penitip->password_penitip)
            ]);
            
            if (Hash::check($password, $penitip->password_penitip)) {
                try {
                    $token = $penitip->createToken('mobile_penitip')->plainTextToken;
                    Log::info('Token created successfully for penitip');
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil',
                        'token' => $token,
                        'user' => [
                            'id' => $penitip->id_penitip,
                            'nama' => $penitip->nama_penitip,
                            'email' => $penitip->email_penitip,
                            'nomor_telepon' => $penitip->nomor_telepon_penitip ?? null,
                            'role' => 'penitip'
                        ]
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error creating token for penitip', ['error' => $e->getMessage()]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Error creating authentication token'
                    ], 500);
                }
            } else {
                Log::warning('Password mismatch for penitip', ['email' => $email]);
            }
        } else {
            Log::info('Penitip not found', ['email' => $email]);
        }

        // Cek di tabel Pegawai (untuk Hunter dan Kurir)
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai) {
            Log::info('Found pegawai', ['id' => $pegawai->id_pegawai]);
            if (Hash::check($password, $pegawai->password_pegawai)) {
                $token = $pegawai->createToken('mobile_pegawai')->plainTextToken;
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'token' => $token,
                    'user' => [
                        'id' => $pegawai->id_pegawai,
                        'nama' => $pegawai->nama_pegawai,
                        'email' => $pegawai->email_pegawai,
                        'nomor_telepon' => $pegawai->nomor_telepon_pegawai ?? null,
                        'role' => $pegawai->role_pegawai
                    ]
                ]);
            } else {
                Log::warning('Password mismatch for pegawai', ['email' => $email]);
            }
        }

        Log::warning('Login failed - no user found or password mismatch', ['email' => $email]);
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }

    // Method lainnya tetap sama...
    public function checkEmail(Request $request) {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $foundRole = null;
        $foundUser = null;

        // Cek di semua tabel untuk menentukan role
        if (Pembeli::where('email_pembeli', $email)->exists()) {
            $foundRole = 'pembeli';
            $foundUser = Pembeli::where('email_pembeli', $email)->first();
        } elseif (Penitip::where('email_penitip', $email)->exists()) {
            $foundRole = 'penitip';
            $foundUser = Penitip::where('email_penitip', $email)->first();
        } elseif (Pegawai::where('email_pegawai', $email)->exists()) {
            $foundUser = Pegawai::where('email_pegawai', $email)->first();
            $foundRole = $foundUser->role_pegawai;
        }

        if ($foundRole) {
            return response()->json([
                'success' => true,
                'role' => $foundRole,
                'user_exists' => true,
                'user_name' => $foundUser ? $this->getUserName($foundUser, $foundRole) : null
            ]);
        }

        return response()->json([
            'success' => false,
            'role' => null,
            'user_exists' => false,
            'message' => 'Email tidak ditemukan'
        ]);
    }

    private function getUserName($user, $role) {
        switch ($role) {
            case 'pembeli':
                return $user->nama_pembeli;
            case 'penitip':
                return $user->nama_penitip;
            case 'hunter':
            case 'kurir':
            case 'admin':
            case 'cs':
            case 'gudang':
            case 'owner':
                return $user->nama_pegawai;
            default:
                return null;
        }
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function getUser(Request $request) {
        $user = $request->user();
        
        if ($user instanceof Pembeli) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id_pembeli,
                    'nama' => $user->nama_pembeli,
                    'email' => $user->email_pembeli,
                    'nomor_telepon' => $user->nomor_telepon_pembeli ?? null,
                    'role' => 'pembeli'
                ]
            ]);
        } elseif ($user instanceof Penitip) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id_penitip,
                    'nama' => $user->nama_penitip,
                    'email' => $user->email_penitip,
                    'nomor_telepon' => $user->nomor_telepon_penitip ?? null,
                    'role' => 'penitip'
                ]
            ]);
        } elseif ($user instanceof Pegawai) {
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id_pegawai,
                    'nama' => $user->nama_pegawai,
                    'email' => $user->email_pegawai,
                    'nomor_telepon' => $user->nomor_telepon_pegawai ?? null,
                    'role' => $user->role_pegawai
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ], 404);
    }
}