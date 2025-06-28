@extends('layouts.gudang')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-dark">Konfirmasi Pengambilan Barang</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID Transaksi</th>
                <th>Nama Barang</th>
                <th>Tanggal Pengambilan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $item)
                <tr>
                    <td>{{ $item->id_transaksi }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->tanggal_pengambilan ?? '-' }}</td>
                    <td>{{ ucfirst($item->status_transaksi) }}</td>
                    <td>
                        @if ($item->tanggal_pengambilan && strtolower($item->status_transaksi) !== 'selesai')
                            <form action="{{ route('gudang.pengambilan.konfirmasi', $item->id_transaksi) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-success btn-sm">Konfirmasi</button>
                            </form>
                        @elseif(strtolower($item->status_transaksi) === 'selesai')
                            <span class="badge bg-success">Selesai</span>
                        @else
                            <span class="text-muted">Belum dijadwalkan</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection