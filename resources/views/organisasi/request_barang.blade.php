<!DOCTYPE html>
<html>
<head>
    <title>Request Barang Titipan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 900px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
        form.inline {
            display: inline;
        }
        .btn {
            padding: 4px 10px;
            margin: 2px;
            cursor: pointer;
            border: none;
            border-radius: 3px;
            font-size: 14px;
        }
        .btn-edit {
            background-color: #4CAF50;
            color: white;
        }
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        .btn-search {
            background-color: #2196F3;
            color: white;
        }
        input[type="text"], input[type="number"], input[type="date"] {
            padding: 6px;
            margin: 5px 0;
            width: 100%;
            max-width: 300px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .form-section {
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Request Barang Donasi</h1>

    {{-- Pesan sukses / error --}}
    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    @if(session('error'))
        <p style="color:red;">{{ session('error') }}</p>
    @endif

    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Search --}}
    <form action="{{ route('organisasi.requestBarang.search') }}" method="GET">
        <input type="text" name="keyword" placeholder="Cari request...">
        <button type="submit">Cari</button>
    </form>

    {{-- Form Tambah / Edit Request --}}
    <div class="form-section">
        <h2>{{ isset($editRequest) ? 'Edit Request' : 'Tambah Request Baru' }}</h2>
        <form action="{{ isset($editRequest) ? route('organisasi.requestBarang.update', $editRequest->id_request) : route('organisasi.requestBarang.store') }}" method="POST">
            @csrf
            @if(isset($editRequest))
                @method('PUT')
            @endif

            <label for="id_organisasi">ID Organisasi:</label>
            <input type="number" id="id_organisasi" name="id_organisasi" value="{{ old('id_organisasi', $editRequest->id_organisasi ?? '') }}" required>

            <label for="nama_request_barang">Nama Barang yang Diminta:</label>
            <input type="text" id="nama_request_barang" name="nama_request_barang" value="{{ old('nama_request_barang', $editRequest->nama_request_barang ?? '') }}" required>

            <label for="tanggal_request">Tanggal Request:</label>
            <input type="date" id="tanggal_request" name="tanggal_request" value="{{ old('tanggal_request', isset($editRequest) ? $editRequest->tanggal_request : date('Y-m-d')) }}" required>

            <label for="status_request">Status Request:</label>
            <input type="text" id="status_request" name="status_request" value="{{ old('status_request', $editRequest->status_request ?? 'pending') }}" required>

            <br><br>
            <button type="submit" class="btn btn-edit">{{ isset($editRequest) ? 'Update' : 'Kirim Request' }}</button>
            @if(isset($editRequest))
                <a href="{{ route('organisasi.requestBarang.index') }}" class="btn">Batal</a>
            @endif
        </form>
    </div>

    {{-- Tabel Daftar Request --}}
    <table>
        <thead>
            <tr>
                <th>ID Request</th>
                <th>ID Organisasi</th>
                <th>Nama Barang</th>
                <th>Tanggal Request</th>
                <th>Status Request</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($requests as $req)
                <tr>
                    <td>{{ $req->id_request }}</td>
                    <td>{{ $req->id_organisasi }}</td>
                    <td>{{ $req->nama_request_barang }}</td>
                    <td>{{ $req->tanggal_request }}</td>
                    <td>{{ $req->status_request }}</td>
                    <td>
                        <a href="{{ route('organisasi.requestBarang.edit', $req->id_request) }}" class="btn btn-edit">Edit</a>

                        <form action="{{ route('organisasi.requestBarang.destroy', $req->id_request) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus request ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-delete">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;">Tidak ada data request.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination jika pakai paginate --}}
    @if(method_exists($requests, 'links'))
        <div style="margin-top:20px;">
            {{ $requests->links() }}
        </div>
    @endif

</body>
</html>
