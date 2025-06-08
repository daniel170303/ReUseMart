<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Gudang</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }

        .header h3 {
            margin: 15px 0 5px 0;
            font-size: 16px;
            font-weight: bold;
            color: #333;
        }

        .info-section {
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
            border: none;
        }

        .info-label {
            width: 100px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 9px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 4px 3px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 8px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .no-data {
            text-align: center;
            padding: 40px;
            color: #666;
            font-style: italic;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }

        .summary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #333;
        }

        .summary-table td {
            padding: 6px;
            border: 1px solid #333;
        }

        .summary-title {
            font-weight: bold;
            text-align: center;
            font-size: 11px;
            background-color: #f9f9f9;
        }

        .summary-label {
            width: 60%;
        }

        .summary-value {
            text-align: right;
            font-weight: bold;
            width: 40%;
        }

        /* Column widths untuk tabel stok */
        .col-kode { width: 10%; }
        .col-nama { width: 20%; }
        .col-id-penitip { width: 8%; }
        .col-nama-penitip { width: 15%; }
        .col-tanggal { width: 10%; }
        .col-perpanjangan { width: 8%; }
        .col-id-hunter { width: 8%; }
        .col-nama-hunter { width: 12%; }
        .col-harga { width: 9%; }

        .status-ya {
            background-color: #fff3cd;
        }

        .status-tidak {
            background-color: #d1ecf1;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h3>LAPORAN STOK GUDANG</h3>
    </div>

    <!-- Info Section -->
    <table class="info-table">
        <tr>
            <td class="info-label">Tanggal cetak</td>
            <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td class="info-label">Waktu cetak</td>
            <td>: {{ \Carbon\Carbon::now()->translatedFormat('H:i') }} WIB</td>
        </tr>
        <tr>
            <td class="info-label">Total stok</td>
            <td>: {{ $totalStok }} item</td>
        </tr>
    </table>

    @if($stokGudang->isNotEmpty())
        <!-- Summary Table -->
        <table class="summary-table">
            <tr>
                <td colspan="2" class="summary-title">RINGKASAN STOK GUDANG</td>
            </tr>
            <tr>
                <td class="summary-label">Total Item di Gudang</td>
                <td class="summary-value">{{ $totalStok }} item</td>
            </tr>
            <tr>
                <td class="summary-label">Total Nilai Stok</td>
                <td class="summary-value">Rp{{ number_format($totalNilaiStok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Stok dengan Perpanjangan</td>
                <td class="summary-value">{{ $stokGudang->where('perpanjangan', 'Ya')->count() }} item</td>
            </tr>
            <tr>
                <td class="summary-label">Stok Normal</td>
                <td class="summary-value">{{ $stokGudang->where('perpanjangan', 'Tidak')->count() }} item</td>
            </tr>
            <tr>
                <td class="summary-label">Rata-rata Harga per Item</td>
                <td class="summary-value">
                    @if($totalStok > 0)
                        Rp{{ number_format($totalNilaiStok / $totalStok, 0, ',', '.') }}
                    @else
                        Rp0
                    @endif
                </td>
            </tr>
        </table>

        <!-- Detail Table -->
        <table>
            <thead>
                <tr>
                    <th class="col-kode">Kode Produk</th>
                    <th class="col-nama">Nama Produk</th>
                    <th class="col-id-penitip">Id Penitip</th>
                    <th class="col-nama-penitip">Nama Penitip</th>
                    <th class="col-tanggal">Tanggal Masuk</th>
                    <th class="col-perpanjangan">Perpanjangan</th>
                    <th class="col-id-hunter">ID Hunter</th>
                    <th class="col-nama-hunter">Nama Hunter</th>
                    <th class="col-harga">Harga</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($stokGudang as $row)
                    <tr class="{{ $row->perpanjangan == 'Ya' ? 'status-ya' : 'status-tidak' }}">
                        <td class="text-center">{{ $row->kode_produk }}</td>
                        <td>{{ $row->nama_produk }}</td>
                        <td class="text-center">{{ $row->id_penitip ?? '-' }}</td>
                        <td>{{ $row->nama_penitip ?? 'Tidak diketahui' }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $row->perpanjangan }}</td>
                        <td class="text-center">{{ $row->id_hunter ?? '-' }}</td>
                        <td>{{ $row->nama_hunter ?? '-' }}</td>
                        <td class="text-right">{{ number_format($row->harga, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="6" class="text-center">TOTAL</th>
                    <th class="text-center">{{ $totalStok }} item</th>
                    <th class="text-center">-</th>
                    <th class="text-right">{{ number_format($totalNilaiStok, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

        <!-- Analisis Stok -->
        <table style="margin-top: 20px; border: 1px solid #333;">
            <tr>
                <td colspan="4" class="summary-title">ANALISIS STOK BERDASARKAN STATUS PERPANJANGAN</td>
            </tr>
            <tr style="background-color: #f9f9f9; font-weight: bold;">
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Status</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Jumlah Item</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Persentase</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Total Nilai</td>
            </tr>
            @php
                $stokYa = $stokGudang->where('perpanjangan', 'Ya');
                $stokTidak = $stokGudang->where('perpanjangan', 'Tidak');
                $nilaiStokYa = $stokYa->sum('harga');
                $nilaiStokTidak = $stokTidak->sum('harga');
            @endphp
            <tr class="status-ya">
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Perpanjangan (Ya)</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">{{ $stokYa->count() }}</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">
                    {{ $totalStok > 0 ? round(($stokYa->count() / $totalStok) * 100, 1) : 0 }}%
                </td>
                <td style="border: 1px solid #333; padding: 6px; text-align: right;">{{ number_format($nilaiStokYa, 0, ',', '.') }}</td>
            </tr>
            <tr class="status-tidak">
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">Normal (Tidak)</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">{{ $stokTidak->count() }}</td>
                <td style="border: 1px solid #333; padding: 6px; text-align: center;">
                    {{ $totalStok > 0 ? round(($stokTidak->count() / $totalStok) * 100, 1) : 0 }}%
                </td>
                <td style="border: 1px solid #333; padding: 6px; text-align: right;">{{ number_format($nilaiStokTidak, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Keterangan -->
        <table style="margin-top: 20px; font-size: 9px; border: none;">
            <tr>
                <td style="border: none; padding: 5px 0;"><strong>Keterangan:</strong></td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• Data stok yang ditampilkan adalah barang yang masih tersedia di gudang per tanggal cetak</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• Status "Ya" = Penitipan sudah diperpanjang oleh penitip</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• Status "Tidak" = Penitipan belum diperpanjang (masih dalam periode normal)</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• ID Hunter "-" = Barang dititipkan langsung tanpa melalui hunter</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• Nama Hunter "-" = Tidak ada hunter yang terlibat dalam penitipan barang</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 0;">• Harga yang tercantum adalah harga jual yang ditetapkan untuk setiap barang</td>
            </tr>
        </table>

    @else
        <div class="no-data">
            <h3>📭 Tidak Ada Stok di Gudang</h3>
            <p>Saat ini tidak ada barang yang tersedia di gudang.</p>
            <p>Kemungkinan penyebab:</p>
            <ul style="text-align: left; display: inline-block;">
                <li>Semua barang sudah terjual habis</li>
                <li>Belum ada barang yang dititipkan</li>
                <li>Semua barang sudah diambil kembali oleh penitip</li>
            </ul>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Laporan ini digenerate secara otomatis oleh sistem ReUse Mart</p>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</p>
        <p>© {{ date('Y') }} ReUse Mart - Semua hak dilindungi</p>
    </div>

</body>

</html>