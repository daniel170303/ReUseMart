<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Login page for ReuseMart. Access your account to manage orders and profile.">
  <meta name="robots" content="noindex, nofollow">
  <title>Login - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white font-sans">

  <div class="flex min-h-screen">
    
    <!-- Form Login -->
    <div class="w-full md:w-1/2 flex items-center justify-center px-6 lg:px-20">
      <div class="w-full max-w-md">
        <h2 class="text-3xl font-semibold text-gray-900 mb-2">Welcome back!</h2>
        <p class="text-gray-600 mb-6">Enter your Credentials to access your account</p>

        <!-- Error Messages -->
        @if ($errors->any())
          <div class="bg-red-100 text-red-700 p-4 mb-4 rounded">
              <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
              </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
          @csrf
          <!-- Role -->
          <div>
            <label for="role" class="block text-sm font-medium text-gray-700">Login sebagai</label>
            <select name="role" id="role" required
              class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600">
              <option value="" disabled {{ old('role') ? '' : 'selected' }}>Pilih Role</option>
              <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
              <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
              <option value="gudang" {{ old('role') == 'gudang' ? 'selected' : '' }}>Gudang</option>
              <option value="cs" {{ old('role') == 'cs' ? 'selected' : '' }}>Customer Service</option>
              <option value="penitip" {{ old('role') == 'penitip' ? 'selected' : '' }}>Penitip</option>
              <option value="organisasi" {{ old('role') == 'organisasi' ? 'selected' : '' }}>Organisasi</option>
            </select>
          </div>

          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
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

  <!-- Script untuk menampilkan loading spinner saat form dikirim -->
  <script>
    const form = document.querySelector('form');
    form.addEventListener('submit', () => {
      const button = document.getElementById('loginButton');
      button.innerHTML = 'Loading...'; // Ganti teks tombol saat loading
      button.disabled = true; // Nonaktifkan tombol sementara
    });
  </script>

</body>
</html>
