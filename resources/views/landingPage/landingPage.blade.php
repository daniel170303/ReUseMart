<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ReuseMart - Barang Bekas Berkualitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
  </head>
  <body class="bg-green-50 font-sans">

    <!-- Banner -->
    <div class="bg-green-800 text-white text-center py-1 text-sm">
      DISKON SPESIAL: Hingga 50% untuk produk pilihan bekas berkualitas!
    </div>

    <!-- Navbar -->
    <nav class="bg-white shadow sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
        <h1 class="text-xl font-bold text-green-700">ReuseMart</h1>
        <div class="hidden md:flex space-x-6">
          <a href="#" class="text-gray-700 hover:text-green-700">Beranda</a>
          <a href="#" class="text-gray-700 hover:text-green-700">Kategori</a>
          <a href="#" class="text-gray-700 hover:text-green-700">Tentang Kami</a>
          <a href="{{ route('login') }}" class="text-gray-700 hover:text-green-700">Akun</a>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-green-200 py-10 text-center">
      <div class="max-w-4xl mx-auto px-4">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Barang Bekas Layak Pakai, Harga Terjangkau</h2>
        <p class="text-gray-700 mb-6">Temukan barang elektronik, furnitur, dan aksesoris dengan kualitas terbaik dan garansi!</p>
        <a href="#" class="bg-green-600 text-white px-6 py-2 rounded-full hover:bg-green-700">Jelajahi Produk</a>
      </div>
    </section>

    <!-- Produk Unggulan -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 justify-items-center">   
  @forelse($barangTitipan as $barang)
    <div class="bg-green-100 p-4 rounded-lg shadow hover:shadow-md transition">
      <img src="{{ asset('storage/' . $barang->gambar_barang) }}"
      alt="{{ $barang->nama_barang_titipan }}"
      class="rounded-md mb-3 w-full h-48 object-cover">
      <h4 class="text-lg font-bold text-gray-800">{{ $barang->nama_barang_titipan }}</h4>
      <p class="text-gray-600 text-sm mt-1">{{ Str::limit($barang->deskripsi_barang, 50) }}</p>
      <p class="text-green-700 font-bold mt-2">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
      
      <p class="text-sm mt-1">
        @if(strtolower($barang->garansi_barang) !== '0 bulan')
          <span class="text-green-600">Garansi: {{ $barang->garansi_barang }}</span>
        @else
          <span class="text-red-500 font-semibold">Tanpa Garansi</span>
        @endif
      </p>

      <a href="{{ route('barang.show', $barang->id_barang) }}" class="block mt-4 text-center bg-green-600 text-white py-2 rounded hover:bg-green-700">Lihat Detail</a>
    </div>
  @empty
    <p class="col-span-3 text-center text-gray-600">Belum ada barang titipan tersedia.</p>
  @endforelse
</div>

      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-green-800 text-white text-center py-6 mt-10">
      <p>&copy; 2025 ReuseMart. Jual Beli Barang Bekas Berkualitas.</p>
    </footer>

  </body>
</html>
