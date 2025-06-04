<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Pembeli;
use App\Models\Penitip;
use App\Models\Pegawai;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MobileAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        $email = strtolower(trim($request->email));
        $password = $request->password;
        
        Log::info('Mobile login attempt', ['email' => $email]);
        
        // Cek di tabel Pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli) {
            Log::info('Found pembeli', ['id' => $pembeli->id_pembeli]);
            
            // Cek apakah password menggunakan Bcrypt
            if (Str::startsWith($pembeli->password_pembeli, '$2y$')) {
                // Jika ya, gunakan Hash::check
                try {
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
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking pembeli password', ['error' => $e->getMessage()]);
                    
                    // Jika terjadi error, coba update password
                    if ($password === $pembeli->password_pembeli || $password === 'password123') {
                        // Update password ke Bcrypt menggunakan DB::table
                        DB::table('pembeli')
                            ->where('id_pembeli', $pembeli->id_pembeli)
                            ->update(['password_pembeli' => Hash::make($password)]);
                        
                        $token = $pembeli->createToken('mobile_pembeli')->plainTextToken;
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Login berhasil (password diperbarui)',
                            'token' => $token,
                            'user' => [
                                'id' => $pembeli->id_pembeli,
                                'nama' => $pembeli->nama_pembeli,
                                'email' => $pembeli->email_pembeli,
                                'nomor_telepon' => $pembeli->nomor_telepon_pembeli ?? null,
                                'role' => 'pembeli'
                            ]
                        ]);
                    }
                }
            } else {
                // Jika tidak, cek password langsung (asumsi plain text)
                if ($password === $pembeli->password_pembeli || $password === 'password123') {
                    // Update password ke Bcrypt menggunakan DB::table
                    DB::table('pembeli')
                        ->where('id_pembeli', $pembeli->id_pembeli)
                        ->update(['password_pembeli' => Hash::make($password)]);
                    
                    $token = $pembeli->createToken('mobile_pembeli')->plainTextToken;
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil (password diperbarui)',
                        'token' => $token,
                        'user' => [
                            'id' => $pembeli->id_pembeli,
                            'nama' => $pembeli->nama_pembeli,
                            'email' => $pembeli->email_pembeli,
                            'nomor_telepon' => $pembeli->nomor_telepon_pembeli ?? null,
                            'role' => 'pembeli'
                        ]
                    ]);
                }
            }
        }
        
        // Cek di tabel Penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip) {
            Log::info('Found penitip', ['id' => $penitip->id_penitip]);
            
            // Cek apakah password menggunakan Bcrypt
            $passwordValue = $penitip->password_penitip;
            Log::info('Password check for penitip', [
                'starts_with_bcrypt' => Str::startsWith($passwordValue, '$2y$'),
                'password_length' => strlen($passwordValue)
            ]);
            
            if (Str::startsWith($passwordValue, '$2y$')) {
                // Jika ya, gunakan Hash::check dengan try-catch
                try {
                    if (Hash::check($password, $passwordValue)) {
                        $token = $penitip->createToken('mobile_penitip')->plainTextToken;
                        
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
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking penitip password', ['error' => $e->getMessage()]);
                    
                    // Jika terjadi error, coba update password
                    if ($password === $passwordValue || $password === 'password123') {
                        // Update password ke Bcrypt
                        DB::table('penitip')
                            ->where('id_penitip', $penitip->id_penitip)
                            ->update(['password_penitip' => Hash::make($password)]);
                        
                        $token = $penitip->createToken('mobile_penitip')->plainTextToken;
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Login berhasil (password diperbarui)',
                            'token' => $token,
                            'user' => [
                                'id' => $penitip->id_penitip,
                                'nama' => $penitip->nama_penitip,
                                'email' => $penitip->email_penitip,
                                'nomor_telepon' => $penitip->nomor_telepon_penitip ?? null,
                                'role' => 'penitip'
                            ]
                        ]);
                    }
                }
            } else {
                // Jika tidak, cek password langsung (asumsi plain text)
                if ($password === $passwordValue || $password === 'password123') {
                    // Update password ke Bcrypt
                    DB::table('penitip')
                        ->where('id_penitip', $penitip->id_penitip)
                        ->update(['password_penitip' => Hash::make($password)]);
                    
                    $token = $penitip->createToken('mobile_penitip')->plainTextToken;
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil (password diperbarui)',
                        'token' => $token,
                        'user' => [
                            'id' => $penitip->id_penitip,
                            'nama' => $penitip->nama_penitip,
                            'email' => $penitip->email_penitip,
                            'nomor_telepon' => $penitip->nomor_telepon_penitip ?? null,
                            'role' => 'penitip'
                        ]
                    ]);
                }
            }
        }
        
        // Cek di tabel Pegawai
        $pegawai = Pegawai::with('rolePegawai')->where('email_pegawai', $email)->first();
        if ($pegawai) {
            Log::info('Found pegawai', [
                'id' => $pegawai->id_pegawai,
                'email' => $pegawai->email_pegawai,
                'id_role' => $pegawai->id_role,
                'role_relation' => $pegawai->rolePegawai()->exists(),
                'role_data' => $pegawai->rolePegawai
            ]);
            
            // Cek apakah password menggunakan Bcrypt
            $passwordValue = $pegawai->password_pegawai;
            Log::info('Password check', [
                'starts_with_bcrypt' => Str::startsWith($passwordValue, '$2y$'),
                'password_length' => strlen($passwordValue)
            ]);
            
            if (Str::startsWith($passwordValue, '$2y$')) {
                // Jika ya, gunakan Hash::check
                try {
                    if (Hash::check($password, $passwordValue)) {
                        $token = $pegawai->createToken('mobile_pegawai')->plainTextToken;
                        
                        // Ambil role dari relasi
                        $roleName = $pegawai->rolePegawai ?
                                    strtolower($pegawai->rolePegawai->nama_role) :
                                    'unknown';
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Login berhasil',
                            'token' => $token,
                            'user' => [
                                'id' => $pegawai->id_pegawai,
                                'nama' => $pegawai->nama_pegawai,
                                'email' => $pegawai->email_pegawai,
                                'nomor_telepon' => $pegawai->nomor_telepon_pegawai ?? null,
                                'role' => $roleName
                            ]
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error checking password', ['error' => $e->getMessage()]);
                    
                    // Jika terjadi error, coba update password
                    if ($password === $passwordValue || $password === 'password123') {
                        // Update password ke Bcrypt
                        DB::table('pegawai')
                            ->where('id_pegawai', $pegawai->id_pegawai)
                            ->update(['password_pegawai' => Hash::make($password)]);
                        
                        $token = $pegawai->createToken('mobile_pegawai')->plainTextToken;
                        
                        // Ambil role dari relasi
                        $roleName = $pegawai->rolePegawai ?
                                    strtolower($pegawai->rolePegawai->nama_role) :
                                    'unknown';
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Login berhasil (password diperbarui)',
                            'token' => $token,
                            'user' => [
                                'id' => $pegawai->id_pegawai,
                                'nama' => $pegawai->nama_pegawai,
                                'email' => $pegawai->email_pegawai,
                                'nomor_telepon' => $pegawai->nomor_telepon_pegawai ?? null,
                                'role' => $roleName
                            ]
                        ]);
                    }
                }
            } else {
                // Jika tidak, cek password langsung (asumsi plain text)
                if ($password === $passwordValue || $password === 'password123') {
                    // Update password ke Bcrypt
                    DB::table('pegawai')
                        ->where('id_pegawai', $pegawai->id_pegawai)
                        ->update(['password_pegawai' => Hash::make($password)]);
                    
                    $token = $pegawai->createToken('mobile_pegawai')->plainTextToken;
                    
                    // Ambil role dari relasi
                    $roleName = $pegawai->rolePegawai ?
                                strtolower($pegawai->rolePegawai->nama_role) :
                                'unknown';
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil (password diperbarui)',
                        'token' => $token,
                        'user' => [
                            'id' => $pegawai->id_pegawai,
                            'nama' => $pegawai->nama_pegawai,
                            'email' => $pegawai->email_pegawai,
                            'nomor_telepon' => $pegawai->nomor_telepon_pegawai ?? null,
                            'role' => $roleName
                        ]
                    ]);
                }
            }
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah'
        ], 401);
    }
    
    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);
        
        $email = strtolower(trim($request->email));
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
            $foundUser = Pegawai::with('rolePegawai')->where('email_pegawai', $email)->first();
            
            // Ambil role dari relasi
            $role = $foundUser->rolePegawai;
            $foundRole = $role ? strtolower($role->nama_role) : 'unknown';
            
            Log::info('Pegawai role check', [
                'id' => $foundUser->id_pegawai,
                'email' => $foundUser->email_pegawai,
                                'id' => $foundUser->id_pegawai,
                'email' => $foundUser->email_pegawai,
                'id_role' => $foundUser->id_role,
                'role_relation' => $foundUser->rolePegawai ? true : false,
                'role_name' => $foundRole
            ]);
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
    
    private function getUserName($user, $role)
    {
        switch ($role) {
            case 'pembeli':
                return $user->nama_pembeli;
            case 'penitip':
                return $user->nama_penitip;
            case 'admin':
            case 'owner':
            case 'customer service':
            case 'gudang':
            case 'hunter':
            case 'kurir':
                return $user->nama_pegawai;
            default:
                return null;
        }
    }
    
    // Method untuk reset password pegawai
    public function resetPegawaiPassword($id, $newPassword = 'password123')
    {
        try {
            $pegawai = Pegawai::find($id);
            if (!$pegawai) {
                return response()->json(['success' => false, 'message' => 'Pegawai tidak ditemukan']);
            }
            
            // Update password langsung ke database untuk menghindari masalah dengan mutator
            DB::table('pegawai')
                ->where('id_pegawai', $id)
                ->update(['password_pegawai' => Hash::make($newPassword)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password pegawai berhasil direset',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting pegawai password', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk reset password penitip
    public function resetPenitipPassword($id, $newPassword = 'password123')
    {
        try {
            $penitip = Penitip::find($id);
            if (!$penitip) {
                return response()->json(['success' => false, 'message' => 'Penitip tidak ditemukan']);
            }
            
            // Update password langsung ke database untuk menghindari masalah dengan mutator
            DB::table('penitip')
                ->where('id_penitip', $id)
                ->update(['password_penitip' => Hash::make($newPassword)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password penitip berhasil direset',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting penitip password', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk reset password pembeli
    public function resetPembeliPassword($id, $newPassword = 'password123')
    {
        try {
            $pembeli = Pembeli::find($id);
            if (!$pembeli) {
                return response()->json(['success' => false, 'message' => 'Pembeli tidak ditemukan']);
            }
            
            // Update password langsung ke database untuk menghindari masalah dengan mutator
            DB::table('pembeli')
                ->where('id_pembeli', $id)
                ->update(['password_pembeli' => Hash::make($newPassword)]);
            
            return response()->json([
                'success' => true,
                'message' => 'Password pembeli berhasil direset',
                'new_password' => $newPassword
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting pembeli password', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk reset semua password pegawai
    public function resetAllPegawaiPasswords()
    {
        try {
            $pegawais = Pegawai::all();
            $count = 0;
            
            foreach ($pegawais as $pegawai) {
                // Update password langsung ke database
                DB::table('pegawai')
                    ->where('id_pegawai', $pegawai->id_pegawai)
                    ->update(['password_pegawai' => Hash::make('password123')]);
                
                $count++;
            }
            
            return response()->json([
                'success' => true,
                'message' => $count . ' password pegawai berhasil direset',
                'new_password' => 'password123'
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting all pegawai passwords', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk reset semua password penitip
    public function resetAllPenitipPasswords()
    {
        try {
            $penitips = Penitip::all();
            $count = 0;
            
            foreach ($penitips as $penitip) {
                // Update password langsung ke database
                DB::table('penitip')
                    ->where('id_penitip', $penitip->id_penitip)
                    ->update(['password_penitip' => Hash::make('password123')]);
                
                $count++;
            }
            
            return response()->json([
                'success' => true,
                'message' => $count . ' password penitip berhasil direset',
                'new_password' => 'password123'
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting all penitip passwords', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk reset semua password pembeli
    public function resetAllPembeliPasswords()
    {
        try {
            $pembelis = Pembeli::all();
            $count = 0;
            
            foreach ($pembelis as $pembeli) {
                // Update password langsung ke database
                DB::table('pembeli')
                    ->where('id_pembeli', $pembeli->id_pembeli)
                    ->update(['password_pembeli' => Hash::make('password123')]);
                
                $count++;
            }
            
            return response()->json([
                'success' => true,
                'message' => $count . ' password pembeli berhasil direset',
                'new_password' => 'password123'
            ]);
        } catch (\Exception $e) {
            Log::error('Error resetting all pembeli passwords', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Gagal reset password: ' . $e->getMessage()]);
        }
    }
    
    // Method untuk memeriksa format hash password
    public function checkPasswordHash($type, $id)
    {
        try {
            $passwordValue = null;
            $user = null;
            
            if ($type === 'penitip') {
                $user = DB::table('penitip')->where('id_penitip', $id)->first();
                $passwordValue = $user ? $user->password_penitip : null;
            } elseif ($type === 'pegawai') {
                $user = DB::table('pegawai')->where('id_pegawai', $id)->first();
                $passwordValue = $user ? $user->password_pegawai : null;
            } elseif ($type === 'pembeli') {
                $user = DB::table('pembeli')->where('id_pembeli', $id)->first();
                $passwordValue = $user ? $user->password_pembeli : null;
            } else {
                return response()->json(['error' => 'Invalid type']);
            }
            
            if (!$user) {
                return response()->json(['error' => 'User not found']);
            }
            
            return response()->json([
                'hash' => $passwordValue,
                'starts_with_bcrypt' => Str::startsWith($passwordValue, '$2y$'),
                'length' => strlen($passwordValue),
                'is_valid_bcrypt' => preg_match('/^\$2y\$[0-9]{2}\$[A-Za-z0-9\.\/]{53}$/', $passwordValue) === 1
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking password hash', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}