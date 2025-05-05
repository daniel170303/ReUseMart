<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{asset('images/BOOKHIVE_LOGOONLY.png')}}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=YourNewFont:wght@900;1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- ===== ANimasi===== --}}
    <link href="https://cdn.jsdelivr.net/gh/yesiamrocks/cssanimation.io@1.0.3/cssanimation.min.css" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <title>ReUseMart</title>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 0;
        }

        .hero-section {
            padding: 0;
        }

        .hero-text h2 {
            font-size: 60px;
            font-weight: 700;   
            color: #2C3761;
            margin-bottom: 10px;
            line-height: 1.2;
        }

        .hero-text h1 {
            font-size: 105px;
            font-weight: 1000;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-text .highlight {
            color: #EDB83E;
        }

        .hero-text .normal {
            color: #2C3761;
        }

        .hero-text p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .hero-buttons .btn {
            padding: 10px 20px;
            font-size: 16px;
            margin-right: 20px;
            margin-top: 2rem;
        }

        .hero-buttons .btn-primary {
            background-color: #5392FB;
            border-color: #5392FB;
            color: #fff;
            width: 150px;
        }

        .hero-buttons .btn-outline-primary {
            color: #5392FB;
            border-color: #5392FB;
            background-color: white;
            width: 150px;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
        }

        .navbar img {
            height: 30px;
        }

        .navbar-nav .nav-link {
            font-size: 1rem;
            font-weight: bold;
            margin-right: 20px;
            z-index: 1000;
        }
        
        section {
            margin-top: 40px;
            margin-bottom: 20px;
        }
        .section-title {
            font-weight: bold;
        }

        .card img {
            height: 300px;
            object-fit: cover;
        }

        .card-layanan {
            border-radius: 10px;
        }
        .card-layanan img {
            width: round;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .card-custom {
            background-color: #EDB83E;
        }

        .card-custom img {
            padding: 15px;
            height: 250px;
            border-radius: 20px;
        }

        .card-custom .card-text {
            color: white;
        }
        
        .hero-section {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 2rem 1rem;
            background-image: url('{{ asset('images/BackgroundLanding.png') }}');
            background-size: 64% auto; 
            background-position: top right; 
            background-repeat: no-repeat; 
            min-height: 100vh;
            margin: 0;
        }

        .hero-text h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2C3761;
        }
        .hero-text h1 {
            font-size: 3rem; 
            font-weight: 1000;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1rem;
            line-height: 1.5;
        }

        .hero-buttons .btn {
            padding: 10px 20px;
            font-size: 14px;
            margin-top: 1rem;
            width: auto;
        }
        footer {
            background-color: #2C3761;
        }

        @media (max-width: 800px) {
            .hero-section {
                background-image: none;
                background-color: #5392FB; 
            }

            .hero-buttons .btn-primary {
                background-color: #5392FB;
                border-color: white;
                border-width: 3px;
                color: #fff;
                width: 150px;
            }

            .hero-buttons .btn-outline-primary {
                color: #5392FB;
                border-color: white;
                background-color: white;
                width: 150px;
            }
        }


        @media (min-width: 768px) {
            .hero-section {
                flex-direction: row; 
                text-align: left;
                padding: 4rem;
                
                background-size: 100% auto;
                background-position: right top;
            }

            .hero-text {
                flex: 1;
            }

            .hero-text h2 {
                font-size: 3rem; 
            }

            .hero-text h1 {
                font-size: 4.5rem;
            }

            .hero-buttons .btn {
                font-size: 16px;
                padding: 12px 24px;
                width: 150px;
            }
        }

        @media (min-width: 1200px) {
            .hero-section {
                background-size: 64% auto;
            }

            .hero-text h1 {
                font-size: 6rem; 
            }
            .hero-text p {
                width: 50%;
            }
        }

        @media (max-width: 1200px) {
            .hero-section {
                background-size: 50% auto;
            }
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            margin-top: 1rem;
        }

    </style>
</head>

<body>
    
    <section class="hero-section m-0 ">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-5 mt-0">
                
                <div class="logo-container pt-0">
                    <img src="{{asset('images/BOOKHIVE_HORIZONTAL.png')}}" alt="Logo" style="height: 3rem;">
                </div>
                <div class="navbar pt-0">
                   
                        <ul class="navbar-nav d-flex flex-row ">
                            <li class="nav-item ">
                                <a class="nav-link text-light me-5 fw-light" href="#bahan-pustaka">Bahan Pustaka</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-light me-5 fw-light" href="#layanan">Layanan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-light fw-light" href="#informasi">Informasi</a>
                            </li>
                        </ul>
                        
                    </div>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-lg-6 hero-text mt-5 ">
                        <div class="cssanimation pushReleaseFromLeft">

                            <h2>Jembatani Ilmu, <br> Jelajahi Dunia Melalui</h2>
                        </div>
                        <h1 class="cssanimation hu__hu__"><span class="highlight">BOOK</span><span class="normal">HIVE</span></h1>
                        <p class="mt-4 mb-5 cssanimation pushReleaseFromLeft" >BookHive, sistem manajemen perpustakaan digital adalah tempat di mana setiap halaman membuka pintu ke pengetahuan dan imajinasi. Jelajahi koleksi buku yang kaya dan beragam, yang siap menginspirasi dan memperkaya wawasan Anda melalui buku-buku terbaik.</p>
                        <div class="hero-buttons mt-5">
                            <a href="{{ url('login-page') }}" class="btn btn-primary shadow">Login</a>
                            <a href="{{ url('register-page') }}" class="btn btn-outline-primary shadow border-3">Register</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="bahan-pustaka" class="my-5" >
        <div class="container">
            <h3 class="section-title">Buku Terbaik</h3>
            <div class="row d-flex justify-content-between">
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_ATOMIC.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_LAUT.jpeg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_SHERLOCK.jpeg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_ITENDS.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_LOTR.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="my-5">
        <div class="container">
            <h3 class="section-title">Buku Terlaris Bulan Ini</h3>
            <div class="row d-flex justify-content-between">
            <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_ATOMIC.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_LAUT.jpeg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_SHERLOCK.jpeg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_ITENDS.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
                <div class="col-md-2 my-2">
                    <div class="card">
                        <img src="{{ asset('images/SAMPUL_LOTR.jpg') }}" class="card-img-top" alt="Atomic Habits">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="my-5">
        <div class="container">
            <h3 class="section-title">Layanan Perpustakaan BookHive</h3>
            <div class="row">
                <div class="col-md-3 my-2">
                    <div class="card card-layanan shadow">
                        <img src="{{ asset('images/perpus1.jpg') }}" alt="Foto layanan" class="card-img-top">
                        <div class="card-body">
                            <h5>Pinjam Bahan Pustaka</h5>
                            <p>Pinjam bahan pustaka apapun yang kamu butuhkan, mulai dari buku, majalah, hingga publikasi ilmiah dengan mudah.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 my-2">
                    <div class="card card-layanan shadow">
                        <img src="{{ asset('images/perpus4.jpg') }}" alt="Foto layanan" class="card-img-top">
                        <div class="card-body">
                            <h5>Reservasi Ruangan</h5>
                            <p>Buat reservasi untuk ruang belajar di perpustakaan untuk pengalaman membaca dan bekerja yang lebih nyaman.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 my-2">
                    <div class="card card-layanan shadow">
                        <img src="{{ asset('images/perpus2.jpg') }}" alt="Foto layanan" class="card-img-top">
                        <div class="card-body">
                            <h5>Donasi Buku</h5>
                            <p>Punya buku yang tidak lagi terpakai? Donasikan sekarang juga agar orang lain dapat membaca apa yang kamu baca.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 my-2">
                    <div class="card card-layanan shadow">
                        <img src="{{ asset('images/perpus3.jpg') }}" alt="Foto layanan" class="card-img-top">
                        <div class="card-body">
                            <h5>Ajukan Rekomendasi</h5>
                            <p>Ingin membaca sesuatu namun belum tersedia di BookHive? Kamu dapat mengajukan rekomendasi bahan pustaka disini.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="informasi" class="my-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow card-custom">
                        <div class="row g-2">
                            <div class="col-3">
                                <img src="{{ asset('images/foto-perpus.jpg') }}" alt="Gambar Perpustakaan" class="img-fluid" style="height:100%; max-height: 18rem; width: auto;">
                            </div>
                            <div class="col-9">
                                <div class="card-body ps-0">
                                    <h2 class="card-text"><strong>Informasi Perpustakaan BookHive</strong></h2>
                                    <p class="card-text">Didirikan sejak 14 Oktober 2002 di Jalan Babarsari No 43, kini perpustakaan telah memiliki 2 gedung dengan ribuan koleksi bahan pustaka dengan berbagai macam kategori. BookHive juga menyediakan berbagai macam ruangan belajar yang dapat digunakan oleh pemustaka untuk kenyamanan belajar dan bekerja dalam kelompok.</p>
                                    <p class="card-text">
                                        <strong>Jam Operasional:</strong> Jam Operasional: Senin sampai Jumat pukul 08.00 WIB hingga 20.00 WIB, Sabtu dan Minggu tutup.
                                        Peminjaman dan reservasi dapat diajukan melalui web. Untuk layanan peminjaman dan pengembalian dilakukan secara langsung di perpustakaan. Pengajuan donasi dan rekomendasi dapat dilakukan melalui web maupun secara langsung di perpustakaan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <h5 class="mb-3">Tentang Kami</h5>
                    <p>Perpustakaan digital BookHive dengan 4 layanan utama berbasis web.</p>
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Perpustakaan Gedung 1</h5>
                    <p>Jalan Babarsari No 43, Depok Sleman, Daerah Istimewa Yogyakarta</p>
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Perpustakaan Gedung 2</h5>
                    <p>Jalan Babarsari No 43, Depok Sleman, Daerah Istimewa Yogyakarta</p>
                </div>
                <div class="col-md-2">
                    <h5 class="mb-3">Kontak Kami</h5>
                    <ul class="list-unstyled">
                        <li>Telepon : 123-123-123</li>
                        <li>Email        : lib@bookhive.ac.id</li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4 pt-3">
                <div class="col-md-6 d-flex align-items-center">
                    <img src="{{ asset('images/BOOKHIVE_HORIZONTAL.png') }}" height="30" class="me-2 bg-light" alt="BookHive Logo">
                    <p class="mb-0">© 2024 Perpustakaan Elektronik BookHive</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="#" class="text-light me-3">Privacy Policy</a>
                    <a href="#" class="text-light">Terms of Use</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    {{-- =====Animasi===== --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/gh/yesiamrocks/cssanimation.io@1.0.3/letteranimation.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script>
        AOS.init();
      </script>
</body>

</html>

