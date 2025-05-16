<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Barang - {{ $barang->nama_barang_titipan }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

  <div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-3xl font-bold text-green-800 mb-4">{{ $barang->nama_barang_titipan }}</h1>

    <p class="mb-2 text-gray-700">{{ $barang->deskripsi_barang }}</p>
    <p class="text-lg font-semibold text-green-700">Harga: Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
    <p class="text-sm text-green-600 mt-1">Garansi: {{ $barang->garansi_barang }}</p>
    <p class="text-sm text-gray-500">Jenis: {{ $barang->jenis_barang }} | Berat: {{ $barang->berat_barang }} gr</p>

    <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-2">Gambar Barang:</h3>
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
      @forelse($barang->gambarBarang as $gambar)
        <img src="{{ asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar) }}"
             class="w-full h-40 object-cover rounded shadow"
             alt="Gambar {{ $barang->nama_barang_titipan }}">
      @empty
        <p class="text-gray-500 col-span-3">Tidak ada gambar tersedia.</p>
      @endforelse
    </div>

    <a href="{{ url('/') }}" class="inline-block mt-6 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Kembali</a>
  </div>

</body>
</html>
