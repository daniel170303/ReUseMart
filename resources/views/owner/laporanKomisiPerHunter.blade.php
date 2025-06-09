@extends('layouts.owner')

@section('title', 'Laporan Komisi Per Hunter')

@section('content')
<div class="container-fluid">
    <h2 class="my-4">🎯 Laporan Komisi Per Hunter</h2>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('owner.laporanKomisiPerHunter') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        @foreach($namaBulan as $key => $nama)
                            <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tahun" class="form-label">Tahun</label>
                    <select name="tahun" id="tahun" class="form-select">
                        @for($i = date('Y'); $i >= 2020; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('owner.laporanKomisiPerHunterPDF', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                       class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Unduh PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-users me-2"></i>Total Hunter
                    </h5>
                    <p class="card-text fs-4">{{ $jumlahHunter }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-money-bill-wave me-2"></i>Total Komisi
                    </h5>
                    <p class="card-text fs-4">Rp{{ number_format($totalKomisiSemua, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-box me-2"></i>Total Produk
                    </h5>
                    <p class="card-text fs-4">{{ $totalProdukSemua }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line me-2"></i>Total Penjualan
                    </h5>
                    <p class="card-text fs-4">Rp{{ number_format($totalPenjualanSemua, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Laporan -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>Laporan Komisi Per Hunter - {{ $namaBulanTerpilih }} {{ $tahun }}
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="8%">ID Hunter</th>
                            <th width="25%">Nama Hunter</th>
                            <th width="12%">Total Produk</th>
                            <th width="18%">Total Penjualan</th>
                            <th width="15%">Rata-rata Harga</th>
                            <th width="17%">Total Komisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporanKomisiHunter as $index => $hunter)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $hunter->id_pegawai }}</span>
                                </td>
                                <td><strong>{{ $hunter->nama_hunter }}</strong></td>
                                <td class="text-center">{{ $hunter->total_produk }} item</td>
                                <td class="text-end">Rp{{ number_format($hunter->total_penjualan, 0, ',', '.') }}</td>
                                <td class="text-end">Rp{{ number_format($hunter->rata_rata_harga, 0, ',', '.') }}</td>
                                <td class="text-end">
                                    <strong class="text-success">Rp{{ number_format($hunter->total_komisi, 0, ',', '.') }}</strong>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>Tidak ada data komisi hunter</h5>
                                        <p>Belum ada penjualan dari hunter pada {{ $namaBulanTerpilih }} {{ $tahun }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($laporanKomisiHunter->isNotEmpty())
                        <tfoot class="table-secondary">
                            <tr>
                                <th colspan="3" class="text-center">TOTAL</th>
                                <th class="text-center">{{ $totalProdukSemua }} item</th>
                                <th class="text-end">Rp{{ number_format($totalPenjualanSemua, 0, ',', '.') }}</th>
                                <th class="text-center">-</th>
                                <th class="text-end">
                                    <strong>Rp{{ number_format($totalKomisiSemua, 0, ',', '.') }}</strong>
                                </th>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto submit form when filter changes
    document.getElementById('bulan').addEventListener('change', function() {
        this.form.submit();
    });
    
    document.getElementById('tahun').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endsection