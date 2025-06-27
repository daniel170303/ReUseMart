@extends('layouts.owner')

@section('title', 'Laporan Komisi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Laporan Komisi {{ $namaBulanTerpilih }} {{ $tahun }}</h3>
                        <a href="{{ route('owner.laporanKomisiPDF', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                           class="btn btn-danger" target="_blank">
                            <i class="fas fa-file-pdf me-2"></i>Download PDF
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="bulan">Bulan:</label>
                                <select name="bulan" id="bulan" class="form-control">
                                    @foreach($namaBulan as $key => $nama)
                                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                                            {{ $nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="tahun">Tahun:</label>
                                <select name="tahun" id="tahun" class="form-control">
                                    @for($i = date('Y'); $i >= date('Y') - 5; $i--)
                                        <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label>&nbsp;</label><br>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Grafik Komisi Tahunan -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5>Grafik Komisi Tahunan {{ $tahun }}</h5>
                                </div>
                                <div class="card-body">
                                    @if($dataGrafikKomisi->sum('komisi') > 0)
                                        <canvas id="komisiChart" width="400" height="200"></canvas>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Tidak ada data komisi untuk tahun {{ $tahun }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5>Total Komisi</h5>
                                    <h3>Rp {{ number_format($totalKomisi, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5>Total Produk</h5>
                                    <h3>{{ $totalProduk }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5>Total Penjualan</h5>
                                    <h3>Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5>Rata-rata Komisi</h5>
                                    <h3>Rp {{ number_format($rataRataKomisi, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Laporan Komisi -->
                    <div class="card">
                        <div class="card-header">
                            <h5>Detail Komisi {{ $namaBulanTerpilih }} {{ $tahun }}</h5>
                        </div>
                        <div class="card-body">
                            @if($laporanKomisi->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Kode Produk</th>
                                                <th>Nama Produk</th>
                                                <th>Penitip</th>
                                                <th>Harga Jual</th>
                                                <th>Persentase</th>
                                                <th>Komisi</th>
                                                <th>Tanggal Terjual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($laporanKomisi as $item)
                                                <tr>
                                                    <td>{{ $item->kode_produk }}</td>
                                                    <td>{{ $item->nama_barang_titipan }}</td>
                                                    <td>{{ $item->nama_penitip ?? 'N/A' }}</td>
                                                    <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                                    <td>{{ $item->persentase_komisi }}</td>
                                                    <td>Rp {{ number_format($item->komisi, 0, ',', '.') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_terjual)->format('d/m/Y') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    Tidak ada data komisi untuk {{ $namaBulanTerpilih }} {{ $tahun }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari controller
    const dataGrafik = @json($dataGrafikKomisi);
    
    console.log('Data Grafik Komisi:', dataGrafik);
    
    if (dataGrafik && dataGrafik.length > 0) {
        const ctx = document.getElementById('komisiChart');
        
        if (ctx) {
            const labels = dataGrafik.map(item => item.bulan);
            const data = dataGrafik.map(item => item.komisi);
            
            console.log('Labels:', labels);
            console.log('Data:', data);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Komisi (Rp)',
                        data: data,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Grafik Komisi Tahunan {{ $tahun }}'
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
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });
        } else {
            console.error('Canvas element not found');
        }
    } else {
        console.log('No data available for chart');
    }
});
</script>
@endpush
