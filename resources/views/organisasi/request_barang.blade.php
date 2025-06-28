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
                    <div class="invalid-feedback">
                        Nama barang minimal 3 karakter
                    </div>
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
                    <div class="form-text">Tanggal tidak boleh di masa lalu</div>
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
                                <th width="8%">ID Request</th>
                                <th width="12%">ID Organisasi</th>
                                <th width="25%">Nama Barang</th>
                                <th width="12%">Tanggal Request</th>
                                <th width="12%">Status</th>
                                <th width="11%">Aksi</th>
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
                                            <!-- Hanya bisa edit jika status masih pending -->
                                            @if($req->status_request == 'pending')
                                                <button type="button" 
                                                        class="btn btn-sm btn-warning" 
                                                        onclick="editRequest({{ json_encode($req) }})" 
                                                        title="Edit Request"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        onclick="confirmDelete({{ $req->id_request }})" 
                                                        title="Hapus Request"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <button type="button" 
                                                        class="btn btn-sm btn-info" 
                                                        onclick="viewRequest({{ json_encode($req) }})" 
                                                        title="Lihat Detail"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <span class="badge bg-light text-dark ms-1" 
                                                      title="Request sudah diproses, tidak dapat diedit"
                                                      data-bs-toggle="tooltip">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @endif
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
                    @if(request('keyword'))
                        <h5 class="text-muted">Tidak ada request yang ditemukan</h5>
                        <p class="text-muted">Tidak ada request barang yang cocok dengan pencarian "{{ request('keyword') }}"</p>
                        <a href="{{ route('organisasi.requestBarang.index') }}" class="btn btn-secondary me-2">
                            <i class="fas fa-list me-2"></i>Lihat Semua Request
                        </a>
                        <button type="button" class="btn btn-primary" onclick="showAddForm()">
                            <i class="fas fa-plus me-2"></i>Buat Request Baru
                        </button>
                    @else
                        <h5 class="text-muted">Belum ada request barang</h5>
                        <p class="text-muted">Mulai buat request barang donasi pertama Anda</p>
                        <button type="button" class="btn btn-primary" onclick="showAddForm()">
                            <i class="fas fa-plus me-2"></i>Buat Request Baru
                        </button>
                    @endif
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

