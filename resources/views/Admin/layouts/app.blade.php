<!-- resources/views/admin/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin ReuseMart</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #2E7D32;
            --secondary-color: #81C784;
            --accent-color: #FFEB3B;
            --dark-color: #1B5E20;
            --light-color: #E8F5E9;
            --text-color: #212121;
            --text-light: #757575;
            --white: #FFFFFF;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
            color: var(--text-color);
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 0;
            box-shadow: 0 2px 5px 0 rgba(0,0,0,.05);
            width: 250px;
            background-color: var(--white);
        }
        
        .sidebar-heading {
            padding: 1rem 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            background-color: var(--light-color);
        }
        
        .sidebar .nav-link {
            color: var(--text-color);
            padding: 0.75rem 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            border-left: 4px solid transparent;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-link:hover {
            background-color: var(--light-color);
            color: var(--primary-color);
        }
        
        .sidebar .nav-link.active {
            background-color: var(--light-color);
            color: var(--primary-color);
            border-left: 4px solid var(--primary-color);
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
            padding-top: 80px;
        }
        
        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            right: 0;
            left: 250px;
            z-index: 99;
            background-color: var(--white);
            box-shadow: 0 2px 5px rgba(0,0,0,.05);
            padding: 0.5rem 1rem;
        }
        
        .user-profile img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid #eee;
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header i {
            margin-right: 10px;
            color: var(--primary-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Stat Cards */
        .stat-card {
            padding: 20px;
            border-radius: 10px;
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            transition: all 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,.1);
        }
        
        .stat-card-icon {
            font-size: 3rem;
            opacity: 0.8;
        }
        
        .stat-card-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-card-title {
            font-size: 1rem;
            opacity: 0.8;
        }
        
        .bg-one {
            background: linear-gradient(45deg, #2E7D32, #4CAF50);
        }
        
        .bg-two {
            background: linear-gradient(45deg, #1565C0, #42A5F5);
        }
        
        .bg-three {
            background: linear-gradient(45deg, #6A1B9A, #9C27B0);
        }
        
        .bg-four {
            background: linear-gradient(45deg, #C62828, #EF5350);
        }
        
        .bg-five {
            background: linear-gradient(45deg, #EF6C00, #FF9800);
        }
        
        /* Button Actions */
        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
        }
        
        /* Table Styles */
        .table-action-col {
            width: 120px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar .nav-link span {
                display: none;
            }
            
            .sidebar .nav-link i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .sidebar-heading {
                padding: 1rem 0.5rem;
                font-size: 0;
            }
            
            .sidebar-heading i {
                font-size: 1.5rem;
                display: block;
                text-align: center;
            }
            
            .main-content, .navbar {
                margin-left: 70px;
                left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding-top: 60px;
                padding-left: 10px;
                padding-right: 10px;
            }
            
            .navbar {
                left: 0;
                margin-left: 0;
            }
            
            .sidebar {
                margin-left: -70px;
            }
            
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-heading d-flex align-items-center">
            <i class="fas fa-recycle"></i>
            <span class="ms-2">ReuseMart</span>
        </div>
        
        <div class="nav flex-column">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            
            <a href="{{ route('admin.pegawai.index') }}" class="nav-link {{ request()->routeIs('admin.pegawai*') ? 'active' : '' }}">
                <i class="fas fa-users"></i>
                <span>Pegawai</span>
            </a>
            
            <a href="{{ route('admin.penitip.index') }}" class="nav-link {{ request()->routeIs('admin.penitip*') ? 'active' : '' }}">
                <i class="fas fa-user-tag"></i>
                <span>Penitip</span>
            </a>
            
            <a href="{{ route('admin.pembeli.index') }}" class="nav-link {{ request()->routeIs('admin.pembeli*') ? 'active' : '' }}">
                <i class="fas fa-user-check"></i>
                <span>Pembeli</span>
            </a>
            
            <a href="{{ route('admin.organisasi.index') }}" class="nav-link {{ request()->routeIs('admin.organisasi*') ? 'active' : '' }}">
                <i class="fas fa-building"></i>
                <span>Organisasi</span>
            </a>
            
            <a href="{{ route('admin.barang.index') }}" class="nav-link {{ request()->routeIs('admin.barang*') ? 'active' : '' }}">
                <i class="fas fa-box"></i>
                <span>Barang Titipan</span>
            </a>
            
            <a href="{{ route('admin.transaksi.index') }}" class="nav-link {{ request()->routeIs('admin.transaksi*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i>
                <span>Transaksi</span>
            </a>
            
            <a href="{{ route('admin.request.index') }}" class="nav-link {{ request()->routeIs('admin.request*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-heart"></i>
                <span>Request Donasi</span>
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
        <div class="d-flex justify-content-between w-100">
            <button type="button" class="btn btn-link d-md-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <div class="d-flex align-items-center user-profile">
                <span class="me-2">{{ Auth::user()->name ?? 'Admin' }}</span>
                <img src="https://via.placeholder.com/32" alt="User Profile">
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Toggle Sidebar on Mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('show');
        });
        
        // Initialize DataTables
        $(document).ready(function() {
            $('.datatable').DataTable({
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>