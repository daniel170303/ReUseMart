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

    {{-- Summary --}}
    <div class="summary">
        <h3>RINGKASAN LAPORAN</h3>
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; text-align: left; width: 50%; padding: 3px 0;">
                    <strong>Total Barang Masa Habis:</strong> {{ number_format($totalBarang) }} item
                </td>
                <td style="border: none; text-align: left; width: 50%; padding: 3px 0;">
                    <strong>Total Penitip Terlibat:</strong> {{ number_format($totalPenitip) }} orang
                </td>
            </tr>
            <tr style="border: none;">
                <td colspan="2" style="border: none; text-align: left; padding: 3px 0;">
                    <strong>Status:</strong> Barang yang melewati tanggal selesai penitipan dan belum diambil penitip
                </td>
            </tr>
        </table>
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
                        <td>{{ $data->id_penitip }}</td>
                        <td class="text-left">{{ $data->nama_penitip }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->tanggal_akhir)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($data->batas_ambil)->format('d/m/Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Rekomendasi Tindakan --}}
        <div style="margin-top: 15px; padding: 10px; border: 1px solid #ccc;">
            <h3 style="margin: 0 0 8px 0; font-size: 12px; color: #000;">REKOMENDASI TINDAKAN</h3>
            <div style="font-size: 10px; line-height: 1.4;">
                <div style="width: 48%; float: left;">
                    <p style="margin: 3px 0; font-weight: bold;">1. Kontak Penitip:</p>
                    <p style="margin: 2px 0 2px 10px;">• Hubungi penitip untuk konfirmasi pengambilan</p>
                    <p style="margin: 2px 0 2px 10px;">• Berikan perpanjangan waktu jika diperlukan</p>
                    <p style="margin: 2px 0 2px 10px;">• Informasikan konsekuensi jika tidak diambil</p>
                </div>
                <div style="width: 48%; float: right;">
                    <p style="margin: 3px 0; font-weight: bold;">2. Proses Donasi:</p>
                    <p style="margin: 2px 0 2px 10px;">• Barang tidak diambil > 7 hari setelah masa habis dapat
                        didonasikan</p>
                    <p style="margin: 2px 0 2px 10px;">• Update status barang menjadi "sudah didonasikan"</p>
                    <p style="margin: 2px 0 2px 10px;">• Dokumentasikan proses donasi</p>
                </div>
                <div style="clear: both;"></div>
            </div>
        </div>

        {{-- Analisis Berdasarkan Keterlambatan --}}
        <div style="margin-top: 15px; padding: 10px; border: 1px solid #ccc;">
            <h3 style="margin: 0 0 8px 0; font-size: 12px; color: #000;">ANALISIS KETERLAMBATAN</h3>
            @php
                $terlambat1_7 = $penitipanHabis
                    ->filter(function ($item) {
                        $hari = now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_akhir));
                        return $hari >= 1 && $hari <= 7;
                    })
                    ->count();

                $terlambat8_30 = $penitipanHabis
                    ->filter(function ($item) {
                        $hari = now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_akhir));
                        return $hari >= 8 && $hari <= 30;
                    })
                    ->count();

                $terlambat30Plus = $penitipanHabis
                    ->filter(function ($item) {
                        $hari = now()->diffInDays(\Carbon\Carbon::parse($item->tanggal_akhir));
                        return $hari > 30;
                    })
                    ->count();
            @endphp
            <div style="font-size: 10px;">
                <p style="margin: 3px 0;">• <strong>Terlambat 1-7 hari:</strong> {{ $terlambat1_7 }} barang (masih
                    dapat dikontakkan)</p>
                <p style="margin: 3px 0;">• <strong>Terlambat 8-30 hari:</strong> {{ $terlambat8_30 }} barang (perlu
                    tindakan segera)</p>
                <p style="margin: 3px 0;">• <strong>Terlambat > 30 hari:</strong> {{ $terlambat30Plus }} barang
                    (kandidat donasi)</p>
            </div>
        </div>

        {{-- Catatan Penting --}}
        <div style="margin-top: 15px; padding: 10px; border: 1px solid #ccc; background-color: #fff3cd;">
            <h3 style="margin: 0 0 8px 0; font-size: 12px; color: #000;">CATATAN PENTING</h3>
            <div style="font-size: 10px;">
                <p style="margin: 3px 0;">• <strong>Masa Penitipan:</strong> Berdasarkan tanggal selesai penitipan (30
                    hari dari tanggal masuk)</p>
                <p style="margin: 3px 0;">• <strong>Batas Pengambilan:</strong> 7 hari setelah masa penitipan selesai
                </p>
                <p style="margin: 3px 0;">• <strong>Perpanjangan:</strong> Dapat dilakukan maksimal 1 kali (30 hari
                    tambahan)</p>
                <p style="margin: 3px 0;">• <strong>Donasi:</strong> Barang yang tidak diambil setelah batas waktu dapat
                    didonasikan</p>
            </div>
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
