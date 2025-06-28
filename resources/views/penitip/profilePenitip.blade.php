@extends('layouts.penitip')

@section('content')
    <div class="container py-5">
        <div class="mx-auto" style="max-width: 900px;">

            {{-- PROFIL PENITIP --}}
            <div class="card shadow border-0 rounded-4 mb-5">
                <div class="card-header bg-gradient bg-primary text-white text-center py-4 rounded-top-4">
                    <h3 class="mb-0"><i class="fas fa-user-circle me-2"></i> Profile Penitip</h3>
                </div>
                <div class="card-body px-4 py-4">
                    @php
                        $profil = [
                            'Nama' => $penitip->nama_penitip,
                            'NIK' => $penitip->nik_penitip,
                            'Email' => $penitip->email_penitip,
                            'No. Telepon' => $penitip->nomor_telepon_penitip,
                        ];
                    @endphp
                    @foreach ($profil as $label => $value)
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 text-muted fw-semibold">
                                <i class="fas fa-chevron-right me-1 text-primary"></i> {{ $label }}
                            </div>
                            <div class="col-sm-8">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- SALDO & REWARD PENITIP --}}
            <div class="card shadow border-0 rounded-4 mb-5">
                <div class="card-header bg-info text-white text-center py-4 rounded-top-4">
                    <h4 class="mb-0"><i class="fas fa-wallet me-2"></i> Saldo & Reward</h4>
                </div>
                <div class="card-body px-4 py-4">
                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 text-muted fw-semibold">
                            <i class="fas fa-wallet me-1 text-primary"></i> Saldo Penitip
                        </div>
                        <div class="col-sm-8">
                            Rp {{ number_format($penitip->saldo->saldo_penitip ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 text-muted fw-semibold">
                            <i class="fas fa-gift me-1 text-primary"></i> Poin Reward
                        </div>
                        <div class="col-sm-8">
                            {{ $penitip->reward->jumlah_poin_penitip ?? 0 }} poin
                        </div>
                    </div>

                    <div class="row mb-3 align-items-center">
                        <div class="col-sm-4 text-muted fw-semibold">
                            <i class="fas fa-percentage me-1 text-primary"></i> Komisi Penitip
                        </div>
                        <div class="col-sm-8">
                            Rp {{ number_format($penitip->reward->komisi_penitip ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- BARANG TITIPAN --}}
            <div class="card shadow border-0 rounded-4 mb-5">
                <div class="card-header bg-success text-white text-center py-4 rounded-top-4">
                    <h4 class="mb-0"><i class="fas fa-box-open me-2"></i> Barang Titipan</h4>
                </div>
                <div class="card-body px-4 py-3">
                    @if ($barangTitipan->isEmpty())
                        <p class="text-center text-muted fst-italic">Belum ada barang yang dititipkan.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($barangTitipan as $barang)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-cube text-info"></i>
                                        {{ $barang->nama_barang_titipan }}
                                    </div>
                                    <span class="badge bg-light text-dark border px-3 py-1 rounded-pill shadow-sm">
                                        {{ ucfirst($barang->status_barang) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- RIWAYAT PENITIPAN --}}
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-secondary text-white text-center py-4 rounded-top-4">
                    <h4 class="mb-0"><i class="fas fa-history me-2"></i> Riwayat Penitipan</h4>
                </div>
                <div class="card-body px-4 py-3">
                    @if ($riwayatPenitipan->isEmpty())
                        <p class="text-center text-muted fst-italic">Belum ada riwayat penitipan.</p>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($riwayatPenitipan as $riwayat)
                                <li class="list-group-item">
                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                    <strong>{{ $riwayat->created_at->format('d M Y') }}</strong> —
                                    <span class="text-muted">{{ $riwayat->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection