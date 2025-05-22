<!-- resources/views/landingPage/landingPage.blade.php -->
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ReuseMart - Platform Barang Bekas Berkualitas</title>
    <meta name="description"
        content="ReuseMart adalah platform jual beli barang bekas berkualitas yang mendukung ekonomi sirkular dan pengurangan limbah.">
    <!-- CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Owl Carousel -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

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
            color: var(--text-color);
            background-color: #F5F5F5;
        }

        /* Header & Navbar */
        .navbar {
            padding: 8px 0;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
        }

        .navbar-nav .nav-link {
            color: var(--text-color);
            font-weight: 500;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-color);
        }

        .search-bar {
            max-width: 600px;
            width: 100%;
        }

        .search-input {
            border-radius: 20px 0 0 20px;
            border-right: none;
        }

        .search-button {
            border-radius: 0 20px 20px 0;
            background-color: var(--primary-color);
            color: white;
            border: 1px solid var(--primary-color);
        }

        .btn-login {
            background-color: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 50px;
            padding: 6px 15px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-login:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }

        .btn-signup {
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 50px;
            padding: 6px 15px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-signup:hover {
            background-color: var(--dark-color);
        }

        /* Banner Carousel */
        .banner-carousel {
            margin-top: 76px;
            /* Untuk menyesuaikan navbar fixed-top */
        }

        .banner-carousel .carousel-item img {
            height: 300px;
            object-fit: cover;
        }

        /* Categories */
        .category-section {
            padding: 20px 0;
            background-color: var(--white);
        }

        .category-item {
            text-align: center;
            padding: 15px 10px;
            transition: all 0.3s;
            border-radius: 10px;
        }

        .category-item:hover {
            background-color: var(--light-color);
            transform: translateY(-5px);
        }

        .category-icon {
            width: 50px;
            height: 50px;
            background-color: var(--light-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            font-size: 1.5rem;
            color: var(--primary-color);
        }

        .category-name {
            font-size: 0.9rem;
            font-weight: 500;
            margin: 0;
        }

        /* Product Section */
        .products-section {
            padding: 30px 0;
        }

        .section-title {
            margin-bottom: 20px;
            font-weight: 700;
            color: var(--text-color);
        }

        .section-subtitle {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .product-card {
            background-color: var(--white);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            margin-bottom: 25px;
            height: 100%;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            height: 180px;
            overflow: hidden;
            position: relative;
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.5s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--accent-color);
            color: var(--text-color);
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .product-body {
            padding: 15px;
        }

        .product-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.4;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: var(--text-light);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .product-rating {
            color: #FFC107;
        }

        .btn-view {
            background-color: var(--primary-color);
            color: var(--white);
            border-radius: 4px;
            padding: 6px 12px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            width: 100%;
            margin-top: 5px;
        }

        .btn-view:hover {
            background-color: var(--dark-color);
            color: var(--white);
        }

        /* Button More */
        .btn-more {
            background-color: var(--white);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 30px;
            padding: 8px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-more:hover {
            background-color: var(--primary-color);
            color: var(--white);
        }

        /* Footer */
        .footer {
            padding: 50px 0 20px;
            background-color: #212121;
            color: rgba(255, 255, 255, 0.8);
        }

        .footer-brand {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: var(--white);
        }

        .footer-text {
            margin-bottom: 20px;
        }

        .footer h5 {
            color: var(--white);
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer-links {
            list-style: none;
            padding-left: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .footer-links a:hover {
            color: var(--white);
            padding-left: 5px;
        }

        .social-icons {
            margin-bottom: 20px;
        }

        .social-icons a {
            display: inline-block;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--white);
            text-align: center;
            line-height: 40px;
            margin-right: 10px;
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 40px;
            text-align: center;
            font-size: 0.9rem;
        }

        .empty-state {
            padding: 40px 0;
            text-align: center;
        }

        .empty-state i {
            font-size: 5rem;
            color: var(--secondary-color);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .search-bar {
                margin: 10px 0;
            }
        }

        @media (max-width: 768px) {
            .banner-carousel .carousel-item img {
                height: 200px;
            }
        }

        @media (max-width: 576px) {
            .banner-carousel .carousel-item img {
                height: 150px;
            }

            .category-section .row {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
        <div class="container">
            <a class="navbar-brand text-success" href="/">
                <i class="fas fa-recycle me-2"></i>ReuseMart
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search Bar -->
                <form class="d-flex search-bar mx-auto">
                    <input class="form-control search-input" type="search"
                        placeholder="Cari produk bekas berkualitas..." aria-label="Search">
                    <button class="btn search-button" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-heart"></i>
                            <span class="d-lg-none ms-2">Wishlist</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="d-lg-none ms-2">Keranjang</span>
                        </a>
                    </li>
                </ul>

                <div class="d-flex">
                    <a href="{{ route('login') }}" class="btn btn-login me-2">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-signup">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Banner Carousel -->
    <div id="bannerCarousel" class="carousel slide banner-carousel" data-bs-ride="carousel">
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="0" class="active"
                aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#bannerCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://via.placeholder.com/1200x300/2E7D32/FFFFFF?text=ReuseMart+-+Barang+Bekas+Berkualitas"
                    class="d-block w-100" alt="Banner 1">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Barang Bekas Berkualitas</h5>
                    <p>Temukan barang bekas berkualitas dengan harga terjangkau</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://via.placeholder.com/1200x300/1B5E20/FFFFFF?text=Hemat+Hingga+70%+dari+Harga+Baru"
                    class="d-block w-100" alt="Banner 2">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Hemat Hingga 70% dari Harga Baru</h5>
                    <p>Kualitas terjamin dengan harga yang lebih terjangkau</p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="https://via.placeholder.com/1200x300/81C784/FFFFFF?text=Donasi+untuk+Organisasi"
                    class="d-block w-100" alt="Banner 3">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Donasi untuk Organisasi</h5>
                    <p>Mari bantu organisasi yang membutuhkan barang donasi</p>
                </div>
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#bannerCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#bannerCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <!-- Category Section -->
    <section class="category-section">
        <div class="container">
            <div class="row">
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-laptop"></i>
                        </div>
                        <p class="category-name">Elektronik</p>
                    </div>
                </div>
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <p class="category-name">Fashion</p>
                    </div>
                </div>
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <p class="category-name">Furniture</p>
                    </div>
                </div>
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <p class="category-name">Buku</p>
                    </div>
                </div>
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-gamepad"></i>
                        </div>
                        <p class="category-name">Gaming</p>
                    </div>
                </div>
                <div class="col-2 col-md">
                    <div class="category-item">
                        <div class="category-icon">
                            <i class="fas fa-bicycle"></i>
                        </div>
                        <p class="category-name">Olahraga</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section">
        <div class="container">
            <h2 class="section-title">Produk Terbaru</h2>
            <p class="section-subtitle">Temukan barang bekas berkualitas dengan harga terjangkau</p>

            <div class="row">
                @forelse($barangTitipan as $index => $barang)
                    <div class="col-6 col-md-3 mb-4">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="{{ $barang->gambar_barang ? asset('storage/' . $barang->gambar_barang) : asset('images/default-product.jpg') }}"
                                    alt="{{ $barang->nama_barang_titipan }}">
                                <div class="product-badge">{{ $barang->jenis_barang }}</div>
                            </div>
                            <div class="product-body">
                                <h5 class="product-title">{{ $barang->nama_barang_titipan }}</h5>
                                <div class="product-price">Rp {{ number_format($barang->harga_barang, 0, ',', '.') }}
                                </div>

                                <div class="product-meta">
                                    <div>
                                        <i class="fas fa-weight me-1"></i> {{ $barang->berat_barang }} gr
                                    </div>
                                    <div class="product-rating">
                                        <i class="fas fa-star"></i> {{ number_format(rand(35, 50) / 10, 1) }}
                                    </div>
                                </div>

                                <a href="{{ url('/barang/' . $barang->id_barang) }}" class="btn btn-view">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-box-open"></i>
                            <h3>Belum Ada Produk</h3>
                            <p>Saat ini belum ada produk yang tersedia di ReuseMart.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary">Jadi Penitip Pertama</a>
                        </div>
                    </div>
                @endforelse
            </div>

            @if (count($barangTitipan) > 0)
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-more">Lihat Semua Produk <i
                            class="fas fa-arrow-right ms-2"></i></a>
                </div>
            @endif
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="footer-brand">
                        <i class="fas fa-recycle me-2"></i>ReuseMart
                    </div>
                    <p class="footer-text">ReuseMart adalah platform yang menghubungkan penitip barang bekas
                        berkualitas dengan pembeli yang peduli terhadap keberlanjutan lingkungan.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>

                <div class="col-md-2 mb-4">
                    <h5>Kategori</h5>
                    <ul class="footer-links">
                        <li><a href="#">Elektronik</a></li>
                        <li><a href="#">Fashion</a></li>
                        <li><a href="#">Furniture</a></li>
                        <li><a href="#">Buku</a></li>
                        <li><a href="#">Gaming</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5>Layanan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Penitipan Barang</a></li>
                        <li><a href="#">Pembelian Barang</a></li>
                        <li><a href="#">Donasi Barang</a></li>
                        <li><a href="#">Pengiriman</a></li>
                        <li><a href="#">Garansi Produk</a></li>
                    </ul>
                </div>

                <div class="col-md-3 mb-4">
                    <h5>Bantuan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Cara Belanja</a></li>
                        <li><a href="#">Cara Menitipkan Barang</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Kebijakan Privasi</a></li>
                        <li><a href="#">Hubungi Kami</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} ReuseMart. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
</body>

</html>
