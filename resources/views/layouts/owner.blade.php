<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Owner | ReuseMart')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: white;
            border-right: 1px solid #dee2e6;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            overflow-y: auto;
        }

        .main-content {
            margin-left: 250px;
            min-height: 100vh;
            background: #f8f9fa;
        }

        .nav-link:hover {
            background-color: #e9ecef !important;
        }

        .nav-link.active {
            background-color: #007bff !important;
            color: white !important;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body class="bg-light">

    {{-- Sidebar --}}
    <aside class="sidebar">
        <div class="d-flex flex-column h-100">
            <div class="text-center bg-success text-white py-4">
                <h5 class="mb-0">
                    <i class="fas fa-store me-2"></i>Menu Owner
                </h5>
            </div>

            <nav class="nav flex-column mt-3 flex-grow-1">
                @auth('pegawai')
                    <a href="{{ route('owner.profile', ['id' => Auth::guard('pegawai')->user()->id_pegawai]) }}"
                        class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle me-2"></i> Profile
                    </a>
                @endauth

                <a href="{{ route('owner.barang.donasi') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.barang.donasi') || request()->routeIs('owner.laporan.donasi') ? 'active' : '' }}">
                    <i class="fas fa-hand-holding-heart me-2"></i> Barang Donasi
                </a>

                <a href="{{ route('owner.laporanPenjualan') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanPenjualan') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i> Laporan Penjualan
                </a>

                <a href="{{ route('owner.laporanKomisi') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanKomisi') ? 'active' : '' }}">
                    <i class="fas fa-coins me-2"></i> Laporan Komisi
                </a>

                <a href="{{ route('owner.laporanStokGudang') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanStokGudang') ? 'active' : '' }}">
                    <i class="fas fa-boxes me-2"></i> Laporan Stok Gudang
                </a>

                <a href="{{ route('owner.laporanPenjualanPerKategori') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanPenjualanPerKategori') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar me-2"></i> Laporan Penjualan Per Kategori
                </a>

                <a href="{{ route('owner.laporanMasaPenitipanHabis') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanMasaPenitipanHabis') ? 'active' : '' }}">
                    <i class="fas fa-clock me-2"></i> Laporan Masa Penitipan Habis
                </a>

                <a href="{{ route('owner.laporanKomisiPerHunter') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporanKomisiPerHunter') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i> Laporan Komisi Hunter
                </a>

                <a href="{{ route('owner.laporan.donasi') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporan.donasi') ? 'active' : '' }}">
                    <i class="fas fa-heart me-2"></i> Laporan Donasi
                </a>

                <a href="{{ route('owner.laporan.request-donasi') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporan.request-donasi') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list me-2"></i> Laporan Request Donasi
                </a>

                <a href="{{ route('owner.laporan.transaksi-penitip') }}"
                    class="nav-link text-dark px-4 py-3 {{ request()->routeIs('owner.laporan.transaksi-penitip') ? 'active' : '' }}">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Laporan Transaksi Penitip
                </a>

                {{-- Logout sebagai menu nav-link --}}
                @auth('pegawai')
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="nav-link text-danger px-4 py-3 mt-auto">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </a>

                    {{-- Hidden form untuk logout --}}
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endauth
            </nav>

            <div class="text-center py-3 small text-muted border-top">
                &copy; {{ date('Y') }} ReuseMart
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content">
        <div class="container-fluid py-4">
            @yield('content')
        </div>
    </main>

    {{-- Scripts Section --}}
    @stack('scripts')

</body>

</html>
