<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>ReuseMart - Barang Bekas Berkualitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="font-sans">

    <div class="bg-green-800 text-white text-center py-1 text-sm">
        BARANG YANG DAPAT DIBELI!!!
    </div>

    <!-- Produk Unggulan -->
    <section id="produk" class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 justify-items-center">
                @forelse ($barangTitipan as $index => $barang)
                    <div x-data="{ open: false }"
                        class="bg-green-100 p-4 rounded-lg shadow hover:shadow-md transition w-full max-w-sm text-center">


                        <!-- Tombol Nama Produk -->
                        <button @click="open = true" class="text-lg font-bold text-left text-green-800 hover:underline">
                            {{ $barang->nama_barang_titipan }}
                        </button>

                        <!-- Modal -->
                        <div x-show="open" x-cloak
                            class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50">
                            <div @click.away="open = false"
                                class="bg-white w-11/12 md:w-1/2 rounded-lg shadow-lg p-6 justify-items-center">
                                <h2 class="text-xl font-semibold text-green-700 mb-4">{{ $barang->nama_barang_titipan }}
                                </h2>
                                <img src="{{ asset('storage/' . $barang->gambar_barang) }}"
                                    alt="{{ $barang->nama_barang_titipan }}"
                                    class="w-50 h-64 object-cover rounded mb-4">
                                <p class="text-gray-700 mb-2"><strong>Deskripsi:</strong>
                                    {{ $barang->deskripsi_barang }}</p>
                                <p class="text-gray-700 mb-2"><strong>Jenis Barang:</strong> {{ $barang->jenis_barang }}
                                </p>
                                <p class="text-gray-700 mb-2"><strong>berat_barang:</strong> {{ $barang->berat_barang }}
                                    gr</p>
                                <p class="text-gray-700 mb-2"><strong>Harga:</strong> Rp
                                    {{ number_format($barang->harga_barang, 0, ',', '.') }}</p>
                                <p class="text-gray-700 mb-2"><strong>Garansi:</strong> {{ $barang->garansi_barang }}
                                </p>
                                <button @click="open = false"
                                    class="mt-4 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Tutup</button>
                            </div>
                        </div>

                    </div>
                @empty
                    <p class="col-span-3 text-center text-gray-600">Belum ada barang titipan tersedia.</p>
                @endforelse
            </div>
        </div>
    </section>

</body>

</html>
