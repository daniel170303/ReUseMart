<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Organisasi;
use App\Models\Request as RequestDonasi;
use Illuminate\Support\Facades\Auth;

class OrganisasiController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('search');
        $organisasi = Organisasi::query()
            ->when($keyword, function ($query, $keyword) {
                return $query->where('nama_organisasi', 'like', "%$keyword%")
                             ->orWhere('alamat_organisasi', 'like', "%$keyword%")
                             ->orWhere('nomor_telepon_organisasi', 'like', "%$keyword%")
                             ->orWhere('email_organisasi', 'like', "%$keyword%");
            })
            ->get();

        return view('admin.organisasi.index', compact('organisasi', 'keyword'));
    }

    public function create()
    {
        return view('admin.organisasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi',
            'password_organisasi' => 'required|string|max:50',
        ]);

        Organisasi::create($validated);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        return view('admin.organisasi.edit', compact('organisasi'));
    }

    public function update(Request $request, $id)
    {
        $organisasi = Organisasi::findOrFail($id);

        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:50',
            'alamat_organisasi' => 'required|string|max:50',
            'nomor_telepon_organisasi' => 'required|string|max:50',
            'email_organisasi' => 'required|email|max:50|unique:organisasi,email_organisasi,' . $id . ',id_organisasi',
            'password_organisasi' => 'required|string|max:50',
        ]);

        $organisasi->update($validated);
        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $organisasi = Organisasi::findOrFail($id);
        $organisasi->delete();

        return redirect()->route('admin.organisasi.index')->with('success', 'Organisasi berhasil dihapus.');
    }

    public function showRequestForm()
    {
        return view('organisasi.request_barang');
    }

    public function submitRequest(Request $request)
    {
        $validated = $request->validate([
            'nama_request_barang' => 'required|string|max:255',
            'tanggal_request' => 'required|date',
        ]);

        // Simpan request baru, id_organisasi dari user yang login
        $validated['id_organisasi'] = auth()->user()->id; // asumsi user model ada relasi ke organisasi
        $validated['status_request'] = 'pending'; // default status

        RequestDonasi::create($validated);

        return redirect()->route('organisasi.request-form')->with('success', 'Request barang titipan berhasil dikirim.');
    }

    public function dashboard()
    {
        $organisasi = Auth::guard('organisasi')->user();
        
        // Data untuk dashboard (sesuaikan dengan kebutuhan)
        $totalRequests = 0; // Hitung dari database
        $pendingRequests = 0; // Hitung dari database
        
        return view('organisasi.dashboardOrganisasi', compact('organisasi', 'totalRequests', 'pendingRequests'));
    }
}
