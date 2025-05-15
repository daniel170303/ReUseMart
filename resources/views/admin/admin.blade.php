<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - ReuseMart</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-green-50 text-gray-800 font-sans">
  <!-- Header -->
  <header class="bg-green-800 text-white px-6 py-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">ReuseMart Admin</h1>
    <div>
      <span class="mr-4">Admin</span>
      <button class="bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Logout</button>
    </div>
  </header>

  <!-- Navigation -->
  <nav class="bg-green-700 text-white flex px-6 py-3 space-x-4">
    <button class="hover:bg-green-600 px-3 py-1 rounded" onclick="showTab('organisasi')">Manajemen Organisasi</button>
    <button class="hover:bg-green-600 px-3 py-1 rounded" onclick="showTab('pegawai')">Manajemen Pegawai</button>
  </nav>

  <!-- Content -->
  <main class="p-6">
    <!-- Organisasi Section -->
    <section id="organisasi" class="hidden">
      <h2 class="text-2xl font-bold mb-4">Manajemen Organisasi</h2>
      <div class="mb-4">
        <input type="text" placeholder="Cari organisasi..." class="border p-2 rounded w-full">
      </div>
      <div class="bg-white rounded shadow p-4 mb-4">
        <!-- Form tambah/ubah organisasi -->
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="text" placeholder="Nama Organisasi" class="border p-2 rounded">
          <input type="text" placeholder="Alamat" class="border p-2 rounded">
          <div class="col-span-2 flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
          </div>
        </form>
      </div>
      <table class="w-full bg-white shadow rounded">
        <thead class="bg-green-100">
          <tr>
            <th class="p-2 text-left">Nama</th>
            <th class="p-2 text-left">Alamat</th>
            <th class="p-2 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t">
            <td class="p-2">Organisasi A</td>
            <td class="p-2">Jl. Contoh 123</td>
            <td class="p-2 space-x-2">
              <button class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
              <button class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>

    <!-- Pegawai Section -->
    <section id="pegawai" class="hidden">
      <h2 class="text-2xl font-bold mb-4">Manajemen Pegawai</h2>
      <div class="mb-4">
        <input type="text" placeholder="Cari pegawai..." class="border p-2 rounded w-full">
      </div>
      <div class="bg-white rounded shadow p-4 mb-4">
        <!-- Form tambah/ubah pegawai -->
        <form class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="text" placeholder="Nama Pegawai" class="border p-2 rounded">
          <input type="email" placeholder="Email" class="border p-2 rounded">
          <select class="border p-2 rounded">
            <option>Pilih Role</option>
            <option value="pegawai">Pegawai</option>
            <option value="cs">Customer Service</option>
            <option value="gudang">Gudang</option>
          </select>
          <input type="password" placeholder="Password" class="border p-2 rounded">
          <div class="col-span-2 flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Simpan</button>
            <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Update</button>
          </div>
        </form>
      </div>
      <table class="w-full bg-white shadow rounded">
        <thead class="bg-green-100">
          <tr>
            <th class="p-2 text-left">Nama</th>
            <th class="p-2 text-left">Email</th>
            <th class="p-2 text-left">Role</th>
            <th class="p-2 text-left">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr class="border-t">
            <td class="p-2">Budi</td>
            <td class="p-2">budi@example.com</td>
            <td class="p-2">CS</td>
            <td class="p-2 space-x-2">
              <button class="bg-yellow-500 text-white px-2 py-1 rounded">Edit</button>
              <button class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
            </td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>

  <script>
    function showTab(tabId) {
      document.getElementById('organisasi').classList.add('hidden');
      document.getElementById('pegawai').classList.add('hidden');
      document.getElementById(tabId).classList.remove('hidden');
    }

    // Default tampilkan organisasi
    window.onload = () => showTab('organisasi');
  </script>
</body>
</html>
