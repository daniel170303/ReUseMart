<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Nota Penitipan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h2>Nota Penitipan Barang</h2>
    <p><strong>ID Penitipan:</strong> {{ $penitipan->id_penitipan }}</p>
    <p><strong>Nama Penitip:</strong> {{ $penitipan->penitip->nama_penitip }}</p>
    <p><strong>Tanggal Penitipan:</strong> {{ $penitipan->tanggal_penitipan }}</p>
    <p><strong>Tanggal Selesai:</strong> {{ $penitipan->tanggal_selesai_penitipan }}</p>

    <h4>Daftar Barang:</h4>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penitipan->detailPenitipan as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->nama_barang_titipan }}</td>
                    <td>{{ $detail->barang->status_barang }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
