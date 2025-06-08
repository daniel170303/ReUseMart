@extends('layouts.owner') <!-- Sesuaikan dengan layout yang digunakan -->

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="container">
        <h2 class="my-4">Laporan Penjualan Tahunan {{ $tahun }}</h2>

        <!-- Filter Tahun -->
        <form method="GET" action="{{ route('owner.laporanPenjualan') }}" class="mb-4 d-flex gap-2">
            <select name="tahun" class="form-select w-auto">
                @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('owner.laporanPenjualanPDF', ['tahun' => $tahun]) }}" class="btn btn-danger">Unduh PDF</a>
        </form>

        <!-- Ringkasan -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Penjualan</h5>
                        <p class="card-text">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Barang</h5>
                        <p class="card-text">{{ $totalBarang }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Total Komisi</h5>
                        <p class="card-text">Rp{{ number_format($totalKomisi, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik -->
        <div class="mb-5">
            <canvas id="grafikPenjualan" height="100"></canvas>
        </div>

        <!-- Tabel Penjualan Per Bulan -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Bulan</th>
                        <th>Total Penjualan</th>
                        <th>Total Barang</th>
                        <th>Total Komisi</th>
                        <th>Rata-rata per Barang</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($laporanTahunan as $row)
                        <tr>
                            <td>{{ $row->nama_bulan }}</td>
                            <td>Rp{{ number_format($row->total_penjualan, 0, ',', '.') }}</td>
                            <td>{{ $row->total_barang }}</td>
                            <td>Rp{{ number_format($row->total_komisi, 0, ',', '.') }}</td>
                            <td>
                                @if ($row->total_barang > 0)
                                    Rp{{ number_format($row->total_penjualan / $row->total_barang, 0, ',', '.') }}
                                @else
                                    Rp0
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('grafikPenjualan').getContext('2d');
        const grafik = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dataGrafik->pluck('bulan')) !!},
                datasets: [{
                    label: 'Total Penjualan',
                    data: {!! json_encode($dataGrafik->pluck('penjualan')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
