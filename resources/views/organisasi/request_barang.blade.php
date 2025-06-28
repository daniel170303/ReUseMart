@extends('layouts.organisasi')

@section('title', 'Request Barang Donasi')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clipboard-list me-2"></i>Request Barang Donasi
        </h1>
        <button type="button" class="btn btn-primary" onclick="showAddForm()">
            <i class="fas fa-plus me-2"></i>Buat Request Baru
        </button>
    </div>

    <!-- Search Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-search me-2"></i>Pencarian Request
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('organisasi.requestBarang.search') }}" method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="keyword" class="form-control" 
                           placeholder="Cari berdasarkan nama barang atau status..." 
                           value="{{ request('keyword') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Cari
                    </button>
                    @if(request('keyword'))
                        <a href="{{ route('organisasi.requestBarang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Form Add/Edit Request (Hidden by default) -->
    <div class="card shadow mb-4" id="formCard" style="display: none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary" id="formTitle">
                <i class="fas fa-plus-circle me-2"></i>Form Request Barang Baru
            </h6>
        </div>
        <div class="card-body">
            <form id="requestForm" method="POST">
                @csrf
                <div id="methodField"></div>

                <!-- ID Organisasi (Auto-filled dari session) -->
                <div class="mb-3">
                    <label for="id_organisasi" class="form-label">
                        <i class="fas fa-building me-1"></i>ID Organisasi <span class="text-danger">*</span>
                    </label>
                    <input type="number" 
                           class="form-control" 
                           id="id_organisasi" 
                           name="id_organisasi" 
                           value="{{ session('user_id') ?? old('id_organisasi') }}" 
                           readonly>
                    <div class="form-text">ID organisasi diambil otomatis dari akun yang sedang login</div>
                </div>

                <!-- Nama Barang -->
                <div class="mb-3">
                    <label for="nama_request_barang" class="form-label">
                        <i class="fas fa-box me-1"></i>Nama Barang yang Diminta <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="nama_request_barang" 
                           name="nama_request_barang" 
                           placeholder="Contoh: Baju Bekas, Sepatu Anak, Tas Sekolah, dll."
                           required>
                </div>

                <!-- Tanggal Request -->
                <div class="mb-3">
                    <label for="tanggal_request" class="form-label">
                        <i class="fas fa-calendar me-1"></i>Tanggal Request <span class="text-danger">*</span>
                    </label>
                    <input type="date" 
                           class="form-control" 
                           id="tanggal_request" 
                           name="tanggal_request" 
                           value="{{ date('Y-m-d') }}"
                           required>
                </div>

                <!-- Status Request (Hidden untuk create, visible untuk edit) -->
                <div class="mb-3" id="statusField" style="display: none;">
                    <label for="status_request" class="form-label">
                        <i class="fas fa-flag me-1"></i>Status Request <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="status_request" name="status_request">
                        <option value="pending">Pending</option>
                        <option value="diterima">Diterima</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                    <div class="form-text">Status akan otomatis diset ke "pending" untuk request baru</div>
                </div>

                <!-- Button Group -->
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" onclick="hideForm()">
                        <i class="fas fa-times me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i>Simpan Request
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Request List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list me-2"></i>Daftar Request Barang
            </h6>
        </div>
        <div class="card-body">
            @if(isset($requests) && $requests->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th width="10%">ID Request</th>
                                <th width="15%">ID Organisasi</th>
                                <th width="25%">Nama Barang</th>
                                <th width="15%">Tanggal Request</th>
                                <th width="15%">Status</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($requests as $req)
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">#{{ $req->id_request }}</span>
                                    </td>
                                    <td class="text-center">
                                        {{ $req->id_organisasi }}
                                    </td>
                                    <td>
                                        <strong>{{ $req->nama_request_barang }}</strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($req->tanggal_request)->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($req->status_request == 'pending')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @elseif($req->status_request == 'diterima')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Diterima
                                            </span>
                                        @elseif($req->status_request == 'ditolak')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times me-1"></i>Ditolak
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($req->status_request) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    onclick="editRequest({{ json_encode($req) }})" 
                                                    title="Edit Request">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete({{ $req->id_request }})" 
                                                    title="Hapus Request">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        
                                        <!-- Form Delete (Hidden) -->
                                        <form id="delete-form-{{ $req->id_request }}" 
                                              action="{{ route('organisasi.requestBarang.destroy', $req->id_request) }}" 
                                              method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(method_exists($requests, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada request barang</h5>
                    <p class="text-muted">Mulai buat request barang donasi pertama Anda</p>
                    <button type="button" class="btn btn-primary" onclick="showAddForm()">
                        <i class="fas fa-plus me-2"></i>Buat Request Baru
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus request barang ini?</p>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash me-2"></i>Hapus
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let deleteFormId = null;
    let isEditMode = false;
    let editRequestId = null;
    
    // Show Add Form
    function showAddForm() {
        isEditMode = false;
        editRequestId = null;
        
        // Reset form
        document.getElementById('requestForm').reset();
        document.getElementById('id_organisasi').value = '{{ session('user_id') ?? '' }}';
        document.getElementById('tanggal_request').value = '{{ date('Y-m-d') }}';
        
        // Hide status field for new request
        document.getElementById('statusField').style.display = 'none';
        
        // Update form action and method
        document.getElementById('requestForm').action = '{{ route('organisasi.requestBarang.store') }}';
        document.getElementById('methodField').innerHTML = '';
        
        // Update form title and button
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Form Request Barang Baru';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Simpan Request';
        
        // Show form
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('nama_request_barang').focus();
        
        // Scroll to form
        document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
    }
    
    // Edit Request
    function editRequest(request) {
        isEditMode = true;
        editRequestId = request.id_request;
        
        // Fill form with request data
        document.getElementById('id_organisasi').value = request.id_organisasi;
        document.getElementById('nama_request_barang').value = request.nama_request_barang;
        document.getElementById('tanggal_request').value = request.tanggal_request;
        document.getElementById('status_request').value = request.status_request;
        
        // Show status field for edit
        document.getElementById('statusField').style.display = 'block';
        
        // Update form action and method
        document.getElementById('requestForm').action = `{{ url('organisasi/request-barang') }}/${request.id_request}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Update form title and button
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Form Edit Request Barang';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Request';
        
        // Show form
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('nama_request_barang').focus();
        
        // Scroll to form
        document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
    }
    
    // Hide Form
    function hideForm() {
        document.getElementById('formCard').style.display = 'none';
        document.getElementById('requestForm').reset();
        document.getElementById('statusField').style.display = 'none';
        isEditMode = false;
        editRequestId = null;
    }
    
    // Confirm Delete
    function confirmDelete(requestId) {
        deleteFormId = requestId;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    // Execute Delete
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteFormId) {
            document.getElementById('delete-form-' + deleteFormId).submit();
        }
    });
    
    // Form Validation
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        const idOrganisasi = document.getElementById('id_organisasi').value.trim();
        const namaBarang = document.getElementById('nama_request_barang').value.trim();
        const tanggalRequest = document.getElementById('tanggal_request').value;

        if (!idOrganisasi || !namaBarang || !tanggalRequest) {
            e.preventDefault();
            alert('Mohon lengkapi semua field yang wajib diisi (*)');
            return false;
        }

        // Validasi ID Organisasi harus berupa angka
        if (isNaN(idOrganisasi) || idOrganisasi <= 0) {
            e.preventDefault();
            alert('ID Organisasi harus berupa angka yang valid');
            return false;
        }

        // Validasi tanggal tidak boleh di masa lalu (kecuali untuk edit)
        const today = new Date().toISOString().split('T')[0];
        if (!isEditMode && tanggalRequest < today) {
            e.preventDefault();
            alert('Tanggal request tidak boleh di masa lalu');
            return false;
        }

        // Konfirmasi sebelum menyimpan
        const action = isEditMode ? 'mengupdate' : 'menyimpan';
        if (!confirm(`Apakah Anda yakin ingin ${action} request ini?`)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                if (alert && alert.parentNode) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    });
    
    // Check if there's an edit request in URL (for direct edit links)
    @if(isset($editRequest))
        document.addEventListener('DOMContentLoaded', function() {
            editRequest(@json($editRequest));
        });
    @endif
</script>
@endpush
