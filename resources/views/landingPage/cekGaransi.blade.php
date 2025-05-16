<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cek Garansi - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 font-sans p-6">

  <h1 class="text-2xl font-bold text-green-700 mb-6">Hasil Cek Garansi Barang</h1>

  <div class="bg-white p-6 rounded shadow max-w-md mx-auto">
    <h2 class="text-xl font-semibold">{{ $barang->nama_barang_titipan }} (ID: {{ $barang->id_barang }})</h2>
    <p class="mt-2">Harga: Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
    <p class="mt-2">Garansi: {{ $barang->garansi_barang }}</p>
    <p class="mt-4 font-semibold text-green-700">{{ $statusGaransi }}</p>

    <a href="{{ url('/') }}" class="inline-block mt-6 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Kembali ke Beranda</a>
  </div>

</body>
</html>
