<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Penitipan - ReUseMart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 10px;
        }

        .nota-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
        }

        .nota-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .nota-left, .nota-right {
            width: 48%;
        }

        .info-row {
            margin-bottom: 8px;
        }

        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }

        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 60px;
            padding-top: 5px;
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
    <!-- Header -->
    <div class="header">
        <div class="company-name">ReUseMart</div>
        <div class="company-address">Jl. Green Eco Park No. 456 Yogyakarta</div>
        <div class="nota-title">NOTA PENITIPAN BARANG</div>
    </div>

    <!-- Informasi Nota -->
    <div class="nota-info">
        <div class="nota-left">
            <div class="info-row">
                <span class="info-label">No. Nota:</span>
                <span>{{ date('Y.m', strtotime($penitipan->tanggal_penitipan)) }}.{{ $penitipan->id_penitipan }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Penitipan:</span>
                <span>{{ \Carbon\Carbon::parse($penitipan->tanggal_penitipan)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal Selesai:</span>
                <span>{{ \Carbon\Carbon::parse($penitipan->tanggal_selesai_penitipan)->format('d/m/Y') }}</span>
            </div>
        </div>
        <div class="nota-right">
            <div class="info-row">
                <span class="info-label">Penitip:</span>
                <span>T{{ $penitipan->penitip->id_penitip }} / {{ $penitipan->penitip->nama_penitip }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">No. Telepon:</span>
                <span>{{ $penitipan->penitip->nomor_telepon_penitip ?? '-' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span>{{ $penitipan->penitip->email_penitip ?? '-' }}</span>
            </div>
        </div>
    </div>

    <!-- Tabel Barang Titipan -->
    <h4 style="margin-bottom: 10px;">Daftar Barang Titipan:</h4>
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Barang</th>
                <th style="width: 20%;">Jenis Barang</th>
                <th style="width: 15%;">Harga (Rp)</th>
                <th style="width: 20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penitipan->detailPenitipan as $index => $detail)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang_titipan }}</td>
                    <td>{{ $detail->barang->jenis_barang ?? '-' }}</td>
                    <td style="text-align: right;">{{ number_format($detail->barang->harga_barang, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($detail->barang->status_barang) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan -->
    <div style="margin-top: 20px;">
        <div class="info-row">
            <span class="info-label">Total Barang:</span>
            <span>{{ $penitipan->detailPenitipan->count() }} item</span>
        </div>
        <div class="info-row">
            <span class="info-label">Total Nilai:</span>
            <span>Rp {{ number_format($penitipan->detailPenitipan->sum(function($detail) { return $detail->barang->harga_barang; }), 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Ketentuan -->
    <div style="margin-top: 30px; font-size: 10px;">
        <h4>Ketentuan Penitipan:</h4>
        <ul style="margin: 0; padding-left: 20px;">
            <li>Barang yang dititipkan menjadi tanggung jawab ReUseMart selama masa penitipan</li>
            <li>Penitip dapat mengambil barang sesuai tanggal yang telah ditentukan</li>
            <li>Apabila barang terjual, komisi akan dibagi sesuai kesepakatan</li>
            <li>Nota ini merupakan bukti sah penitipan barang</li>
        </ul>
    </div>

    <!-- Tanda Tangan -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Penitip</div>
            <div class="signature-line">{{ $penitipan->penitip->nama_penitip }}</div>
        </div>
        <div class="signature-box">
            <div>Petugas ReUseMart</div>
            <div class="signature-line">{{ session('user_name', 'Petugas') }}</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Terima kasih atas kepercayaan Anda kepada ReUseMart</p>
        <p>Dokumen ini dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>

</html>
