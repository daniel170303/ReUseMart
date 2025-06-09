<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Tahunan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .report-info {
            margin-bottom: 20px;
        }

        h2, h3, h4 {
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .summary-table {
            width: 60%;
            margin: 0 auto 30px auto;
        }

        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }

        .chart-container {
            margin: 30px 0;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <h2>ReUse Mart</h2>
        <p>Jl. Green Eco Park No. 456 Yogyakarta</p>
        <hr>
    </div>

    <!-- Report Info -->
    <div class="report-info">
        <h3><strong>LAPORAN PENJUALAN TAHUNAN</strong></h3>
        <p><strong>Tahun:</strong> {{ $tahun }}</p>
        <p><strong>Tanggal cetak:</strong> {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i:s') }}</p>
    </div>

    <!-- Tabel Laporan Per Bulan -->
    <h4>DETAIL PENJUALAN PER BULAN</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Bulan</th>
                <th>Total Penjualan</th>
                <th>Total Barang</th>
                <th>Total Komisi</th>
                <th>Rata-rata per Barang</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach ($laporanTahunan as $data)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td class="text-left">{{ $data->nama_bulan }}</td>
                    <td class="text-right">Rp{{ number_format($data->total_penjualan, 0, ',', '.') }}</td>
                    <td>{{ $data->total_barang }}</td>
                    <td class="text-right">Rp{{ number_format($data->total_komisi, 0, ',', '.') }}</td>
                    <td class="text-right">
                        @if($data->total_barang > 0)
                            Rp{{ number_format($data->total_penjualan / $data->total_barang, 0, ',', '.') }}
                        @else
                            Rp0
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="2">TOTAL</th>
                <th class="text-right">Rp{{ number_format($totalPenjualan, 0, ',', '.') }}</th>
                <th>{{ $totalBarang }}</th>
                <th class="text-right">Rp{{ number_format($totalKomisi, 0, ',', '.') }}</th>
                <th class="text-right">
                    @if($totalBarang > 0)
                        Rp{{ number_format($totalPenjualan / $totalBarang, 0, ',', '.') }}
                    @else
                        Rp0
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>

    <!-- HTML Chart -->
    <div class="chart-container">
        {!! $htmlChart !!}
    </div>

    <!-- Footer -->
    <div style="margin-top: 50px; text-align: right;">
        <p>Yogyakarta, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        <br><br><br>
        <p>_________________________</p>
        <p><strong>Owner ReUse Mart</strong></p>
    </div>

</body>

</html>
