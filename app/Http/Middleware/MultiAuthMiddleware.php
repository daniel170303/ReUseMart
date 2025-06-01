<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MultiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = ['pembeli', 'organisasi', 'penitip', 'pegawai'];
        }

        Log::info('MultiAuthMiddleware - Checking guards: ' . implode(', ', $guards));

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                Log::info("MultiAuthMiddleware - User authenticated with guard: {$guard}");

                Auth::shouldUse($guard);
                $this->syncSessionWithAuthUser($guard);

                return $next($request);
            }
        }

        Log::warning('MultiAuthMiddleware - No authenticated user found, redirecting to login');

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->route('login')->withErrors([
            'auth_error' => 'Silakan login terlebih dahulu untuk mengakses halaman ini.'
        ]);
    }

    /**
     * Sinkronisasi data session dengan user yang sedang login
     */
    private function syncSessionWithAuthUser($guard)
    {
        $user = Auth::guard($guard)->user();

        if (!$user) return;

        $guardMapping = [
            'pembeli' => [
                'role' => 'pembeli',
                'model' => 'Pembeli',
                'id_field' => 'id_pembeli',
                'name_field' => 'nama_pembeli',
                'email_field' => 'email_pembeli'
            ],
            'organisasi' => [
                'role' => 'organisasi',
                'model' => 'Organisasi',
                'id_field' => 'id_organisasi',
                'name_field' => 'nama_organisasi',
                'email_field' => 'email_organisasi'
            ],
            'penitip' => [
                'role' => 'penitip',
                'model' => 'Penitip',
                'id_field' => 'id_penitip',
                'name_field' => 'nama_penitip',
                'email_field' => 'email_penitip'
            ],
            'pegawai' => [
                'model' => 'Pegawai',
                'id_field' => 'id_pegawai',
                'name_field' => 'nama_pegawai',
                'email_field' => 'email_pegawai'
            ]
        ];

        if (!isset($guardMapping[$guard])) {
            Log::warning("MultiAuthMiddleware - Guard mapping not found for: $guard");
            return;
        }

        $mapping = $guardMapping[$guard];

        // Default role name
        $roleName = $mapping['role'] ?? 'pegawai';

        // Untuk pegawai, dapatkan role dari relasi
        if ($guard === 'pegawai') {
            $roleName = $this->getPegawaiRole($user);
        }

        session([
            'user_role' => $roleName,
            'user_model' => $mapping['model'],
            'user_id' => $user->{$mapping['id_field']},
            'user_name' => $user->{$mapping['name_field']},
            'user_email' => $user->{$mapping['email_field']}
        ]);

        if ($guard === 'pegawai') {
            session([
                'pegawai_id' => $user->id_pegawai,
                'pegawai_nama' => $user->nama_pegawai,
                'pegawai_email' => $user->email_pegawai,
                'pegawai_role_id' => $user->id_role,
                'pegawai_role_name' => optional($user->rolePegawai)->nama_role, // <== INI PENTING
            ]);
        }
    }

    /**
     * Ambil role pegawai dari relasi RolePegawai
     */
    private function getPegawaiRole($pegawai)
    {
        if (!method_exists($pegawai, 'rolePegawai')) {
            Log::error('MultiAuthMiddleware - Method rolePegawai tidak ditemukan di model Pegawai.');
            return 'pegawai';
        }

        $role = $pegawai->rolePegawai; // pastikan eager loaded atau akses relasi

        if (!$role) {
            return 'pegawai';
        }

        $roleMapping = [
            'Owner' => 'owner',
            'Admin' => 'admin',
            'Customer Service' => 'cs',
            'Gudang' => 'gudang',
            'Hunter' => 'hunter',
            'Kurir' => 'kurir'
        ];

        return $roleMapping[$role->nama_role] ?? 'pegawai';
    }
}