<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Owner | ReuseMart')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Font Awesome (opsional) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container-fluid">
        <div class="row min-vh-100">

            {{-- Sidebar --}}
            <aside class="col-md-3 col-lg-2 bg-white border-end shadow-sm p-0"
                style="position: sticky; top: 0; height: 100vh;">

                <div class="d-flex flex-column h-100">
                    <div class="text-center bg-success text-white py-4">
                        <h5 class="mb-0">Menu Owner</h5>
                    </div>
                    <nav class="nav flex-column mt-3">
                        @auth('pegawai')
                            <a href="{{ route('owner.profile', ['id' => Auth::guard('pegawai')->user()->id_pegawai]) }}"
                                class="nav-link text-dark px-4 py-2">
                                <i class="fas fa-user-circle me-2"></i> Profile
                            </a>
                        @endauth
                        <a href="" class="nav-link text-dark px-4 py-2">
                            <i class="fas fa-hand-holding-heart me-2"></i> Barang Donasi
                        </a>

                        {{-- Logout sebagai menu nav-link --}}
                        @auth('pegawai')
                            <a href="#"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                class="nav-link text-danger px-4 py-2">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>

                            {{-- Hidden form untuk logout --}}
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        @endauth
                    </nav>

                    <div class="mt-auto text-center py-3 small text-muted">
                        &copy; {{ date('Y') }} ReuseMart
                    </div>
                </div>
            </aside>

            {{-- Main Content --}}
            <main class="col-md-9 col-lg-10 py-4 px-5">
                @yield('content')
            </main>

        </div>
    </div>

</body>

</html>
