<!-- resources/views/barang_titipan/edit.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Barang Titipan</h1>
    
    <form action="{{ route('barang_titipan.update', $barangTitipan->id_barang) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="nama_barang_titipan" class="form-label">Nama Barang Titipan</label>
            <input type="text" class="form-control" id="nama_barang_titipan" name="nama_barang_titipan" value="{{ $barangTitipan->nama_barang_titipan }}" required>
        </div>
        
        <div class="mb-3">
            <label for="harga_barang" class="form-label">Harga Barang</label>
            <input type="number" step="0.01" class="form-control" id="harga_barang" name="harga_barang" value="{{ $barangTitipan->harga_barang }}" required>
        </div>
        
        <div class="mb-3">
            <label for="deskripsi_barang" class="form-label">Deskripsi Barang</label>
            <textarea class="form-control" id="deskripsi_barang" name="deskripsi_barang" rows="3" required>{{ $barangTitipan->deskripsi_barang }}</textarea>
        </div>
        
        <div class="mb-3">
            <label for="jenis_barang" class="form-label">Jenis Barang</label>
            <input type="text" class="form-control" id="jenis_barang" name="jenis_barang" value="{{ $barangTitipan->jenis_barang }}" required>
        </div>
        
        <div class="mb-3">
            <label for="garansi_barang" class="form-label">Garansi Barang</label>
            <input type="text" class="form-control" id="garansi_barang" name="garansi_barang" value="{{ $barangTitipan->garansi_barang }}" required>
        </div>
        
        <div class="mb-3">
            <label for="berat_barang" class="form-label">Berat Barang</label>
            <input type="number" class="form-control" id="berat_barang" name="berat_barang" value="{{ $barangTitipan->berat_barang }}" required>
        </div>
        
        <button type="submit" class="btn btn-warning">Update</button>
    </form>
</div>
@endsection
