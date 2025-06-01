<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pembeli | ReuseMart')</title>

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-success fw-bold" href="#">ReuseMart - Pembeli</a>
            <div class="ms-auto">
                <a class="nav-link d-inline text-dark" href="/">Beranda</a>
                <a class="nav-link d-inline text-dark" href="{{ route('profil.pembeli') }}">Profil</a>
            </div>
        </div>
    </nav>

    {{-- Konten --}}
    <main class="container my-5">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white text-center py-4 border-top">
        <small class="text-muted">&copy; {{ date('Y') }} ReuseMart. All rights reserved.</small>
    </footer>

</body>

</html>
