@extends('layouts.hunter')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">
                    <i class="fas fa-user-circle me-2"></i>
                    Profile Hunter
                </h2>
            </div>
        </div>

        @if (isset($hunter) && $hunter)
            <!-- Informasi Dasar Hunter -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-id-card me-2"></i>
                                Informasi Personal
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4"><strong>Nama:</strong></div>
                                <div class="col-sm-8">{{ $hunter->nama_pegawai ?? 'N/A' }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4"><strong>Email:</strong></div>
                                <div class="col-sm-8">{{ $hunter->email_pegawai ?? 'N/A' }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4"><strong>ID Hunter:</strong></div>
                                <div class="col-sm-8">{{ $hunter->id_pegawai ?? 'N/A' }}</div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4"><strong>Role:</strong></div>
                                <div class="col-sm-8">
                                    <span class="badge bg-success">
                                        {{ optional($hunter->rolePegawai)->nama_role ?? 'Hunter' }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4"><strong>Telepon:</strong></div>
                                <div class="col-sm-8">{{ $hunter->nomor_telepon_pegawai ?? 'Tidak ada' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistik Kinerja -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Statistik Kinerja
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $totalBarang ?? 0 }}</h4>
                                        <small class="text-muted">Total Barang</small>
                                    </div>
                                </div>
                                <div class="col-6 mb-3">
                                    <h4 class="text-success">{{ $barangTerjual ?? 0 }}</h4>
                                    <small class="text-muted">Barang Terjual</small>
                                </div>
                                <div class="col-12">
                                    <hr>
                                    <h5 class="text-warning">
                                        Rp {{ number_format($totalKomisi ?? 0, 0, ',', '.') }}
                                    </h5>
                                    <small class="text-muted">Total Komisi Earned</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Detail -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Detail Statistik
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-primary">{{ $barangDijual ?? 0 }}</h4>
                                        <small class="text-muted">Sedang Dijual</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-secondary">{{ $barangDiambil ?? 0 }}</h4>
                                        <small class="text-muted">Sudah Diambil</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h4 class="text-info">{{ $barangDonasi ?? 0 }}</h4>
                                        <small class="text-muted">Untuk Donasi</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h4 class="text-warning">
                                        Rp {{ number_format($rataRataKomisi ?? 0, 0, ',', '.') }}
                                    </h4>
                                    <small class="text-muted">Rata-rata Komisi</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daftar Barang yang Ditangani -->
            @if (isset($barangHunter) && $barangHunter->count() > 0)
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-dark text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-boxes me-2"></i>
                                    Barang yang Ditangani ({{ $barangHunter->count() }})
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th>Penitip</th>
                                                <th>Status</th>
                                                <th>Harga</th>
                                                <th>Komisi (20%)</th>
                                                <th>Tanggal Penitipan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($barangHunter as $index => $item)
                                                @if ($item->barangTitipan)
                                                    @php
                                                        // Ambil tanggal penitipan melalui relasi
                                                        $tanggalPenitipan = null;
                                                        $namaPenitip = 'N/A';

                                                        if (
                                                            $item->barangTitipan->detailPenitipan &&
                                                            $item->barangTitipan->detailPenitipan->first() &&
                                                            $item->barangTitipan->detailPenitipan->first()->penitipan
                                                        ) {
                                                            $penitipan = $item->barangTitipan->detailPenitipan->first()
                                                                ->penitipan;
                                                            $tanggalPenitipan = $penitipan->tanggal_penitipan;

                                                            if ($penitipan->penitip) {
                                                                $namaPenitip = $penitipan->penitip->nama_penitip;
                                                            }
                                                        }

                                                        $status = $item->barangTitipan->status_barang ?? 'N/A';
                                                        $badgeClass = match ($status) {
                                                            'dijual' => 'bg-primary',
                                                            'terjual' => 'bg-success',
                                                            'sudah diambil penitip' => 'bg-secondary',
                                                            'barang untuk donasi' => 'bg-info',
                                                            'sudah didonasikan' => 'bg-warning',
                                                            default => 'bg-light text-dark',
                                                        };
                                                    @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong>{{ $item->barangTitipan->nama_barang_titipan ?? 'N/A' }}</strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                {{ Str::limit($item->barangTitipan->deskripsi_barang ?? '', 50) }}
                                                            </small>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $namaPenitip }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $badgeClass }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <strong>Rp
                                                                {{ number_format($item->barangTitipan->harga_barang ?? 0, 0, ',', '.') }}</strong>
                                                        </td>
                                                        <td>
                                                            @if ($status === 'terjual')
                                                                <span class="text-success fw-bold">
                                                                    Rp
                                                                    {{ number_format(($item->barangTitipan->harga_barang ?? 0) * 0.2, 0, ',', '.') }}
                                                                </span>
                                                            @else
                                                                <span class="text-muted">
                                                                    Rp
                                                                    {{ number_format(($item->barangTitipan->harga_barang ?? 0) * 0.2, 0, ',', '.') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">
                                                                @if ($tanggalPenitipan)
                                                                    {{ $tanggalPenitipan }}
                                                                @else
                                                                    N/A
                                                                @endif
                                                            </small>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum Ada Barang yang Ditangani</h5>
                                <p class="text-muted">
                                    Anda belum menangani barang apapun. Hubungi admin untuk mendapatkan tugas hunting
                                    barang.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Data hunter tidak ditemukan.
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        // Add some interactivity if needed
        document.addEventListener('DOMContentLoaded', function() {
            // Tooltip initialization
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
