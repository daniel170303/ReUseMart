@extends('layouts.owner')

@section('title', 'Laporan Transaksi Penitip')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="fas fa-file-invoice-dollar me-2"></i>Laporan Transaksi Penitip
                        </h4>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('owner.laporan.transaksi-penitip') }}" class="mb-4">
                        <div class="row align-items-end">
                            <div class="col-md-4">
                                <label for="id_penitip" class="form-label">Pilih Penitip <span class="text-danger">*</span></label>
                                <select name="id_penitip" id="id_penitip" class="form-select" required>
                                    <option value="">-- Pilih Penitip --</option>
                                    @foreach($penitipList as $penitip)
                                        <option value="{{ $penitip->id_penitip }}" {{ $idPenitip == $penitip->id_penitip ? 'selected' : '' }}>
                                            T{{ $penitip->id_penitip }} - {{ $penitip->nama_penitip }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="bulan" class="form-label">Bulan</label>
                                <select name="bulan" id="bulan" class="form-select">
                                    @foreach($namaBulan as $key => $nama)
                                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tahun" class="form-label">Tahun</label>
                                <select name="tahun" id="tahun" class="form-select">
                                    @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Tampilkan Laporan
                                </button>
                                @if($idPenitip)
                                    <a href="{{ route('owner.laporan.transaksi-penitip.pdf', ['id_penitip' => $idPenitip, 'bulan' => $bulan, 'tahun' => $tahun]) }}" 
                                       class="btn btn-danger" target="_blank">
                                        <i class="fas fa-file-pdf me-1"></i>Download PDF
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    @if($penitipData)
                        <!-- Penitip Info -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>ID Penitip:</strong> T{{ $penitipData->id_penitip }}<br>
                                    <strong>Nama:</strong> {{ $penitipData->nama_penitip }}<br>
                                    <strong>Email:</strong> {{ $penitipData->email_penitip }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Periode:</strong> {{ $namaBulan[$bulan] }} {{ $tahun }}<br>
                                    <strong>Total Transaksi:</strong> {{ $laporanTransaksi->count() }} produk<br>
                                    <strong>Total Pendapatan:</strong> Rp{{ number_format($totalBersih, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Summary Cards -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total Produk</h6>
                                                <h3 class="mb-0">{{ $laporanTransaksi->count() }}</h3>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-box fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="card-title">Total Penjualan</h6>
                                                <h4 class="mb-0">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-money-bill-wave fa-2x"></i>
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
                                                <h6 class="card-title">Total Bonus</h6>
                                                <h4 class="mb-0">Rp{{ number_format($totalBonus, 0, ',', '.') }}</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-gift fa-2x"></i>
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
                                                <h6 class="card-title">Pendapatan Bersih</h6>
                                                <h4 class="mb-0">Rp{{ number_format($totalBersih, 0, ',', '.') }}</h4>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-coins fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Data Table -->
                        @if($laporanTransaksi->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Tanggal Laku</th>
                                            <th>Harga Jual</th>
                                            <th>Bersih (Setelah Komisi)</th>
                                            <th>Bonus Terjual Cepat</th>
                                            <th>Total Pendapatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($laporanTransaksi as $index => $transaksi)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td><strong>K{{ $transaksi->kode_produk }}</strong></td>
                                                <td>{{ $transaksi->nama_produk }}</td>
                                                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_masuk)->format('d/m/Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($transaksi->tanggal_laku)->format('d/m/Y') }}</td>
                                                <td class="text-end">Rp{{ number_format($transaksi->harga_jual, 0, ',', '.') }}</td>
                                                <td class="text-end">Rp{{ number_format($transaksi->harga_bersih, 0, ',', '.') }}</td>
                                                <td class="text-end">
                                                    @if($transaksi->bonus_terjual_cepat > 0)
                                                        <span class="badge bg-success">Rp{{ number_format($transaksi->bonus_terjual_cepat, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><strong>Rp{{ number_format($transaksi->pendapatan, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-secondary">
                                        <tr>
                                            <th colspan="5" class="text-center">TOTAL</th>
                                            <th class="text-end">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</th>
                                            <th class="text-end">Rp{{ number_format($totalPendapatan - $totalBonus, 0, ',', '.') }}</th>
                                            <th class="text-end">Rp{{ number_format($totalBonus, 0, ',', '.') }}</th>
                                            <th class="text-end"><strong>Rp{{ number_format($totalBersih, 0, ',', '.') }}</strong></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Tidak ada transaksi untuk penitip <strong>{{ $penitipData->nama_penitip }}</strong> 
                                pada bulan <strong>{{ $namaBulan[$bulan] }} {{ $tahun }}</strong>.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Silakan pilih penitip terlebih dahulu untuk melihat laporan transaksi.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto submit form when penitip is selected
    document.getElementById('id_penitip').addEventListener('change', function() {
        if (this.value) {
            this.form.submit();
        }
    });
</script>
@endpush