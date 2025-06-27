@extends('layouts.owner')

@section('title', 'Laporan Penjualan')

@section('content')
    <div class="container">
        <h2 class="my-4">Laporan Penjualan Tahunan {{ $tahun }}</h2>

        <!-- Debug Info -->
        @if(config('app.debug'))
            <div class="alert alert-info">
                <strong>Debug Info:</strong><br>
                Data Grafik Count: {{ $dataGrafik->count() }}<br>
                Data Grafik Sum: {{ $dataGrafik->sum('penjualan') }}<br>
                <details>
                    <summary>Data Grafik Detail</summary>
                    <pre>{{ json_encode($dataGrafik->toArray(), JSON_PRETTY_PRINT) }}</pre>
                </details>
            </div>
        @endif

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
            <div class="card">
                <div class="card-header">
                    <h5>Grafik Penjualan Tahunan {{ $tahun }}</h5>
                </div>
                <div class="card-body">
                    @if($dataGrafik->sum('penjualan') > 0)
                        <div style="height: 400px;">
                            <canvas id="grafikPenjualan"></canvas>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Tidak ada data penjualan untuk tahun {{ $tahun }}
                        </div>
                    @endif
                </div>
            </div>
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
                            <td colspan="5" class="text-center">Tidak ada data</td>
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
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing sales chart...');
            
            // Data dari controller
            const dataGrafik = @json($dataGrafik);
            
            console.log('Raw sales data from controller:', dataGrafik);
            console.log('Data type:', typeof dataGrafik);
            console.log('Is array:', Array.isArray(dataGrafik));
            
            if (dataGrafik && (Array.isArray(dataGrafik) || dataGrafik.length > 0)) {
                const ctx = document.getElementById('grafikPenjualan');
                
                console.log('Canvas element:', ctx);
                
                if (ctx) {
                    const labels = dataGrafik.map(item => item.bulan || item.bulan_nama);
                    const data = dataGrafik.map(item => parseFloat(item.penjualan) || 0);
                    
                    console.log('Chart labels:', labels);
                    console.log('Chart data:', data);
                    console.log('Data sum:', data.reduce((a, b) => a + b, 0));
                    
                    // Cek apakah ada data yang tidak nol
                    const hasData = data.some(value => value > 0);
                    console.log('Has non-zero data:', hasData);
                    
                    try {
                        const chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                    label: 'Total Penjualan (Rp)',
                                    data: data,
                                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                                    borderColor: 'rgba(54, 162, 235, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    title: {
                                        display: true,
                                        text: 'Grafik Penjualan Tahunan {{ $tahun }}'
                                    },
                                    legend: {
                                        display: true,
                                        position: 'top'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value, index, values) {
                                                return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                                            }
                                        }
                                    }
                                }
                            }
                        });
                        
                        console.log('Sales chart created successfully:', chart);
                    } catch (chartError) {
                        console.error('Error creating sales chart:', chartError);
                    }
                } else {
                    console.error('Canvas element with id "grafikPenjualan" not found');
                }
            } else {
                console.log('No sales chart data available');
                const chartContainer = document.getElementById('grafikPenjualan');
                if (chartContainer && chartContainer.parentElement) {
                    chartContainer.parentElement.innerHTML = '<div class="alert alert-warning">Data grafik tidak tersedia</div>';
                }
            }
        });
    </script>
@endsection
