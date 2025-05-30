@extends('layouts.penitip')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Barang Titipan Saya</h2>

        <div class="bg-white shadow-md rounded-lg overflow-x-auto">
            <table class="min-w-full table-auto text-sm text-left text-gray-700">
                <thead class="bg-gray-100 text-gray-800 font-semibold">
                    <tr>
                        <th class="px-4 py-3">Gambar</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Jenis</th>
                        <th class="px-4 py-3">Harga</th>
                        <th class="px-4 py-3">Berat (g)</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Garansi</th>
                        <th class="px-4 py-3">Deskripsi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($barangTitipan as $barang)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-center">
                                @if ($barang->gambar_barang)
                                    <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar"
                                        class="w-20 h-20 object-cover rounded">
                                @else
                                    <span class="text-gray-400 italic">Tidak ada gambar</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $barang->nama_barang_titipan }}</td>
                            <td class="px-4 py-3">{{ $barang->jenis_barang }}</td>
                            <td class="px-4 py-3">Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                            <td class="px-4 py-3">{{ $barang->berat_barang }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block px-2 py-1 text-xs rounded-full
                            {{ $barang->status_barang == 'dijual' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($barang->status_barang) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block px-2 py-1 text-xs rounded-full
                            {{ Str::contains($barang->status_garansi, 'Tanpa') ? 'bg-gray-200 text-gray-700' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $barang->status_garansi }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $barang->deskripsi_barang }}</td>
                            <td class="px-4 py-3 text-center">
                                @if (
                                    $barang->detailPenitipan &&
                                        $barang->detailPenitipan->penitipan &&
                                        $barang->detailPenitipan->penitipan->status_perpanjangan == 'ya')
                                    <form method="POST"
                                        action="{{ route('penitip.penitipan.perpanjang', $barang->detailPenitipan->penitipan->id_penitipan) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-blue-500 hover:bg-blue-600 text-white text-xs px-3 py-1 rounded w-full">
                                            Perpanjang 30 hari
                                        </button>
                                    </form>
                                @endif
                                <button type="button"
                                    onclick="openModal({{ $barang->id_barang }}, '{{ $barang->nama_barang_titipan }}', {{ $barang->detailPenitipan->id_penitipan ?? 'null' }})"
                                    class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    Jadwalkan
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-4 text-center text-gray-500">Belum ada barang titipan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal --}}
    <div id="modalPengambilan" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
            <button onclick="closeModal()"
                class="absolute top-2 right-3 text-gray-600 hover:text-gray-900 text-xl font-bold">&times;</button>
            <h3 class="text-lg font-semibold mb-4">Jadwal Pengambilan Barang</h3>
            <form action="{{ route('penitip.jadwalPengambilan') }}" method="POST" id="formPengambilan">
                @csrf
                <input type="hidden" name="id_penitipan" id="modalIdPenitipan" value="">
                <label for="tanggal_pengambilan" class="block mb-1 font-medium text-gray-700">Tanggal Pengambilan</label>
                <input type="date" name="tanggal_pengambilan" id="tanggal_pengambilan" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    min="{{ date('Y-m-d') }}">
                <div class="text-right">
                    <button type="button" onclick="closeModal()"
                        class="mr-2 px-4 py-2 rounded border border-gray-300 hover:bg-gray-100">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function openModal(id_barang, nama_barang, id_penitipan) {
            if (!id_penitipan) {
                alert('Barang belum ditautkan ke penitipan, tidak bisa dijadwalkan.');
                return;
            }
            document.getElementById('modalIdPenitipan').value = id_penitipan;
            document.getElementById('tanggal_pengambilan').value = '';
            document.getElementById('modalPengambilan').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalPengambilan').classList.add('hidden');
        }

        // Klik di luar modal untuk menutupnya
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('modalPengambilan');
            if (event.target === modal) {
                closeModal();
            }
        });
    </script>
@endsection
