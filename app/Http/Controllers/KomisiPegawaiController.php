<?php

namespace App\Http\Controllers;

use App\Models\KomisiPegawai;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KomisiPegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $komisiPegawai = KomisiPegawai::with('pegawai')->get();
            
            return response()->json([
                'success' => true,
                'message' => 'Data komisi pegawai berhasil diambil',
                'data' => $komisiPegawai
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching komisi pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_pegawai' => 'required|exists:pegawai,id_pegawai',
                'jumlah_komisi' => 'required|numeric|min:0'
            ], [
                'id_pegawai.required' => 'ID Pegawai harus diisi!',
                'id_pegawai.exists' => 'Pegawai tidak ditemukan!',
                'jumlah_komisi.required' => 'Jumlah komisi harus diisi!',
                'jumlah_komisi.numeric' => 'Jumlah komisi harus berupa angka!',
                'jumlah_komisi.min' => 'Jumlah komisi tidak boleh negatif!'
            ]);

            $komisiPegawai = KomisiPegawai::create([
                'id_pegawai' => $request->id_pegawai,
                'jumlah_komisi' => $request->jumlah_komisi
            ]);

            $komisiPegawai->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Komisi pegawai berhasil ditambahkan',
                'data' => $komisiPegawai
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating komisi pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $komisiPegawai = KomisiPegawai::with('pegawai')->find($id);

            if (!$komisiPegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi pegawai tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data komisi pegawai berhasil diambil',
                'data' => $komisiPegawai
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching komisi pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $komisiPegawai = KomisiPegawai::find($id);

            if (!$komisiPegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi pegawai tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'id_pegawai' => 'sometimes|exists:pegawai,id_pegawai',
                'jumlah_komisi' => 'sometimes|numeric|min:0'
            ], [
                'id_pegawai.exists' => 'Pegawai tidak ditemukan!',
                'jumlah_komisi.numeric' => 'Jumlah komisi harus berupa angka!',
                'jumlah_komisi.min' => 'Jumlah komisi tidak boleh negatif!'
            ]);

            $komisiPegawai->update($request->only(['id_pegawai', 'jumlah_komisi']));
            $komisiPegawai->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Komisi pegawai berhasil diperbarui',
                'data' => $komisiPegawai
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating komisi pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $komisiPegawai = KomisiPegawai::find($id);

            if (!$komisiPegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komisi pegawai tidak ditemukan'
                ], 404);
            }

            $komisiPegawai->delete();

            return response()->json([
                'success' => true,
                'message' => 'Komisi pegawai berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error deleting komisi pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get komisi by pegawai ID
     */
    public function getByPegawai($id_pegawai)
    {
        try {
            $komisiPegawai = KomisiPegawai::with('pegawai')
                ->where('id_pegawai', $id_pegawai)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data komisi pegawai berhasil diambil',
                'data' => $komisiPegawai
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching komisi by pegawai: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data komisi pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get total komisi by pegawai ID
     */
    public function getTotalKomisiByPegawai($id_pegawai)
    {
        try {
            $totalKomisi = KomisiPegawai::where('id_pegawai', $id_pegawai)
                ->sum('jumlah_komisi');

            $pegawai = Pegawai::find($id_pegawai);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Total komisi pegawai berhasil diambil',
                'data' => [
                    'id_pegawai' => $id_pegawai,
                    'nama_pegawai' => $pegawai->nama ?? 'N/A',
                    'total_komisi' => $totalKomisi
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error calculating total komisi: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung total komisi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
