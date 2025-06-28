<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang - Barang Titipan</title>

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome (opsional untuk ikon) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f9fa;
            display: flex;
            flex-direction: row;
            min-height: 100vh;
        }

        .sidebar {
            background-color: #343a40;
            color: #fff;
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 1rem;
            overflow-y: auto;
        }

        .sidebar a {
            color: #ccc;
            display: block;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }

        .content-wrapper {
            margin-left: 250px;
            padding-top: 2rem;
            width: 100%;
            overflow-y: auto;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        main {
            padding: 2rem;
            height: 100%;
            overflow-y: auto;
        }
    </style>
</head>

<body>

    <div class="sidebar p-3">
        <h4 class="text-white mb-4">Gudang</h4>
        <a href="{{ route('pegawai.gudang') }}"><i class="fas fa-boxes"></i> Barang Titipan</a>
        <a href="{{ route('penitipan.index') }}"><i class="fas fa-clipboard-list"></i> Penitipan</a>
        <a href="{{ route('gudang.jadwalPengembalian') }}"><i class="fas fa-undo"></i> Konfirmasi Pengembalian</a>
        <a href="{{ route('gudang.jadwalPengiriman') }}"><i class="fas fa-truck"></i> Jadwal Pengiriman &
            Pengambilan</a>
        <a href="{{ route('gudang.konfirmasiPengambilan') }}"><i class="fas fa-check-circle"></i> Konfirmasi
            Pengambilan</a>
        <a href="{{ route('gudang.listTransaksi') }}"><i class="fas fa-check-circle"></i> List Transaksi</a>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="content-wrapper">
        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')

</body>

</html>
