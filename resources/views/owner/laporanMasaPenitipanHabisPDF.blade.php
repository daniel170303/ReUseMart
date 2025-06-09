<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masa Penitipan Habis</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 0;
            padding: 15px;
            color: #000;
            line-height: 1.3;
        }

        .header {
            text-align: center;
            margin-bottom: 25px;
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
            margin: 8px 0;
            font-size: 11px;
            color: #000;
        }

        .header h2 {
            margin: 15px 0 5px 0;
            font-size: 14px;
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

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            color: #000;
            font-size: 10px;
        }

        th {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 10px;
        }

        td.text-left {
            text-align: left;
        }

        .summary {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #000;
            background-color: #f9f9f9;
        }

        .summary h3 {
            margin: 0 0 8px 0;
            font-size: 12px;
            color: #000;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 9px;
            color: #000;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 30px;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
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

        <h2>LAPORAN</h2>
        <h2>Barang yang Masa Penitipannya Sudah Habis</h2>
        <div class="report-info">
            <strong>Tanggal cetak:</strong> {{ $tanggalCetak }}
        </div>
    </div>

    {{-- Tabel Laporan --}}
    @if (count($penitipanHabis) > 0)
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">Kode Produk</th>
                    <th style="width: 25%;">Nama Produk</th>
                    <th style="width: 8%;">ID Penitip</th>
                    <th style="width: 20%;">Nama Penitip</th>
                    <th style="width: 10%;">Tanggal Masuk</th>
                    <th style="width: 10%;">Tanggal Akhir</th>
                    <th style="width: 10%;">Batas Ambil</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penitipanHabis as $data)
                    @php
                        $hariTerlambat = now()->diffInDays(\Carbon\Carbon::parse($data->tanggal_akhir));
                    @endphp
                    <tr>
                        <td>{{ $data->kode_produk }}</td>
                        <td class="text-left">{{ $data->nama_produk }}</td>
                        <td>T{{ $data->id_penitip }}</td>
                        <td class="text-left">{{ $data->nama_penitip }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_akhir)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->batas_ambil)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        </div>
    @else
        <div class="no-data">
            <h3>Tidak Ada Data</h3>
            <p>Tidak ada barang yang masa penitipannya sudah habis</p>
            <p>Semua barang masih dalam masa penitipan yang valid</p>
        </div>
    @endif

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem ReUse Mart</p>
        <p>Dicetak pada: {{ $tanggalCetak }}</p>
    </div>
</body>

</html>
