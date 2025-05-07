<!-- resources/views/barang_titipan/index.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Barang Titipan</h1>
    
    <!-- Tampilkan pesan sukses jika ada -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    
    <a href="{{ route('barang_titipan.create') }}" class="btn btn-primary mb-3">Tambah Barang Titipan</a>
    
    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Harga Barang</th>
                <th>Jenis Barang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($barangTitipan as $barang)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $barang->nama_barang_titipan }}</td>
                <td>{{ $barang->harga_barang }}</td>
                <td>{{ $barang->jenis_barang }}</td>
                <td>
                    <a href="{{ route('barang_titipan.show', $barang->id_barang) }}" class="btn btn-info btn-sm">Detail</a>
                    <a href="{{ route('barang_titipan.edit', $barang->id_barang) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('barang_titipan.destroy', $barang->id_barang) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
