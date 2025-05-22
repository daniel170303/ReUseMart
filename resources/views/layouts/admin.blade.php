<!-- resources/views/layouts/admin.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Admin ReuseMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2E7D32;
            --light-color: #E8F5E9;
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
        
        .profile-dropdown {
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
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
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            
            <a href="{{ route('admin.products') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Products</span>
            </a>
            
            <a href="{{ route('admin.transactions') }}" class="nav-link {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Transactions</span>
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
                
                <div class="dropdown profile-dropdown">
                    <div class="d-flex align-items-center" id="profileDropdown" data-bs-toggle="dropdown">
                        <img src="https://via.placeholder.com/32" alt="User" class="rounded-circle me-2" width="32" height="32">
                        <span>{{ Auth::user()->name ?? 'Admin' }}</span>
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
            document.querySelector('.sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>