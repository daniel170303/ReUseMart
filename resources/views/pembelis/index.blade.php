<!-- resources/views/pembelis/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pembeli</title>
</head>
<body>
    <h1>Daftar Pembeli</h1>
    <a href="{{ route('pembelis.create') }}">Tambah Pembeli</a>
    <table border="1">
        <tr>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No. Telepon</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
        @foreach ($pembelis as $pembeli)
        <tr>
            <td>{{ $pembeli->nama_pembeli }}</td>
            <td>{{ $pembeli->alamat_pembeli }}</td>
            <td>{{ $pembeli->nomor_telepon_pembeli }}</td>
            <td>{{ $pembeli->email_pembeli }}</td>
            <td>
                <a href="{{ route('pembelis.edit', $pembeli->id_pembeli) }}">Edit</a>
                <form action="{{ route('pembelis.destroy', $pembeli->id_pembeli) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin hapus?')">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</body>
</html>
