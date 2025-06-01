@extends('layouts.penitip')

@section('content')
    <div class="container d-flex justify-content-center align-items-start min-vh-100 py-5">
        <div class="w-100" style="max-width: 900px;">
            <!-- Profil Penitip -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-user-circle me-2"></i>Profil Penitip</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Nama</div>
                        <div class="col-sm-8">{{ $penitip->nama_penitip }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">NIK</div>
                        <div class="col-sm-8">{{ $penitip->nik_penitip }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-4 text-muted">Email</div>
                        <div class="col-sm-8">{{ $penitip->email_penitip }}</div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4 text-muted">No. Telepon</div>
                        <div class="col-sm-8">{{ $penitip->nomor_telepon_penitip }}</div>
                    </div>
                </div>
            </div>

            <!-- Rating Penitip -->
            @if (!is_null($averageRating))
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0"><i class="fas fa-star me-2"></i>Rating Penitip</h4>
                    </div>
                    <div class="card-body text-center">
                        <h1 class="display-5 fw-bold text-warning">
                            {{ $averageRating }} <i class="fas fa-star text-warning"></i>
                        </h1>
                        <p class="text-muted mb-0">Berdasarkan rating dari pembeli untuk semua barang titipan</p>
                    </div>
                </div>
            @endif

            <!-- Barang Titipan -->
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-success text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-box-open me-2"></i>Barang Titipan</h4>
                </div>
                <div class="card-body p-0">
                    @if ($barangTitipan->isEmpty())
                        <div class="p-3 text-center text-muted">Belum ada barang yang dititipkan.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($barangTitipan as $barang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $barang->nama_barang_titipan }}
                                    <span class="badge bg-info text-dark">{{ $barang->status_barang }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <!-- Riwayat Penitipan -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondary text-white text-center">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i>Riwayat Penitipan</h4>
                </div>
                <div class="card-body p-0">
                    @if ($riwayatPenitipan->isEmpty())
                        <div class="p-3 text-center text-muted">Belum ada riwayat penitipan.</div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($riwayatPenitipan as $riwayat)
                                <li class="list-group-item">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    <strong>{{ $riwayat->created_at->format('d M Y') }}</strong> —
                                    {{ $riwayat->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
