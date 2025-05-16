<!-- resources/views/register/register.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Register page for ReuseMart. Create your account to join our platform.">
  <meta name="robots" content="noindex, nofollow">
  <title>Register - ReuseMart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #F5F5F5;
      font-family: 'Poppins', sans-serif;
    }
    .register-card {
      background-color: #FFFFFF;
      border-radius: 10px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .form-control:focus, .form-select:focus {
      border-color: #2E7D32;
      box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
    }
    .btn-primary {
      background-color: #2E7D32;
      border-color: #2E7D32;
    }
    .btn-primary:hover {
      background-color: #1B5E20;
      border-color: #1B5E20;
    }
    .register-image {
      border-radius: 0 10px 10px 0;
      object-fit: cover;
    }
    .role-selector {
      display: flex;
      margin-bottom: 20px;
    }
    .role-option {
      flex: 1;
      text-align: center;
      padding: 15px;
      border: 2px solid #ddd;
      border-radius: 10px;
      cursor: pointer;
      margin: 0 10px;
      transition: all 0.3s;
    }
    .role-option:hover {
      border-color: #2E7D32;
    }
    .role-option.active {
      border-color: #2E7D32;
      background-color: rgba(46, 125, 50, 0.1);
    }
    .role-option i {
      font-size: 2rem;
      color: #2E7D32;
      margin-bottom: 10px;
    }
    .role-title {
      font-weight: 600;
      margin-bottom: 5px;
    }
    .role-desc {
      font-size: 0.85rem;
      color: #757575;
    }
    @media (max-width: 768px) {
      .register-image {
        display: none;
      }
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div class="register-card overflow-hidden">
          <div class="row g-0">
            <div class="col-md-7">
              <div class="p-5">
                <div class="mb-4 text-center">
                  <a href="/" class="d-inline-block">
                    <h4 class="text-success fw-bold mb-0">
                      <i class="fas fa-recycle me-2"></i>ReuseMart
                    </h4>
                  </a>
                  <p class="text-muted mt-2">Daftar akun baru</p>
                </div>

                <!-- Alert untuk errors -->
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                  @csrf
                  
                  <!-- Role selector -->
                  <div class="mb-4">
                    <label class="form-label">Daftar Sebagai</label>
                    <div class="role-selector">
                      <div class="role-option active" data-role="pembeli">
                        <i class="fas fa-user"></i>
                        <div class="role-title">Pembeli</div>
                        <div class="role-desc">Beli barang bekas berkualitas</div>
                      </div>
                      <div class="role-option" data-role="organisasi">
                        <i class="fas fa-building"></i>
                        <div class="role-title">Organisasi</div>
                        <div class="role-desc">Terima donasi barang</div>
                      </div>
                    </div>
                    <input type="hidden" name="role_type" id="role_type" value="pembeli">
                  </div>

                  <!-- Form untuk Pembeli -->
                  <div id="pembeli-form">
                    <div class="mb-3">
                      <label for="nama_pembeli" class="form-label">Nama Lengkap</label>
                      <input type="text" id="nama_pembeli" name="nama_pembeli" class="form-control" value="{{ old('nama_pembeli') }}" required>
                    </div>
                    
                    <div class="mb-3">
                      <label for="alamat_pembeli" class="form-label">Alamat</label>
                      <textarea id="alamat_pembeli" name="alamat_pembeli" class="form-control" rows="2" required>{{ old('alamat_pembeli') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                      <label for="nomor_telepon_pembeli" class="form-label">Nomor Telepon</label>
                      <input type="text" id="nomor_telepon_pembeli" name="nomor_telepon_pembeli" class="form-control" value="{{ old('nomor_telepon_pembeli') }}" required>
                    </div>
                  </div>

                  <!-- Form untuk Organisasi -->
                  <div id="organisasi-form" style="display: none;">
                    <div class="mb-3">
                      <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                      <input type="text" id="nama_organisasi" name="nama_organisasi" class="form-control" value="{{ old('nama_organisasi') }}">
                    </div>
                    
                    <div class="mb-3">
                      <label for="alamat_organisasi" class="form-label">Alamat Organisasi</label>
                      <textarea id="alamat_organisasi" name="alamat_organisasi" class="form-control" rows="2">{{ old('alamat_organisasi') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                      <label for="nomor_telepon_organisasi" class="form-label">Nomor Telepon Organisasi</label>
                      <input type="text" id="nomor_telepon_organisasi" name="nomor_telepon_organisasi" class="form-control" value="{{ old('nomor_telepon_organisasi') }}">
                    </div>
                  </div>

                  <!-- Email & Password untuk kedua jenis akun -->
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
                  </div>
                  
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                  </div>
                  
                  <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                  </div>

                  <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Daftar</button>
                  </div>
                </form>

                <div class="mt-4 text-center">
                  <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-success fw-bold">Login</a></p>
                </div>
              </div>
            </div>
            <div class="col-md-5 d-none d-md-block">
              <img src="{{ asset('images/register.jpg') }}" alt="Register Image" class="w-100 h-100 register-image">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Role selector logic
    document.addEventListener('DOMContentLoaded', function() {
      const roleOptions = document.querySelectorAll('.role-option');
      const roleInput = document.getElementById('role_type');
      const pembeliForm = document.getElementById('pembeli-form');
      const organisasiForm = document.getElementById('organisasi-form');
      
      roleOptions.forEach(option => {
        option.addEventListener('click', function() {
          // Remove active class from all options
          roleOptions.forEach(el => el.classList.remove('active'));
          
          // Add active class to clicked option
          this.classList.add('active');
          
          // Update hidden input
          const selectedRole = this.getAttribute('data-role');
          roleInput.value = selectedRole;
          
          // Show/hide appropriate form
          if (selectedRole === 'pembeli') {
            pembeliForm.style.display = 'block';
            organisasiForm.style.display = 'none';
            
            // Make pembeli fields required
            document.getElementById('nama_pembeli').required = true;
            document.getElementById('alamat_pembeli').required = true;
            document.getElementById('nomor_telepon_pembeli').required = true;
            
            // Make organisasi fields not required
            document.getElementById('nama_organisasi').required = false;
            document.getElementById('alamat_organisasi').required = false;
            document.getElementById('nomor_telepon_organisasi').required = false;
          } else {
            pembeliForm.style.display = 'none';
            organisasiForm.style.display = 'block';
            
            // Make pembeli fields not required
            document.getElementById('nama_pembeli').required = false;
            document.getElementById('alamat_pembeli').required = false;
            document.getElementById('nomor_telepon_pembeli').required = false;
            
            // Make organisasi fields required
            document.getElementById('nama_organisasi').required = true;
            document.getElementById('alamat_organisasi').required = true;
            document.getElementById('nomor_telepon_organisasi').required = true;
          }
        });
      });
      
      // Form validation
      const form = document.querySelector('form');
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      });
    });
  </script>
</body>
</html>