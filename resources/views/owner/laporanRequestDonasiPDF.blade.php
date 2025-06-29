<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $judulLaporan }} - {{ $tahun }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #007bff;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 0 0 10px 0;
            color: #333;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 10px;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }
        
        .info-label {
            display: table-cell;
            width: 120px;
            font-weight: bold;
            color: #333;
        }
        
        .info-value {
            display: table-cell;
            color: #666;
        }
        
        .summary-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .summary-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
            border-bottom: 1px solid #007bff;
            padding-bottom: 5px;
        }
        
        .summary-item {
            display: inline-block;
            margin-right: 30px;
            margin-bottom: 5px;
        }
        
        .summary-label {
            font-weight: bold;
            color: #333;
        }
        
        .summary-value {
            color: #007bff;
            font-weight: bold;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 10px;
        }
        
        th, td {
            border: 1px solid #333;
            padding: 8px 5px;
            text-align: left;
            vertical-align: top;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e3f2fd;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            
            .no-print {
                display: none;
            }
        }
        
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-diterima {
            background-color: #28a745;
            color: white;
        }
        
        .badge-ditolak {
            background-color: #dc3545;
            color: white;
        }
        
        .request-text {
            max-width: 250px;
            word-wrap: break-word;
            line-height: 1.3;
        }
        
        .org-info {
            line-height: 1.2;
        }
        
        .org-name {
            font-weight: bold;
            color: #333;
        }
        
        .org-contact {
            font-size: 9px;
            color: #666;
            margin-top: 2px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ $judulLaporan }}</h1>
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <p>Telp: (0274) 123-4567 | Email: info@reusemart.com</p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">LAPORAN</div>
            <div class="info-value">: Request Donasi</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div class="info-value">: 
                @if($status == 'pending')
                    Belum Terpenuhi (Pending)
                @elseif($status == 'diterima')
                    Sudah Terpenuhi (Diterima)
                @elseif($status == 'ditolak')
                    Ditolak
                @else
                    Semua Status
                @endif
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Tahun</div>
            <div class="info-value">: {{ $tahun }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Tanggal cetak</div>
            <div class="info-value">: {{ $tanggalCetak }}</div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <div class="summary-title">📊 RINGKASAN LAPORAN</div>
        <div class="summary-item">
            <span class="summary-label">Total Request:</span>
            <span class="summary-value">{{ $totalRequest }} request</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Organisasi:</span>
            <span class="summary-value">{{ $totalOrganisasi }} organisasi</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Periode:</span>
            <span class="summary-value">Tahun {{ $tahun }}</span>
        </div>
        @if($status == 'pending')
            <div class="summary-item">
                <span class="summary-label">Status:</span>
                <span class="summary-value" style="color: #ffc107;">Menunggu Pemenuhan</span>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    @if($laporanRequest->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 8%;">ID Org</th>
                    <th style="width: 18%;">Nama Organisasi</th>
                    <th style="width: 20%;">Alamat</th>
                    <th style="width: 35%;">Request Barang</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 5%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanRequest as $index => $request)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <strong>{{ $request->id_organisasi ? 'ORG' . $request->id_organisasi : 'N/A' }}</strong>
                        </td>
                        <td>
                            <div class="org-info">
                                <div class="org-name">{{ $request->nama_organisasi ?? 'N/A' }}</div>
                                @if($request->email_organisasi)
                                    <div class="org-contact">{{ $request->email_organisasi }}</div>
                                @endif
                                @if($request->nomor_telepon_organisasi)
                                    <div class="org-contact">{{ $request->nomor_telepon_organisasi }}</div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 180px; word-wrap: break-word;">
                                {{ $request->alamat_organisasi ?? 'Alamat tidak tersedia' }}
                            </div>
                        </td>
                        <td>
                            <div class="request-text">
                                {{ $request->request_barang ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="text-center">
                            {{ $request->tanggal_request ? \Carbon\Carbon::parse($request->tanggal_request)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="text-center">
                            @if($request->status_request == 'pending')
                                <span class="status-badge badge-pending">Pending</span>
                            @elseif($request->status_request == 'diterima')
                                <span class="status-badge badge-diterima">Diterima</span>
                            @elseif($request->status_request == 'ditolak')
                                <span class="status-badge badge-ditolak">Ditolak</span>
                            @else
                                <span class="status-badge">{{ ucfirst($request->status_request) }}</span>
                            @endif
                        </td>
                    </tr>
                    
                    @if(($index + 1) % 20 == 0 && $index + 1 < $laporanRequest->count())
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 4%;">No</th>
                                    <th style="width: 8%;">ID Org</th>
                                    <th style="width: 18%;">Nama Organisasi</th>
                                    <th style="width: 20%;">Alamat</th>
                                    <th style="width: 35%;">Request Barang</th>
                                    <th style="width: 10%;">Tanggal</th>
                                    <th style="width: 5%;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="7" class="text-center" style="padding: 12px; font-size: 11px;">
                        <strong>TOTAL: {{ $totalRequest }} REQUEST DONASI 
                        @if($status != 'all')
                            ({{ strtoupper($status) }})
                        @endif
                        PADA TAHUN {{ $tahun }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3 style="color: #666; margin-bottom: 10px;">📋 Tidak Ada Data</h3>
            <p>Belum ada data request donasi untuk filter yang dipilih.</p>
            <p style="font-size: 9px; margin-top: 15px;">
                Laporan ini akan menampilkan data ketika sudah ada request donasi yang tercatat dalam sistem.
            </p>
        </div>
    @endif

    <!-- Additional Information -->
    @if($laporanRequest->count() > 0)
        <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #007bff;">
                📝 CATATAN LAPORAN:
            </div>
            <div style="font-size: 10px; line-height: 1.4; color: #666;">
                <p style="margin: 5px 0;">• Laporan ini mencakup semua request donasi 
                @if($status == 'pending')
                    yang masih <strong>belum terpenuhi (pending)</strong>
                @elseif($status == 'diterima')
                    yang sudah <strong>diterima dan terpenuhi</strong>
                @elseif($status == 'ditolak')
                    yang <strong>ditolak</strong>
                @else
                    dengan semua status
                @endif
                pada tahun {{ $tahun }}</p>
                <p style="margin: 5px 0;">• Data diurutkan berdasarkan tanggal request terbaru</p>
                <p style="margin: 5px 0;">• ID Organisasi dengan awalan "ORG" menunjukkan organisasi yang terdaftar</p>
                <p style="margin: 5px 0;">• Request barang berisi detail kebutuhan yang diminta organisasi</p>
                @if($status == 'pending')
                    <p style="margin: 5px 0; color: #ffc107; font-weight: bold;">• Request dengan status pending memerlukan tindak lanjut untuk dipenuhi</p>
                @endif
            </div>
        </div>

        <!-- Statistics Section -->
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border: 1px solid #007bff; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #007bff;">
                📈 STATISTIK REQUEST {{ $tahun }}:
            </div>
            <div style="display: table; width: 100%; font-size: 10px;">
                <div style="display: table-row;">
                    <div style="display: table-cell; width: 50%; padding-right: 15px;">
                        <p style="margin: 3px 0;"><strong>Total Request:</strong> {{ $totalRequest }} request</p>
                        <p style="margin: 3px 0;"><strong>Rata-rata per Bulan:</strong> {{ round($totalRequest / 12, 1) }} request</p>
                        <p style="margin: 3px 0;"><strong>Organisasi Aktif:</strong> {{ $totalOrganisasi }} organisasi</p>
                    </div>
                    <div style="display: table-cell; width: 50%; padding-left: 15px; border-left: 1px solid #007bff;">
                        @php
                            $bulanTerbanyak = $laporanRequest->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tanggal_request)->format('F');
                            })->sortByDesc(function($items) {
                                return $items->count();
                            })->first();
                            
                            $bulanTerbanyakNama = $laporanRequest->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tanggal_request)->format('F');
                            })->sortByDesc(function($items) {
                                return $items->count();
                            })->keys()->first();
                            
                            $statusCounts = $laporanRequest->groupBy('status_request');
                        @endphp
                        
                        <p style="margin: 3px 0;"><strong>Bulan Terbanyak:</strong> {{ $bulanTerbanyakNama ?? 'N/A' }}</p>
                        <p style="margin: 3px 0;"><strong>Jumlah di Bulan Tersebut:</strong> {{ $bulanTerbanyak ? $bulanTerbanyak->count() : 0 }} request</p>
                        <p style="margin: 3px 0;"><strong>Status Dominan:</strong> 
                            @if($status == 'pending')
                                <span class="status-badge badge-pending">Belum Terpenuhi</span>
                            @elseif($status == 'diterima')
                                <span class="status-badge badge-diterima">Sudah Terpenuhi</span>
                            @elseif($status == 'ditolak')
                                <span class="status-badge badge-ditolak">Ditolak</span>
                            @else
                                <span class="status-badge">Mixed</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Priority Section for Pending Requests -->
        @if($status == 'pending' && $laporanRequest->count() > 0)
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #856404;">
                    ⚠️ PRIORITAS TINDAK LANJUT:
                </div>
                <div style="font-size: 10px; line-height: 1.4; color: #856404;">
                    @php
                        $requestLama = $laporanRequest->filter(function($item) {
                            return \Carbon\Carbon::parse($item->tanggal_request)->diffInDays(now()) > 30;
                        });
                        
                        $requestBaru = $laporanRequest->filter(function($item) {
                            return \Carbon\Carbon::parse($item->tanggal_request)->diffInDays(now()) <= 7;
                        });
                    @endphp
                    
                    <p style="margin: 3px 0;"><strong>Request Lama (>30 hari):</strong> {{ $requestLama->count() }} request - <em>Perlu prioritas tinggi</em></p>
                    <p style="margin: 3px 0;"><strong>Request Baru (≤7 hari):</strong> {{ $requestBaru->count() }} request - <em>Masih dalam batas wajar</em></p>
                    <p style="margin: 3px 0;"><strong>Rekomendasi:</strong> 
                        @if($requestLama->count() > 0)
                            Segera tindak lanjuti request yang sudah lama menunggu
                        @else
                            Pertahankan responsivitas dalam menangani request baru
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <!-- Success Section for Accepted Requests -->
        @if($status == 'diterima' && $laporanRequest->count() > 0)
            <div style="margin-top: 20px; padding: 15px; background: #d4edda; border: 1px solid #28a745; border-radius: 5px;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #155724;">
                    ✅ PENCAPAIAN DONASI:
                </div>
                <div style="font-size: 10px; line-height: 1.4; color: #155724;">
                    <p style="margin: 3px 0;"><strong>Total Request Terpenuhi:</strong> {{ $totalRequest }} request</p>
                    <p style="margin: 3px 0;"><strong>Organisasi Terbantu:</strong> {{ $totalOrganisasi }} organisasi</p>
                    <p style="margin: 3px 0;"><strong>Dampak Sosial:</strong> Berkontribusi positif terhadap {{ $totalOrganisasi }} lembaga sosial</p>
                    <p style="margin: 3px 0;"><strong>Status:</strong> <span class="status-badge badge-diterima">Program Donasi Berhasil</span></p>
                </div>
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>ReUse Mart - {{ $judulLaporan }} {{ $tahun }}</strong></p>
        <p>Dicetak pada: {{ $tanggalCetak }} | Halaman: <span class="page-number"></span></p>
        <p style="font-size: 9px; font-style: italic;">
            Dokumen ini dibuat secara otomatis oleh sistem ReUse Mart
        </p>
        @if($status == 'pending')
            <p style="font-size: 9px; color: #ffc107; font-weight: bold;">
                * Request dengan status pending memerlukan tindak lanjut segera
            </p>
        @endif
    </div>
</body>
</html>