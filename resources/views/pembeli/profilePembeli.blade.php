<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profil Pembeli - ReuseMart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function confirmHapusAlamat() {
            return confirm("Apakah Anda yakin ingin menghapus alamat?");
        }
    </script>
</head>

<body class="bg-gray-100 font-sans">

    {{-- Notifikasi Flash --}}
    @if (session('success'))
        <div class="max-w-3xl mx-auto mt-6 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl mx-auto p-6 bg-white shadow-md mt-10 rounded-lg">
        <h1 class="text-2xl font-bold text-green-800 mb-4">Profil Anda</h1>

        <div class="space-y-2 text-gray-700">
            <p><strong>Nama:</strong> {{ $pembeli->nama_pembeli }}</p>
            <p><strong>Email:</strong> {{ $pembeli->email_pembeli }}</p>
            <p><strong>Nomor Telepon:</strong> {{ $pembeli->nomor_telepon_pembeli }}</p>

            <div>
                <p><strong>Alamat:</strong></p>
                <form action="{{ route('pembeli.updateAlamat') }}" method="POST" class="flex flex-col gap-2 mt-1">
                    @csrf
                    <input type="text" name="alamat_pembeli"
                        value="{{ old('alamat_pembeli', $pembeli->alamat_pembeli) }}"
                        class="border border-gray-300 rounded px-3 py-1 w-full"
                        placeholder="Tulis alamat atau kosongkan untuk menghapus" />

                    <div class="flex gap-2">
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                            Simpan
                        </button>
                        <button type="submit" name="alamat_pembeli" value=""
                            onclick="return confirmHapusAlamat();"
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
                            Hapus Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <a href="{{ url('/') }}"
            class="inline-block mt-6 px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Kembali ke Dashboard
        </a>
    </div>
</body>

</html>
