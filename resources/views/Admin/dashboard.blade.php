<!-- resources/views/admin/dashboard.blade.php -->
@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span class="text-muted">Selamat Datang di Panel Admin ReuseMart</span>
    </div>

    <!-- Statistik Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 h-100">
                <div class="card-body p-0">
                    <div class="stat-card bg-one">
                        <div>
                            <div class="stat-card-number">{{ $data['jumlahBarang'] }}</div>
                            <div class="stat-card-title">Barang Titipan</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-box"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 h-100">
                <div class="card-body p-0">
                    <div class="stat-card bg-two">
                        <div>
                            <div class="stat-card-number">{{ $data['jumlahPenitip'] }}</div>
                            <div class="stat-card-title">Penitip</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 h-100">
                <div class="card-body p-0">
                    <div class="stat-card bg-three">
                        <div>
                            <div class="stat-card-number">{{ $data['jumlahPembeli'] }}</div>
                            <div class="stat-card-title">Pembeli</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 h-100">
                <div class="card-body p-0">
                    <div class="stat-card bg-four">
                        <div>
                            <div class="stat-card-number">{{ $data['jumlahOrganisasi'] }}</div>
                            <div class="stat-card-title">Organisasi</div>
                        </div>
                        <div class="stat-card-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Grafik Pertumbuhan Data -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line"></i> Pertumbuhan Data (6 Bulan Terakhir)
                </div>
                <div class="card-body">
                    <canvas id="growthChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Grafik Jenis Barang -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie"></i> Distribusi Jenis Barang
                </div>
                <div class="card-body">
                    <canvas id="itemTypeChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Barang Terbaru -->
    <div class="card">
        <div class="card-header">
            <div>
                <i class="fas fa-box"></i> Barang Titipan Terbaru
            </div>
            <a href="{{ route('admin.barang.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nama Barang</th>
                            <th>Jenis Barang</th>
                            <th>Harga</th>
                            <th>Berat (gr)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangTerbaru as $barang)
                        <tr>
                            <td>{{ $barang->id_barang }}</td>
                            <td>{{ $barang->nama_barang_titipan }}</td>
                            <td>{{ $barang->jenis_barang }}</td>
                            <td>Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                            <td>{{ $barang->berat_barang }}</td>
                            <td>
                                <a href="{{ route('admin.barang.show', $barang->id_barang) }}" class="btn btn-sm btn-info btn-action" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.barang.edit', $barang->id_barang) }}" class="btn btn-sm btn-warning btn-action" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data barang titipan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Data untuk grafik pertumbuhan
    const growthData = @json($pertumbuhanData);
    
    // Data untuk grafik jenis barang
    const itemTypeData = {
        labels: {!! $jenisBarang->pluck('jenis_barang') !!},
        datasets: [{
            data: {!! $jenisBarang->pluck('total') !!},
            backgroundColor: [
                'rgba(46, 125, 50, 0.7)',
                'rgba(21, 101, 192, 0.7)',
                'rgba(106, 27, 154, 0.7)',
                'rgba(198, 40, 40, 0.7)',
                'rgba(239, 108, 0, 0.7)',
                'rgba(0, 150, 136, 0.7)'
            ],
            borderColor: [
                'rgba(46, 125, 50, 1)',
                'rgba(21, 101, 192, 1)',
                'rgba(106, 27, 154, 1)',
                'rgba(198, 40, 40, 1)',
                'rgba(239, 108, 0, 1)',
                'rgba(0, 150, 136, 1)'
            ],
            borderWidth: 1
        }]
    };
    
    // Membuat grafik pertumbuhan data
    const growthChart = new Chart(
        document.getElementById('growthChart'),
        {
            type: 'line',
            data: growthData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        }
    );
    
    // Membuat grafik jenis barang
    const itemTypeChart = new Chart(
        document.getElementById('itemTypeChart'),
        {
            type: 'pie',
            data: itemTypeData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: false
                    }
                }
            }
        }
    );
</script>
@endpush