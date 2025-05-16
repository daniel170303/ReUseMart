<?php

namespace App\Http\Controllers;
use App\Models\Request; // Model Request Donasi
use App\Models\Donasi;
use App\Models\BarangTitipan;
use App\Models\Penitip;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DonasiDiterimaNotification;

class RequestController extends Controller
{
    // Menampilkan semua data request (API)
    public function index()
    {
        $requests = Request::all(); // ambil data
        return view('organisasi.request_barang', compact('requests')); // kirim ke view blade
    }

    // Menyimpan request baru (API)
    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'id_organisasi' => 'required|integer|exists:organisasi,id_organisasi',
            'nama_request_barang' => 'required|string|max:255',
            'tanggal_request' => 'required|date',
        ]);

        $validated['status_request'] = 'pending';

        \App\Models\Request::create($validated);

        return redirect()->route('organisasi.requestBarang.create')->with('success', 'Request berhasil dikirim!');
    }

    // Menampilkan request berdasarkan ID (API)
    public function show($id)
    {
        $data = Request::find($id);
        if (!$data) {
            return redirect()->back()->with('error', 'Request tidak ditemukan');
        }

        return view('organisasi.detail_request', compact('data'));
    }

    // Update request berdasarkan ID (API)
    public function update(HttpRequest $request, $id)
    {
        $data = Request::find($id);
        if (!$data) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'id_organisasi' => 'required|integer',
            'nama_request_barang' => 'required|string',
            'tanggal_request' => 'required|date',
            'status_request' => 'required|string',
        ]);

        $data->update($validated);
        return redirect()->route('organisasi.requestBarang.index')
                     ->with('success', 'Request berhasil diperbarui.');
    }

    public function edit($id)
    {
        $request = Request::find($id);
        if (!$request) {
            return redirect()->route('organisasi.requestBarang.index')->with('error', 'Request tidak ditemukan');
        }
        return view('organisasi.edit_request_barang', compact('request'));
    }

    // Hapus request berdasarkan ID (API)
    public function destroy($id)
    {
        $data = Request::find($id);
        if (!$data) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        $data->delete();
        return redirect()->route('organisasi.requestBarang.index')
                     ->with('success', 'Request berhasil dihapus.');
    }

    // Search request berdasarkan keyword (API)
    public function search($keyword)
    {
        $results = Request::where('id_request', 'like', "%$keyword%")
            ->orWhere('id_organisasi', 'like', "%$keyword%")
            ->orWhere('nama_request_barang', 'like', "%$keyword%")
            ->orWhere('tanggal_request', 'like', "%$keyword%")
            ->orWhere('status_request', 'like', "%$keyword%")
            ->get();

        return response()->json($results, 200);
    }

    // Menampilkan halaman owner untuk menerima request donasi
    public function ownerPage()
    {
        $requests = Request::where('status_request', 'pending')->get();
        $donasis = Donasi::all();
        return view('owner.owner', compact('requests', 'donasis'));
    }

    // Terima request donasi: simpan di donasi, ubah status request dan status barang
    public function terimaRequest(HttpRequest $request, $id_request)
    {
        $request->validate([
            'tanggal_donasi' => 'required|date',
            'nama_penerima_donasi' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Ambil request donasi
            $requestDonasi = Request::findOrFail($id_request);

            // Cari barang titipan yang cocok
            $barangTitipan = BarangTitipan::where('nama_barang', $requestDonasi->nama_request_barang)
                                ->where('status_barang', 'barang untuk donasi')
                                ->first();

            if (!$barangTitipan) {
                return redirect()->back()->with('error', 'Barang titipan tidak tersedia atau sudah didonasikan.');
            }

            // Simpan ke tabel donasi
            Donasi::create([
                'id_barang' => $barangTitipan->id_barang,
                'id_request' => $requestDonasi->id_request,
                'tanggal_donasi' => $request->tanggal_donasi,
                'penerima_donasi' => $request->nama_penerima_donasi,
            ]);

            // Update status request
            $requestDonasi->status_request = 'diterima';
            $requestDonasi->save();

            // Update status barang
            $barangTitipan->status_barang = 'dijual'; // atau 'sudah didonasikan' jika enum diubah
            $barangTitipan->save();

            // Kirim notifikasi ke penitip
            $penitip = Penitip::find($barangTitipan->id_penitip);
            if ($penitip) {
                Notification::send($penitip, new DonasiDiterimaNotification($barangTitipan, $requestDonasi));
            }

            DB::commit();
            return redirect()->back()->with('success', 'Request donasi berhasil diterima dan tercatat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function create()
    {
        $requests = \App\Models\Request::all(); // atau bisa dikosongkan kalau tidak ingin tampilkan
        return view('organisasi.request_barang', compact('requests'));
    }

}
