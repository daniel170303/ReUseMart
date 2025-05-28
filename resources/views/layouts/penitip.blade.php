<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Penitip</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: 'Inter', sans-serif;
            background-color: #f1f3f5;
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
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <h4>Menu Penitip</h4>
        <ul>
            <li>
                <a href="{{ url('penitip/profile/11') }}"
                    class="{{ request()->is('penitip/profile/11') ? 'active' : '' }}">
                    Profil
                </a>
            </li>
            <li>
                <a href="{{ url('penitip/11/barang-titipan') }}"
                    class="{{ request()->is('penitip/11/barang-titipan') ? 'active' : '' }}">
                    Barang Titipan
                </a>
            </li>
            <li>
                <a href="#" class="{{ request()->is('riwayat') ? 'active' : '' }}">
                    Riwayat
                </a>
            </li>
        </ul>
    </div>

    <div class="content">
        @yield('content')
    </div>
</body>

</html>
