<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gudang - Barang Titipan</title>

    {{-- Bootstrap CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome (opsional untuk ikon) --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <style>
        body {
            background-color: #f8f9fa;
        }

        .navbar-brand {
            font-weight: bold;
        }

        .sidebar {
            background-color: #343a40;
            color: #fff;
            height: 100vh;
            padding-top: 1rem;
        }

        .sidebar a {
            color: #ccc;
            display: block;
            padding: 0.75rem 1.25rem;
            text-decoration: none;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: #fff;
        }
    </style>
</head>

<body>

    <div class="d-flex">
        {{-- Sidebar --}}
        <div class="sidebar p-3">
            <h4 class="text-white mb-4">Gudang</h4>
            <a href="{{ route('pegawai.gudang') }}"><i class="fas fa-boxes"></i> Barang Titipan</a>
            <a href="{{ route('penitipan.index') }}"><i class="fas fa-clipboard-list"></i> Penitipan</a>
            <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>

        {{-- Content --}}
        <div class="flex-grow-1">
            {{-- Navbar --}}
            <nav class="navbar navbar-light bg-light">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1">Sistem Gudang</span>
                </div>
            </nav>

            <main class="p-4">
                @yield('content')
            </main>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Tambahan JS --}}
    @yield('scripts')

</body>

</html>
