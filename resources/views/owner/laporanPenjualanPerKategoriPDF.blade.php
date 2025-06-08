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
                    <th style="width: 20%;">Jumlah Item Belum Terjual</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laporanKategori as $data)
                    <tr>
                        <td style="text-align: left; font-weight: bold;">{{ $data['kategori'] }}</td>
                        <td style="text-align: center;">{{ number_format($data['terjual']) }}</td>
                        <td style="text-align: center;">{{ number_format($data['gagal_terjual']) }}</td>
                        <td style="text-align: center;">{{ number_format($data['belum_terjual']) }}</td>
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
                    <td style="text-align: center; font-weight: bold;">
                        <strong>{{ number_format($totalBelumTerjual) }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>

        {{-- Summary Section --}}
        <div style="margin-top: 20px; padding: 15px; border: 1px solid #000; background-color: #f9f9f9;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #000;">RINGKASAN LAPORAN</h3>
            <table style="border: none; width: 100%;">
                <tr style="border: none;">
                    <td style="border: none; width: 50%; padding: 5px 0;">
                        <strong>Total Item Terjual:</strong> {{ number_format($totalTerjual) }} item
                    </td>
                    <td style="border: none; width: 50%; padding: 5px 0;">
                        <strong>Total Item Gagal Terjual:</strong> {{ number_format($totalGagalTerjual) }} item
                    </td>
                </tr>
                <tr style="border: none;">
                    <td style="border: none; width: 50%; padding: 5px 0;">
                        <strong>Total Item Belum Terjual:</strong> {{ number_format($totalBelumTerjual) }} item
                    </td>
                    <td style="border: none; width: 50%; padding: 5px 0;">
                        <strong>Total Keseluruhan:</strong> {{ number_format($totalTerjual + $totalGagalTerjual + $totalBelumTerjual) }} item
                    </td>
                </tr>
                <tr style="border: none;">
                    <td colspan="2" style="border: none; padding: 5px 0;">
                        @php
                            $totalKeseluruhan = $totalTerjual + $totalGagalTerjual + $totalBelumTerjual;
                            $persentaseKeberhasilan = $totalKeseluruhan > 0 ? ($totalTerjual / $totalKeseluruhan) * 100 : 0;
                        @endphp
                        <strong>Tingkat Keberhasilan Penjualan:</strong> {{ number_format($persentaseKeberhasilan, 1) }}%
                    </td>
                </tr>
            </table>
        </div>

        {{-- Analisis Singkat --}}
        <div style="margin-top: 15px; padding: 10px; border: 1px solid #ccc;">
            <h3 style="margin: 0 0 8px 0; font-size: 12px; color: #000;">ANALISIS SINGKAT</h3>
            @php
                $terbaik = collect($laporanKategori)->sortByDesc('terjual')->first();
                $terburuk = collect($laporanKategori)->sortBy('terjual')->first();
                $kategoriTerbanyak = collect($laporanKategori)->sortByDesc(function($item) {
                    return $item['terjual'] + $item['gagal_terjual'] + $item['belum_terjual'];
                })->first();
            @endphp
            
            <div style="font-size: 10px; line-height: 1.5;">
                @if($terbaik && $terbaik['terjual'] > 0)
                    <p style="margin: 3px 0;">• <strong>Kategori Terlaris:</strong> {{ $terbaik['kategori'] }} dengan {{ number_format($terbaik['terjual']) }} item terjual</p>
                @endif
                
                @if($kategoriTerbanyak)
                    @php
                        $totalKategoriTerbanyak = $kategoriTerbanyak['terjual'] + $kategoriTerbanyak['gagal_terjual'] + $kategoriTerbanyak['belum_terjual'];
                    @endphp
                    <p style="margin: 3px 0;">• <strong>Kategori dengan Item Terbanyak:</strong> {{ $kategoriTerbanyak['kategori'] }} dengan {{ number_format($totalKategoriTerbanyak) }} total item</p>
                @endif
                
                @if($persentaseKeberhasilan < 50)
                    <p style="margin: 3px 0;">• <strong>Catatan:</strong> Tingkat keberhasilan penjualan masih rendah ({{ number_format($persentaseKeberhasilan, 1) }}%), perlu evaluasi strategi pemasaran</p>
                @elseif($persentaseKeberhasilan >= 75)
                    <p style="margin: 3px 0;">• <strong>Catatan:</strong> Tingkat keberhasilan penjualan sangat baik ({{ number_format($persentaseKeberhasilan, 1) }}%), pertahankan strategi yang ada</p>
                @endif
            </div>
        </div>
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