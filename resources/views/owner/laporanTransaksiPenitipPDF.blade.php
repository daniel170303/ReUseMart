<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Penitip - T{{ $penitipData->id_penitip }}</title>
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
            font-size: 16px;
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
            margin-bottom: 25px;
            background: #f8f9fa;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
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
            font-weight: bold;
        }
        
        .summary-section {
            background: #e8f5e8;
            border: 1px solid #28a745;
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
            vertical-align: top;
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
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-end {
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
        
        .bonus-badge {
            background-color: #28a745;
            color: white;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #e9ecef !important;
            font-weight: bold;
            border-top: 2px solid #333 !important;
        }
        
        .highlight-value {
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN TRANSAKSI PENITIP</h1>
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <p>Telp: (0274) 123-4567 | Email: info@reusemart.com</p>
    </div>

    <!-- Info Section -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">ID Penitip</div>
            <div class="info-value">: T{{ $penitipData->id_penitip }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Penitip</div>
            <div class="info-value">: {{ $penitipData->nama_penitip }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Bulan</div>
            <div class="info-value">: {{ $namaBulanTerpilih }}</div>
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
        <div class="summary-title">💰 RINGKASAN PENDAPATAN</div>
        <div class="summary-item">
            <span class="summary-label">Total Produk Terjual:</span>
            <span class="summary-value">{{ $totalProduk }} item</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Penjualan:</span>
            <span class="summary-value">Rp{{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Total Bonus:</span>
            <span class="summary-value">Rp{{ number_format($totalBonus, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">Pendapatan Bersih:</span>
            <span class="summary-value">Rp{{ number_format($totalBersih, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Data Table -->
    @if($laporanTransaksi->count() > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 10%;">Kode Produk</th>
                    <th style="width: 25%;">Nama Produk</th>
                    <th style="width: 10%;">Tanggal Masuk</th>
                    <th style="width: 10%;">Tanggal Laku</th>
                    <th style="width: 12%;">Harga Jual</th>
                    <th style="width: 12%;">Bersih<br>(sudah dipotong Komisi)</th>
                    <th style="width: 10%;">Bonus terjual cepat</th>
                    <th style="width: 12%;">Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanTransaksi as $index => $transaksi)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <strong>K{{ $transaksi->kode_produk }}</strong>
                        </td>
                        <td>{{ $transaksi->nama_produk }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaksi->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($transaksi->tanggal_laku)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($transaksi->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($transaksi->harga_bersih, 0, ',', '.') }}</td>
                        <td class="text-right">
                            @if($transaksi->bonus_terjual_cepat > 0)
                                <span class="bonus-badge">{{ number_format($transaksi->bonus_terjual_cepat, 0, ',', '.') }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            <strong>{{ number_format($transaksi->pendapatan, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                    
                    @if(($index + 1) % 15 == 0 && $index + 1 < $laporanTransaksi->count())
                        </tbody>
                        </table>
                        <div class="page-break"></div>
                        
                        <!-- Header ulang untuk halaman baru -->
                        <div style="text-align: center; margin-bottom: 20px; font-size: 12px; color: #666;">
                            <strong>Laporan Transaksi Penitip - T{{ $penitipData->id_penitip }} ({{ $penitipData->nama_penitip }}) - {{ $namaBulanTerpilih }} {{ $tahun }} (Lanjutan)</strong>
                        </div>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 4%;">No</th>
                                    <th style="width: 10%;">Kode Produk</th>
                                    <th style="width: 25%;">Nama Produk</th>
                                    <th style="width: 10%;">Tanggal Masuk</th>
                                    <th style="width: 10%;">Tanggal Laku</th>
                                    <th style="width: 12%;">Harga Jual</th>
                                    <th style="width: 12%;">Bersih<br>(sudah dipotong Komisi)</th>
                                    <th style="width: 10%;">Bonus terjual cepat</th>
                                    <th style="width: 12%;">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="5" class="text-center" style="font-weight: bold; font-size: 11px;">
                        <strong>TOTAL</strong>
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        <strong>{{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        <strong>{{ number_format($totalPendapatan - $totalBonus, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        <strong>{{ number_format($totalBonus, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right" style="font-weight: bold; color: #28a745; font-size: 11px;">
                        <strong>{{ number_format($totalBersih, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3 style="color: #666; margin-bottom: 10px;">📋 Tidak Ada Transaksi</h3>
            <p>Tidak ada transaksi untuk penitip <strong>{{ $penitipData->nama_penitip }}</strong> pada periode <strong>{{ $namaBulanTerpilih }} {{ $tahun }}</strong>.</p>
            <p style="font-size: 9px; margin-top: 15px;">
                Laporan ini akan menampilkan data ketika sudah ada transaksi yang selesai pada periode tersebut.
            </p>
        </div>
    @endif

    <!-- Additional Information -->
    @if($laporanTransaksi->count() > 0)
        <div style="margin-top: 25px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #28a745;">
                📝 KETERANGAN LAPORAN:
            </div>
            <div style="font-size: 10px; line-height: 1.4; color: #666;">
                <p style="margin: 5px 0;">• <strong>Harga Bersih:</strong> Harga jual setelah dipotong komisi ReUse Mart (20% untuk penitipan normal, 30% untuk perpanjangan)</p>
                <p style="margin: 5px 0;">• <strong>Bonus Terjual Cepat:</strong> Bonus Rp30.000 untuk barang yang terjual dalam 7 hari sejak masuk</p>
                <p style="margin: 5px 0;">• <strong>Pendapatan:</strong> Total yang diterima penitip (Harga Bersih + Bonus)</p>
                <p style="margin: 5px 0;">• <strong>Kode Produk:</strong> Dimulai dengan huruf "K" diikuti ID barang</p>
                <p style="margin: 5px 0;">• Data hanya menampilkan transaksi yang sudah selesai dan dilunasi</p>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div style="margin-top: 20px; padding: 15px; background: #e3f2fd; border: 1px solid #2196f3; border-radius: 5px;">
            <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #2196f3;">
                📊 ANALISIS PERFORMA {{ $namaBulanTerpilih }} {{ $tahun }}:
            </div>
            <div style="display: table; width: 100%; font-size: 10px;">
                <div style="display: table-row;">
                    <div style="display: table-cell; width: 50%; padding-right: 15px;">
                        @php
                            $rataHargaJual = $totalProduk > 0 ? $totalPendapatan / $totalProduk : 0;
                            $rataPendapatan = $totalProduk > 0 ? $totalBersih / $totalProduk : 0;
                            $produkBonusCepat = $laporanTransaksi->where('bonus_terjual_cepat', '>', 0)->count();
                            $persentaseBonus = $totalProduk > 0 ? ($produkBonusCepat / $totalProduk) * 100 : 0;
                        @endphp
                        
                        <p style="margin: 3px 0;"><strong>Rata-rata Harga Jual:</strong> Rp{{ number_format($rataHargaJual, 0, ',', '.') }}</p>
                        <p style="margin: 3px 0;"><strong>Rata-rata Pendapatan:</strong> Rp{{ number_format($rataPendapatan, 0, ',', '.') }}</p>
                        <p style="margin: 3px 0;"><strong>Produk Dapat Bonus:</strong> {{ $produkBonusCepat }} dari {{ $totalProduk }} ({{ number_format($persentaseBonus, 1) }}%)</p>
                    </div>
                    <div style="display: table-cell; width: 50%; padding-left: 15px; border-left: 1px solid #2196f3;">
                        @php
                            $hargaTertinggi = $laporanTransaksi->max('harga_jual');
                            $hargaTerendah = $laporanTransaksi->min('harga_jual');
                            $produkTertinggi = $laporanTransaksi->where('harga_jual', $hargaTertinggi)->first();
                            $produkTerendah = $laporanTransaksi->where('harga_jual', $hargaTerendah)->first();
                        @endphp
                        
                        <p style="margin: 3px 0;"><strong>Harga Tertinggi:</strong> Rp{{ number_format($hargaTertinggi, 0, ',', '.') }}</p>
                        <p style="margin: 3px 0; font-size: 9px; color: #666;">{{ $produkTertinggi ? $produkTertinggi->nama_produk : 'N/A' }}</p>
                        <p style="margin: 3px 0;"><strong>Harga Terendah:</strong> Rp{{ number_format($hargaTerendah, 0, ',', '.') }}</p>
                        <p style="margin: 3px 0; font-size: 9px; color: #666;">{{ $produkTerendah ? $produkTerendah->nama_produk : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison -->
        @if($totalProduk > 0)
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border: 1px solid #ffc107; border-radius: 5px;">
                <div style="font-size: 11px; font-weight: bold; margin-bottom: 10px; color: #856404;">
                    📈 PROYEKSI & REKOMENDASI:
                </div>
                <div style="font-size: 10px; line-height: 1.4; color: #856404;">
                    @php
                        $proyeksiTahunan = $totalBersih * 12;
                        $targetOptimal = 5; // target 5 produk per bulan
                        $statusPerforma = $totalProduk >= $targetOptimal ? 'Sangat Baik' : ($totalProduk >= 3 ? 'Baik' : 'Perlu Ditingkatkan');
                    @endphp
                    
                    <p style="margin: 3px 0;"><strong>Proyeksi Pendapatan Tahunan:</strong> Rp{{ number_format($proyeksiTahunan, 0, ',', '.') }}</p>
                    <p style="margin: 3px 0;"><strong>Status Performa:</strong> {{ $statusPerforma }} ({{ $totalProduk }} produk/bulan)</p>
                    <p style="margin: 3px 0;"><strong>Rekomendasi:</strong> 
                        @if($persentaseBonus < 50)
                            Tingkatkan kualitas foto dan deskripsi produk untuk mempercepat penjualan
                        @else
                            Pertahankan kualitas produk dan strategi pricing yang sudah baik
                        @endif
                    </p>
                </div>
            </div>
        @endif
    @endif

    <!-- Footer -->
    <div class="footer">
        <p><strong>ReUse Mart - Laporan Transaksi Penitip T{{ $penitipData->id_penitip }}</strong></p>
        <p>Dicetak pada: {{ $tanggalCetak }} | Periode: {{ $namaBulanTerpilih }} {{ $tahun }}</p>
        <p style="font-size: 9px; font-style: italic;">
            Dokumen ini dibuat secara otomatis oleh sistem ReUse Mart
        </p>
        <p style="font-size: 9px; color: #28a745; font-weight: bold;">
            * Terima kasih atas kepercayaan Anda menjadi mitra penitip ReUse Mart
        </p>
    </div>
</body>
</html>