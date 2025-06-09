<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Komisi Bulanan {{ $namaBulanTerpilih }} {{ $tahun }}</title>
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
            font-size: 10px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
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
            padding: 8px;
            border: 1px solid #333;
        }

        .summary-title {
            font-weight: bold;
            text-align: center;
            font-size: 12px;
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

        /* Column widths */
        .col-kode { width: 8%; }
        .col-nama { width: 25%; }
        .col-harga { width: 12%; }
        .col-masuk { width: 10%; }
        .col-laku { width: 10%; }
        .col-hunter { width: 11%; }
        .col-reuse { width: 12%; }
        .col-bonus { width: 12%; }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <h3>LAPORAN KOMISI BULANAN</h3>
    </div>

    <!-- Info Section -->
    <table class="info-table">
        <tr>
            <td class="info-label">Bulan</td>
            <td>: {{ $namaBulanTerpilih }}</td>
        </tr>
        <tr>
            <td class="info-label">Tahun</td>
            <td>: {{ $tahun }}</td>
        </tr>
        <tr>
            <td class="info-label">Tanggal cetak</td>
            <td>: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    @if($laporanKomisi->isNotEmpty())

        <!-- Detail Table -->
        <table>
            <thead>
                <tr>
                    <th class="col-kode">Kode Produk</th>
                    <th class="col-nama">Nama Produk</th>
                    <th class="col-harga">Harga Jual</th>
                    <th class="col-masuk">Tanggal Masuk</th>
                    <th class="col-laku">Tanggal Laku</th>
                    <th class="col-hunter">Komisi Hunter</th>
                    <th class="col-reuse">Komisi ReUse Mart</th>
                    <th class="col-bonus">Bonus Penitip</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($laporanKomisi as $row)
                    <tr>
                        <td class="text-center">{{ $row->id_barang }}</td>
                        <td>{{ $row->nama_barang_titipan }}</td>
                        <td class="text-right">{{ number_format($row->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($row->tanggal_terjual)->format('d/m/Y') }}</td>
                        <td class="text-right">{{ number_format($row->komisi_hunter, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->komisi_reuse_mart, 0, ',', '.') }}</td>
                        <td class="text-right">{{ number_format($row->bonus_penitip, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <th colspan="2" class="text-center">TOTAL</th>
                    <th class="text-right">{{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                    <th colspan="2" class="text-center">-</th>
                    <th class="text-right">{{ number_format($totalKomisiHunter, 0, ',', '.') }}</th>
                    <th class="text-right">{{ number_format($totalKomisiReUse, 0, ',', '.') }}</th>
                    <th class="text-right">{{ number_format($totalBonusPenitip, 0, ',', '.') }}</th>
                </tr>
            </tfoot>
        </table>

    @else
        <div class="no-data">
            <h3>📭 Tidak Ada Data Komisi</h3>
            <p>Belum ada transaksi yang menghasilkan komisi pada bulan {{ $namaBulanTerpilih }} {{ $tahun }}</p>
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