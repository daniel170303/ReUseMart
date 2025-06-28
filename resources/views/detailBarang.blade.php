<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Detail Barang - {{ $barang->nama_barang_titipan }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">

    <!-- Container utama -->
    <div class="max-w-5xl mx-auto py-10 px-6">
        <!-- Kartu utama -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <!-- Nama barang -->
            <h1 class="text-4xl font-extrabold text-green-800 mb-4">{{ $barang->nama_barang_titipan }}</h1>

            <!-- Deskripsi -->
            <p class="mb-4 text-gray-700 text-lg leading-relaxed">{{ $barang->deskripsi_barang }}</p>

            <!-- Info harga dan garansi -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <p class="text-2xl font-semibold text-green-700">Harga: Rp
                    {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
                <div class="flex flex-col sm:items-end text-sm mt-4 sm:mt-0">
                    <span class="text-green-600">Garansi: {{ $barang->garansi_barang }}</span>
                </div>
            </div>

            <!-- Info jenis dan berat -->
            <p class="text-sm text-gray-500 mb-6">Jenis: {{ $barang->jenis_barang }} | Berat:
                {{ $barang->berat_barang }} kg</p>

            <!-- Gambar Barang -->
            <h3 class="text-xl font-semibold text-gray-800 mt-6 mb-3">Gambar Barang:</h3>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($barang->gambarBarang ?? [] as $gambar)
                    <div
                        class="bg-gray-100 rounded-lg overflow-hidden shadow hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar) }}"
                            class="w-full h-40 object-cover" alt="Gambar {{ $barang->nama_barang_titipan }}">
                    </div>
                @empty
                    <p class="text-gray-500 col-span-3">Tidak ada gambar tersedia.</p>
                @endforelse
            </div>

            <!-- Info penitip & rating -->
            <div class="mb-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                @if ($penitip)
                    <p class="text-gray-800 font-semibold">
                        Dititipkan oleh: <span class="text-blue-700">{{ $penitip->nama_penitip }}</span>
                    </p>
                @else
                    <p class="text-muted">Penitip tidak ditemukan</p>
                @endif

                @if ($averageRating)
                    <p class="text-yellow-600 mt-1">
                        Rating Penitip: <strong>{{ $averageRating }} / 5</strong> <i
                            class="fas fa-star text-yellow-500"></i>
                    </p>
                @else
                    <p class="text-gray-500 mt-1">Belum ada rating untuk penitip ini</p>
                @endif
            </div>

            <!-- Tombol kembali -->
            <div class="mt-8">
                <a href="{{ url('/') }}"
                    class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Diskusi Produk -->
    <div class="max-w-5xl mx-auto px-6 mt-12">
        <div class="bg-white rounded-xl shadow-md p-6">
            <h4 class="text-2xl font-bold mb-6 text-gray-800">💬 Diskusi Produk</h4>

            <!-- Alert jika berhasil kirim pesan -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form tambah diskusi -->
            <form action="{{ route('diskusi.store', $barang->id_barang) }}" method="POST" class="mb-8">
                @csrf
                <div class="mb-4">
                    <label class="block font-semibold text-gray-700 mb-1">Nama Anda</label>
                    <input type="text" name="nama_pengirim"
                        class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400"
                        required>
                </div>
                <div class="mb-4">
                    <label class="block font-semibold text-gray-700 mb-1">Pesan</label>
                    <textarea name="isi_pesan" class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-400"
                        rows="4" required></textarea>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    Kirim Pesan
                </button>
            </form>

            <!-- Daftar diskusi -->
            @forelse($diskusi as $pesan)
                <div class="mb-5 p-4 border rounded-lg bg-gray-50 shadow-sm">
                    <div class="flex justify-between items-center mb-1">
                        <p class="font-semibold text-gray-800">{{ $pesan->nama_pengirim }}</p>
                        <span
                            class="text-sm text-gray-400">{{ \Carbon\Carbon::parse($pesan->created_at)->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-700">{{ $pesan->isi_pesan }}</p>
                </div>
            @empty
                <p class="text-gray-500">Belum ada diskusi untuk produk ini.</p>
            @endforelse
        </div>
    </div>

</body>

</html>
