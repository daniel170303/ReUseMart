@php
    use Carbon\Carbon;
@endphp

@extends('layouts.gudang')

@section('content')
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-dark">Konfirmasi Pengembalian Barang</h2>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm border rounded-4 bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Nama Barang</th>
                    <th>Nama Pemilik</th>
                    <th>Tanggal Pengambilan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($jadwalPengambilan as $item)
                    <tr class="text-center">
                        <td>{{ $item->barangTitipan->nama_barang_titipan }}</td>
                        <td>{{ $item->penitipan->penitip->nama_penitip }}</td>
                        <td>
                            {{ Carbon::parse($item->penitipan->tanggal_pengambilan)->translatedFormat('d F Y') }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $item->penitipan->status_barang == 'sudah diambil penitip' ? 'success' : 'warning text-dark' }}">
                                {{ ucfirst($item->penitipan->status_barang) }}
                            </span>
                        </td>
                        <td>
                            @if ($item->barangTitipan->status_barang !== 'sudah diambil penitip')
                                <form action="{{ route('gudang.konfirmasiPengembalian') }}" method="POST" onsubmit="return confirm('Konfirmasi barang telah diambil oleh pemilik?')">
                                    @csrf
                                    <input type="hidden" name="id_barang" value="{{ $item->barangTitipan->id_barang_titipan }}">
                                    <input type="hidden" name="id_penitipan" value="{{ $item->penitipan->id_penitipan }}">
                                    <button type="submit" class="btn btn-sm btn-success">Konfirmasi</button>
                                </form>
                            @else
                                <span class="text-success">Sudah diambil</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Tidak ada jadwal pengambilan barang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection