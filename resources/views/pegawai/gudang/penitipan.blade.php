@extends('layouts.gudang')

@section('content')
    <div class="container">
        <h2 class="mb-4">Tambah Penitipan Baru</h2>

        {{-- Alert untuk menampilkan informasi --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Informasi tentang filter barang --}}
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            <strong>Informasi:</strong>
            Hanya barang yang belum dititipkan dan bukan barang hunter yang dapat ditambahkan ke penitipan.
            Barang dengan status "sudah diambil penitip" atau "sudah didonasikan" juga tidak ditampilkan.
        </div>

        {{-- Form Tambah Penitipan --}}
        <form action="{{ route('penitipan.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_penitip" class="form-label">Nama Penitip</label>
                <select class="form-control @error('id_penitip') is-invalid @enderror" name="id_penitip" required>
                    <option value="">-- Pilih Penitip --</option>
                    @foreach ($penitipList as $penitip)
                        <option value="{{ $penitip->id_penitip }}"
                            {{ old('id_penitip') == $penitip->id_penitip ? 'selected' : '' }}>
                            {{ $penitip->nama_penitip }} ({{ $penitip->email_penitip }})
                        </option>
                    @endforeach
                </select>
                @error('id_penitip')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="id_barang[]" class="form-label">Pilih Barang Titipan</label>
                @if ($barangList->count() > 0)
                    <select class="form-control @error('id_barang') is-invalid @enderror" name="id_barang[]" multiple
                        required>
                        @foreach ($barangList as $barang)
                            <option value="{{ $barang->id_barang }}"
                                {{ in_array($barang->id_barang, old('id_barang', [])) ? 'selected' : '' }}>
                                {{ $barang->nama_barang_titipan }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">
                        Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu.
                        <strong>{{ $barangList->count() }} barang tersedia</strong> untuk penitipan.
                    </small>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Tidak ada barang tersedia!</strong>
                        Semua barang sudah dititipkan, terhubung dengan hunter, atau memiliki status yang tidak memungkinkan
                        untuk dititipkan.
                    </div>
                    <select class="form-control" disabled>
                        <option>Tidak ada barang tersedia</option>
                    </select>
                @endif
                @error('id_barang')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-success" {{ $barangList->count() == 0 ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Simpan Penitipan
            </button>

            @if ($barangList->count() == 0)
                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i>
                    Tombol simpan dinonaktifkan karena tidak ada barang yang tersedia.
                </small>
            @endif
        </form>

        <hr>

        {{-- Statistik Barang --}}
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Statistik Barang</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-success">{{ $barangList->count() }}</h4>
                                    <small class="text-muted">Tersedia untuk Penitipan</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-warning">{{ \App\Models\DetailPenitipan::count() }}</h4>
                                    <small class="text-muted">Sudah Dititipkan</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-info">{{ \App\Models\BarangTitipanHunter::count() }}</h4>
                                    <small class="text-muted">Barang Hunter</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="text-center">
                                    <h4 class="text-primary">{{ \App\Models\BarangTitipan::count() }}</h4>
                                    <small class="text-muted">Total Barang</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-5 mb-4">Daftar Penitipan</h2>

        {{-- Tabel Daftar Penitipan --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Penitipan Aktif</h5>
            </div>
            <div class="card-body">
                @if ($penitipanList->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID Penitipan</th>
                                <th>Nama Penitip</th>
                                <th>Barang Titipan</th>
                                <th>Tanggal Penitipan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penitipanList as $penitipan)
                                <tr>
                                    <td>{{ $penitipan->id_penitipan }}</td>
                                    <td>
                                        {{ $penitipan->penitip->nama_penitip ?? 'Tidak ditemukan' }}
                                        @if ($penitipan->penitip)
                                            <br><small class="text-muted">{{ $penitipan->penitip->email_penitip }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach ($detailPenitipan->where('id_penitipan', $penitipan->id_penitipan) as $detail)
                                                <li>
                                                    {{ $detail->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}
                                                    @if ($detail->barang)
                                                        <br><small class="text-muted">{{ $detail->barang->jenis_barang }} -
                                                            Rp{{ number_format($detail->barang->harga_barang, 0, ',', '.') }}</small>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($penitipan->tanggal_penitipan)->format('d M Y') }}</td>
                                    <td>
                                        <span
                                            class="badge badge-{{ $penitipan->status_barang == 'sudah diambil penitip' ? 'success' : 'primary' }}">
                                            {{ ucfirst($penitipan->status_barang) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- @if (session('nota_path') && session('last_penitipan_id') == $penitipan->id_penitipan)
                                            <a href="{{ route('penitipan.download', $penitipan->id_penitipan) }}"
                                                target="_blank" class="btn btn-primary btn-sm">
                                                <i class="fas fa-download"></i> Download Nota
                                            </a>
                                        @endif --}}

                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#detailModal{{ $penitipan->id_penitipan }}">
                                            <i class="fas fa-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada penitipan</h5>
                        <p class="text-muted">Tambahkan penitipan baru menggunakan form di atas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Detail untuk setiap penitipan --}}
    @foreach ($penitipanList as $penitipan)
        <div class="modal fade" id="detailModal{{ $penitipan->id_penitipan }}" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel{{ $penitipan->id_penitipan }}" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailModalLabel{{ $penitipan->id_penitipan }}">
                            Detail Penitipan #{{ $penitipan->id_penitipan }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Informasi Penitipan</strong></h6>
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>ID Penitipan:</strong></td>
                                        <td>{{ $penitipan->id_penitipan }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Penitipan:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($penitipan->tanggal_penitipan)->format('d M Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Selesai:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($penitipan->tanggal_selesai_penitipan)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Batas Pengambilan:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($penitipan->tanggal_batas_pengambilan)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $penitipan->status_barang == 'sudah diambil penitip' ? 'success' : 'primary' }}">
                                                {{ ucfirst($penitipan->status_barang) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($penitipan->tanggal_pengambilan)
                                        <tr>
                                            <td><strong>Tanggal Pengambilan:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($penitipan->tanggal_pengambilan)->format('d M Y H:i') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Informasi Penitip</strong></h6>
                                @if($penitipan->penitip)
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Nama:</strong></td>
                                            <td>{{ $penitipan->penitip->nama_penitip }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $penitipan->penitip->email_penitip }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Telepon:</strong></td>
                                            <td>{{ $penitipan->penitip->nomor_telepon_penitip ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIK:</strong></td>
                                            <td>{{ $penitipan->penitip->nik_penitip ?? '-' }}</td>
                                        </tr>
                                    </table>
                                @else
                                    <p class="text-muted">Data penitip tidak ditemukan</p>
                                @endif
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h6><strong>Daftar Barang Titipan</strong></h6>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Jenis</th>
                                        <th>Harga</th>
                                        <th>Berat</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach ($detailPenitipan->where('id_penitipan', $penitipan->id_penitipan) as $detail)
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $detail->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}</td>
                                            <td>{{ $detail->barang->jenis_barang ?? '-' }}</td>
                                            <td>
                                                @if($detail->barang)
                                                    Rp{{ number_format($detail->barang->harga_barang, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $detail->barang->berat_barang ?? '-' }} gr</td>
                                            <td>
                                                @if($detail->barang)
                                                    <span class="badge badge-secondary">{{ ucfirst($detail->barang->status_barang) }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('penitipan.download', $penitipan->id_penitipan) }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Nota
                        </a>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Script untuk auto download nota --}}
    {{-- @if (session('nota_path'))
        <script>
            window.onload = function() {
                const link = document.createElement('a');
                link.href = "{{ asset('storage/nota/' . session('nota_path')) }}";
                link.download = "";
                link.style.display = 'none';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };
        </script>
    @endif --}}

    {{-- Script untuk enhanced UX dan Debug --}}
    <script>
        $(document).ready(function() {
            console.log('jQuery loaded:', typeof $ !== 'undefined');
            console.log('Bootstrap modal available:', typeof $.fn.modal !== 'undefined');
            
            // Debug: Log semua modal yang ada
            $('.modal').each(function() {
                console.log('Modal found:', this.id);
            });

            // Enhanced select untuk multiple selection
            const barangSelect = document.querySelector('select[name="id_barang[]"]');
            if (barangSelect) {
                barangSelect.addEventListener('change', function() {
                    const selectedCount = this.selectedOptions.length;
                    const helpText = this.nextElementSibling;
                    if (helpText && helpText.classList.contains('text-muted')) {
                        const originalText = helpText.textContent.split('.')[0] + '.';
                        helpText.innerHTML = originalText + ` <strong>${selectedCount} barang dipilih</strong>.`;
                    }
                });
            }

            // Form validation
            const form = document.querySelector('form[action="{{ route('penitipan.store') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const penitipSelect = document.querySelector('select[name="id_penitip"]');
                    const barangSelect = document.querySelector('select[name="id_barang[]"]');
                    
                    if (!penitipSelect.value) {
                        e.preventDefault();
                        alert('Silakan pilih penitip terlebih dahulu!');
                        penitipSelect.focus();
                        return false;
                    }
                    
                    if (!barangSelect.value || barangSelect.selectedOptions.length === 0) {
                        e.preventDefault();
                        alert('Silakan pilih minimal satu barang untuk dititipkan!');
                        barangSelect.focus();
                        return false;
                    }
                });
            }

            // Debug: Test modal functionality
            $('[data-toggle="modal"]').on('click', function(e) {
                e.preventDefault();
                const target = $(this).attr('data-target');
                console.log('Modal button clicked, target:', target);
                console.log('Target element exists:', $(target).length > 0);
                
                if ($(target).length > 0) {
                    $(target).modal('show');
                } else {
                    console.error('Modal target not found:', target);
                }
            });

            // Debug: Modal events
            $('.modal').on('show.bs.modal', function (e) {
                console.log('Modal showing:', this.id);
            });

            $('.modal').on('shown.bs.modal', function (e) {
                console.log('Modal shown:', this.id);
            });

            $('.modal').on('hide.bs.modal', function (e) {
                console.log('Modal hiding:', this.id);
            });
        });
    </script>
@endsection
