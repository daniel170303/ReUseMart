@extends('layouts.owner')

@section('title', 'Laporan Stok Gudang')

@section('content')
    <div class="container-fluid">
        <h2 class="my-4">📦 Laporan Stok Gudang - {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</h2>

        <!-- Action Buttons -->
        <div class="mb-4 d-flex gap-2">
            <button onclick="refreshData()" class="btn btn-primary">
                <i class="fas fa-sync-alt me-1"></i> Refresh Data
            </button>
            <a href="{{ route('owner.laporanStokGudangPDF') }}" class="btn btn-danger">
                <i class="fas fa-file-pdf me-1"></i> Unduh PDF
            </a>
        </div>

        <!-- Ringkasan Stok -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-boxes me-2"></i>Total Stok
                        </h5>
                        <p class="card-text fs-4">{{ $totalStok }} item</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-money-bill-wave me-2"></i>Total Nilai Stok
                        </h5>
                        <p class="card-text fs-4">Rp{{ number_format($totalNilaiStok, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-clock me-2"></i>Stok Perpanjangan
                        </h5>
                        <p class="card-text fs-4">{{ $stokPerpanjangan }} item</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-box me-2"></i>Stok Normal
                        </h5>
                        <p class="card-text fs-4">{{ $stokNormal }} item</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analisis Stok -->
        @if ($stokGudang->isNotEmpty())
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-pie me-2"></i>Analisis Stok per Status
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                $persentasePerpanjangan = $totalStok > 0 ? ($stokPerpanjangan / $totalStok) * 100 : 0;
                                $persentaseNormal = $totalStok > 0 ? ($stokNormal / $totalStok) * 100 : 0;
                            @endphp

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Stok Perpanjangan ({{ $stokPerpanjangan }} item)</span>
                                    <strong>{{ round($persentasePerpanjangan, 1) }}%</strong>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-warning" style="width: {{ $persentasePerpanjangan }}%"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>Stok Normal ({{ $stokNormal }} item)</span>
                                    <strong>{{ round($persentaseNormal, 1) }}%</strong>
                                </div>
                                <div class="progress mt-1">
                                    <div class="progress-bar bg-info" style="width: {{ $persentaseNormal }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>Top 5 Stok Termahal
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach ($stokGudang->sortByDesc('harga')->take(5) as $index => $item)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 {{ $index == 0 ? 'bg-warning bg-opacity-25' : ($index == 1 ? 'bg-info bg-opacity-25' : ($index == 2 ? 'bg-secondary bg-opacity-25' : '')) }}">
                                    <div>
                                        <strong>#{{ $index + 1 }}</strong>
                                        <span class="ms-2">{{ Str::limit($item->nama_produk, 25) }}</span>
                                        <br>
                                        <small class="text-muted">ID: {{ $item->kode_produk }} | {{ $item->perpanjangan }}</small>
                                    </div>
                                    <div class="text-end">
                                        <strong class="text-success">Rp{{ number_format($item->harga, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Tabel Detail Stok -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>Detail Stok Gudang
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="tabelStokGudang">
                        <thead class="table-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode Produk</th>
                                <th width="25%">Nama Produk</th>
                                <th width="8%">ID Penitip</th>
                                <th width="20%">Nama Penitip</th>
                                <th width="12%">Tanggal Masuk</th>
                                <th width="10%">Perpanjangan</th>
                                <!-- <th width="8%">ID Hunter</th> -->
                                <!-- <th width="12%">Nama Hunter</th> -->
                                <th width="15%">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($stokGudang as $index => $row)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $row->kode_produk }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $row->nama_produk }}</strong>
                                    </td>
                                    <td class="text-center">{{ $row->id_penitip ?? '-' }}</td>
                                    <td>{{ $row->nama_penitip ?? 'Tidak diketahui' }}</td>
                                    <td class="text-center">
                                        <small>{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $row->perpanjangan == 'Ya' ? 'bg-warning' : 'bg-info' }}">
                                            {{ $row->perpanjangan }}
                                        </span>
                                    </td>
                                    <!-- Hapus kolom hunter -->
                                    <!-- <td class="text-center">{{ $row->id_hunter ?? '-' }}</td> -->
                                    <!-- <td>{{ $row->nama_hunter ?? '-' }}</td> -->
                                    <td class="text-end">
                                        <strong class="text-success">Rp{{ number_format($row->harga, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4"> <!-- Update colspan dari 10 ke 7 -->
                                        <div class="text-muted">
                                            <i class="fas fa-inbox fa-3x mb-3"></i>
                                            <h5>Tidak ada stok gudang</h5>
                                            <p>Semua barang sudah terjual atau tidak ada barang di gudang</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if ($stokGudang->isNotEmpty())
                            <tfoot class="table-secondary">
                                <tr>
                                    <th colspan="5" class="text-center">TOTAL</th> <!-- Update colspan dari 7 ke 5 -->
                                    <th class="text-center">{{ $totalStok }} item</th>
                                    <th class="text-center">-</th>
                                    <th class="text-end">Rp{{ number_format($totalNilaiStok, 0, ',', '.') }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <!-- Informasi Tambahan -->
        @if ($stokGudang->isNotEmpty())
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i>Informasi Stok Gudang
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><i class="fas fa-calendar-alt me-2"></i>Periode Stok:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-dot-circle me-2 text-success"></i>Data stok per tanggal: <strong>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</strong></li>
                                        <li><i class="fas fa-dot-circle me-2 text-info"></i>Waktu update terakhir: <strong>{{ \Carbon\Carbon::now()->translatedFormat('H:i') }} WIB</strong></li>
                                        <li><i class="fas fa-dot-circle me-2 text-warning"></i>Status: <strong>Real-time</strong></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Keterangan:</h6>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-dot-circle me-2 text-primary"></i>Stok yang ditampilkan adalah barang yang <strong>belum terjual</strong></li>
                                        <li><i class="fas fa-dot-circle me-2 text-success"></i>Status "Ya" = Penitipan sudah diperpanjang</li>
                                        <li><i class="fas fa-dot-circle me-2 text-info"></i>Status "Tidak" = Penitipan belum diperpanjang</li>
                                        <li><i class="fas fa-dot-circle me-2 text-secondary"></i>Hunter "-" = Barang dititipkan langsung</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
@endsection

@section('scripts')
    <script>
        // Function untuk refresh data
        function refreshData() {
            // Show loading
            const refreshBtn = document.querySelector('button[onclick="refreshData()"]');
            const originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memuat...';
            refreshBtn.disabled = true;

            // Reload halaman setelah delay singkat
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }

        // Auto refresh setiap 5 menit
        setInterval(() => {
            console.log('Auto refresh stok gudang...');
            // Bisa ditambahkan AJAX call untuk update data tanpa reload
        }, 300000); // 5 menit

        // Tooltip untuk informasi tambahan
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips jika menggunakan Bootstrap
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Highlight baris dengan nilai tinggi
            const rows = document.querySelectorAll('#tabelStokGudang tbody tr');
            rows.forEach(row => {
                const hargaCell = row.querySelector('td:last-child');
                if (hargaCell) {
                    const hargaText = hargaCell.textContent.replace(/[^\d]/g, '');
                    const harga = parseInt(hargaText);
                    
                    if (harga > 1000000) { // Lebih dari 1 juta
                        row.classList.add('table-warning');
                        hargaCell.innerHTML += ' <i class="fas fa-star text-warning ms-1" title="Stok bernilai tinggi"></i>';
                    }
                }
            });
        });

        // Function untuk export data (bisa dikembangkan lebih lanjut)
        function exportToExcel() {
            // Implementasi export ke Excel bisa ditambahkan di sini
            alert('Fitur export Excel akan segera tersedia!');
        }

        // Function untuk filter data
        function filterStok(status) {
            const rows = document.querySelectorAll('#tabelStokGudang tbody tr');
            
            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    const statusCell = row.querySelector('td:nth-child(7)');
                    if (statusCell) {
                        const statusText = statusCell.textContent.trim();
                        if (statusText.toLowerCase().includes(status.toLowerCase())) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                }
            });
        }

        // Search function
        function searchStok() {
            const searchInput = document.getElementById('searchStok');
            if (searchInput) {
                const filter = searchInput.value.toLowerCase();
                const rows = document.querySelectorAll('#tabelStokGudang tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        }
    </script>

    <!-- Optional: Tambahan CSS untuk styling khusus -->
    <style>
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1) !important;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .progress {
            height: 8px;
        }
        
        .alert-heading {
            color: inherit;
        }
        
        /* Custom styling untuk highlight */
        .table-warning {
            --bs-table-accent-bg: rgba(255, 193, 7, 0.1);
        }
        
        /* Loading animation */
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fa-spin {
            animation: spin 1s linear infinite;
        }
    </style>
@endsection