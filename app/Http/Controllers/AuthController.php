<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Organisasi;

class AuthController extends Controller
{
    /**
     * Login API dengan deteksi otomatis role
     */
    public function apiLogin(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $email = $request->email;
        $password = $request->password;
        $role = null;
        $user_id = null;

        // Periksa di tabel user (jika sudah pernah login)
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('auth_token')->plainTextToken;
            $role = $user->role;
            
            // Set user_id sesuai dengan role
            switch ($role) {
                case 'admin':
                case 'owner':
                case 'pegawai':
                case 'gudang':
                case 'cs':
                    $pegawai = Pegawai::where('email_pegawai', $email)->first();
                    $user_id = $pegawai ? $pegawai->id_pegawai : null;
                    break;
                case 'penitip':
                    $penitip = Penitip::where('email_penitip', $email)->first();
                    $user_id = $penitip ? $penitip->id_penitip : null;
                    break;
                case 'pembeli':
                    $pembeli = Pembeli::where('email_pembeli', $email)->first();
                    $user_id = $pembeli ? $pembeli->id_pembeli : null;
                    break;
                case 'organisasi':
                    $organisasi = Organisasi::where('email_organisasi', $email)->first();
                    $user_id = $organisasi ? $organisasi->id_organisasi : null;
                    break;
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'role' => $role,
                'user_id' => $user_id
            ]);
        }

        // Jika tidak ditemukan di tabel user, cek di tabel lain

        // 1. Periksa di tabel pegawai (termasuk admin)
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai && Hash::check($password, $pegawai->password_pegawai)) {
            // Tentukan role berdasarkan id_role
            $role = $this->determineRoleFromPegawai($pegawai->id_role);
            
            // Buat user baru di tabel users jika belum ada
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $pegawai->nama_pegawai,
                    'password' => $pegawai->password_pegawai,
                    'role' => $role
                ]
            );
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'role' => $role,
                'user_id' => $pegawai->id_pegawai
            ]);
        }

        // 2. Periksa di tabel penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip && Hash::check($password, $penitip->password_penitip)) {
            // Buat user baru di tabel users jika belum ada
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $penitip->nama_penitip,
                    'password' => $penitip->password_penitip,
                    'role' => 'penitip'
                ]
            );
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'role' => 'penitip',
                'user_id' => $penitip->id_penitip
            ]);
        }

        // 3. Periksa di tabel pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli && Hash::check($password, $pembeli->password_pembeli)) {
            // Buat user baru di tabel users jika belum ada
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $pembeli->nama_pembeli,
                    'password' => $pembeli->password_pembeli,
                    'role' => 'pembeli'
                ]
            );
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'role' => 'pembeli',
                'user_id' => $pembeli->id_pembeli
            ]);
        }

        // 4. Periksa di tabel organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi && Hash::check($password, $organisasi->password_organisasi)) {
            // Buat user baru di tabel users jika belum ada
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $organisasi->nama_organisasi,
                    'password' => $organisasi->password_organisasi,
                    'role' => 'organisasi'
                ]
            );
            
            $token = $user->createToken('auth_token')->plainTextToken;
            
            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'token' => $token,
                'role' => 'organisasi',
                'user_id' => $organisasi->id_organisasi
            ]);
        }

        // Jika tidak ditemukan di semua tabel
        return response()->json([
            'status' => 'error',
            'message' => 'Email atau password salah.',
        ], 401);
    }

    /**
     * Register API untuk berbagai role
     */
    public function apiRegister(Request $request)
    {
        // Validasi umum
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role_type' => 'required|in:pembeli,organisasi',
        ]);

        // Validasi berdasarkan jenis role
        if ($request->role_type == 'pembeli') {
            $request->validate([
                'nama_pembeli' => 'required|string|max:50',
                'alamat_pembeli' => 'required|string|max:50',
                'nomor_telepon_pembeli' => 'required|string|max:50',
            ]);

            // Simpan data pembeli
            $pembeli = Pembeli::create([
                'nama_pembeli' => $request->nama_pembeli,
                'alamat_pembeli' => $request->alamat_pembeli,
                'nomor_telepon_pembeli' => $request->nomor_telepon_pembeli,
                'email_pembeli' => $request->email,
                'password_pembeli' => Hash::make($request->password),
            ]);

            // Buat user di tabel users untuk autentikasi
            $user = User::create([
                'name' => $request->nama_pembeli,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'pembeli',
            ]);

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi pembeli berhasil',
                'token' => $token,
                'role' => 'pembeli',
                'user_id' => $pembeli->id_pembeli
            ], 201);

        } elseif ($request->role_type == 'organisasi') {
            $request->validate([
                'nama_organisasi' => 'required|string|max:50',
                'alamat_organisasi' => 'required|string|max:50',
                'nomor_telepon_organisasi' => 'required|string|max:50',
            ]);

            // Simpan data organisasi
            $organisasi = Organisasi::create([
                'nama_organisasi' => $request->nama_organisasi,
                'alamat_organisasi' => $request->alamat_organisasi,
                'nomor_telepon_organisasi' => $request->nomor_telepon_organisasi,
                'email_organisasi' => $request->email,
                'password_organisasi' => Hash::make($request->password),
            ]);

            // Buat user di tabel users untuk autentikasi
            $user = User::create([
                'name' => $request->nama_organisasi,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'organisasi',
            ]);

            // Buat token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi organisasi berhasil',
                'token' => $token,
                'role' => 'organisasi',
                'user_id' => $organisasi->id_organisasi
            ], 201);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Role yang dipilih tidak valid',
        ], 400);
    }

    /**
     * Logout API
     */
    public function apiLogout(Request $request)
    {
        // Revoke token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout'
        ]);
    }

    /**
     * Tentukan role pegawai berdasarkan id_role
     */
    private function determineRoleFromPegawai($id_role)
    {
        // Sesuaikan dengan struktur database Anda
        switch ($id_role) {
            case 1:
                return 'admin';
            case 2:
                return 'owner';
            case 3:
                return 'pegawai';
            case 4:
                return 'gudang';
            case 5:
                return 'cs';
            default:
                return 'pegawai';
        }
    }
}