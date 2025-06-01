<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    // Tampilkan semua pegawai
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $pegawai = Pegawai::with('role')
            ->when($keyword, function ($query, $keyword) {
                $query->where('nama_pegawai', 'like', "%{$keyword}%")
                    ->orWhere('nomor_telepon_pegawai', 'like', "%{$keyword}%")
                    ->orWhere('email_pegawai', 'like', "%{$keyword}%");
            })
            ->get();

        $roles = RolePegawai::all();

        return view('pegawai.pegawai', compact('pegawai', 'roles'));
    }

    // Simpan pegawai baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_role' => 'required|integer|exists:role_pegawai,id_role',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai',
            'password_pegawai' => 'required|string|min:8|max:50|confirmed',
        ]);

        $validated['password_pegawai'] = Hash::make($validated['password_pegawai']);

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan');
    }

    // Update data pegawai
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'id_role' => 'required|integer|exists:role_pegawai,id_role',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => [
                'required',
                'email',
                'max:50',
                Rule::unique('pegawai', 'email_pegawai')->ignore($pegawai->id_pegawai, 'id_pegawai'),
            ],
        ]);

        // Jika ingin ubah password, validasi dan hash
        if ($request->filled('password_pegawai')) {
            $request->validate([
                'password_pegawai' => 'nullable|string|min:8|max:50|confirmed',
            ]);
            $pegawai->password_pegawai = Hash::make($request->password_pegawai);
        }

        $pegawai->update($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil diperbarui');
    }

    // Hapus pegawai
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        $pegawai->delete();

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil dihapus');
    }

    // Pencarian pegawai berdasarkan keyword
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $results = Pegawai::where('nama_pegawai', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_pegawai', 'like', "%$keyword%")
            ->orWhere('email_pegawai', 'like', "%$keyword%")
            ->orWhere('id_role', 'like', "%$keyword%")
            ->get();

        return view('pegawai.search', compact('results', 'keyword'));
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('role')->find($id);
        
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        return response()->json($pegawai);
    }

    /**
     * Reset password pegawai ke tanggal lahir
     */
    public function resetPasswordToBirthDate(Request $request, $id)
    {
        $pegawai = Pegawai::find($id);
        if (!$pegawai) {
            return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
        }

        $request->validate([
            'tanggal_lahir' => 'required|date_format:Y-m-d',
        ]);

        $birthDate = date('dmY', strtotime($request->tanggal_lahir));
        $pegawai->password_pegawai = Hash::make($birthDate);
        $pegawai->save();

        return response()->json([
            'success' => true,
            'message' => 'Password pegawai berhasil direset ke tanggal lahir',
        ]);
    }

    public function dashboardCs()
    {
        // Cek apakah user yang login adalah pegawai
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $user = Auth::guard('pegawai')->user();

        // Pastikan role-nya adalah CS (Customer Service)
        if (!$user->rolePegawai || $user->rolePegawai->nama_role !== 'Customer Service') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Customer Service.']);
        }

        // Jika role sesuai, lanjut tampilkan dashboard CS
        return view('customerService.dashboardCustomerService');
    }

    public function dashboardGudang()
    {
        // Cek apakah user yang login adalah pegawai
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $user = Auth::guard('pegawai')->user();

        // Pastikan role-nya adalah Gudang
        if (!$user->rolePegawai || $user->rolePegawai->nama_role !== 'Gudang') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Gudang.']);
        }

        // Jika role sesuai, tampilkan dashboard Gudang
        return view('pegawai.gudang.dashboardGudang');
    }

    public function dashboardOwner()
    {
        // Cek apakah user yang login adalah pegawai
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $owner = Auth::guard('pegawai')->user();
        
        // Pastikan role-nya adalah Owner
        if (!$owner->rolePegawai || $owner->rolePegawai->nama_role !== 'Owner') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Owner.']);
        }

        // Jika role sesuai, tampilkan dashboard Owner dengan data owner
        return view('owner.dashboardOwner', compact('owner'));
    }

    public function dashboardHunter()
    {
        // Cek apakah user yang login adalah pegawai
        if (!Auth::guard('pegawai')->check()) {
            return redirect()->route('login')->withErrors(['access_denied' => 'Anda tidak diizinkan mengakses halaman ini.']);
        }

        $hunter = Auth::guard('pegawai')->user();
        
        // Pastikan role-nya adalah Owner
        if (!$hunter->rolePegawai || $hunter->rolePegawai->nama_role !== 'Hunter') {
            return redirect('/')->withErrors(['access_denied' => 'Anda tidak memiliki hak akses sebagai Hunter.']);
        }

        // Jika role sesuai, tampilkan dashboard Owner dengan data owner
        return view('pegawai.hunter.dashboardHunter', compact('hunter'));
    }

}
