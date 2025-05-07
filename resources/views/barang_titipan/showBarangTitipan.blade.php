<!-- resources/views/barang_titipan/show.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Detail Barang Titipan</h1>
    
    <table class="table">
        <tr>
            <th>Nama Barang</th>
            <td>{{ $barangTitipan->nama_barang_titipan }}</td>
        </tr>
        <tr>
            <th>Harga Barang</th>
            <td>{{ $barangTitipan->harga_barang }}</td>
        </tr>
        <tr>
            <th>Deskripsi Barang</th>
            <td>{{ $barangTitipan->deskripsi_barang }}</td>
        </tr>
        <tr>
            <th>Jenis Barang</th>
            <td>{{ $barangTitipan->jenis_barang }}</td>
        </tr>
        <tr>
            <th>Garansi Barang</th>
            <td>{{ $barangTitipan->garansi_barang }}</td>
        </tr>
        <tr>
            <th>Berat Barang</th>
            <td>{{ $barangTitipan->berat_barang }}</td>
        </tr>
    </table>
    
    <a href="{{ route('barang_titipan.index') }}" class="btn btn-secondary">Kembali</a>
</div>
@endsection
