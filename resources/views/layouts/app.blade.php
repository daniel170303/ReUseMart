<!-- resources/views/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Link ke CSS (Bootstrap, atau kamu bisa menambahkan CSS kustom) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJp8D1+K4v5zJ9KJXkyoem7rD4Rfr02h7e2p6IoA6tZ7pITjYybm/sVn/Td7D6" crossorigin="anonymous">

    <!-- JS (untuk Bootstrap atau JavaScript lainnya) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gyb2B+qE2k3bht3Vd7jpx0Lg+4p1y/fojlwktXZ6VLVf8xF/CTtB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-cmfOgQym1jOq0X5js5Pbz+1g5y10z6RrH3swuJmc0ydWqItp5F7eF6NK29O4D7c6" crossorigin="anonymous"></script>
</head>
<body>
    <div id="app">
        <!-- Navbar atau Header -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">Laravel</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('barang_titipan.index') }}">Barang Titipan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Link Lain</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="py-4">
            @yield('content') <!-- Konten dinamis halaman ini akan ditampilkan di sini -->
        </main>
    </div>
</body>
</html>
