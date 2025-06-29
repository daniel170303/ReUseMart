<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Donasi Barang - {{ $tahun }}</title>
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
            border-bottom: 3px solid #28a745;
            padding-bottom: 20px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
            color: #28a745;
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
            color: #28a745;
            border-bottom: 1px solid #28a745;
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
            color: #28a745;
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
            vertical-align: middle;
        }
        
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:hover {
            background-color: #e8f5e8;
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
        
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Laporan Donasi Barang</h1>
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <p>Telp: (0274) 123-4567 | Email: info@reusemart.com</p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">LAPORAN</div>
            <div class="info-value">: Donasi Barang</div>
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
            <span class="summary-label">Total Donasi:</span>
            <span class="summary-value">{{ $totalDonasi }} item</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Organisasi Penerima:</span>
            <span class="summary-value">{{ $totalOrganisasi }} organisasi</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Periode:</span>
            <span class="summary-value">Januari - Desember {{ $tahun }}</span>
        </div>
    </div>

    <!-- Data Table -->
    @if($laporanDonasi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Kode Produk</th>
                    <th style="width: 20%;">Nama Produk</th>
                    <th style="width: 8%;">ID Penitip</th>
                    <th style="width: 15%;">Nama Penitip</th>
                    <th style="width: 12%;">Tanggal Donasi</th>
                    <th style="width: 15%;">Organisasi</th>
                    <th style="width: 13%;">Nama Penerima</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanDonasi as $index => $donasi)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <strong>{{ $donasi->kode_produk ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            <strong>{{ $donasi->nama_produk ?? 'N/A' }}</strong>
                            @if($donasi->nama_request_barang)
                                <br><small style="color: #666;">Request: {{ $donasi->nama_request_barang }}</small>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ $donasi->id_penitip ? 'T' . $donasi->id_penitip : 'N/A' }}
                        </td>
                        <td>{{ $donasi->nama_penitip ?? 'N/A' }}</td>
                        <td class="text-center">
                            {{ $donasi->tanggal_donasi ? \Carbon\Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td>
                            <strong>{{ $donasi->nama_organisasi ?? 'N/A' }}</strong>
                        </td>
                        <td>{{ $donasi->nama_penerima ?? 'N/A' }}</td>
                    </tr>
                    
                    @if(($index + 1) % 25 == 0 && $index + 1 < $laporanDonasi->count())
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 12%;">Kode Produk</th>
                                    <th style="width: 20%;">Nama Produk</th>
                                    <th style="width: 8%;">ID Penitip</th>
                                    <th style="width: 15%;">Nama Penitip</th>
                                    <th style="width: 12%;">Tanggal Donasi</th>
                                    <th style="width: 15%;">Organisasi</th>
                                    <th style="width: 13%;">Nama Penerima</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="8" class="text-center" style="padding: 12px; font-size: 11px;">
                        <strong>TOTAL: {{ $totalDonasi }} ITEM DONASI PADA TAHUN {{ $tahun }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3 style="color: #666; margin-bottom: 10px;">📋 Tidak Ada Data</h3>
            <p>Belum ada data donasi barang untuk tahun {{ $tahun }}.</p>
            <p style="font-size: 9px; margin-top: 15px;">
                Laporan ini akan menampilkan data ketika sudah ada transaksi donasi yang tercatat dalam sistem.
            </p>
        </div>
    @endif

    <!-- Additional Information -->
    @if($laporanDonasi->count() > 0)
        <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #28a745;">
                📝 CATATAN LAPORAN:
            </div>
            <div style="font-size: 10px; line-height: 1.4; color: #666;">
                <p style="margin: 5px 0;">• Laporan ini mencakup semua donasi barang yang dilakukan pada tahun {{ $tahun }}</p>
                <p style="margin: 5px 0;">• Data diurutkan berdasarkan tanggal donasi terbaru</p>
                <p style="margin: 5px 0;">• Kode produk mengacu pada ID barang dalam sistem inventory</p>
                <p style="margin: 5px 0;">• ID Penitip dengan awalan "T" menunjukkan penitip yang terdaftar</p>
                <p style="margin: 5px 0;">• Organisasi penerima adalah lembaga yang mengajukan request donasi</p>
            </div>
        </div>

        <!-- Statistics Section -->
        <div style="margin-top: 20px; padding: 15px; background: #e8f5e8; border: 1px solid #28a745; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #28a745;">
                📈 STATISTIK DONASI {{ $tahun }}:
            </div>
            <div style="display: table; width: 100%; font-size: 10px;">
                <div style="display: table-row;">
                    <div style="display: table-cell; width: 50%; padding-right: 15px;">
                        <p style="margin: 3px 0;"><strong>Total Item Didonasikan:</strong> {{ $totalDonasi }} item</p>
                        <p style="margin: 3px 0;"><strong>Rata-rata per Bulan:</strong> {{ round($totalDonasi / 12, 1) }} item</p>
                        <p style="margin: 3px 0;"><strong>Organisasi Terlayani:</strong> {{ $totalOrganisasi }} organisasi</p>
                    </div>
                    <div style="display: table-cell; width: 50%; padding-left: 15px; border-left: 1px solid #28a745;">
                        @php
                            $bulanTerbanyak = $laporanDonasi->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tanggal_donasi)->format('F');
                            })->sortByDesc(function($items) {
                                return $items->count();
                            })->first();
                            
                            $bulanTerbanyakNama = $laporanDonasi->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tanggal_donasi)->format('F');
                            })->sortByDesc(function($items) {
                                return $items->count();
                            })->keys()->first();
                        @endphp
                        
                        <p style="margin: 3px 0;"><strong>Bulan Terbanyak:</strong> {{ $bulanTerbanyakNama ?? 'N/A' }}</p>
                        <p style="margin: 3px 0;"><strong>Jumlah di Bulan Tersebut:</strong> {{ $bulanTerbanyak ? $bulanTerbanyak->count() : 0 }} item</p>
                        <p style="margin: 3px 0;"><strong>Status:</strong> <span class="status-badge badge-success">Aktif Berdonasi</span></p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>ReUse Mart - Laporan Donasi Barang {{ $tahun }}</strong></p>
        <p>Dicetak pada: {{ $tanggalCetak }} | Halaman: <span class="page-number"></span></p>
        <p style="font-size: 9px; font-style: italic;">
            Dokumen ini dibuat secara otomatis oleh sistem ReUse Mart
        </p>
    </div>
</body>
</html>