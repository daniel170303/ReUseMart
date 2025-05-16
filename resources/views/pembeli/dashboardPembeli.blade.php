<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Pembeli - ReuseMart</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-white font-sans">

  <!-- Navbar -->
  <nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-16">
        <div class="flex-shrink-0">
          <a href="{{ route('dashboard.pembeli') }}" class="text-2xl font-bold text-green-800">ReuseMart</a>
        </div>
        <div class="flex items-center space-x-6">
          <a href="{{ route('pembeli.profile') }}" class="text-gray-700 hover:text-green-700 font-medium">Profile</a>
          <a href="{{ route('pembeli.history') }}" class="text-gray-700 hover:text-green-700 font-medium">History</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Logout</button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <!-- Konten -->
  <div class="max-w-7xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Selamat datang, {{ $pembeli->nama_pembeli }}</h1>

    <!-- Profil Pembeli -->
    <div class="bg-green-50 p-6 rounded-lg shadow-md mb-10">
      <h2 class="text-xl font-semibold text-green-800 mb-3">Informasi Profil</h2>
      <p><strong>Nama:</strong> {{ $pembeli->nama_pembeli }}</p>
      <p><strong>Email:</strong> {{ $pembeli->email_pembeli }}</p>
      <p><strong>Telepon:</strong> {{ $pembeli->nomor_telepon_pembeli }}</p>
      <p><strong>Alamat:</strong> {{ $pembeli->alamat_pembeli }}</p>
      <p><strong>Total Poin Reward:</strong> {{ $rewardPoints ?? 0 }}</p>
    </div>

    <!-- Riwayat Transaksi -->
    <div class="bg-white p-6 rounded-lg shadow-md">
      <h2 class="text-xl font-semibold text-green-800 mb-4">Riwayat Transaksi</h2>

      @if ($transactions->isEmpty())
        <p class="text-gray-600">Belum ada transaksi yang dilakukan.</p>
      @else
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-green-100">
              <tr>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Produk</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach ($transactions as $transaksi)
                <tr>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ $transaksi->tanggal_pemesanan }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                  <td class="px-6 py-4 text-sm text-gray-800">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                  <td class="px-6 py-4 text-sm">
                    <span class="px-2 py-1 rounded-full text-white
                      @if ($transaksi->status == 'Selesai') bg-green-600
                      @elseif ($transaksi->status == 'Diproses') bg-yellow-500
                      @else bg-red-500 @endif">
                      {{ $transaksi->status }}
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>

</body>
</html>