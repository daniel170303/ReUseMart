<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReuseMart Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    <!-- Overlay (untuk mobile sidebar) -->
    <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden lg:hidden"></div>

    <!-- Sidebar -->
    <aside id="sidebar"
        class="fixed top-0 left-0 w-64 h-full bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <a href="/" class="text-green-600 text-xl font-bold">
                <i class="fas fa-recycle mr-2"></i>ReuseMart
            </a>
            <button id="sidebarClose" class="text-gray-600 hover:text-red-600 lg:hidden">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="mt-2 mb-2">
            <ul class="space-y-2 px-4">
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="block py-2 px-3 rounded hover:bg-green-100 text-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-green-200 font-semibold' : '' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.organisasi.index') }}"
                        class="block py-2 px-3 rounded hover:bg-green-100 text-gray-700 {{ request()->routeIs('admin.organisasi.*') ? 'bg-green-200 font-semibold' : '' }}">
                        <i class="fas fa-building mr-2"></i>Organisasi
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pegawai.index') }}"
                        class="block py-2 px-3 rounded hover:bg-green-100 text-gray-700 {{ request()->routeIs('admin.pegawai.*') ? 'bg-green-200 font-semibold' : '' }}">
                        <i class="fas fa-users mr-2"></i>Pegawai
                    </a>
                </li>
            </ul>
        </nav>
        <div class="px-4 py-4 mt-auto border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 bg-red-600 text-white rounded hover:bg-red-700">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="ml-0 lg:ml-64 p-4">
        @yield('content')
    </main>

    <!-- Script Toggle -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const closeBtn = document.getElementById('sidebarClose');
        const overlay = document.getElementById('overlay');

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        });

        closeBtn?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>

    {{-- Tambahkan ini --}}
    @yield('scripts')

</body>

</html>
