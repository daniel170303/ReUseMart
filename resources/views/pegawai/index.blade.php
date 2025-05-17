<!-- resources/views/pegawai/index.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pegawai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Daftar Pegawai</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <a href="{{ route('pegawai.create') }}" class="btn btn-primary mb-3">Tambah Pegawai</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Role</th>
                <th>Nama Pegawai</th>
                <th>Nomor Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pegawai as $p)
                <tr>
                    <td>{{ $p->id_pegawai }}</td>
                    <td>{{ $p->id_role }}</td>
                    <td>{{ $p->nama_pegawai }}</td>
                    <td>{{ $p->nomor_telepon_pegawai }}</td>
                    <td>{{ $p->email_pegawai }}</td>
                    <td>
                        <a href="{{ route('pegawai.edit', $p->id_pegawai) }}" class="btn btn-warning btn-sm">Edit</a>

                        <form action="{{ route('pegawai.destroy', $p->id_pegawai) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Yakin ingin hapus?')" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">Data pegawai tidak ditemukan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
