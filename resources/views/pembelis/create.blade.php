<!-- resources/views/pembelis/create.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Pembeli</title>
</head>
<body>
    <h1>Tambah Pembeli</h1>
    <form action="{{ route('pembelis.store') }}" method="POST">
        @csrf
        <input type="text" name="nama_pembeli" placeholder="Nama"><br>
        <input type="text" name="alamat_pembeli" placeholder="Alamat"><br>
        <input type="text" name="nomor_telepon_pembeli" placeholder="No. Telepon"><br>
        <input type="email" name="email_pembeli" placeholder="Email"><br>
        <input type="password" name="password_pembeli" placeholder="Password"><br>
        <button type="submit">Simpan</button>
    </form>
    <a href="{{ route('pembelis.index') }}">Kembali</a>
</body>
</html>
