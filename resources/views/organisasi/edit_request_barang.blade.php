<!DOCTYPE html>
<html>
<head>
    <title>Edit Request Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            margin: 0;
            padding: 40px;
        }

        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="date"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .btn-group {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        button, a.btn-back {
            padding: 10px 20px;
            border: none;
            background: #007BFF;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
        }

        a.btn-back {
            background: #6c757d;
        }

        .alert {
            padding: 10px;
            background-color: #f44336;
            color: white;
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #4CAF50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Request Barang</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('organisasi.requestBarang.update', $request->id_request) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="id_organisasi">ID Organisasi:</label>
            <input type="number" id="id_organisasi" name="id_organisasi" value="{{ old('id_organisasi', $request->id_organisasi) }}" required>

            <label for="nama_request_barang">Nama Barang yang Diminta:</label>
            <input type="text" id="nama_request_barang" name="nama_request_barang" value="{{ old('nama_request_barang', $request->nama_request_barang) }}" required>

            <label for="tanggal_request">Tanggal Request:</label>
            <input type="date" id="tanggal_request" name="tanggal_request" value="{{ old('tanggal_request', $request->tanggal_request) }}" required>

            <label for="status_request">Status Request:</label>
            <input type="text" id="status_request" name="status_request" value="{{ old('status_request', $request->status_request) }}" required>

            <div class="btn-group">
                <a href="{{ route('organisasi.requestBarang.index') }}" class="btn-back">Kembali</a>
                <button type="submit">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</body>
</html>
