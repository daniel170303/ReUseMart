<!-- resources/views/login/login.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Login page for ReuseMart. Access your account to manage orders and profile.">
  <meta name="robots" content="noindex, nofollow">
  <title>Login - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background-color: #F5F5F5;
      font-family: 'Poppins', sans-serif;
    }
    .login-card {
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
    .login-image {
      border-radius: 0 10px 10px 0;
      object-fit: cover;
    }
    @media (max-width: 768px) {
      .login-image {
        border-radius: 0 0 10px 10px;
      }
    }
    .role-selector {
      margin-bottom: 1rem;
    }
    .role-icon {
      font-size: 1.5rem;
      margin-right: 0.5rem;
      color: #2E7D32;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
      <div class="col-md-10">
        <div class="login-card overflow-hidden">
          <div class="row g-0">
            <div class="col-md-6">
              <div class="p-5">
                <div class="mb-4 text-center">
                  <a href="/" class="d-inline-block">
                    <h4 class="text-success fw-bold mb-0">
                      <i class="fas fa-recycle me-2"></i>ReuseMart
                    </h4>
                  </a>
                  <p class="text-muted mt-2">Masuk ke akun Anda</p>
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

                <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
                  @csrf

                  <!-- Role Selection -->
                  <div class="mb-4">
                    <label for="role" class="form-label">Login sebagai</label>
                    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                      <option value="" disabled selected>-- Pilih Role --</option>
                      <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                        Admin
                      </option>
                      <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>
                        Pegawai
                      </option>
                      <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>
                        Owner
                      </option>
                      <option value="gudang" {{ old('role') == 'gudang' ? 'selected' : '' }}>
                        Gudang
                      </option>
                      <option value="cs" {{ old('role') == 'cs' ? 'selected' : '' }}>
                        Customer Service
                      </option>
                      <option value="penitip" {{ old('role') == 'penitip' ? 'selected' : '' }}>
                        Penitip
                      </option>
                      <option value="pembeli" {{ old('role') == 'pembeli' ? 'selected' : '' }}>
                        Pembeli
                      </option>
                      <option value="organisasi" {{ old('role') == 'organisasi' ? 'selected' : '' }}>
                        Organisasi
                      </option>
                    </select>
                    @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <!-- Email -->
                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan email" required>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>

                  <!-- Password -->
                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                      <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan password" required>
                      <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                      </button>
                      @error('password')
                      <div class="invalid-feedback">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>

                  <!-- Remember Me -->
                  <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Ingat Saya</label>
                  </div>

                  <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Login</button>
                  </div>
                </form>

                <div class="mt-4 text-center">
                  <p class="mb-0">Belum memiliki akun? <a href="{{ route('register') }}" class="text-success fw-bold">Daftar</a></p>
                </div>
              </div>
            </div>
            <div class="col-md-6 d-none d-md-block">
              <img src="{{ asset('images/GambarLogin.png') }}" alt="Login Image" class="w-100 h-100 login-image">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');
      
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
    
    // Add role icons based on selection
    document.getElementById('role').addEventListener('change', function() {
      const selectedRole = this.value;
      const roleIcon = document.createElement('i');
      roleIcon.className = 'role-icon';
      
      // Set icon based on role
      switch (selectedRole) {
        case 'admin':
          roleIcon.className = 'fas fa-user-shield role-icon';
          break;
        case 'pegawai':
          roleIcon.className = 'fas fa-user-tie role-icon';
          break;
        case 'owner':
          roleIcon.className = 'fas fa-crown role-icon';
          break;
        case 'gudang':
          roleIcon.className = 'fas fa-warehouse role-icon';
          break;
        case 'cs':
          roleIcon.className = 'fas fa-headset role-icon';
          break;
        case 'penitip':
          roleIcon.className = 'fas fa-box-open role-icon';
          break;
        case 'pembeli':
          roleIcon.className = 'fas fa-shopping-bag role-icon';
          break;
        case 'organisasi':
          roleIcon.className = 'fas fa-building role-icon';
          break;
      }
      
      // Update form validation styles
      if (this.value) {
        this.classList.add('is-valid');
      } else {
        this.classList.remove('is-valid');
      }
    });
    
    // Bootstrap form validation
    (function () {
      'use strict'
      const forms = document.querySelectorAll('.needs-validation')
      
      Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()
          }
          
          form.classList.add('was-validated')
        }, false)
      })  
    })()
  </script>
</body>
</html>