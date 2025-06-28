@extends('layouts.gudang')

@section('content')
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-truck me-2"></i>Daftar Transaksi - Jadwal Pengiriman & Pengambilan
                        </h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>ID Transaksi</th>
                                        <th>Nama Barang</th>
                                        <th>Jenis Pengiriman</th>
                                        <th>Status</th>
                                        <th>Tanggal Dijadwalkan</th>
                                        <th>Detail</th>
                                        <th>Jadwalkan</th>
                                        <th>Cetak PDF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transaksi as $item)
                                        @php
                                            $status = strtolower(trim($item->status_transaksi));
                                            $jenis = strtolower(trim($item->jenis_pengiriman));
                                            $sudah_dijadwalkan =
                                                !is_null($item->tanggal_pengiriman) ||
                                                !is_null($item->tanggal_pengambilan);
                                        @endphp
                                        <tr>
                                            <td><strong>{{ $item->id_transaksi }}</strong></td>
                                            <td>{{ $item->nama_barang }}</td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $jenis == 'pengantaran' ? 'primary' : 'info' }}">
                                                    {{ ucfirst($item->jenis_pengiriman) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge badge-{{ $status == 'dikirim' ? 'success' : ($status == 'diambil pembeli' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($item->status_transaksi) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($item->tanggal_pengiriman)
                                                    <small class="text-success">
                                                        <i class="fas fa-truck"></i>
                                                        {{ \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d/m/Y H:i') }}
                                                    </small>
                                                @elseif($item->tanggal_pengambilan)
                                                    <small class="text-info">
                                                        <i class="fas fa-hand-holding"></i>
                                                        {{ \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d/m/Y H:i') }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">Belum dijadwalkan</small>
                                                @endif
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-primary btn-sm btn-detail"
                                                    data-toggle="modal"
                                                    data-target="#detailModal{{ $item->id_transaksi }}">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </button>
                                            </td>
                                            <td>
                                                @if ($sudah_dijadwalkan)
                                                    <button class="btn btn-secondary btn-sm" disabled>
                                                        <i class="fas fa-check"></i> Sudah Dijadwalkan
                                                    </button>
                                                @elseif(in_array($status, ['dikirim', 'lunas', 'siap dikirim', 'diambil pembeli']))
                                                    <button type="button" class="btn btn-success btn-sm btn-jadwal"
                                                        data-toggle="modal"
                                                        data-target="#jadwalModal{{ $item->id_transaksi }}"
                                                        data-id="{{ $item->id_transaksi }}"
                                                        data-jenis="{{ $item->jenis_pengiriman }}">
                                                        <i class="fas fa-calendar-plus"></i>
                                                        {{ $jenis == 'pengantaran' ? 'Jadwalkan Pengiriman' : 'Jadwalkan Pengambilan' }}
                                                    </button>
                                                @else
                                                    <span class="text-muted">
                                                        <i class="fas fa-times"></i> Tidak bisa dijadwalkan
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($sudah_dijadwalkan)
                                                    @if ($jenis == 'pengantaran')
                                                        <a href="{{ route('gudang.transaksi.cetakPDF', $item->id_transaksi) }}"
                                                            class="btn btn-danger btn-sm" target="_blank"
                                                            title="Cetak Nota Pengantaran">
                                                            <i class="fas fa-file-pdf"></i> PDF
                                                        </a>
                                                    @elseif($jenis == 'ambil sendiri')
                                                        <a href="{{ route('gudang.transaksi.cetakPDFAmbil', $item->id_transaksi) }}"
                                                            class="btn btn-danger btn-sm" target="_blank"
                                                            title="Cetak Nota Ambil Sendiri">
                                                            <i class="fas fa-file-pdf"></i> PDF
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Belum Dijadwalkan</span>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Modal Detail --}}
                                        <div class="modal fade" id="detailModal{{ $item->id_transaksi }}" tabindex="-1"
                                            role="dialog" aria-labelledby="detailModalLabel{{ $item->id_transaksi }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white">
                                                        <h5 class="modal-title"
                                                            id="detailModalLabel{{ $item->id_transaksi }}">
                                                            <i class="fas fa-info-circle mr-2"></i>Detail Transaksi
                                                            #{{ $item->id_transaksi }}
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item"><strong>ID Barang:</strong>
                                                                        {{ $item->id_barang }}</li>
                                                                    <li class="list-group-item"><strong>ID Pembeli:</strong>
                                                                        {{ $item->id_pembeli }}</li>
                                                                    <li class="list-group-item"><strong>Nama
                                                                            Barang:</strong> {{ $item->nama_barang }}</li>
                                                                    <li class="list-group-item"><strong>Tanggal
                                                                            Pemesanan:</strong>
                                                                        {{ \Carbon\Carbon::parse($item->tanggal_pemesanan)->format('d/m/Y H:i') }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <ul class="list-group list-group-flush">
                                                                    <li class="list-group-item"><strong>Tanggal
                                                                            Pelunasan:</strong>
                                                                        {{ $item->tanggal_pelunasan ? \Carbon\Carbon::parse($item->tanggal_pelunasan)->format('d/m/Y H:i') : '-' }}
                                                                    </li>
                                                                    <li class="list-group-item"><strong>Jenis
                                                                            Pengiriman:</strong>
                                                                        {{ $item->jenis_pengiriman }}</li>
                                                                    <li class="list-group-item"><strong>Tanggal
                                                                            Pengiriman:</strong>
                                                                        {{ $item->tanggal_pengiriman ? \Carbon\Carbon::parse($item->tanggal_pengiriman)->format('d/m/Y H:i') : '-' }}
                                                                    </li>
                                                                    <li class="list-group-item"><strong>Tanggal
                                                                            Pengambilan:</strong>
                                                                        {{ $item->tanggal_pengambilan ? \Carbon\Carbon::parse($item->tanggal_pengambilan)->format('d/m/Y H:i') : '-' }}
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        @if (strtolower($item->jenis_pengiriman) == 'pengantaran' && $item->tanggal_pengiriman && isset($item->nama_kurir))
                                                            <div class="mt-3">
                                                                <div class="alert alert-info">
                                                                    <i class="fas fa-user mr-2"></i><strong>Kurir:</strong>
                                                                    {{ $item->nama_kurir }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">
                                                            <i class="fas fa-times mr-1"></i>Tutup
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Modal Jadwal --}}
                                        <div class="modal fade" id="jadwalModal{{ $item->id_transaksi }}" tabindex="-1"
                                            role="dialog" aria-labelledby="jadwalModalLabel{{ $item->id_transaksi }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <form method="POST"
                                                        action="{{ route('gudang.penitipan.prosesJadwalkanPengiriman') }}"
                                                        id="formJadwal{{ $item->id_transaksi }}" autocomplete="off">
                                                        @csrf
                                                        <input type="hidden" name="id_transaksi"
                                                            value="{{ $item->id_transaksi }}">
                                                        <input type="hidden" name="jenis_pengiriman"
                                                            value="{{ $item->jenis_pengiriman }}">

                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title"
                                                                id="jadwalModalLabel{{ $item->id_transaksi }}">
                                                                <i class="fas fa-calendar-plus mr-2"></i>
                                                                {{ $jenis == 'pengantaran' ? 'Jadwalkan Pengiriman' : 'Jadwalkan Pengambilan' }}
                                                                #{{ $item->id_transaksi }}
                                                            </h5>
                                                            <button type="button" class="close text-white"
                                                                data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle mr-2"></i>
                                                                <strong>Transaksi:</strong> {{ $item->nama_barang }}<br>
                                                                <strong>Jenis:</strong>
                                                                {{ ucfirst($item->jenis_pengiriman) }}<br>
                                                                <strong>Status:</strong>
                                                                {{ ucfirst($item->status_transaksi) }}
                                                            </div>

                                                            {{-- Debug Info --}}
                                                            <div class="alert alert-warning" style="font-size: 12px;">
                                                                <strong>Debug Info:</strong><br>
                                                                ID Transaksi: {{ $item->id_transaksi }}<br>
                                                                Jenis Pengiriman: {{ $item->jenis_pengiriman }}<br>
                                                                Action URL:
                                                                {{ route('gudang.penitipan.prosesJadwalkanPengiriman') }}
                                                            </div>

                                                            {{-- Input Tanggal --}}
                                                            <div class="form-group">
                                                                <label for="tanggal_{{ $item->id_transaksi }}"
                                                                    class="form-label">
                                                                    <i class="fas fa-calendar mr-1"></i>
                                                                    {{ $jenis == 'pengantaran' ? 'Tanggal Pengiriman' : 'Tanggal Pengambilan' }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="date" name="tanggal_pengiriman"
                                                                    id="tanggal_{{ $item->id_transaksi }}"
                                                                    class="form-control" value="{{ date('Y-m-d') }}"
                                                                    min="{{ date('Y-m-d') }}"
                                                                    max="{{ date('Y-m-d', strtotime('+30 days')) }}"
                                                                    required
                                                                    style="background-color: white !important; color: black !important;">
                                                                <small class="form-text text-muted">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    Pilih tanggal untuk
                                                                    {{ $jenis == 'pengantaran' ? 'pengiriman' : 'pengambilan' }}
                                                                </small>
                                                            </div>

                                                            {{-- Input Waktu --}}
                                                            <div class="form-group">
                                                                <label for="waktu_{{ $item->id_transaksi }}"
                                                                    class="form-label">
                                                                    <i class="fas fa-clock mr-1"></i>
                                                                    {{ $jenis == 'pengantaran' ? 'Waktu Pengiriman' : 'Waktu Pengambilan' }}
                                                                    <span class="text-danger">*</span>
                                                                </label>
                                                                <input type="time" name="waktu_pengiriman"
                                                                    id="waktu_{{ $item->id_transaksi }}"
                                                                    class="form-control" value="09:00" min="08:00"
                                                                    max="17:00" required
                                                                    style="background-color: white !important; color: black !important;">
                                                                <small class="form-text text-muted">
                                                                    <i class="fas fa-info-circle"></i>
                                                                    Jam operasional: 08:00 - 17:00
                                                                </small>
                                                            </div>

                                                            {{-- Input Kurir (hanya untuk pengantaran) --}}
                                                            @if ($jenis == 'pengantaran')
                                                                <div class="form-group">
                                                                    <label for="kurir_{{ $item->id_transaksi }}"
                                                                        class="form-label">
                                                                        <i class="fas fa-user mr-1"></i>
                                                                        Pilih Kurir <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="id_kurir"
                                                                        id="kurir_{{ $item->id_transaksi }}"
                                                                        class="form-control" required
                                                                        style="background-color: white !important; color: black !important;">
                                                                        <option value="" disabled selected>-- Pilih
                                                                            Kurir --</option>
                                                                        @if (isset($kurirs) && $kurirs->count() > 0)
                                                                            @foreach ($kurirs as $kurir)
                                                                                <option value="{{ $kurir->id_pegawai }}">
                                                                                    {{ $kurir->nama_pegawai }}
                                                                                </option>
                                                                            @endforeach
                                                                        @else
                                                                            <option value="" disabled>Tidak ada kurir
                                                                                tersedia</option>
                                                                        @endif
                                                                    </select>
                                                                    <small class="form-text text-muted">
                                                                        <i class="fas fa-info-circle"></i>
                                                                        Pilih kurir yang akan mengirim barang
                                                                    </small>
                                                                </div>
                                                            @endif

                                                            {{-- Test Button untuk Debug --}}
                                                            <div class="form-group">
                                                                <button type="button" class="btn btn-info btn-sm"
                                                                    onclick="debugForm{{ $item->id_transaksi }}()">
                                                                    <i class="fas fa-bug"></i> Debug Form Data
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal">
                                                                <i class="fas fa-times mr-1"></i>Batal
                                                            </button>
                                                            <button type="submit" class="btn btn-success"
                                                                id="btnSubmit{{ $item->id_transaksi }}">
                                                                <i class="fas fa-save mr-1"></i>Simpan Jadwal
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Script untuk debug form spesifik --}}
                                        <script>
                                            function debugForm{{ $item->id_transaksi }}() {
                                                const form = document.getElementById('formJadwal{{ $item->id_transaksi }}');
                                                const formData = new FormData(form);

                                                console.log('=== DEBUG FORM {{ $item->id_transaksi }} ===');
                                                console.log('Form element:', form);
                                                console.log('Form action:', form.action);
                                                console.log('Form method:', form.method);

                                                console.log('Form data:');
                                                for (let [key, value] of formData.entries()) {
                                                    console.log(`${key}: ${value}`);
                                                }

                                                // Test field values
                                                const tanggal = document.getElementById('tanggal_{{ $item->id_transaksi }}').value;
                                                const waktu = document.getElementById('waktu_{{ $item->id_transaksi }}').value;

                                                console.log('Direct field values:');
                                                console.log('Tanggal:', tanggal);
                                                console.log('Waktu:', waktu);

                                                // Test if fields are disabled
                                                const tanggalField = document.getElementById('tanggal_{{ $item->id_transaksi }}');
                                                const waktuField = document.getElementById('waktu_{{ $item->id_transaksi }}');

                                                console.log('Field states:');
                                                console.log('Tanggal disabled:', tanggalField.disabled);
                                                console.log('Waktu disabled:', waktuField.disabled);

                                                alert('Debug info logged to console. Check browser console (F12).');
                                            }
                                        </script>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                                <br>Tidak ada transaksi yang perlu dijadwalkan
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            console.log('Document ready - jQuery version:', $.fn.jquery);

            // Test Bootstrap modal
            if (typeof $.fn.modal === 'undefined') {
                console.error('Bootstrap modal tidak tersedia!');
                return;
            }

            // Event handler untuk button jadwal
            $('.btn-jadwal').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                const targetModal = $(this).data('target');
                const transaksiId = $(this).data('id');
                const jenisKirim = $(this).data('jenis');

                console.log('Button jadwal diklik!');
                console.log('Target modal:', targetModal);
                console.log('Transaksi ID:', transaksiId);
                console.log('Jenis pengiriman:', jenisKirim);

                if (targetModal && $(targetModal).length > 0) {
                    console.log('Modal ditemukan, membuka...');
                    $(targetModal).modal('show');
                } else {
                    console.error('Modal tidak ditemukan:', targetModal);
                    alert('Error: Modal tidak ditemukan!');
                }
            });

            // HAPUS VALIDASI JAVASCRIPT - Biarkan server-side validation yang handle
            $('form[id^="formJadwal"]').on('submit', function(e) {
                const form = $(this);
                const submitBtn = form.find('button[type="submit"]');
                const originalText = submitBtn.html();

                console.log('Form submission started...');

                // HANYA disable button untuk mencegah double submit
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

                // Debug form data
                const formData = new FormData(form[0]);
                console.log('Form data being submitted:');
                for (let [key, value] of formData.entries()) {
                    console.log(key + ': ' + value);
                }

                // JANGAN preventDefault() - biarkan form submit normal
                console.log('Form submitting normally...');
            });

            // Event handler untuk button detail
            $('.btn-detail').on('click', function(e) {
                e.preventDefault();
                const targetModal = $(this).data('target');
                console.log('Button detail diklik, target:', targetModal);

                if (targetModal && $(targetModal).length > 0) {
                    $(targetModal).modal('show');
                }
            });

            // Modal event handlers
            $('.modal').on('show.bs.modal', function(e) {
                console.log('Modal showing:', this.id);
            });

            $('.modal').on('shown.bs.modal', function(e) {
                console.log('Modal shown:', this.id);

                // Pastikan form fields tidak disabled
                const form = $(this).find('form');
                if (form.length > 0) {
                    // Enable semua input
                    form.find('input, select, textarea').prop('disabled', false);

                    // Set default values jika kosong
                    const tanggalInput = form.find('input[name="tanggal_pengiriman"]');
                    const waktuInput = form.find('input[name="waktu_pengiriman"]');

                    if (!tanggalInput.val()) {
                        tanggalInput.val(new Date().toISOString().split('T')[0]);
                    }

                    if (!waktuInput.val()) {
                        waktuInput.val('09:00');
                    }

                    console.log('Default values set:', {
                        tanggal: tanggalInput.val(),
                        waktu: waktuInput.val()
                    });
                }
            });

            $('.modal').on('hide.bs.modal', function(e) {
                console.log('Modal hiding:', this.id);

                // Reset form ketika modal ditutup
                const form = $(this).find('form');
                if (form.length > 0) {
                    // Re-enable submit button
                    const submitBtn = form.find('button[type="submit"]');
                    submitBtn.prop('disabled', false);
                    submitBtn.html('<i class="fas fa-save mr-1"></i>Simpan Jadwal');

                    // Enable semua input
                    form.find('input, select, textarea').prop('disabled', false);
                }
            });

            // Auto-hide alerts
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
        });

        // Global function untuk testing
        function testFormSubmission(formId) {
            const form = $(formId);
            const formData = new FormData(form[0]);

            console.log('Testing form submission for:', formId);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            // Test submit
            form.submit();
        }
    </script>
@endsection