<!-- View Request Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="fas fa-eye text-info me-2"></i>Detail Request Barang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>ID Request:</strong> <span id="view-id"></span></p>
                        <p><strong>ID Organisasi:</strong> <span id="view-organisasi"></span></p>
                        <p><strong>Nama Barang:</strong> <span id="view-nama-barang"></span></p>
                        <p><strong>Tanggal Request:</strong> <span id="view-tanggal"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Status:</strong> <span id="view-status"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
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
        
        // Clear any validation classes
        clearValidationClasses();
        
        // Update form action and method
        document.getElementById('requestForm').action = '{{ route('organisasi.requestBarang.store') }}';
        document.getElementById('methodField').innerHTML = '';
        
        // Update form title and button
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-plus-circle me-2"></i>Form Request Barang Baru';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Simpan Request';
        
        // Show form
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('nama_request_barang').focus();
        
        // Update character count
        updateCharCount();
        
        // Scroll to form
        document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
    }
    
    // Edit Request (hanya untuk status pending)
    function editRequest(request) {
        // Cek apakah status masih pending
        if (request.status_request !== 'pending') {
            alert('Request yang sudah diproses tidak dapat diedit!');
            return;
        }
        
        isEditMode = true;
        editRequestId = request.id_request;
        
        // Clear any validation classes
        clearValidationClasses();
        
        // Fill form with request data - SAMA SEPERTI ADD FORM
        document.getElementById('id_organisasi').value = request.id_organisasi;
        document.getElementById('nama_request_barang').value = request.nama_request_barang;
        document.getElementById('tanggal_request').value = request.tanggal_request;
        
        // Update form action and method
        document.getElementById('requestForm').action = `{{ url('organisasi/request-barang') }}/${request.id_request}`;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        // Update form title and button
        document.getElementById('formTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Form Edit Request Barang';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Update Request';
        
        // Show form
        document.getElementById('formCard').style.display = 'block';
        document.getElementById('nama_request_barang').focus();
        
        // Update character count
        updateCharCount();
        
        // Scroll to form
        document.getElementById('formCard').scrollIntoView({ behavior: 'smooth' });
    }
    
    // View Request Detail
    function viewRequest(request) {
        // Fill modal with request data
        document.getElementById('view-id').textContent = '#' + request.id_request;
        document.getElementById('view-organisasi').textContent = request.id_organisasi;
        document.getElementById('view-nama-barang').textContent = request.nama_request_barang;
        document.getElementById('view-tanggal').textContent = formatDate(request.tanggal_request);
        document.getElementById('view-status').innerHTML = getStatusBadge(request.status_request);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('viewModal'));
        modal.show();
    }
    
    // Hide Form
    function hideForm() {
        document.getElementById('formCard').style.display = 'none';
        document.getElementById('requestForm').reset();
        clearValidationClasses();
        isEditMode = false;
        editRequestId = null;
    }
    
    // Clear validation classes
    function clearValidationClasses() {
        const inputs = document.querySelectorAll('#requestForm input, #requestForm textarea');
        inputs.forEach(input => {
            input.classList.remove('is-valid', 'is-invalid');
        });
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
    
    // Helper function to get status badge HTML
    function getStatusBadge(status) {
        switch(status) {
            case 'pending':
                return '<span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Pending</span>';
            case 'diterima':
                return '<span class="badge bg-success"><i class="fas fa-check me-1"></i>Diterima</span>';
            case 'ditolak':
                return '<span class="badge bg-danger"><i class="fas fa-times me-1"></i>Ditolak</span>';
            default:
                return '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
        }
    }
    
    // Helper function to format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    
    // Helper function to format datetime
    function formatDateTime(dateTimeString) {
        const date = new Date(dateTimeString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Form Validation
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        const idOrganisasi = document.getElementById('id_organisasi').value.trim();
        const namaBarang = document.getElementById('nama_request_barang').value.trim();
        const tanggalRequest = document.getElementById('tanggal_request').value;
        let isValid = true;

        // Reset validation classes
        clearValidationClasses();

        // Validasi ID Organisasi
        if (!idOrganisasi || isNaN(idOrganisasi) || idOrganisasi <= 0) {
            document.getElementById('id_organisasi').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('id_organisasi').classList.add('is-valid');
        }

        // Validasi nama barang
        if (!namaBarang) {
            document.getElementById('nama_request_barang').classList.add('is-invalid');
            isValid = false;
        } else if (namaBarang.length < 3) {
            document.getElementById('nama_request_barang').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('nama_request_barang').classList.add('is-valid');
        }

        // Validasi tanggal
        if (!tanggalRequest) {
            document.getElementById('tanggal_request').classList.add('is-invalid');
            isValid = false;
        } else {
            const today = new Date().toISOString().split('T')[0];
            if (!isEditMode && tanggalRequest < today) {
                document.getElementById('tanggal_request').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('tanggal_request').classList.add('is-valid');
            }
        }

        if (!isValid) {
            e.preventDefault();
            alert('Mohon perbaiki field yang tidak valid');
            return false;
        }

        // Konfirmasi sebelum menyimpan
        const action = isEditMode ? 'mengupdate' : 'menyimpan';
        if (!confirm(`Apakah Anda yakin ingin ${action} request ini?`)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Real-time validation untuk nama barang
    document.getElementById('nama_request_barang').addEventListener('input', function() {
        const namaBarang = this.value.trim();
        if (namaBarang.length === 0) {
            this.classList.remove('is-valid', 'is-invalid');
        } else if (namaBarang.length < 3) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Real-time validation untuk tanggal
    document.getElementById('tanggal_request').addEventListener('change', function() {
        const tanggalRequest = this.value;
        const today = new Date().toISOString().split('T')[0];
        
        if (!tanggalRequest) {
            this.classList.remove('is-valid', 'is-invalid');
        } else if (!isEditMode && tanggalRequest < today) {
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
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
        
        // Set minimum date untuk tanggal request (hari ini)
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('tanggal_request').setAttribute('min', today);
        
        // Initialize character count
        updateCharCount();
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Check if there's an edit request in URL (for direct edit links)
    @if(isset($editRequest))
        document.addEventListener('DOMContentLoaded', function() {
            editRequest(@json($editRequest));
        });
    @endif
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + N untuk form baru
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            showAddForm();
        }
        
        // Escape untuk hide form
        if (e.key === 'Escape') {
            hideForm();
        }
    });
    
    // Auto-save functionality
    ['nama_request_barang', 'deskripsi_request'].forEach(fieldId => {
        document.getElementById(fieldId).addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(autoSaveDraft, 2000); // Auto-save after 2 seconds of inactivity
        });
    });
    
    // Load draft when showing add form (modify showAddForm function)
    const originalShowAddForm = showAddForm;
    showAddForm = function() {
        originalShowAddForm();
        setTimeout(loadDraft, 100); // Load draft after form is shown
    };
    
    // Clear draft on successful form submission
    document.getElementById('requestForm').addEventListener('submit', function() {
        // Clear draft only if form validation passes
        setTimeout(function() {
            if (!document.querySelector('.is-invalid')) {
                clearDraft();
            }
        }, 100);
    });
    
    // Show success message with auto-hide
    function showSuccessMessage(message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert at the top of the container
        const container = document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            if (alertDiv && alertDiv.parentNode) {
                const bsAlert = new bootstrap.Alert(alertDiv);
                bsAlert.close();
            }
        }, 5000);
    }
    
    // Enhanced form validation with better UX
    function validateField(fieldId, validationFn, errorMessage) {
        const field = document.getElementById(fieldId);
        const value = field.value.trim();
        
        if (validationFn(value)) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
            return true;
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            
            // Show custom error message
            let feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) {
                feedback.textContent = errorMessage;
            }
            return false;
        }
    }
    
    // Enhanced real-time validation
    document.getElementById('nama_request_barang').addEventListener('blur', function() {
        validateField('nama_request_barang', 
            value => value.length >= 3, 
            'Nama barang minimal 3 karakter'
        );
    });
    
    document.getElementById('tanggal_request').addEventListener('blur', function() {
        const today = new Date().toISOString().split('T')[0];
        validateField('tanggal_request', 
            value => value && (isEditMode || value >= today), 
            'Tanggal tidak boleh di masa lalu'
        );
    });
    
    // Form state management
    let formState = {
        isDirty: false,
        originalData: {}
    };
    
    // Track form changes
    function trackFormChanges() {
        const inputs = document.querySelectorAll('#requestForm input, #requestForm textarea');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                formState.isDirty = true;
            });
        });
    }
    
    // Warn user about unsaved changes
    function checkUnsavedChanges() {
        if (formState.isDirty) {
            return confirm('Anda memiliki perubahan yang belum disimpan. Yakin ingin meninggalkan halaman?');
        }
        return true;
    }
    
    // Override hideForm to check for unsaved changes
    const originalHideForm = hideForm;
    hideForm = function() {
        if (checkUnsavedChanges()) {
            originalHideForm();
            formState.isDirty = false;
        }
    };
    
    // Initialize form state tracking
    document.addEventListener('DOMContentLoaded', function() {
        trackFormChanges();
    });
    
    // Prevent accidental page refresh/close with unsaved changes
    window.addEventListener('beforeunload', function(e) {
        if (formState.isDirty) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // Enhanced search functionality
    function highlightSearchResults() {
        const keyword = '{{ request('keyword') }}';
        if (keyword) {
            const cells = document.querySelectorAll('table tbody td');
            cells.forEach(cell => {
                const text = cell.textContent;
                if (text.toLowerCase().includes(keyword.toLowerCase())) {
                    const regex = new RegExp(`(${keyword})`, 'gi');
                    cell.innerHTML = cell.innerHTML.replace(regex, '<mark>$1</mark>');
                }
            });
        }
    }
    
    // Call highlight function after DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        highlightSearchResults();
    });
    
    // Quick actions with keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Only trigger shortcuts when not typing in inputs
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        switch(e.key) {
            case 'n':
            case 'N':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    showAddForm();
                }
                break;
            case 'Escape':
                hideForm();
                break;
            case 'f':
            case 'F':
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    document.querySelector('input[name="keyword"]').focus();
                }
                break;
        }
    });
    
    // Accessibility improvements
    function improveAccessibility() {
        // Add ARIA labels
        document.getElementById('formCard').setAttribute('aria-label', 'Form Request Barang');
        document.querySelector('table').setAttribute('aria-label', 'Daftar Request Barang');
        
        // Add keyboard navigation for table
        const tableRows = document.querySelectorAll('table tbody tr');
        tableRows.forEach((row, index) => {
            row.setAttribute('tabindex', '0');
            row.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    const viewButton = row.querySelector('.btn-info');
                    const editButton = row.querySelector('.btn-warning');
                    if (editButton) {
                        editButton.click();
                    } else if (viewButton) {
                        viewButton.click();
                    }
                }
            });
        });
    }
    
    // Initialize accessibility improvements
    document.addEventListener('DOMContentLoaded', function() {
        improveAccessibility();
    });
    
    // Performance optimization: Debounce search
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // Apply debounce to search input
    const searchInput = document.querySelector('input[name="keyword"]');
    if (searchInput) {
        const debouncedSearch = debounce(function() {
            // Auto-submit search after user stops typing
            if (searchInput.value.length >= 3 || searchInput.value.length === 0) {
                searchInput.closest('form').submit();
            }
        }, 1000);
        
        searchInput.addEventListener('input', debouncedSearch);
    }
    
    // Mobile responsiveness enhancements
    function handleMobileView() {
        if (window.innerWidth < 768) {
            // Adjust table for mobile
            const table = document.querySelector('table');
            if (table) {
                table.classList.add('table-sm');
            }
            
            // Stack form buttons vertically on mobile
            const buttonGroup = document.querySelector('#requestForm .d-flex');
            if (buttonGroup) {
                buttonGroup.classList.remove('justify-content-between');
                buttonGroup.classList.add('flex-column', 'gap-2');
            }
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', handleMobileView);
    document.addEventListener('DOMContentLoaded', handleMobileView);
    
    // Error handling for AJAX requests (if any)
    function handleAjaxError(error) {
        console.error('AJAX Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi atau refresh halaman.');
    }
    
    // Form submission with loading state
    document.getElementById('requestForm').addEventListener('submit', function() {
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
        
        // Reset button after 5 seconds (fallback)
        setTimeout(function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 5000);
    });
    
    // Initialize all features when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Request Barang page initialized');
        
        // Show help tooltip on first visit
        if (!localStorage.getItem('request_help_shown')) {
            setTimeout(function() {
                alert('Tips: Gunakan Ctrl+N untuk membuat request baru, dan Escape untuk menutup form.');
                localStorage.setItem('request_help_shown', 'true');
            }, 2000);
        }
    });
</script>
@endpush
