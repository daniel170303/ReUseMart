<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profil Pembeli - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
  <div class="max-w-3xl mx-auto p-6 bg-white shadow-md mt-10 rounded-lg">
    <h1 class="text-2xl font-bold text-green-800 mb-4">Profil Anda</h1>

    <div class="space-y-2 text-gray-700">
      <p><strong>Nama:</strong> {{ $pembeli->nama_pembeli }}</p>
      <p><strong>Email:</strong> {{ $pembeli->email_pembeli }}</p>
      <p><strong>Nomor Telepon:</strong> {{ $pembeli->nomor_telepon_pembeli }}</p>
      <p><strong>Alamat:</strong> {{ $pembeli->alamat_pembeli }}</p>
      <p><strong>Total Poin Reward:</strong> <span class="text-green-600 font-bold">{{ $totalPoin }}</span></p>
    </div>

    <a href="{{ route('dashboard.pembeli') }}" class="inline-block mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Kembali ke Dashboard</a>
  </div>
</body>
</html>
