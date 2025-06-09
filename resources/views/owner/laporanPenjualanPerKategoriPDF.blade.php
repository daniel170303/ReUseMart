<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan Per Kategori {{ $tahun }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #000;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }
        
        .header .company-info {
            margin: 10px 0;
            font-size: 11px;
            color: #000;
        }
        
        .header h2 {
            margin: 15px 0 5px 0;
            font-size: 16px;
            font-weight: bold;
            color: #000;
        }
        
        .header .report-info {
            margin: 5px 0;
            font-size: 11px;
            color: #000;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            color: #000;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 11px;
        }
        
        td {
            text-align: center;
            font-size: 11px;
        }
        
        td:nth-child(1) {
            text-align: left;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #f8f8f8;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 10px;
            color: #000;
        }
        
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ReUse Mart</h1>
        <div class="company-info">
            Jl. Green Eco Park No. 456 Yogyakarta
        </div>
        
        <h2>LAPORAN PENJUALAN PER KATEGORI BARANG</h2>
        <div class="report-info">
            <strong>Tahun:</strong> {{ $tahun }}
        </div>
        <div class="report-info">
            <strong>Tanggal cetak:</strong> {{ $tanggalCetak }}
        </div>
    </div>

    {{-- Tabel Laporan --}}
    @if(count($laporanKategori) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 40%;">Kategori</th>
                    <th style="width: 20%;">Jumlah Item Terjual</th>
                    <th style="width: 20%;">Jumlah Item Gagal Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanKategori as $data)
                    <tr>
                        <td style="text-align: left; font-weight: bold;">{{ $data['kategori'] }}</td>
                        <td style="text-align: center;">{{ number_format($data['terjual']) }}</td>
                        <td style="text-align: center;">{{ number_format($data['gagal_terjual']) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td style="text-align: center; font-weight: bold;"><strong>TOTAL</strong></td>
                    <td style="text-align: center; font-weight: bold;">
                        <strong>{{ number_format($totalTerjual) }}</strong>
                    </td>
                    <td style="text-align: center; font-weight: bold;">
                        <strong>{{ number_format($totalGagalTerjual) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ada data penjualan untuk tahun {{ $tahun }}</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem ReUse Mart</p>
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>
</body>
</html>