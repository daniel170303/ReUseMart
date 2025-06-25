php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f8fafc;
        }
        .navbar {
            background: linear-gradient(90deg, #4e54c8 0%, #8f94fb 100%);
            box-shadow: 0 2px 8px rgba(78,84,200,0.1);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .nav-link.active, .nav-link:hover {
            color: #ffd700 !important;
        }
        main {
            padding: 2rem 0;
            min-height: 70vh;
        }
        footer {
            background: #4e54c8;
            color: #fff;
            text-align: center;
            padding: 1rem 0;
            margin-top: 2.5rem;
            letter-spacing: 1px;
        }
        .navbar-brand img {
            height: 32px;
            margin-right: .5rem;
        }
    </style>
</head>
<body>
    <div id="app">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <img src="https://img.icons8.com/color/36/000000/recycle.png" alt="Logo">
                    ReUseMart
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer>
            &copy; {{ date('Y') }} ReUseMart. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>