<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nota Pengambilan</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .judul { font-weight: bold; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { padding: 4px; vertical-align: top; }
        th { background-color: #f2f2f2; }
        .bordered td, .bordered th { border: 1px solid #000; }
    </style>
</head>
<body>
    <p class="judul">ReUse Mart</p>
    <p>Jl. Green Eco Park No. 456 Yogyakarta</p>

    <table>
        <tr>
            <td>No Nota</td>
            <td>: {{ $no_nota }}</td>
        </tr>
        <tr>
            <td>Tanggal Pemesanan</td>
            <td>: {{ $tanggal_pemesanan }}</td>
        </tr>
        <tr>
            <td>Tanggal Pelunasan</td>
            <td>: {{ $tanggal_pelunasan ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Pengambilan</td>
            <td>: {{ $tanggal_pengiriman ?? '-' }}</td>
        </tr>
        <tr>
            <td>Pembeli</td>
            <td>: {{ $nama_pembeli }} / {{ $email_pembeli }}</td>
        </tr>
        <tr>
            <td>Alamat</td>
            <td>: {{ $alamat_pembeli }}</td>
        </tr>
        <tr>
            <td>Delivery</td>
            <td>: Diambil Pembeli</td>
        </tr>
    </table>

    <br>
    <table class="bordered">
        <thead>
            <tr>
                <th>Barang</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $barang }}</td>
            </tr>
        </tbody>
    </table>

    <br>
    <p><strong>Ongkos Kirim: Rp{{ number_format($ongkir, 0, ',', '.') }}</strong></p>
</body>
</html>