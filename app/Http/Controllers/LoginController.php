<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('login.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        // Log untuk debugging
        Log::info('Login attempt for email: ' . $email);

        // Cari user di berbagai tabel
        $user = $this->findUserAcrossTables($email);

        if (!$user) {
            Log::info('User not found for email: ' . $email);
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->withInput($request->except('password'));
        }

        // Verifikasi password
        if (!Hash::check($password, $user['password'])) {
            Log::info('Password mismatch for email: ' . $email);
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->except('password'));
        }

        // Buat user object untuk session
        $authUser = $this->createAuthUser($user);
        
        Auth::login($authUser);
        $request->session()->regenerate();

        Log::info('User logged in successfully: ' . $email . ' with role: ' . $user['role']);

        // Redirect sesuai role
        return $this->redirectByRole($user['role']);
    }

    /**
     * Cari user di semua tabel yang relevan
     */
    private function findUserAcrossTables($email)
    {
        // Cek di tabel pegawai (owner, admin, cs, gudang, hunter, kurir)
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai) {
            // Ambil nama role dari tabel role_pegawai
            $rolePegawai = RolePegawai::find($pegawai->id_role);
            
            // Mapping nama role dari database ke role yang digunakan di sistem
            $roleMapping = [
                'Owner' => 'owner',
                'Admin' => 'admin', 
                'Customer Service' => 'cs',
                'Gudang' => 'gudang',
                'Hunter' => 'hunter',
                'Kurir' => 'kurir'
            ];
            
            $roleName = 'pegawai'; // default
            if ($rolePegawai && isset($roleMapping[$rolePegawai->nama_role])) {
                $roleName = $roleMapping[$rolePegawai->nama_role];
            }
            
            return [
                'id' => $pegawai->id_pegawai,
                'name' => $pegawai->nama_pegawai,
                'email' => $pegawai->email_pegawai,
                'password' => $pegawai->password_pegawai,
                'role' => $roleName,
                'phone' => $pegawai->nomor_telepon_pegawai,
                'model' => 'Pegawai',
                'role_id' => $pegawai->id_role,
                'role_name' => $rolePegawai ? $rolePegawai->nama_role : null,
                'original_data' => $pegawai
            ];
        }

        // Cek di tabel pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli) {
            return [
                'id' => $pembeli->id_pembeli,
                'name' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
                'password' => $pembeli->password_pembeli,
                'role' => 'pembeli',
                'phone' => $pembeli->nomor_telepon_pembeli ?? null,
                'model' => 'Pembeli',
                'original_data' => $pembeli
            ];
        }

        // Cek di tabel organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi) {
            return [
                'id' => $organisasi->id_organisasi,
                'name' => $organisasi->nama_organisasi,
                'email' => $organisasi->email_organisasi,
                'password' => $organisasi->password_organisasi,
                'role' => 'organisasi',
                'phone' => $organisasi->nomor_telepon_organisasi ?? null,
                'model' => 'Organisasi',
                'original_data' => $organisasi
            ];
        }

        // Cek di tabel penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip) {
            return [
                'id' => $penitip->id_penitip,
                'name' => $penitip->nama_penitip,
                'email' => $penitip->email_penitip,
                'password' => $penitip->password_penitip,
                'role' => 'penitip',
                'phone' => $penitip->nomor_telepon_penitip ?? null,
                'model' => 'Penitip',
                'original_data' => $penitip
            ];
        }

        return null;
    }

    /**
     * Buat user object untuk autentikasi Laravel
     */
    private function createAuthUser($userData)
    {
        // Create a temporary User model for authentication
        $user = new User();
        $user->id = $userData['id'];
        $user->name = $userData['name'];
        $user->email = $userData['email'];
        $user->password = $userData['password'];
        $user->role = $userData['role'];
        $user->phone = $userData['phone'] ?? null;
        $user->exists = true; // Important: tell Eloquent this exists

        // Store original model data in session for future use
        session(['user_model' => $userData['model']]);
        session(['user_original_data' => $userData['original_data']]);

        return $user;
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectByRole($role)
    {
        $routes = [
            // Role dari tabel pegawai
            'owner' => 'owner.dashboard',
            'admin' => 'admin.dashboard',
            'cs' => 'cs.dashboard',
            'gudang' => 'gudang.dashboard',
            'hunter' => 'hunter.dashboard',
            'kurir' => 'kurir.dashboard',
            
            // Role dari tabel lain
            'pembeli' => 'pembeli.dashboard',
            'organisasi' => 'organisasi.dashboard',
            'penitip' => 'penitip.dashboard',
        ];

        $routeName = $routes[$role] ?? 'dashboard';

        // Cek apakah route exists
        if (\Route::has($routeName)) {
            return redirect()->route($routeName)->with('success', 'Login berhasil! Selamat datang ' . ucfirst($role));
        }

        // Fallback redirect
        return redirect('/dashboard')->with('success', 'Login berhasil!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();

        // Clear session data
        $request->session()->forget(['user_model', 'user_original_data']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}