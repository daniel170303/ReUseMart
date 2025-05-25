<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    // Tampilkan semua pegawai
    public function index()
    {
        $pegawai = Pegawai::with('role')->get();
        $roles = RolePegawai::all();
        return view('pegawai.crud', compact('pegawai', 'roles'));
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
}
