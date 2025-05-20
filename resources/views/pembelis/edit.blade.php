<!-- resources/views/pembelis/edit.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Edit Pembeli</title>
</head>
<body>
    <h1>Edit Pembeli</h1>
    <form action="{{ route('pembelis.update', $pembeli->id_pembeli) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="text" name="nama_pembeli" value="{{ $pembeli->nama_pembeli }}"><br>
        <input type="text" name="alamat_pembeli" value="{{ $pembeli->alamat_pembeli }}"><br>
        <input type="text" name="nomor_telepon_pembeli" value="{{ $pembeli->nomor_telepon_pembeli }}"><br>
        <input type="email" name="email_pembeli" value="{{ $pembeli->email_pembeli }}"><br>
        <input type="password" name="password_pembeli" placeholder="Kosongkan jika tidak diubah"><br>
        <button type="submit">Update</button>
    </form>
    <a href="{{ route('pembelis.index') }}">Kembali</a>
</body>
</html>
