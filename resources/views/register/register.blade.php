<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-white font-sans">

  <div class="flex min-h-screen">

    <!-- Gambar di sebelah kiri -->
    <div class="hidden md:block md:w-1/2">
      <img src="{{ asset('images/GambarLogin.png') }}" alt="Register Illustration" class="w-full h-screen object-cover">
    </div>

    <!-- Form Pilih Role -->
    <div class="w-full md:w-1/2 flex items-center justify-center px-6 lg:px-20">
      <div class="w-full max-w-md">
        <h2 class="text-4xl font-bold text-gray-900 mb-4">Register</h2>
        <p class="text-lg text-gray-600 mb-8">Pilih role untuk melanjutkan registrasi</p>

        <form id="roleForm" class="space-y-6">
          @csrf
          <div>
            <label for="role" class="block text-base font-medium text-gray-700 mb-2">Pilih Role</label>
            <select name="role" id="role"
              class="w-full px-4 py-3 border border-gray-300 rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
              required>
              <option value="" disabled selected>-- Pilih Role --</option>
              <option value="pembeli">Pembeli</option>
              <option value="organisasi">Organisasi</option>
            </select>
            <p id="roleError" class="text-red-600 text-sm mt-1 hidden">Role harus dipilih!</p>
          </div>

          <div>
            <button type="submit" class="w-full bg-green-700 text-white py-3 px-4 rounded-md hover:bg-green-800 transition font-semibold text-lg">
              Lanjutkan
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
    document.getElementById('roleForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const roleSelect = document.getElementById('role');
      const roleError = document.getElementById('roleError');
      const role = roleSelect.value;

      if (!role || role === "") {
        roleError.classList.remove('hidden');
        roleSelect.classList.add('border-red-500', 'focus:border-red-500');
        return;
      }

      roleError.classList.add('hidden');
      roleSelect.classList.remove('border-red-500', 'focus:border-red-500');

      if (role === 'pembeli') {
        window.location.href = "{{ route('register.pembeli.form') }}";
      } else if (role === 'organisasi') {
        window.location.href = "{{ route('register.organisasi.form') }}";
      }
    });
  </script>
</body>
</html>