<!-- resources/views/layouts/pembeli.blade.php -->
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
            --light-color: #E8F5E9;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .top-navbar {
            background-color: var(--primary-color);
            padding: 0.75rem 1rem;
            color: white;
        }
        
        .sidebar {
            background-color: white;
            min-height: calc(100vh - 60px);
            border-right: 1px solid #eee;
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--light-color);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .content-area {
            padding: 20px;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 20px;
        }
        
        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <a href="/" class="text-white text-decoration-none fw-bold fs-4">
                    <i class="fas fa-recycle me-2"></i>ReuseMart
                </a>
                
                <div class="dropdown">
                    <div class="d-flex align-items-center" id="userDropdown" data-bs-toggle="dropdown" style="cursor: pointer;">
                        <img src="https://via.placeholder.com/40" alt="User" class="profile-img me-2">
                        <span>{{ Auth::user()->name }}</span>
                        <i class="fas fa-chevron-down ms-2 small"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i> My Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 p-0 sidebar">
                <div class="nav flex-column p-2">
                    <a href="{{ route('pembeli.dashboard') }}" class="nav-link {{ request()->routeIs('pembeli.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="{{ route('pembeli.products') }}" class="nav-link {{ request()->routeIs('pembeli.products*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-bag"></i> Shop Products
                    </a>
                    <a href="{{ route('pembeli.transactions') }}" class="nav-link {{ request()->routeIs('pembeli.transactions*') ? 'active' : '' }}">
                        <i class="fas fa-shopping-cart"></i> My Orders
                    </a>
                    <a href="{{ route('profile.show') }}" class="nav-link {{ request()->routeIs('profile.show') ? 'active' : '' }}">
                        <i class="fas fa-user"></i> My Profile
                    </a>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="col-md-9 col-lg-10 content-area">
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>