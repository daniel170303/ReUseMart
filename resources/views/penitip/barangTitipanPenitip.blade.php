@extends('layouts.penitip')

@section('content')
    <div class="container mt-4">
        <h2>Barang Titipan Penitip</h2>
        <div class="row">
            @forelse ($barangTitipan as $barang)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if ($barang->gambar_barang)
                            <img src="{{ asset('storage/' . $barang->gambar_barang) }}" class="card-img-top"
                                alt="{{ $barang->nama_barang_titipan }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $barang->nama_barang_titipan }}</h5>
                            <p class="card-text">{{ $barang->deskripsi_barang }}</p>
                            <ul class="list-unstyled">
                                <li><strong>Jenis:</strong> {{ $barang->jenis_barang }}</li>
                                <li><strong>Harga:</strong> Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</li>
                                <li><strong>Berat:</strong> {{ $barang->berat_barang }} gram</li>
                                <li><strong>Status:</strong> {{ $barang->status_barang }}</li>
                                <li><strong>Status Garansi:</strong> {{ $barang->status_garansi }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">Belum ada barang titipan.</p>
            @endforelse
        </div>
    </div>
@endsection
