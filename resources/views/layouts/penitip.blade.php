<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Penitip</title>

    {{-- BOOTSTRAP --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- FONT AWESOME --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- GOOGLE FONTS --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    
    {{-- CUSTOM CSS --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body {
            display: flex;
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            margin: 0;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 240px;
            background-color: #212529;
            color: white;
            padding: 30px 20px;
        }

        .sidebar h4 {
            margin-bottom: 30px;
            font-size: 20px;
            font-weight: bold;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .sidebar a {
            display: block;
            color: #f8f9fa;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #495057;
            padding-left: 20px;
        }

        .content {
            flex-grow: 1;
            padding: 40px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>Menu Penitip</h4>
        <ul>
            <li>
                <a href="{{ url('penitip/profile/' . session('user_id')) }}"
                    class="{{ request()->is('penitip/profile/' . session('user_id')) ? 'active' : '' }}">
                    <i class="fas fa-user"></i> Profile
                </a>
            </li>
            <li>
                <a href="{{ url('penitip/' . session('user_id') . '/barang-titipan') }}"
                    class="{{ request()->is('penitip/' . session('user_id') . '/barang-titipan') ? 'active' : '' }}">
                    <i class="fas fa-box-open"></i> Barang Titipan
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>

    <div class="content">
        @yield('content')
    </div>

    {{-- BOOTSTRAP SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>

</html>