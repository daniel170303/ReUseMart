@extends('layouts.owner')

@section('title', 'Laporan Donasi Barang')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-hands-helping me-2"></i>Laporan Donasi Barang
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('owner.laporan.donasi') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select name="tahun" id="tahun" class="form-select">
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('owner.laporan.donasi.pdf', ['tahun' => $tahun]) }}" 
                                   class="btn btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Donasi</h6>
                                            <h3 class="mb-0">{{ $totalDonasi }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-gift fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Organisasi</h6>
                                            <h3 class="mb-0">{{ $totalOrganisasi }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-building fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    @if($laporanDonasi->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th>ID Penitip</th>
                                        <th>Nama Penitip</th>
                                        <th>Tanggal Donasi</th>
                                        <th>Organisasi</th>
                                        <th>Nama Penerima</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laporanDonasi as $index => $donasi)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $donasi->kode_produk ?? 'N/A' }}</td>
                                            <td>{{ $donasi->nama_produk ?? 'N/A' }}</td>
                                            <td>{{ $donasi->id_penitip ?? 'N/A' }}</td>
                                            <td>{{ $donasi->nama_penitip ?? 'N/A' }}</td>
                                            <td>{{ \Carbon\Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y') }}</td>
                                            <td>{{ $donasi->nama_organisasi ?? 'N/A' }}</td>
                                            <td>{{ $donasi->nama_penerima ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada data donasi untuk tahun {{ $tahun }}.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection