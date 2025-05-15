<?php

namespace App\Http\Controllers;

use App\Http\Controllers;
use App\Models\Request;
use Illuminate\Http\Request as HttpRequest;


class RequestController extends Controller
{
    // GET /api/request
    public function index()
    {
        $data = Request::all();
        return response()->json($data, 200);
    }

    // POST /api/request
    public function store(HttpRequest $request)
    {
        $validated = $request->validate([
            'id_organisasi' => 'required|integer',
            'nama_request_barang' => 'required|string',
            'tanggal_request' => 'required|date',
            'status_request' => 'required|string',
        ]);

        $data = Request::create($validated);
        return response()->json(['message' => 'Request berhasil ditambahkan', 'data' => $data], 201);
    }

    // GET /api/request/{id}
    public function show($id)
    {
        $data = Request::find($id);
        if (!$data) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        return response()->json($data, 200);
    }

    // PUT /api/request/{id}
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
        return response()->json(['message' => 'Request berhasil diperbarui', 'data' => $data], 200);
    }

    // DELETE /api/request/{id}
    public function destroy($id)
    {
        $data = Request::find($id);
        if (!$data) {
            return response()->json(['message' => 'Request tidak ditemukan'], 404);
        }

        $data->delete();
        return response()->json(['message' => 'Request berhasil dihapus'], 200);
    }

    // GET /api/request/search/{keyword}
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
}
