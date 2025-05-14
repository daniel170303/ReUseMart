<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Landing Page - ReuseMart</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white font-sans">

    <!-- Header -->
    <nav class="bg-green-700 p-4">
        <div class="container mx-auto text-white flex justify-between items-center">
            <a href="/" class="text-xl font-semibold">ReuseMart</a>
            <div>
                <a href="/login" class="text-white">Login</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="flex min-h-screen">
        <!-- Display Barang Titipan -->
        <div class="w-full md:w-2/3 px-6 py-4">
            <h2 class="text-3xl font-semibold text-gray-900 mb-4">Barang Titipan Terbaru</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($barangTitipan as $barang)
                    <div class="border p-4 rounded-md shadow-md">
                        <h3 class="font-semibold">{{ $barang->nama_barang }}</h3>
                        <p>{{ $barang->deskripsi }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Sidebar / Gambar -->
        <div class="hidden md:block md:w-1/3 bg-gray-100 p-4">
            <img src="{{ asset('images/GambarLandingPage.png') }}" alt="Landing Page Image" class="w-full h-full object-cover">
        </div>
    </div>

</body>
</html>
