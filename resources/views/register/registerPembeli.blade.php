<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register Pembeli - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body class="bg-white font-sans">
  <div class="flex min-h-screen">
    <div class="hidden md:block md:w-1/2">
      <img src="{{ asset('images/GambarLogin.png') }}" alt="Register Illustration" class="w-full h-screen object-cover" />
    </div>

    <div class="w-full md:w-1/2 flex items-center justify-center px-6 lg:px-20">
      <div class="w-full max-w-md">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Register as Pembeli</h2>
        <p class="text-lg text-gray-600 mb-8">Fill in the information to get started</p>

        <form method="POST" action="{{ route('register.pembeli.submit') }}" class="space-y-6">
          @csrf
          <div>
            <label for="nama_pembeli" class="block text-base font-medium text-gray-700">Nama Pembeli</label>
            <input type="text" name="nama_pembeli" id="nama_pembeli" required
              class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>
          <div>
            <label for="alamat_pembeli" class="block text-base font-medium text-gray-700">Alamat Pembeli</label>
            <input type="text" name="alamat_pembeli" id="alamat_pembeli" required
              class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>
          <div>
            <label for="nomor_telepon_pembeli" class="block text-base font-medium text-gray-700">Nomor Telepon Pembeli</label>
            <input type="text" name="nomor_telepon_pembeli" id="nomor_telepon_pembeli" required
              class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>
          <div>
            <label for="email_pembeli" class="block text-base font-medium text-gray-700">Email Pembeli</label>
            <input type="email" name="email_pembeli" id="email_pembeli" required
              class="mt-2 block w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600" />
          </div>
          <div>
            <label for="password_pembeli" class="block text-base font-medium text-gray-700">Password Pembeli</label>
            <div class="relative mt-2">
              <input type="password" name="password_pembeli" id="password_pembeli" required
                class="block w-full px-4 py-3 text-base border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600 pr-12" />
              <button type="button" onclick="togglePassword('password_pembeli', 'eyeIconPembeli')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                <i id="eyeIconPembeli" class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div>
            <button type="submit" class="w-full bg-green-700 text-white py-3 px-4 rounded-md hover:bg-green-800 transition font-semibold text-lg">
              Register
            </button>
          </div>
        </form>

        <div class="mt-6 text-center">
          <p class="text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-green-700 font-semibold hover:underline">Login di sini!</a>
          </p>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePassword(passwordId, eyeIconId) {
      const passwordInput = document.getElementById(passwordId);
      const eyeIcon = document.getElementById(eyeIconId);
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
      }
    }
  </script>
</body>
</html>
