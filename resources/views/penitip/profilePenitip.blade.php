@extends('layouts.penitip')

@section('content')
    <div class="container mt-4">
        <h2>Profil Penitip</h2>
        <div class="card mb-4">
            <div class="card-body">
                <p><strong>Nama:</strong> {{ $penitip->nama_penitip }}</p>
                <p><strong>NIK:</strong> {{ $penitip->nik_penitip }}</p>
                <p><strong>Email:</strong> {{ $penitip->email_penitip }}</p>
                <p><strong>No. Telepon:</strong> {{ $penitip->nomor_telepon_penitip }}</p>
            </div>
        </div>

        <h4>Barang Titipan</h4>
        <ul class="list-group mb-4">
            @forelse ($barangTitipan as $barang)
                <li class="list-group-item">{{ $barang->nama_barang_titipan }}</li>
            @empty
                <li class="list-group-item text-muted">Belum ada barang yang dititipkan.</li>
            @endforelse
        </ul>

        <h4>Riwayat Penitipan</h4>
        <ul class="list-group">
            @forelse ($riwayatPenitipan as $riwayat)
                <li class="list-group-item">
                    {{ $riwayat->created_at->format('d M Y') }} -
                    {{ $riwayat->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}
                </li>
            @empty
                <li class="list-group-item text-muted">Belum ada riwayat penitipan.</li>
            @endforelse
        </ul>
    </div>
@endsection
