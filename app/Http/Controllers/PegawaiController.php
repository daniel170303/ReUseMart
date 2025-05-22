<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    // Tampilkan semua pegawai
    public function index()
    {
        // Load pegawai dengan data role-nya (relasi 'role')
        $pegawai = Pegawai::with('role')->get();
        $roles = RolePegawai::all();
        return view('pegawai.crud', compact('pegawai', 'roles'));
    }

    // Tampilkan form tambah pegawai (opsional)
    public function create()
    {
        return view('pegawai.create');
    }

    // Simpan pegawai baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_role' => 'required|integer',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai',
            'password_pegawai' => 'required|string|min:8|max:50',
        ]);

        Pegawai::create($validated);

        return redirect()->route('pegawai.index')->with('success', 'Pegawai berhasil ditambahkan');
    }

    // Tampilkan form edit pegawai
    public function edit($id)
    {
        $pegawai = Pegawai::findOrFail($id);
        return view('pegawai.edit', compact('pegawai'));
    }

    // Update data pegawai
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $validated = $request->validate([
            'id_role' => 'required|integer',
            'nama_pegawai' => 'required|string|max:50',
            'nomor_telepon_pegawai' => 'required|string|max:50',
            'email_pegawai' => 'required|email|max:50|unique:pegawai,email_pegawai,' . $pegawai->id_pegawai . ',id_pegawai',
            'password_pegawai' => 'required|string|min:8|max:50',
        ]);

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

    /**
 * Reset password pegawai ke tanggal lahir
 */
public function resetPasswordToBirthDate(Request $request, $id)
{
    // Cek apakah pegawai ditemukan
    $pegawai = Pegawai::find($id);
    if (!$pegawai) {
        return response()->json(['message' => 'Pegawai tidak ditemukan'], 404);
    }
    
    // Validasi input tanggal lahir
    $request->validate([
        'tanggal_lahir' => 'required|date_format:Y-m-d',
    ]);
    
    // Format tanggal lahir menjadi ddmmyyyy untuk password
    $birthDate = date('dmY', strtotime($request->tanggal_lahir));
    
    // Hash password dan update
    $pegawai->password_pegawai = Hash::make($birthDate);
    $pegawai->save();
    
    return response()->json([
        'success' => true,
        'message' => 'Password pegawai berhasil direset ke tanggal lahir'
    ]);
}
}
