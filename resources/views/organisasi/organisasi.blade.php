<!-- resources/views/layouts/organisasi.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #81C784;
            --light-color: #E8F5E9;
            --dark-color: #1B5E20;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0,0,0,.05);
            width: 250px;
            background-color: #fff;
        }
        
        .sidebar-heading {
            padding: 1rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            background-color: var(--light-color);
        }
        
        .sidebar .nav-link {
            color: #212121;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            border-left: 4px solid transparent;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .sidebar .nav-link.active {
            background-color: var(--light-color);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 80px;
        }
        
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 99;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,.05);
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }
        
        .card.border-left-primary {
            border-left: 4px solid var(--primary-color) !important;
        }
        
        .card.border-left-warning {
            border-left: 4px solid #ffc107 !important;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-color);
            border-color: var(--dark-color);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
                padding-top: 20px;
            }
            .navbar {
                position: relative;
                left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-heading d-flex align-items-center">
            <i class="fas fa-recycle me-2"></i>
            <span>ReuseMart</span>
        </div>
        
        <div class="nav flex-column">
            <a href="{{ route('organisasi.dashboard') }}" class="nav-link {{ request()->routeIs('organisasi.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('organisasi.requests') }}" class="nav-link {{ request()->routeIs('organisasi.requests*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart"></i>
                <span>Donation Requests</span>
            </a>
            
            <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                <span>My Profile</span>
            </a>
            
            <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </nav>

    <!-- Top Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <button type="button" class="btn d-md-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                
                <div class="dropdown">
                    <div class="d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <img src="https://via.placeholder.com/32" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <span>{{ Auth::user()->name ?? 'User' }}</span>
                        <i class="fas fa-chevron-down ms-2 small"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form-nav').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form-nav" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('d-none');
        });
    </script>
    @stack('scripts')
</body>
</html>