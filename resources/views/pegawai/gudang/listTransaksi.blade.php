@extends('layouts.gudang')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-dark">Transaksi Siap Diambil & Selesai</h2>

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

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Nama Barang</th>
                <th>Status Transaksi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksi as $item)
                @if (in_array(strtolower($item->status_transaksi), ['siap diambil', 'selesai']))
                    <tr>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ ucfirst($item->status_transaksi) }}</td>
                        <td>
                            @if (strtolower($item->status_transaksi) === 'siap diambil')
                                <form action="{{ route('gudang.transaksi.konfirmasiPengambilan', $item->id_transaksi) }}" method="POST" onsubmit="return confirm('Yakin ingin konfirmasi pengambilan untuk barang ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Konfirmasi Selesai</button>
                                </form>
                            @else
                                <span class="text-muted">Sudah Selesai</span>
                            @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="3" class="text-center">Tidak ada transaksi yang siap diambil atau sudah selesai.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection