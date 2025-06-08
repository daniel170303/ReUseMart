@extends('layouts.owner')

@section('title', 'Laporan Komisi')

@section('content')
    <div class="container-fluid">
        <h2 class="my-4">💰 Laporan Komisi Bulan {{ $namaBulanTerpilih }} {{ $tahun }}</h2>

        <!-- Filter Bulan & Tahun -->
        <form method="GET" action="{{ route('owner.laporanKomisi') }}" class="mb-4 d-flex gap-2">
            <select name="bulan" class="form-select w-auto">
                @foreach ($namaBulan as $key => $nama)
                    <option value="{{ $key }}" {{ $key == $bulan ? 'selected' : '' }}>{{ $nama }}</option>
                @endforeach
            </select>
            <select name="tahun" class="form-select w-auto">
                @for ($y = date('Y') - 5; $y <= date('Y'); $y++)
                    <option value="{{ $y }}" {{ $y == $tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
            <a href="{{ route('owner.laporanKomisiPDF', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Unduh PDF
            </a>
        </form>

        <!-- Ringkasan Komisi -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-coins me-2"></i>Total Komisi
                        </h5>
                        <p class="card-text fs-4">Rp{{ number_format($totalKomisi, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-box me-2"></i>Total Produk
                        </h5>
                        <p class="card-text fs-4">{{ $totalProduk }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-chart-line me-2"></i>Total Penjualan
                        </h5>
                        <p class="card-text fs-4">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-secondary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-calculator me-2"></i>Rata-rata Komisi
                        </h5>
                        <p class="card-text fs-4">Rp{{ number_format($rataRataKomisi, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grafik Komisi Tahunan -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Grafik Komisi Tahunan {{ $tahun }}
                </h5>
            </div>
            <div class="card-body">
                <canvas id="grafikKomisi" height="100"></canvas>
            </div>
        </div>

        <!-- Analisis Komisi -->
        @if ($laporanKomisi->isNotEmpty())
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top 5 Produk Komisi Tertinggi
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Top 5 Produk Komisi Tertinggi -->
                            @foreach ($laporanKomisi->take(5) as $index => $produk)
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2 p-2 {{ $index == 0 ? 'bg-warning bg-opacity-25' : ($index == 1 ? 'bg-info bg-opacity-25' : ($index == 2 ? 'bg-secondary bg-opacity-25' : '')) }}">
                                    <div>
                                        <strong>#{{ $index + 1 }}</strong>
                                        <span class="ms-2">{{ Str::limit($produk->nama_barang_titipan, 25) }}</span>
                                        <br>
                                        <small class="text-muted">ID: {{ $produk->id_barang }} |
                                            {{ $produk->persentase_komisi }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong
                                            class="text-success">Rp{{ number_format($produk->komisi, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Analisis Komisi
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $komisi20 = $laporanKomisi->where('persentase_komisi', '20%');
                                $komisi30 = $laporanKomisi->where('persentase_komisi', '30%');
                                $totalKomisi20 = $komisi20->sum('komisi');
                                $totalKomisi30 = $komisi30->sum('komisi');
                            @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Komisi 20% ({{ $komisi20->count() }} produk)</span>
                                    <strong>Rp{{ number_format($totalKomisi20, 0, ',', '.') }}</strong>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-primary"
                                        style="width: {{ $totalKomisi > 0 ? ($totalKomisi20 / $totalKomisi) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Komisi 30% ({{ $komisi30->count() }} produk)</span>
                                    <strong>Rp{{ number_format($totalKomisi30, 0, ',', '.') }}</strong>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-success"
                                        style="width: {{ $totalKomisi > 0 ? ($totalKomisi30 / $totalKomisi) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="text-center">
                                <small class="text-muted">
                                    Persentase Komisi 30%:
                                    {{ $totalKomisi > 0 ? round(($totalKomisi30 / $totalKomisi) * 100, 1) : 0 }}%<br>
                                    Persentase Komisi 20%:
                                    {{ $totalKomisi > 0 ? round(($totalKomisi20 / $totalKomisi) * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabel Detail Komisi -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Detail Komisi per Produk
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tabelKomisi">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">ID Barang</th>
                                <th width="30%">Nama Produk</th>
                                <th width="15%">Penitip</th>
                                <th width="10%">Harga Jual</th>
                                <th width="8%">% Komisi</th>
                                <th width="12%">Komisi</th>
                                <th width="10%">Tanggal Terjual</th>
                            </tr>
                        </thead>
                        <!-- Bagian tabel detail komisi -->
                        <tbody>
                            @forelse ($laporanKomisi as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $row->id_barang }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $row->nama_barang_titipan }}</strong>
                                        @if ($row->deskripsi_barang)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($row->deskripsi_barang, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $row->nama_penitip ?? 'Tidak diketahui' }}</td>
                                    <td class="text-end">Rp{{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $row->persentase_komisi == '30%' ? 'bg-success' : 'bg-primary' }}">
                                            {{ $row->persentase_komisi }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <strong
                                            class="text-success">Rp{{ number_format($row->komisi, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <small>{{ \Carbon\Carbon::parse($row->tanggal_terjual)->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>Tidak ada data komisi</h5>
                                            <p>Belum ada transaksi yang menghasilkan komisi pada bulan
                                                {{ $namaBulanTerpilih }} {{ $tahun }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($laporanKomisi->isNotEmpty())
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="4" class="text-center">TOTAL</th>
                                    <th class="text-end">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                                    <th class="text-center">Mix</th>
                                    <th class="text-end">Rp{{ number_format($totalKomisi, 0, ',', '.') }}</th>
                                    <th class="text-center">-</th>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Grafik Komisi Tahunan
        const ctx = document.getElementById('grafikKomisi').getContext('2d');
        const grafikKomisi = new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dataGrafikKomisi->pluck('bulan')) !!},
                datasets: [{
                    label: 'Total Komisi',
                    data: {!! json_encode($dataGrafikKomisi->pluck('komisi')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.2)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(40, 167, 69, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Trend Komisi Sepanjang Tahun {{ $tahun }}',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 30
                        }
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return 'Komisi: Rp' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                            },
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)',
                            drawBorder: false
                        },
                        title: {
                            display: true,
                            text: 'Total Komisi (Rupiah)',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        title: {
                            display: true,
                            text: 'Bulan',
                            font: {
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    point: {
                        hoverRadius: 10
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // DataTable untuk tabel komisi (opsional)
        $(document).ready(function() {
            if ($.fn.DataTable) {
                $('#tabelKomisi').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                    },
                    "pageLength": 25,
                    "order": [
                        [6, "desc"]
                    ], // Sort by komisi column (descending)
                    "columnDefs": [{
                            "orderable": false,
                            "targets": 0
                        }, // No column tidak bisa di-sort
                        {
                            "className": "text-center",
                            "targets": [0, 1, 5, 7]
                        },
                        {
                            "className": "text-end",
                            "targets": [4, 6]
                        }
                    ],
                    "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                    "responsive": true,
                    "scrollX": true
                });
            }
        });

        // Print function (opsional)
        function printReport() {
            window.print();
        }

        // Export to Excel function (opsional)
        function exportToExcel() {
            // Implementasi export ke Excel jika diperlukan
            alert('Fitur export Excel akan segera tersedia');
        }

        // Refresh data function
        function refreshData() {
            location.reload();
        }

        // Format currency untuk display
        function formatCurrency(amount) {
            return 'Rp' + new Intl.NumberFormat('id-ID').format(amount);
        }

        // Highlight current month in chart
        document.addEventListener('DOMContentLoaded', function() {
            const currentMonth = {{ $bulan }};
            const chartLabels = {!! json_encode($dataGrafikKomisi->pluck('bulan')) !!};

            // Add visual indicator for current selected month
            const monthIndex = chartLabels.findIndex(label => {
                const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                ];
                return monthNames[currentMonth - 1] === label;
            });

            if (monthIndex !== -1) {
                // Highlight the selected month point
                grafikKomisi.data.datasets[0].pointBackgroundColor = chartLabels.map((label, index) =>
                    index === monthIndex ? 'rgba(255, 193, 7, 1)' : 'rgba(40, 167, 69, 1)'
                );
                grafikKomisi.data.datasets[0].pointRadius = chartLabels.map((label, index) =>
                    index === monthIndex ? 8 : 6
                );
                grafikKomisi.update();
            }
        });

        // Add loading state for filter form
        document.querySelector('form').addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Loading...';
            submitBtn.disabled = true;

            // Re-enable after 3 seconds as fallback
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });

        // Add loading state for PDF download
        document.querySelector('a[href*="laporanKomisiPDF"]').addEventListener('click', function(e) {
            const btn = this;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating PDF...';
            btn.classList.add('disabled');

            // Re-enable after 5 seconds
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.classList.remove('disabled');
            }, 5000);
        });

        // Tooltip untuk informasi tambahan
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Console log untuk debugging (hapus di production)
        console.log('Laporan Komisi Data:', {
            totalKomisi: {{ $totalKomisi }},
            totalProduk: {{ $totalProduk }},
            totalPenjualan: {{ $totalPenjualan }},
            bulan: {{ $bulan }},
            tahun: {{ $tahun }}
        });
    </script>

    <!-- DataTables CSS & JS (opsional, jika ingin menggunakan DataTables) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
@endsection
