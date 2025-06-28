@extends('layouts.cs')

@section('title', 'Data Penitip')

@section('content')
<div class="container mt-4">
    <h2>Data Penitip</h2>

    {{-- Pesan sukses/error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Validasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <h6><i class="fas fa-exclamation-triangle me-2"></i>Terjadi kesalahan:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Form Pencarian --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-search me-2"></i>Pencarian Penitip
            </h5>
        </div>
        <div class="card-body">
            <form id="searchForm" method="GET" action="{{ route('cs.penitip') }}" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               name="keyword" 
                               id="searchInput"
                               value="{{ request('keyword') ?? $keyword ?? '' }}" 
                               placeholder="Cari berdasarkan nama, NIK, telepon, email, atau ID..." 
                               class="form-control">
                    </div>
                    <small class="form-text text-muted">
                        Masukkan kata kunci untuk mencari penitip berdasarkan nama, NIK, nomor telepon, email, atau ID
                    </small>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100" id="searchBtn">
                        <i class="fas fa-search me-1"></i>Cari
                    </button>
                    @if(request('keyword') || isset($keyword))
                        <a href="{{ route('cs.penitip') }}" class="btn btn-secondary w-100 mt-2">
                            <i class="fas fa-times me-1"></i>Reset
                        </a>
                    @endif
                </div>
            </form>
            
            {{-- Search Results Info --}}
            @if(request('keyword') || isset($keyword))
                <div class="mt-3">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Hasil pencarian untuk:</strong> "{{ request('keyword') ?? $keyword }}" 
                        - Ditemukan <strong>{{ $penitips->count() }}</strong> penitip
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Form Tambah / Edit --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0" id="formTitle">
                <i class="fas fa-plus me-2"></i>Form Tambah Penitip
            </h5>
        </div>
        <div class="card-body">
            <form id="penitipForm" method="POST" action="{{ route('penitip.store') }}">
                @csrf
                <input type="hidden" name="id_penitip" id="id_penitip">
                <input type="hidden" name="_method" id="_method">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_penitip" class="form-label">
                            Nama Penitip <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nama_penitip') is-invalid @enderror" 
                               name="nama_penitip" 
                               id="nama_penitip" 
                               required 
                               value="{{ old('nama_penitip') }}"
                               placeholder="Masukkan nama lengkap penitip">
                        @error('nama_penitip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nik_penitip" class="form-label">
                            NIK <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nik_penitip') is-invalid @enderror" 
                               name="nik_penitip" 
                               id="nik_penitip" 
                               required 
                               value="{{ old('nik_penitip') }}"
                               placeholder="16 digit NIK"
                               maxlength="16">
                        @error('nik_penitip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">NIK harus 16 digit</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nomor_telepon_penitip" class="form-label">
                            Nomor Telepon <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('nomor_telepon_penitip') is-invalid @enderror" 
                               name="nomor_telepon_penitip" 
                               id="nomor_telepon_penitip" 
                               required 
                               value="{{ old('nomor_telepon_penitip') }}"
                               placeholder="Contoh: 081234567890">
                        @error('nomor_telepon_penitip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email_penitip" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email_penitip') is-invalid @enderror" 
                               name="email_penitip" 
                               id="email_penitip" 
                               required 
                               value="{{ old('email_penitip') }}"
                               placeholder="contoh@email.com">
                        @error('email_penitip')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password_penitip" class="form-label">
                        Password <span class="text-danger" id="passwordRequired">*</span>
                    </label>
                    <input type="password" 
                           class="form-control @error('password_penitip') is-invalid @enderror" 
                           name="password_penitip" 
                           id="password_penitip" 
                           required
                           placeholder="Minimal 8 karakter">
                    @error('password_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted" id="passwordHelp">Minimal 8 karakter</small>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="resetBtn">
                        <i class="fas fa-times me-1"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Daftar Penitip
                <span class="badge bg-primary ms-2">{{ $penitips->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($penitips->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th width="8%">ID</th>
                                <th width="20%">Nama</th>
                                <th width="15%">NIK</th>
                                <th width="15%">Telepon</th>
                                <th width="20%">Email</th>

                                <th width="22%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($penitips as $penitip)
                                <tr>





                                    <td class="text-center">
                                        <span class="badge bg-secondary">#{{ $penitip->id_penitip }}</span>
                                    </td>
                                    <td>












                                        <strong>{{ $penitip->nama_penitip }}</strong>
                                    </td>
                                    <td>
                                        <code>{{ $penitip->nik_penitip }}</code>
                                    </td>
                                    <td>
                                        <i class="fas fa-phone me-1"></i>{{ $penitip->nomor_telepon_penitip }}
                                    </td>
                                    <td>
                                        <i class="fas fa-envelope me-1"></i>{{ $penitip->email_penitip }}
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-warning btn-sm editBtn"
                                                data-id="{{ $penitip->id_penitip }}"
                                                data-nama="{{ $penitip->nama_penitip }}"
                                                data-nik="{{ $penitip->nik_penitip }}"
                                                data-telp="{{ $penitip->nomor_telepon_penitip }}"
                                                data-email="{{ $penitip->email_penitip }}"
                                                title="Edit Penitip"
                                                type="button">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            
                                            <button class="btn btn-info btn-sm" 
                                                onclick="viewDetail({{ $penitip->id_penitip }})"
                                                title="Lihat Detail"
                                                type="button">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            
                                            <button class="btn btn-danger btn-sm deleteBtn" 
                                                data-id="{{ $penitip->id_penitip }}"
                                                data-nama="{{ $penitip->nama_penitip }}"
                                                title="Hapus Penitip"
                                                type="button">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else



                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    @if(request('keyword') || isset($keyword))
                        <h5 class="text-muted">Tidak ada penitip yang ditemukan</h5>
                        <p class="text-muted">Coba gunakan kata kunci yang berbeda atau reset pencarian</p>
                        <a href="{{ route('cs.penitip') }}" class="btn btn-primary">
                            <i class="fas fa-times me-2"></i>Reset Pencarian
                        </a>
                    @else
                        <h5 class="text-muted">Belum ada data penitip</h5>
                        <p class="text-muted">Mulai tambahkan data penitip pertama</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
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
                <p>Apakah Anda yakin ingin menghapus penitip <strong id="deleteName"></strong>?</p>
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

<!-- Modal Detail Penitip -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">
                    <i class="fas fa-user me-2"></i>Detail Penitip
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="detailContent">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Starting initialization...');
            
            // Get form elements
            const form = document.getElementById('penitipForm');
            const formTitle = document.getElementById('formTitle');
            const idInput = document.getElementById('id_penitip');
            const namaInput = document.getElementById('nama_penitip');
            const nikInput = document.getElementById('nik_penitip');
            const telpInput = document.getElementById('nomor_telepon_penitip');
            const emailInput = document.getElementById('email_penitip');
            const passwordInput = document.getElementById('password_penitip');
            const passwordRequired = document.getElementById('passwordRequired');
            const passwordHelp = document.getElementById('passwordHelp');
            const methodInput = document.getElementById('_method');
            const submitBtn = document.getElementById('submitBtn');
            const resetBtn = document.getElementById('resetBtn');
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');
            const searchBtn = document.getElementById('searchBtn');

            let currentDeleteId = null;
            let isEditMode = false;

            // Check if critical elements exist
            if (!form) {
                console.error('CRITICAL: Form not found!');
                return;
            }

            // Function to reset form
            function resetForm() {
                console.log('Resetting form...');
                
                form.reset();
                if (idInput) idInput.value = '';
                if (methodInput) methodInput.value = '';
                

                form.action = "{{ route('penitip.store') }}";
                if (formTitle) formTitle.innerHTML = '<i class="fas fa-plus me-2"></i>Form Tambah Penitip';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Simpan';
                
                if (passwordInput) {
                    passwordInput.required = true;
                    passwordInput.value = '';
                }
                if (passwordRequired) passwordRequired.style.display = 'inline';
                if (passwordHelp) passwordHelp.textContent = 'Minimal 8 karakter';
                
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                isEditMode = false;
                console.log('Form reset completed');
            }

            // Function to set edit mode
            function setEditMode(penitipData) {
                console.log('Setting edit mode with data:', penitipData);
                
                if (!namaInput || !nikInput || !telpInput || !emailInput) {
                    console.error('Required form elements not found!');
                    return;
                }
                
                isEditMode = true;
                
                // Fill form with data
                if (idInput) idInput.value = penitipData.id;
                namaInput.value = penitipData.nama || '';
                nikInput.value = penitipData.nik || '';
                telpInput.value = penitipData.telp || '';
                emailInput.value = penitipData.email || '';
                


                // Update form action
                const updateUrl = "{{ url('/cs/penitip') }}/" + penitipData.id;
                form.action = updateUrl;
                
                if (methodInput) methodInput.value = 'PUT';
                if (formTitle) formTitle.innerHTML = '<i class="fas fa-edit me-2"></i>Form Edit Penitip';
                if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update';
                
                // Make password optional for edit
                if (passwordInput) {
                    passwordInput.required = false;
                    passwordInput.value = '';
                }
                if (passwordRequired) passwordRequired.style.display = 'none';
                if (passwordHelp) passwordHelp.textContent = 'Kosongkan jika tidak ingin mengubah password';
                
                // Remove validation classes
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Scroll to form and focus
                form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                setTimeout(() => {
                    namaInput.focus();
                    namaInput.select();
                }, 500);
                
                console.log('Edit mode set successfully');


            }

            // Handle edit button click
            function handleEdit(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('=== EDIT BUTTON CLICKED ===');

                
                const penitipData = {
                    id: this.getAttribute('data-id'),
                    nama: this.getAttribute('data-nama'),
                    nik: this.getAttribute('data-nik'),
                    telp: this.getAttribute('data-telp'),
                    email: this.getAttribute('data-email')
                };
                
                console.log('Extracted data:', penitipData);
                



                if (!penitipData.id || !penitipData.nama) {
                    console.error('Invalid penitip data');
                    alert('Error: Data penitip tidak valid');
                    return;
                }
                






                setEditMode(penitipData);
            }

            // Handle delete button click
            function handleDelete(e) {
                e.preventDefault();
                e.stopPropagation();
                


                currentDeleteId = this.getAttribute('data-id');
                const nama = this.getAttribute('data-nama');
                
                if (!currentDeleteId) {
                    alert('Error: Data ID tidak ditemukan');
                    return;
                }
                
                const deleteNameEl = document.getElementById('deleteName');
                if (deleteNameEl) {
                    deleteNameEl.textContent = nama || 'Unknown';
                }
                
                const deleteModal = document.getElementById('deleteModal');
                if (deleteModal) {
                    const modal = new bootstrap.Modal(deleteModal);
                    modal.show();
                }
            }

            // Search functionality
            function handleSearch(e) {
                e.preventDefault();
                
                const keyword = searchInput.value.trim();
                console.log('Search triggered with keyword:', keyword);
                
                if (keyword === '') {
                    // If empty, redirect to index without search
                    window.location.href = "{{ route('cs.penitip') }}";
                    return;
                }
                
                // Show loading state
                searchBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mencari...';
                searchBtn.disabled = true;
                
                // Submit form
                searchForm.submit();
            }

            // View detail function
            window.viewDetail = function(id) {
                console.log('Viewing detail for ID:', id);
                
                const detailModal = document.getElementById('detailModal');
                const detailContent = document.getElementById('detailContent');
                
                if (!detailModal || !detailContent) {
                    console.error('Detail modal elements not found');
                    return;
                }
                
                // Show loading
                detailContent.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat detail penitip...</p>
                    </div>
                `;
                
                // Show modal
                const modal = new bootstrap.Modal(detailModal);
                modal.show();
                
                // Fetch detail data
                fetch(`{{ url('/cs/penitip') }}/${id}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.id_penitip) {
                        detailContent.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>ID Penitip:</strong></td>
                                            <td>#${data.id_penitip}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Nama:</strong></td>
                                            <td>${data.nama_penitip}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>NIK:</strong></td>
                                            <td>${data.nik_penitip}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td><strong>Telepon:</strong></td>
                                            <td>${data.nomor_telepon_penitip}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>${data.email_penitip}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Terdaftar:</strong></td>
                                            <td>${new Date(data.created_at).toLocaleDateString('id-ID')}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        `;
                    } else {
                        detailContent.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                Data penitip tidak ditemukan atau terjadi kesalahan.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error fetching detail:', error);
                    detailContent.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            Terjadi kesalahan saat memuat detail penitip.
                        </div>
                    `;
                });
            }

            // Attach event listeners using event delegation
            document.addEventListener('click', function(e) {
                // Handle edit button clicks
                if (e.target.closest('.editBtn')) {
                    const btn = e.target.closest('.editBtn');
                    handleEdit.call(btn, e);
                }
                
                // Handle delete button clicks
                if (e.target.closest('.deleteBtn')) {
                    const btn = e.target.closest('.deleteBtn');
                    handleDelete.call(btn, e);
                }
            });

            // Search form event listener
            if (searchForm) {
                searchForm.addEventListener('submit', handleSearch);
            }

            // Search input enter key
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        handleSearch(e);
                    }
                });

                // Real-time search (optional)
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const keyword = this.value.trim();
                    
                    if (keyword.length >= 3) {
                        searchTimeout = setTimeout(() => {
                            console.log('Real-time search for:', keyword);
                            // You can implement AJAX search here if needed
                        }, 500);
                    }
                });
            }

            // Reset button
            if (resetBtn) {
                resetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    resetForm();
                });
            }

            // Confirm delete
            const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
            if (confirmDeleteBtn) {
                confirmDeleteBtn.addEventListener('click', function() {
                    if (!currentDeleteId) {
                        console.error('No delete ID set');
                        return;
                    }
                    
                    console.log('Confirming delete for ID:', currentDeleteId);
                    
                    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menghapus...';
                    this.disabled = true;
                    
                    // Create delete form
                    const deleteForm = document.createElement('form');
                    deleteForm.method = 'POST';
                    deleteForm.action = "{{ url('/cs/penitip') }}/" + currentDeleteId;
                    deleteForm.style.display = 'none';
                    
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    
                    const methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    methodField.value = 'DELETE';
                    
                    deleteForm.appendChild(csrfToken);
                    deleteForm.appendChild(methodField);
                    document.body.appendChild(deleteForm);
                    
                    deleteForm.submit();
                });
            }

            // Form validation and submission
            form.addEventListener('submit', function(e) {
                console.log('Form submitted, mode:', isEditMode ? 'edit' : 'create');
                
                // Basic validation
                let isValid = true;
                const requiredFields = [
                    { input: namaInput, name: 'Nama Penitip' },
                    { input: nikInput, name: 'NIK' },
                    { input: telpInput, name: 'Nomor Telepon' },
                    { input: emailInput, name: 'Email' }
                ];
                
                // Add password validation for create mode or if password is filled
                if (!isEditMode || (passwordInput && passwordInput.value.trim() !== '')) {
                    requiredFields.push({ input: passwordInput, name: 'Password' });
                }
                
                // Clear previous validation
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Validate required fields
                requiredFields.forEach(field => {
                    if (!field.input || !field.input.value.trim()) {
                        if (field.input) field.input.classList.add('is-invalid');
                        isValid = false;
                    }
                });
                
                // Validate NIK length
                if (nikInput && nikInput.value.trim() && nikInput.value.trim().length !== 16) {
                    nikInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validate email format
                if (emailInput && emailInput.value.trim()) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value.trim())) {
                        emailInput.classList.add('is-invalid');
                        isValid = false;
                    }
                }
                
                // Validate password length (if provided)
                if (passwordInput && passwordInput.value.trim() && passwordInput.value.trim().length < 8) {
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                    console.log('Form validation failed');
                    
                    // Show error message
                    const firstInvalidField = document.querySelector('.is-invalid');
                    if (firstInvalidField) {
                        firstInvalidField.focus();
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    return false;
                }
                
                // Show loading state
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                submitBtn.disabled = true;
            });

            // Initialize
            resetForm();

            // Auto-hide alerts
            setTimeout(() => {
                document.querySelectorAll('.alert-dismissible').forEach(alert => {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) closeBtn.click();
                });
            }, 5000);

            // Focus search input if there's a search term
            @if(request('keyword') || isset($keyword))
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            @endif

            console.log('Initialization completed');
        });

        // Global functions for debugging
        window.debugSearch = function() {
            console.log('=== SEARCH DEBUG ===');
            console.log('Search form:', document.getElementById('searchForm'));
            console.log('Search input:', document.getElementById('searchInput'));
            console.log('Current keyword:', document.getElementById('searchInput')?.value);
            console.log('Current URL:', window.location.href);
            console.log('=== END DEBUG ===');
        };

        window.testSearch = function(keyword) {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = keyword || 'test';
                document.getElementById('searchForm').submit();
            }
        };
    </script>
@endpush
