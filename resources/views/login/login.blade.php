<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Login page for ReuseMart. Access your account to manage orders and profile.">
  <meta name="robots" content="noindex, nofollow">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white font-sans">

  <div class="flex min-h-screen">
    <!-- Form Login -->
    <div class="w-full md:w-1/2 flex items-center justify-center px-6 lg:px-20">
      <div class="w-full max-w-md">
        <h2 class="text-3xl font-semibold text-gray-900 mb-2">Welcome back!</h2>
        <p class="text-gray-600 mb-6">Enter your credentials to access your account</p>

        <!-- Tempat pesan error -->
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div id="errorMessage" class="bg-red-100 text-red-700 p-4 mb-4 rounded hidden"></div>

        <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4">
          @csrf
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input type="email" name="email" id="email" required autofocus
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" id="password" required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>

          <!-- Tombol Login -->
          <div>
            <button type="submit" id="loginButton"
              class="w-full bg-green-700 text-white py-2 px-4 rounded hover:bg-green-800 transition font-semibold">
              Login
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Gambar -->
    <div class="hidden md:block md:w-1/2">
      <img src="{{ asset('images/GambarLogin.png') }}" alt="Login Illustration" class="w-full h-screen object-cover">
    </div>
  </div>

  <!-- Script JS Login (opsional untuk API) -->
  <script>
    const form = document.getElementById('loginForm');
    const button = document.getElementById('loginButton');
    const errorDiv = document.getElementById('errorMessage');

    // Jika ingin menggunakan API untuk login, uncomment kode di bawah

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      button.innerHTML = 'Loading...';
      button.disabled = true;
      errorDiv.classList.add('hidden');
      errorDiv.innerHTML = '';

      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      try {
        const response = await fetch('/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ email, password })
        });

        const data = await response.json();

        if (response.ok) {
          localStorage.setItem('token', data.token);
          localStorage.setItem('user', JSON.stringify(data.user));

          // Redirect ke dashboard sesuai role dari respons server
          switch (data.user.role) {
            case 'admin':
              window.location.href = '/admin/dashboard';
              break;
            case 'pegawai':
              window.location.href = '/pegawai/dashboard';
              break;
            case 'owner':
              window.location.href = '/owner/dashboard';
              break;
            case 'gudang':
              window.location.href = '/gudang/dashboard';
              break;
            case 'cs':
              window.location.href = '/cs/dashboard';
              break;
            case 'penitip':
              window.location.href = '/penitip/dashboard';
              break;
            case 'pembeli':
              window.location.href = '/pembeli/dashboard';
              break;
            case 'organisasi':
              window.location.href = '/organisasi/dashboard';
              break;
            default:
              window.location.href = '/dashboard';
          }
        } else {
          errorDiv.innerHTML = data.message || 'Login gagal.';
          errorDiv.classList.remove('hidden');
        }
      } catch (error) {
        errorDiv.innerHTML = 'Terjadi kesalahan. Coba lagi nanti.';
        errorDiv.classList.remove('hidden');
      } finally {
        button.innerHTML = 'Login';
        button.disabled = false;
      }
    });
    */
  </script>

</body>
</html>