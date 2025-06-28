@php
    use Carbon\Carbon;
@endphp

@extends('layouts.penitip')

@section('content')
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<div class="container py-4">
    <h2 class="mb-4 fw-bold text-dark">Barang Titipan Saya</h2>
    <div class="mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari barang berdasarkan semua kolom...">
    </div>
    <div class="table-responsive shadow-sm border rounded-4 bg-white">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr class="text-center">
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Status</th>
                    <th>Deskripsi</th>
                    <th>Durasi Penitipan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($barangTitipan as $barang)
                    <tr>
                        <td class="text-center">
                            @if ($barang->gambar_barang)
                                <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar" class="img-thumbnail rounded" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <small class="text-muted fst-italic">Tidak ada gambar</small>
                            @endif
                        </td>
                        <td>{{ $barang->nama_barang_titipan }}</td>
                        <td>
                            <span class="badge {{ $barang->status_barang == 'dijual' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($barang->status_barang) }}
                            </span>
                        </td>
                        <td class="text-truncate" style="max-width: 200px;">
                            {{ $barang->deskripsi_barang }}
                        </td>
                        <td class="text-center">
                            @if ($barang->detailPenitipan && $barang->detailPenitipan->penitipan)
                                @php
                                    $mulai = Carbon::parse($barang->detailPenitipan->penitipan->tanggal_penitipan);
                                    $selesai = Carbon::parse($barang->detailPenitipan->penitipan->tanggal_selesai_penitipan);
                                    $durasi = $mulai->diffInDays($selesai);
                                @endphp
                                {{ $durasi }} hari
                            @else
                                <small class="text-muted fst-italic">-</small>
                            @endif
                        </td>
                        <td class="text-center">
                            {{-- Tombol Jadwalkan --}}
                            <button type="button"
                                onclick="openModal(
                                    {{ $barang->detailPenitipan->id_penitipan ?? 'null' }},
                                    '{{ $barang->nama_barang_titipan }}',
                                    '{{ $barang->detailPenitipan->penitipan->tanggal_selesai_penitipan ?? '' }}'
                                )"
                                class="btn btn-sm btn-outline-primary shadow-sm">
                                Jadwalkan
                            </button>

                            {{-- Tombol Detail --}}
                            <button type="button"
                                onclick="showDetail(
                                    '{{ asset('storage/' . $barang->gambar_barang) }}',
                                    '{{ $barang->nama_barang_titipan }}',
                                    '{{ $barang->jenis_barang }}',
                                    'Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}',
                                    '{{ $barang->berat_barang }} gram',
                                    '{{ $barang->status_garansi }}',
                                    {{ $barang->deskripsi_barang }}
                                )"
                                class="btn btn-sm btn-outline-info shadow-sm ms-1">
                                Detail
                            </button>

                            {{-- Tombol Perpanjang --}}
                            @if ($barang->detailPenitipan && $barang->detailPenitipan->penitipan && $barang->detailPenitipan->penitipan->status_perpanjangan === 'tidak')
                                <button type="button"
                                    onclick="konfirmasiPerpanjangan({{ $barang->detailPenitipan->penitipan->id_penitipan }})"
                                    class="btn btn-sm btn-outline-success shadow-sm ms-1">
                                    Perpanjang
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada barang titipan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modal Jadwal Pengambilan --}}
<div id="modalPengambilan" class="modal fade" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Jadwal Pengambilan Barang Oleh Pemilik</h5>
                <button type="button" class="btn-close" onclick="closeModal()" aria-label="Close"></button>
            </div>
            <form action="{{ route('penitip.jadwalPengambilan') }}" method="POST" id="formPengambilan">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id_penitipan" id="modalIdPenitipan">
                    <div class="mb-3">
                        <label for="tanggal_pengambilan" class="form-label">Tanggal Pengambilan</label>
                        <input type="date" name="tanggal_pengambilan" id="tanggal_pengambilan" class="form-control" required min="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Detail Barang --}}
<div class="modal fade" id="modalDetailBarang" tabindex="-1" aria-labelledby="detailLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 shadow">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLabel">Detail Barang Titipan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <div class="row align-items-center">
                    <div class="col-md-4 text-center">
                        <img id="detailGambar" src="" alt="Gambar" class="img-fluid rounded shadow-sm mb-3" style="max-height: 200px;">
                    </div>
                    <div class="col-md-8">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><strong>Nama:</strong> <span id="detailNama"></span></li>
                            <li class="list-group-item"><strong>Jenis:</strong> <span id="detailJenis"></span></li>
                            <li class="list-group-item"><strong>Harga:</strong> <span id="detailHarga"></span></li>
                            <li class="list-group-item"><strong>Berat:</strong> <span id="detailBerat"></span></li>
                            <li class="list-group-item"><strong>Garansi:</strong> <span id="detailGaransi"></span></li>
                            <li class="list-group-item"><strong>Deskripsi:</strong><br><span id="detailDeskripsi"></span></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalKonfirmasiPerpanjang" tabindex="-1" aria-labelledby="konfirmasiLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <form method="POST" action="{{ route('penitipan.perpanjang') }}">
                @csrf
                <input type="hidden" name="id_penitipan" id="idPenitipanPerpanjang">
                <div class="modal-header">
                    <h5 class="modal-title" id="konfirmasiLabel">Konfirmasi Perpanjangan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin memperpanjang masa penitipan barang ini selama 30 hari?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Perpanjang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openModal(idPenitipan, namaBarang, tanggalSelesai) {
        document.getElementById('modalIdPenitipan').value = idPenitipan;

        if (tanggalSelesai) {
            const selesai = new Date(tanggalSelesai);
            const min = new Date(selesai);
            min.setDate(min.getDate() + 1);

            const max = new Date(selesai);
            max.setDate(max.getDate() + 7);

            const formatDate = (date) => {
                const yyyy = date.getFullYear();
                const mm = String(date.getMonth() + 1).padStart(2, '0');
                const dd = String(date.getDate()).padStart(2, '0');
                return ${yyyy}-${mm}-${dd};
            };

            const input = document.getElementById('tanggal_pengambilan');
            input.min = formatDate(min);
            input.max = formatDate(max);
            input.value = '';
        }

        const modal = new bootstrap.Modal(document.getElementById('modalPengambilan'));
        modal.show();
    }

    function closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPengambilan'));
        modal.hide();
    }

    function showDetail(gambar, nama, jenis, harga, berat, garansi, deskripsi) {
        document.getElementById('detailGambar').src = gambar;
        document.getElementById('detailNama').textContent = nama;
        document.getElementById('detailJenis').textContent = jenis;
        document.getElementById('detailHarga').textContent = harga;
        document.getElementById('detailBerat').textContent = berat;
        document.getElementById('detailGaransi').textContent = garansi;
        document.getElementById('detailDeskripsi').textContent = deskripsi;

        const detailModal = new bootstrap.Modal(document.getElementById('modalDetailBarang'));
        detailModal.show();
    }

    document.getElementById('searchInput').addEventListener('keyup', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    function konfirmasiPerpanjangan(id_penitipan) {
        document.getElementById('idPenitipanPerpanjang').value = id_penitipan;
        const modal = new bootstrap.Modal(document.getElementById('modalKonfirmasiPerpanjang'));
        modal.show();
    }

    setTimeout(() => {
        const alert = document.querySelector('.alert');
        if (alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    }, 4000);
</script>
@endsection