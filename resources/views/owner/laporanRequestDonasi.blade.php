@extends('layouts.owner')

@section('title', 'Laporan Request Donasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-clipboard-list me-2"></i>Laporan Request Donasi
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('owner.laporan.request-donasi') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="status" class="form-label">Status Request</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Semua Status</option>
                                    <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending (Belum Terpenuhi)</option>
                                    <option value="diterima" {{ $status == 'diterima' ? 'selected' : '' }}>Diterima (Sudah Terpenuhi)</option>
                                    <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
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
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                                <a href="{{ route('owner.laporan.request-donasi.pdf', ['status' => $status, 'tahun' => $tahun]) }}" 
                                   class="btn btn-danger" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Download PDF
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Request</h6>
                                            <h3 class="mb-0">{{ $totalRequest }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clipboard-list fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Pending</h6>
                                            <h3 class="mb-0">{{ $requestPending }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Diterima</h6>
                                            <h3 class="mb-0">{{ $requestDiterima }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Ditolak</h6>
                                            <h3 class="mb-0">{{ $requestDitolak }}</h3>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fas fa-times-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Data Table -->
                    @if($laporanRequest->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>ID Request</th>
                                        <th>ID Organisasi</th>
                                        <th>Nama Organisasi</th>
                                        <th>Alamat</th>
                                        <th>Request Barang</th>
                                        <th>Tanggal Request</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($laporanRequest as $index => $request)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $request->id_request }}</td>
                                            <td>{{ $request->id_organisasi ? 'ORG' . $request->id_organisasi : 'N/A' }}</td>
                                            <td>
                                                <strong>{{ $request->nama_organisasi ?? 'N/A' }}</strong>
                                                @if($request->email_organisasi)
                                                    <br><small class="text-muted">{{ $request->email_organisasi }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $request->alamat_organisasi ?? 'N/A' }}</td>
                                            <td>
                                                <div class="text-wrap" style="max-width: 200px;">
                                                    {{ $request->request_barang ?? 'N/A' }}
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($request->tanggal_request)->format('d/m/Y') }}</td>
                                            <td>
                                                @if($request->status_request == 'pending')
                                                    <span class="badge bg-warning">Pending</span>
                                                @elseif($request->status_request == 'diterima')
                                                    <span class="badge bg-success">Diterima</span>
                                                @elseif($request->status_request == 'ditolak')
                                                    <span class="badge bg-danger">Ditolak</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($request->status_request) }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Tidak ada data request donasi untuk filter yang dipilih.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection