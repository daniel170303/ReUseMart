@extends('layouts.owner')

@section('title', 'Kelola Barang Donasi')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Kelola Barang Donasi</h2>

        {{-- Notifikasi --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Request Donasi Pending --}}
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Request Donasi Pending ({{ $requests->count() }})
                </h5>
            </div>
            <div class="card-body">
                @if ($requests->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada request donasi dengan status pending.
                        <br><small>Debug: Total requests di database dapat dilihat di log.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Request</th>
                                    <th>Organisasi</th>
                                    <th>Nama Request Barang</th>
                                    <th>Tanggal Request</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $req)
                                    <tr>
                                        <td>{{ $req->id_request }}</td>
                                        <td>
                                            {{ $req->nama_organisasi ?? 'N/A' }}
                                            <br>
                                            <small class="text-muted">{{ $req->email_organisasi ?? '' }}</small>
                                        </td>
                                        <td>{{ $req->nama_request_barang }}</td>
                                        <td>{{ \Carbon\Carbon::parse($req->tanggal_request)->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <span class="badge bg-warning">{{ ucfirst($req->status_request) }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('request.terima', $req->id_request) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm"
                                                        onclick="return confirm('Yakin ingin menerima request ini?')">
                                                        <i class="fas fa-check me-1"></i>Terima
                                                    </button>
                                                </form>
                                                <button class="btn btn-danger btn-sm ms-1"
                                                    onclick="tolakRequest({{ $req->id_request }})">
                                                    <i class="fas fa-times me-1"></i>Tolak
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Daftar Donasi --}}
        <div class="card">
            <div class="card-header bg-success text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-hands-helping me-2"></i>Daftar Donasi ({{ $donasis->count() }})
                    </h5>
                </div>
            </div>
            <div class="card-body">
                @if ($donasis->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Belum ada data donasi.
                        <br><small>Debug: Cek log untuk melihat data yang tersedia.</small>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Donasi</th>
                                    <th>Barang</th>
                                    <th>Request</th>
                                    <th>Tanggal Donasi</th>
                                    <th>Penerima</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donasis as $donasi)
                                    <tr>
                                        <td>{{ $donasi->id_donasi }}</td>
                                        <td>
                                            <strong>{{ $donasi->nama_barang_titipan ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $donasi->jenis_barang ?? '' }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $donasi->nama_request_barang ?? 'N/A' }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $donasi->nama_organisasi ?? '' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($donasi->tanggal_donasi)->format('d/m/Y H:i') }}</td>
                                        <td>{{ $donasi->penerima_donasi }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-primary btn-sm btn-edit-donasi"
                                                    data-bs-toggle="modal" data-bs-target="#editDonasiModal"
                                                    data-id="{{ $donasi->id_donasi }}"
                                                    data-id_barang="{{ $donasi->id_barang }}"
                                                    data-id_request="{{ $donasi->id_request }}"
                                                    data-tanggal_donasi="{{ $donasi->tanggal_donasi }}"
                                                    data-penerima_donasi="{{ $donasi->penerima_donasi }}">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                                <form action="{{ route('owner.donasi.hapus', $donasi->id_donasi) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm ms-1"
                                                        onclick="return confirm('Yakin ingin menghapus donasi ini?')">
                                                        <i class="fas fa-trash me-1"></i>Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Edit Donasi -->
    <div class="modal fade" id="editDonasiModal" tabindex="-1" aria-labelledby="editDonasiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="editDonasiForm" method="POST" action="#">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDonasiModalLabel">
                            <i class="fas fa-edit me-2"></i>Edit Donasi
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="donasiId" />

                        <div class="mb-3">
                            <label for="id_barang" class="form-label">ID Barang</label>
                            <input type="number" class="form-control" id="id_barang" name="id_barang" required />
                        </div>

                        <div class="mb-3">
                            <label for="id_request" class="form-label">ID Request</label>
                            <input type="number" class="form-control" id="id_request" name="id_request" required />
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_donasi" class="form-label">Tanggal Donasi</label>
                            <input type="date" class="form-control" id="tanggal_donasi" name="tanggal_donasi"
                                required />
                        </div>

                        <div class="mb-3">
                            <label for="penerima_donasi" class="form-label">Penerima Donasi</label>
                            <input type="text" class="form-control" id="penerima_donasi" name="penerima_donasi"
                                required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var editDonasiModal = document.getElementById('editDonasiModal');
            editDonasiModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var id = button.getAttribute('data-id');
                var id_barang = button.getAttribute('data-id_barang');
                var id_request = button.getAttribute('data-id_request');
                var tanggal_donasi = button.getAttribute('data-tanggal_donasi');
                var penerima_donasi = button.getAttribute('data-penerima_donasi');

                // Format tanggal untuk input date
                var formattedDate = tanggal_donasi.split(' ')[0]; // Ambil bagian tanggal saja

                // Isi form dengan data yang diterima
                this.querySelector('#donasiId').value = id;
                this.querySelector('#id_barang').value = id_barang;
                this.querySelector('#id_request').value = id_request;
                this.querySelector('#tanggal_donasi').value = formattedDate;
                this.querySelector('#penerima_donasi').value = penerima_donasi;

                // Set action form secara dinamis menggunakan base URL Laravel
                var form = this.querySelector('#editDonasiForm');
                var baseUrl = "{{ url('owner/donasi') }}";
                form.action = baseUrl + "/" + id + "/edit";
            });
        });
    </script>
    <script>
        function tolakRequest(id) {
            if (confirm('Yakin ingin menolak request ini?')) {
                // Buat form dinamis untuk menolak request
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/request/${id}/tolak`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                form.appendChild(csrfToken);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
@endpush
