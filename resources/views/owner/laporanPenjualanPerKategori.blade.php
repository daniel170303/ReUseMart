@extends('layouts.owner')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-2"></i>
                            Laporan Penjualan Per Kategori Barang
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Form Filter Tahun --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form method="GET" action="{{ route('owner.laporanPenjualanPerKategori') }}"
                                    class="form-inline">
                                    <div class="form-group mr-3">
                                        <label for="tahun" class="mr-2">Tahun:</label>
                                        <select name="tahun" id="tahun" class="form-control">
                                            @for ($i = date('Y'); $i >= 2020; $i--)
                                                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search mr-1"></i>Tampilkan
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('owner.laporanPenjualanPerKategoriPDF', ['tahun' => $tahun]) }}"
                                    class="btn btn-success">
                                    <i class="fas fa-download mr-1"></i>Download PDF
                                </a>
                            </div>
                        </div>

                        

                        {{-- Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4>{{ number_format($totalTerjual) }}</h4>
                                                <p class="mb-0">Total Item Terjual</p>
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
                                                <h4>{{ number_format($totalGagalTerjual) }}</h4>
                                                <p class="mb-0">Total Item Gagal Terjual</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-times-circle fa-2x"></i>
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
                                                <h4>{{ number_format($totalBelumTerjual) }}</h4>
                                                <p class="mb-0">Total Item Belum Terjual</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-clock fa-2x"></i>
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
                                                <h4>{{ number_format($totalTerjual + $totalGagalTerjual + $totalBelumTerjual) }}
                                                </h4>
                                                <p class="mb-0">Total Keseluruhan Item</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-boxes fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Chart Section --}}
                        @if (count($laporanKategori) > 0)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chart-bar mr-2"></i>
                                                Grafik Status Barang per Kategori
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="categoryChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chart-pie mr-2"></i>
                                                Distribusi Total Barang
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="pieChart" width="400" height="200"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Tabel Laporan --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>No</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Jumlah Item Terjual</th>
                                        <th class="text-center">Jumlah Item Gagal Terjual</th>
                                        <th class="text-center">Jumlah Item Belum Terjual</th>
                                        <th class="text-center">Total Item</th>
                                        <th class="text-center">Persentase Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($laporanKategori as $index => $data)
                                        @php
                                            $totalItem =
                                                $data['terjual'] + $data['gagal_terjual'] + $data['belum_terjual'];
                                            $persentase = $totalItem > 0 ? ($data['terjual'] / $totalItem) * 100 : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $data['kategori'] }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-success badge-lg">
                                                    {{ number_format($data['terjual']) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-danger badge-lg">
                                                    {{ number_format($data['gagal_terjual']) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge badge-warning badge-lg">
                                                    {{ number_format($data['belum_terjual']) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ number_format($totalItem) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $persentase }}%"
                                                        aria-valuenow="{{ $persentase }}" aria-valuemin="0"
                                                        aria-valuemax="100">
                                                        {{ number_format($persentase, 1) }}%
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <br>Tidak ada data untuk tahun {{ $tahun }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if (count($laporanKategori) > 0)
                                    <tfoot class="thead-light">
                                        <tr>
                                            <th colspan="2" class="text-center">TOTAL</th>
                                            <th class="text-center">
                                                <span class="badge badge-success badge-lg">
                                                    {{ number_format($totalTerjual) }}
                                                </span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-danger badge-lg">
                                                    {{ number_format($totalGagalTerjual) }}
                                                </span>
                                            </th>
                                            <th class="text-center">
                                                <span class="badge badge-warning badge-lg">
                                                    {{ number_format($totalBelumTerjual) }}
                                                </span>
                                            </th>
                                            <th class="text-center">
                                                <strong>{{ number_format($totalTerjual + $totalGagalTerjual + $totalBelumTerjual) }}</strong>
                                            </th>
                                            <th class="text-center">
                                                @php
                                                    $totalKeseluruhan =
                                                        $totalTerjual + $totalGagalTerjual + $totalBelumTerjual;
                                                    $persentaseKeseluruhan =
                                                        $totalKeseluruhan > 0
                                                            ? ($totalTerjual / $totalKeseluruhan) * 100
                                                            : 0;
                                                @endphp
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-success" role="progressbar"
                                                        style="width: {{ $persentaseKeseluruhan }}%">
                                                        <strong>{{ number_format($persentaseKeseluruhan, 1) }}%</strong>
                                                    </div>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>

                        {{-- Analisis Section --}}
                        @if (count($laporanKategori) > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-chart-line mr-2"></i>
                                                Analisis Penjualan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $terbaik = collect($laporanKategori)->sortByDesc('terjual')->first();
                                                $terburuk = collect($laporanKategori)->sortBy('terjual')->first();
                                                $totalKeseluruhan =
                                                    $totalTerjual + $totalGagalTerjual + $totalBelumTerjual;
                                                $tingkatKeberhasilan =
                                                    $totalKeseluruhan > 0
                                                        ? ($totalTerjual / $totalKeseluruhan) * 100
                                                        : 0;
                                                $kategoriTerbanyak = collect($laporanKategori)
                                                    ->sortByDesc(function ($item) {
                                                        return $item['terjual'] +
                                                            $item['gagal_terjual'] +
                                                            $item['belum_terjual'];
                                                    })
                                                    ->first();
                                            @endphp

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="info-box bg-success">
                                                        <span class="info-box-icon"><i class="fas fa-trophy"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Kategori Terlaris</span>
                                                            <span
                                                                class="info-box-number">{{ $terbaik['kategori'] ?? '-' }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: 100%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                {{ number_format($terbaik['terjual'] ?? 0) }} item terjual
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="info-box bg-info">
                                                        <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Kategori Terbanyak</span>
                                                            <span
                                                                class="info-box-number">{{ $kategoriTerbanyak['kategori'] ?? '-' }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: 90%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                @php
                                                                    $totalKategoriTerbanyak =
                                                                        ($kategoriTerbanyak['terjual'] ?? 0) +
                                                                        ($kategoriTerbanyak['gagal_terjual'] ?? 0) +
                                                                        ($kategoriTerbanyak['belum_terjual'] ?? 0);
                                                                @endphp
                                                                {{ number_format($totalKategoriTerbanyak) }} total item
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="info-box bg-warning">
                                                        <span class="info-box-icon"><i
                                                                class="fas fa-exclamation-triangle"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Perlu Perhatian</span>
                                                            <span
                                                                class="info-box-number">{{ $terburuk['kategori'] ?? '-' }}</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" style="width: 70%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                {{ number_format($terburuk['terjual'] ?? 0) }} item terjual
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="info-box bg-primary">
                                                        <span class="info-box-icon"><i
                                                                class="fas fa-percentage"></i></span>
                                                        <div class="info-box-content">
                                                            <span class="info-box-text">Tingkat Keberhasilan</span>
                                                            <span
                                                                class="info-box-number">{{ number_format($tingkatKeberhasilan, 1) }}%</span>
                                                            <div class="progress">
                                                                <div class="progress-bar"
                                                                    style="width: {{ $tingkatKeberhasilan }}%"></div>
                                                            </div>
                                                            <span class="progress-description">
                                                                dari total {{ number_format($totalKeseluruhan) }} item
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @if (count($laporanKategori) > 0)
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Data untuk chart
                const categories = @json(array_column($laporanKategori, 'kategori'));
                const terjualData = @json(array_column($laporanKategori, 'terjual'));
                const gagalData = @json(array_column($laporanKategori, 'gagal_terjual'));
                const belumTerjualData = @json(array_column($laporanKategori, 'belum_terjual'));

                // Warna untuk chart
                const colors = [
                    '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                    '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
                ];

                // Bar Chart - Stacked
                const ctxBar = document.getElementById('categoryChart').getContext('2d');
                new Chart(ctxBar, {
                    type: 'bar',
                    data: {
                        labels: categories.map(cat => cat.length > 15 ? cat.substring(0, 15) + '...' : cat),
                        datasets: [{
                            label: 'Terjual',
                            data: terjualData,
                            backgroundColor: '#28a745',
                            borderColor: '#1e7e34',
                            borderWidth: 1
                        }, {
                            label: 'Gagal Terjual',
                            data: gagalData,
                            backgroundColor: '#dc3545',
                            borderColor: '#c82333',
                            borderWidth: 1
                        }, {
                            label: 'Belum Terjual',
                            data: belumTerjualData,
                            backgroundColor: '#ffc107',
                            borderColor: '#e0a800',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45
                                }
                            },
                            y: {
                                stacked: true,
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            title: {
                                display: true,
                                text: 'Status Barang per Kategori'
                            },
                            tooltip: {
                                callbacks: {
                                    afterLabel: function(context) {
                                        const datasetIndex = context.datasetIndex;
                                        const dataIndex = context.dataIndex;
                                        const total = terjualData[dataIndex] + gagalData[dataIndex] +
                                            belumTerjualData[dataIndex];
                                        const percentage = total > 0 ? ((context.parsed.y / total) * 100)
                                            .toFixed(1) : 0;
                                        return `(${percentage}% dari total)`;
                                    }
                                }
                            }
                        }
                    }
                });

                // Pie Chart - Total barang per kategori
                const ctxPie = document.getElementById('pieChart').getContext('2d');
                const totalPerKategori = categories.map((cat, index) =>
                    terjualData[index] + gagalData[index] + belumTerjualData[index]
                );
                const categoriesWithData = categories.filter((cat, index) => totalPerKategori[index] > 0);
                const totalFiltered = totalPerKategori.filter(total => total > 0);

                new Chart(ctxPie, {
                    type: 'doughnut',
                    data: {
                        labels: categoriesWithData.map(cat => cat.length > 20 ? cat.substring(0, 20) + '...' :
                            cat),
                        datasets: [{
                            data: totalFiltered,
                            backgroundColor: colors.slice(0, categoriesWithData.length),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            title: {
                                display: true,
                                text: 'Distribusi Total Barang per Kategori'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = totalFiltered.reduce((a, b) => a + b, 0);
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return context.label + ': ' + context.parsed + ' (' + percentage +
                                            '%)';
                                    },
                                    afterLabel: function(context) {
                                        const dataIndex = categoriesWithData.findIndex(cat =>
                                            cat === context.label || cat.startsWith(context.label
                                                .substring(0, 20))
                                        );
                                        if (dataIndex !== -1) {
                                            const originalIndex = categories.findIndex(cat =>
                                                cat === categoriesWithData[dataIndex]
                                            );
                                            return [
                                                `Terjual: ${terjualData[originalIndex]}`,
                                                `Gagal: ${gagalData[originalIndex]}`,
                                                `Belum: ${belumTerjualData[originalIndex]}`
                                            ];
                                        }
                                        return '';
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

    {{-- Custom CSS --}}
    <style>
        .badge-lg {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }

        .info-box {
            display: block;
            min-height: 90px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 90px;
            width: 90px;
            text-align: center;
            font-size: 45px;
            line-height: 90px;
            background: rgba(0, 0, 0, 0.2);
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 90px;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .info-box-text {
            display: block;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .info-box .progress {
            background: rgba(0, 0, 0, 0.2);
            margin: 5px -10px 5px -10px;
            height: 2px;
        }

        .info-box .progress .progress-bar {
            background: rgba(255, 255, 255, 0.4);
        }

        .progress-description {
            display: block;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .bg-success .info-box-icon,
        .bg-success .info-box-content {
            color: #fff;
        }

        .bg-warning .info-box-icon,
        .bg-warning .info-box-content {
            color: #fff;
        }

        .bg-info .info-box-icon,
        .bg-info .info-box-content {
            color: #fff;
        }

        .bg-primary .info-box-icon,
        .bg-primary .info-box-content {
            color: #fff;
        }

        .card-header .card-title {
            margin: 0;
        }

        .table th {
            border-top: none;
        }

        .progress {
            overflow: visible;
        }

        .progress-bar {
            color: #fff;
            font-weight: bold;
            line-height: 20px;
        }

        @media (max-width: 768px) {
            .form-inline .form-group {
                margin-bottom: 10px;
            }

            .info-box {
                margin-bottom: 10px;
            }

            .info-box-icon {
                width: 70px;
                height: 70px;
                font-size: 35px;
                line-height: 70px;
            }

            .info-box-content {
                margin-left: 70px;
            }

            .col-md-3 {
                margin-bottom: 15px;
            }
        }
    </style>
@endsection
