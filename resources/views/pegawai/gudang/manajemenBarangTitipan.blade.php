@extends('layouts.gudang')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Manajemen Barang Titipan</h2>

        {{-- Form Tambah Barang --}}
        <div class="card mb-4">
            <div class="card-header">Tambah Barang Titipan</div>
            <div class="card-body">
                <form action="{{ route('gudang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang_titipan">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang_titipan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga_barang">Harga</label>
                            <input type="number" class="form-control" name="harga_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_barang">Jenis</label>
                            <input type="text" class="form-control" name="jenis_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="garansi_barang">Garansi</label>
                            <input type="text" class="form-control" name="garansi_barang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="berat_barang">Berat (gram)</label>
                            <input type="number" class="form-control" name="berat_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_barang">Status</label>
                            <select class="form-control" name="status_barang" required>
                                <option value="dijual">dijual</option>
                                <option value="barang untuk donasi">barang untuk donasi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deskripsi_barang">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi_barang" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gambar_barang">Gambar Utama</label>
                            <input type="file" class="form-control" name="gambar_barang" accept="image/*">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="gambar[]">Gambar Tambahan</label>
                            <input type="file" class="form-control" name="gambar[]" accept="image/*" multiple>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar Barang --}}
        <div class="card">
            <div class="card-header">Daftar Barang Titipan</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Jenis</th>
                            <th>Berat</th>
                            <th>Status</th>
                            <th>Garansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangTitipan as $barang)
                            <tr>
                                <td>
                                    @if ($barang->gambar_barang && file_exists(public_path('storage/' . $barang->gambar_barang)))
                                        <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar"
                                            width="80">
                                    @else
                                        <span class="text-muted">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td>{{ $barang->nama_barang_titipan }}</td>
                                <td>Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                                <td>{{ $barang->jenis_barang }}</td>
                                <td>{{ $barang->berat_barang }} g</td>
                                <td>{{ ucfirst($barang->status_barang) }}</td>
                                <td>{{ $barang->garansi_barang ?? '-' }}</td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button type="button" class="btn btn-primary btn-edit-barang"
                                        data-barang='@json($barang)'>
                                        Edit
                                    </button>
                                    <form action="{{ route('gudang.destroy', $barang->id_barang) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin hapus barang ini?')"
                                            class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                    <a href="{{ route('barang.show', $barang->id_barang) }}"
                                        class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Tabel Barang dengan Jadwal Pengembalian --}}
        <div class="card mt-5">
            <div class="card-header bg-success text-white">Barang dengan Jadwal Pengembalian</div>
            <div class="card-body">
                @php
                    $barangDenganJadwal = $barangTitipan->filter(function ($barang) {
                        $penitipan = $barang->penitipan;
                        return $penitipan &&
                            $penitipan->tanggal_pengambilan !== null &&
                            $barang->status_barang !== 'sudah diambil penitip';
                    });
                @endphp

                @if ($barangDenganJadwal->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Tanggal Pengambilan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangDenganJadwal as $barang)
                                <tr>
                                    <td>
                                        @if ($barang->gambar_barang && file_exists(public_path('storage/' . $barang->gambar_barang)))
                                            <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar"
                                                width="80">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>{{ $barang->nama_barang_titipan }}</td>
                                    <td>{{ $barang->jenis_barang }}</td>
                                    <td>{{ ucfirst($barang->status_barang) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($barang->penitipan->tanggal_pengambilan)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <form
                                            action="{{ route('penitipan.konfirmasiPengambilan', $barang->penitipan->id_penitipan) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Yakin ingin mengonfirmasi pengambilan barang ini?')">
                                                Konfirmasi Pengambilan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Tidak ada barang yang dijadwalkan untuk pengambilan saat ini.</p>
                @endif
            </div>
        </div>

        <h3 class="mt-10 mb-4 text-xl font-semibold">Jadwal Pengiriman & Pengambilan</h3>

        <table class="min-w-full divide-y divide-gray-300 border border-gray-300 rounded-lg shadow-sm">
            <thead class="bg-gray-100">
                <tr>
                    @foreach (['ID Transaksi', 'Nama Barang', 'Status Transaksi', 'Tanggal Pengiriman', 'Tanggal Pengambilan', 'Foto Barang', 'Aksi'] as $header)
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($transaksiProses as $transaksi)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $transaksi->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-800">
                            {{ $transaksi->barangTitipan->nama_barang_titipan ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        {{ $transaksi->status_transaksi == 'Dikirim' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $transaksi->status_transaksi == 'Diambil' ? 'bg-green-100 text-green-800' : '' }}">
                                {{ $transaksi->status_transaksi }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaksi->tanggal_pengiriman ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $transaksi->tanggal_pengambilan ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex space-x-3">
                            @if ($transaksi->barangTitipan && $transaksi->barangTitipan->gambar_barang)
                                <img src="{{ asset('storage/' . $transaksi->barangTitipan->gambar_barang) }}"
                                    alt="Foto utama"
                                    class="w-16 h-16 object-cover rounded border border-gray-300 shadow-sm">
                            @endif
                            @if ($transaksi->barangTitipan && $transaksi->barangTitipan->gambarBarangTitipan)
                                @foreach ($transaksi->barangTitipan->gambarBarangTitipan->take(1) as $gambar)
                                    <img src="{{ asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar) }}"
                                        alt="Foto tambahan"
                                        class="w-16 h-16 object-cover rounded border border-gray-300 shadow-sm">
                                @endforeach
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap flex space-x-4">
                            <a href="{{ route('gudang.barang.showDetail', $transaksi->barangTitipan->id_barang ?? '') }}"
                                class="text-indigo-600 hover:text-indigo-900 font-semibold transition">
                                Detail
                            </a>

                            @if ($transaksi->status_transaksi == 'Dikirim')
                                <button onclick="openScheduleModal({{ $transaksi->id }})"
                                    class="ml-2 inline-block px-4 py-2 text-sm text-white bg-blue-600 hover:bg-blue-700 rounded">
                                    Jadwalkan Pengiriman
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-6 text-center text-gray-500 italic">Tidak ada transaksi dengan
                            status 'Dikirim' atau 'Diambil'</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Modal Edit --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Barang Titipan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body row">
                            <input type="hidden" name="id_barang" id="edit_id_barang">
                            <div class="col-md-6 mb-3">
                                <label for="edit_nama_barang_titipan">Nama Barang</label>
                                <input type="text" class="form-control" name="nama_barang_titipan"
                                    id="edit_nama_barang_titipan" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_harga_barang">Harga</label>
                                <input type="number" class="form-control" name="harga_barang" id="edit_harga_barang"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_jenis_barang">Jenis</label>
                                <input type="text" class="form-control" name="jenis_barang" id="edit_jenis_barang"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_garansi_barang">Garansi (bulan)</label>
                                <input type="text" class="form-control" name="garansi_barang"
                                    id="edit_garansi_barang">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_berat_barang">Berat (gram)</label>
                                <input type="number" class="form-control" name="berat_barang" id="edit_berat_barang"
                                    required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_status_barang">Status</label>
                                <select class="form-control" name="status_barang" id="edit_status_barang" required>
                                    <option value="ready">Ready</option>
                                    <option value="terjual">Terjual</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_deskripsi_barang">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi_barang" id="edit_deskripsi_barang" required></textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_gambar_barang">Gambar Utama (opsional)</label>
                                <input type="file" class="form-control" name="gambar_barang" id="edit_gambar_barang"
                                    accept="image/*">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_gambar_tambahan">Gambar Tambahan (boleh lebih dari satu)</label>
                                <input type="file" class="form-control" name="gambar[]" id="edit_gambar_tambahan"
                                    accept="image/*" multiple>
                            </div>
                            <div class="col-12">
                                <label>Gambar Tambahan Saat Ini:</label>
                                <div id="gambarTambahanContainer" class="d-flex flex-wrap"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Penjadwalan --}}
    <!-- Modal -->
    <div id="scheduleModal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg max-w-md mx-auto mt-20 p-6 relative">
            <h2 class="text-xl font-semibold mb-4">Penjadwalan Pengiriman</h2>
            <form id="scheduleForm" method="POST" action="{{ route('gudang.schedulePengiriman') }}">
                @csrf
                <input type="hidden" name="transaksi_id" id="transaksiIdField">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal & Waktu Pengiriman</label>
                    <input type="datetime-local" name="jadwal_pengiriman" class="mt-1 block w-full border rounded p-2"
                        required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Pilih Kurir</label>
                    <select name="kurir_id" class="mt-1 block w-full border rounded p-2" required>
                        @foreach ($kurirs as $kurir)
                            <option value="{{ $kurir->id }}">{{ $kurir->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="closeScheduleModal()"
                        class="px-4 py-2 mr-2 bg-gray-300 hover:bg-gray-400 rounded">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded">Jadwalkan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.btn-edit-barang');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const barang = JSON.parse(this.getAttribute('data-barang'));
                    const form = document.getElementById('editForm');
                    form.action = `/gudang/${barang.id_barang}`;
                    document.getElementById('edit_id_barang').value = barang.id_barang;
                    document.getElementById('edit_nama_barang_titipan').value = barang
                        .nama_barang_titipan;
                    document.getElementById('edit_harga_barang').value = barang.harga_barang;
                    document.getElementById('edit_jenis_barang').value = barang.jenis_barang;
                    document.getElementById('edit_garansi_barang').value = barang.garansi_barang;
                    document.getElementById('edit_berat_barang').value = barang.berat_barang;
                    document.getElementById('edit_status_barang').value = barang.status_barang;
                    document.getElementById('edit_deskripsi_barang').value = barang
                        .deskripsi_barang;

                    const gambarContainer = document.getElementById('gambarTambahanContainer');
                    gambarContainer.innerHTML = '';
                    if (barang.gambar_tambahan) {
                        barang.gambar_tambahan.forEach(gambar => {
                            const div = document.createElement('div');
                            div.classList.add('position-relative', 'm-1');
                            div.innerHTML = `
                            <img src="/storage/gambar_barang_titipan/${gambar.nama_file_gambar}" width="80" class="me-2">
                        `;
                            gambarContainer.appendChild(div);
                        });
                    }
                    $('#editModal').modal('show');
                });
            });
        });

        function openScheduleModal(transaksiId) {
            document.getElementById('scheduleModal').classList.remove('hidden');
            document.getElementById('transaksiIdField').value = transaksiId;
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
        }

        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            const jadwalInput = this.querySelector('[name="jadwal_pengiriman"]');
            const selectedDate = new Date(jadwalInput.value);
            const now = new Date();

            if (
                selectedDate.toDateString() === now.toDateString() &&
                selectedDate.getHours() >= 16
            ) {
                e.preventDefault();
                alert('Pengiriman di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.');
            }
        });
    </script>
@endsection
