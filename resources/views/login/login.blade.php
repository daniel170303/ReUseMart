<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-50 font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 flex justify-between items-center h-16">
      <h1 class="text-xl font-bold text-green-700">ReuseMart</h1>
      <a href="/" class="text-green-700 hover:underline">← Kembali ke Beranda</a>
    </div>
  </nav>

  <!-- Login Form -->
  <div class="flex justify-center items-center min-h-screen py-12 px-4">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
      <h2 class="text-2xl font-bold text-green-700 mb-6 text-center">Login ke ReuseMart</h2>

      <!-- Form Login -->
      <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <!-- Email -->
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" id="email" required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
        </div>

        <!-- Password -->
        <div>
          <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi</label>
          <input type="password" name="password" id="password" required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500" />
        </div>

        <!-- Role -->
        <div>
          <label for="role" class="block text-sm font-medium text-gray-700">Login sebagai</label>
          <select name="role" id="role" required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
            <option value="" disabled selected>Pilih Role</option>
            <option value="owner">Owner</option>
            <option value="admin">Admin</option>
            <option value="pegawai">Pegawai</option>
            <option value="gudang">Gudang</option>
            <option value="cs">Customer Service</option>
            <option value="penitip">Penitip</option>
            <option value="pembeli">Pembeli</option>
            <option value="organisasi">Organisasi</option>
          </select>
        </div>

        <!-- Tombol Login -->
        <div>
          <button type="submit"
            class="w-full bg-green-600 text-white py-2 px-4 rounded hover:bg-green-700 transition">
            Masuk
          </button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
