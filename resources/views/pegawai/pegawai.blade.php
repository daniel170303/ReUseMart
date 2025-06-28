@extends('layouts.admin')

@section('content')
    <style>
        /* Basic styling */
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            margin-bottom: 20px;
        }

        button {
            padding: 8px 14px;
            background-color: #007bff;
            border: none;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .danger-button {
            background-color: #dc3545;
        }

        .danger-button:hover {
            background-color: #c82333;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            font-weight: bold;
            padding: 10px;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
        }

        .error-message {
            color: #721c24;
            margin-top: 10px;
            font-weight: bold;
            padding: 10px;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        }

        .no-data-message {
            text-align: center;
            padding: 40px 20px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-top: 20px;
            color: #6c757d;
        }

        .no-data-message h3 {
            margin-bottom: 10px;
            color: #495057;
            font-size: 18px;
        }

        .no-data-message p {
            margin-bottom: 15px;
            font-size: 14px;
        }

        .search-info {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
            color: #1565c0;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
        }

        td {
            padding: 10px;
        }

        /* Modal styling */
        #modal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.2);
            padding: 25px;
            z-index: 999;
            width: 420px;
            max-height: 80vh;
            overflow-y: auto;
        }

        #modal-title {
            margin-bottom: 15px;
            font-size: 20px;
        }

        #modal label {
            font-weight: bold;
        }

        #modal input,
        #modal select {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            margin-bottom: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        #modal button {
            margin-right: 10px;
        }

        #overlay {
            position: fixed;
            display: none;
            top: 0;
            left: 0;
            height: 100vh;
            width: 100vw;
            background: rgba(0, 0, 0, 0.4);
            z-index: 998;
        }

        .input-error {
            border: 1px solid #dc3545 !important;
            background-color: #ffe6e6;
        }

        .text-error {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: -8px;
            margin-bottom: 8px;
            display: block;
        }

        .info-button {
            background-color: #28a745;
        }

        .info-button:hover {
            background-color: #218838;
        }

        .field-group {
            margin-bottom: 15px;
        }

        .password-requirements {
            font-size: 0.8em;
            color: #6c757d;
            margin-top: -8px;
            margin-bottom: 8px;
        }
    </style>

    <h1>👤 Manajemen Pegawai</h1>

    <button onclick="showForm('create')">+ Tambah Pegawai</button>

    {{-- Form Search --}}
    <div style="margin-top: 16px; margin-bottom: 12px;">
        <form action="{{ route('admin.pegawai.index') }}" method="GET" style="display: inline-block;">
            <input type="text" 
                   name="keyword" 
                   placeholder="🔍 Cari pegawai..." 
                   value="{{ request('keyword') }}"
                   style="padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 12px; margin-left: 4px;">Cari</button>
        </form>

        @if (request('keyword'))
            <a href="{{ route('admin.pegawai.index') }}"
               style="padding: 8px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 8px; display: inline-block;">
                Reset
            </a>
        @endif
    </div>

    {{-- Success Message --}}
    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

    {{-- Error Message --}}
    @if (session('error'))
        <div class="error-message">{{ session('error') }}</div>
    @endif

    {{-- Tabel Data atau Pesan Tidak Ada Data --}}
    @if(count($pegawai) > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Telepon</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pegawai as $p)
                    <tr>
                        <td>{{ $p->id_pegawai }}</td>
                        <td>{{ $p->nama_pegawai }}</td>
                        <td>{{ $p->nomor_telepon_pegawai }}</td>
                        <td>{{ $p->email_pegawai }}</td>
                        <td>{{ $p->role->nama_role ?? '-' }}</td>
                        <td>
                            <button class="info-button" onclick="showForm('show', {{ $p->id_pegawai }})">Detail</button>
                            <button onclick="showForm('edit', {{ $p->id_pegawai }})">Edit</button>
                            <form action="{{ route('admin.pegawai.destroy', $p->id_pegawai) }}" method="POST"
                                style="display:inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="danger-button">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        {{-- Pesan Tidak Ada Data --}}
        <div class="no-data-message">
            @if(request('keyword'))
                {{-- Jika sedang melakukan pencarian --}}
                <h3>🔍 Data Tidak Ditemukan</h3>
                <p>Tidak ada pegawai yang cocok dengan pencarian "<strong>{{ request('keyword') }}</strong>"</p>
                <p>Silakan coba dengan kata kunci yang berbeda atau periksa ejaan Anda.</p>
                <div style="margin-top: 20px;">
                    <a href="{{ route('admin.pegawai.index') }}" 
                       style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px;">
                        📋 Tampilkan Semua Pegawai
                    </a>
                    <button onclick="document.querySelector('input[name=keyword]').focus()" 
                            style="padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 4px;">
                        🔄 Cari Lagi
                    </button>
                </div>
            @else
                {{-- Jika memang tidak ada data sama sekali --}}
                <h3>📋 Belum Ada Data Pegawai</h3>
                <p>Belum ada pegawai yang terdaftar dalam sistem.</p>
                <p>Klik tombol "Tambah Pegawai" untuk menambahkan pegawai baru.</p>
                <div style="margin-top: 20px;">
                    <button onclick="showForm('create')" 
                            style="padding: 12px 24px; background-color: #007bff; color: white; border: none; border-radius: 4px; font-size: 16px;">
                        ➕ Tambah Pegawai Pertama
                    </button>
                </div>
            @endif
        </div>
    @endif

    <div id="overlay" onclick="hideModal()"></div>

    {{-- Modal --}}
    <div id="modal">
        <h3 id="modal-title"></h3>

        {{-- Form Create/Edit --}}
        <form id="form-create-edit" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            {{-- Role --}}
            <div class="field-group">
                <label>Role Pegawai <span style="color: red;">*</span></label>
                <select name="id_role" id="id_role" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                            {{ $role->nama_role }}
                        </option>
                    @endforeach
                </select>
                <div id="error-id_role" class="text-error" style="display: none;"></div>
            </div>

            {{-- Nama --}}
            <div class="field-group">
                <label>Nama Pegawai <span style="color: red;">*</span></label>
                <input type="text" name="nama_pegawai" id="nama_pegawai" value="{{ old('nama_pegawai') }}"
                    maxlength="50" required placeholder="Masukkan nama lengkap">
                <div id="error-nama_pegawai" class="text-error" style="display: none;"></div>
            </div>

            {{-- Telepon --}}
            <div class="field-group">
                <label>Nomor Telepon <span style="color: red;">*</span></label>
                <input type="text" name="nomor_telepon_pegawai" id="nomor_telepon_pegawai"
                    value="{{ old('nomor_telepon_pegawai') }}" maxlength="15" required 
                    placeholder="Contoh: 08123456789">
                <div id="error-nomor_telepon_pegawai" class="text-error" style="display: none;"></div>
            </div>

            {{-- Email --}}
            <div class="field-group">
                <label>Email <span style="color: red;">*</span></label>
                <input type="email" name="email_pegawai" id="email_pegawai" value="{{ old('email_pegawai') }}"
                    maxlength="50" required placeholder="contoh@email.com">
                <div id="error-email_pegawai" class="text-error" style="display: none;"></div>
            </div>

            {{-- Password --}}
            <div class="field-group">
                <label>Password <span id="password-required" style="color: red;">*</span></label>
                <input type="password" name="password_pegawai" id="password_pegawai" 
                    placeholder="Minimal 8 karakter">
                <div class="password-requirements">
                    Password harus mengandung: huruf besar, huruf kecil, dan angka
                </div>
                <div id="error-password_pegawai" class="text-error" style="display: none;"></div>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="field-group">
                <label>Konfirmasi Password <span id="confirm-password-required" style="color: red;">*</span></label>
                <input type="password" name="password_pegawai_confirmation" id="password_pegawai_confirmation"
                    placeholder="Ulangi password">
                <div id="error-password_pegawai_confirmation" class="text-error" style="display: none;"></div>
            </div>

            <button type="submit" id="submit-button">Simpan</button>
            <button type="button" onclick="hideModal()">Batal</button>
        </form>

        {{-- Detail Pegawai --}}
        <div id="show-detail" style="display:none;">
            <p><strong>ID:</strong> <span id="detail-id"></span></p>
            <p><strong>Nama:</strong> <span id="detail-nama"></span></p>
            <p><strong>Telepon:</strong> <span id="detail-telepon"></span></p>
            <p><strong>Email:</strong> <span id="detail-email"></span></p>
            <p><strong>Role:</strong> <span id="detail-role"></span></p>
            <button type="button" onclick="hideModal()">Tutup</button>
        </div>
    </div>

    <script>
        // Clear all error messages
        function clearErrors() {
            const errorElements = document.querySelectorAll('.text-error');
            errorElements.forEach(element => {
                element.style.display = 'none';
                element.textContent = '';
            });

            const inputElements = document.querySelectorAll('input, select');
            inputElements.forEach(element => {
                element.classList.remove('input-error');
            });
        }

        // Show error for specific field
        function showError(fieldName, message) {
            const errorElement = document.getElementById(`error-${fieldName}`);
            const inputElement = document.getElementById(fieldName);
            
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
            }
            
            if (inputElement) {
                inputElement.classList.add('input-error');
            }
        }

        // Validate form before submit
        function validateForm() {
            clearErrors();
            let isValid = true;

            // Validate Role
            const role = document.getElementById('id_role').value;
            if (!role) {
                showError('id_role', 'Role pegawai harus dipilih.');
                isValid = false;
            }

            // Validate Nama
            const nama = document.getElementById('nama_pegawai').value.trim();
            if (!nama) {
                showError('nama_pegawai', 'Nama pegawai harus diisi.');
                isValid = false;
            } else if (nama.length < 2) {
                showError('nama_pegawai', 'Nama pegawai minimal 2 karakter.');
                isValid = false;
            } else if (nama.length > 50) {
                showError('nama_pegawai', 'Nama pegawai maksimal 50 karakter.');
                isValid = false;
            } else if (!/^[a-zA-Z\s]+$/.test(nama)) {
                showError('nama_pegawai', 'Nama pegawai hanya boleh mengandung huruf dan spasi.');
                isValid = false;
            }

            // Validate Telepon
            const telepon = document.getElementById('nomor_telepon_pegawai').value.trim();
            if (!telepon) {
                showError('nomor_telepon_pegawai', 'Nomor telepon harus diisi.');
                isValid = false;
            } else if (telepon.length < 10) {
                showError('nomor_telepon_pegawai', 'Nomor telepon minimal 10 karakter.');
                isValid = false;
            } else if (telepon.length > 15) {
                showError('nomor_telepon_pegawai', 'Nomor telepon maksimal 15 karakter.');
                isValid = false;
            } else if (!/^[0-9+\-\s]+$/.test(telepon)) {
                showError('nomor_telepon_pegawai', 'Format nomor telepon tidak valid.');
                isValid = false;
            }

            // Validate Email
            const email = document.getElementById('email_pegawai').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email) {
                showError('email_pegawai', 'Email harus diisi.');
                isValid = false;
            } else if (!emailRegex.test(email)) {
                showError('email_pegawai', 'Format email tidak valid.');
                isValid = false;
            } else if (email.length > 50) {
                showError('email_pegawai', 'Email maksimal 50 karakter.');
                isValid = false;
            }

            // Validate Password (only for create or if password is filled in edit)
            const password = document.getElementById('password_pegawai').value;
            const confirmPassword = document.getElementById('password_pegawai_confirmation').value;
            const isCreateMode = document.getElementById('form-method').value === 'POST';
            const passwordRequired = document.getElementById('password_pegawai').required;

            if (passwordRequired && !password) {
                showError('password_pegawai', 'Password harus diisi.');
                isValid = false;
            } else if (password) {
                if (password.length < 8) {
                    showError('password_pegawai', 'Password minimal 8 karakter.');
                    isValid = false;
                } else if (password.length > 50) {
                    showError('password_pegawai', 'Password maksimal 50 karakter.');
                    isValid = false;
                } else if (!/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/.test(password)) {
                    showError('password_pegawai', 'Password harus mengandung huruf besar, huruf kecil, dan angka.');
                    isValid = false;
                }

                // Validate Password Confirmation
                if (password !== confirmPassword) {
                    showError('password_pegawai_confirmation', 'Konfirmasi password tidak cocok.');
                    isValid = false;
                }
            }

            return isValid;
        }

        function showForm(type, id = null) {
            clearErrors();
            document.getElementById('modal-title').textContent = '';
            document.getElementById('form-create-edit').reset();
            document.getElementById('form-create-edit').style.display = 'none';
            document.getElementById('show-detail').style.display = 'none';
            document.getElementById('form-method').value = 'POST';

            if (type === 'create') {
                document.getElementById('modal-title').textContent = 'Tambah Pegawai';
                document.getElementById('form-create-edit').action = "{{ route('admin.pegawai.store') }}";
                document.getElementById('password_pegawai').required = true;
                document.getElementById('password_pegawai_confirmation').required = true;
                document.getElementById('password-required').style.display = 'inline';
                document.getElementById('confirm-password-required').style.display = 'inline';
                document.getElementById('form-create-edit').style.display = 'block';
                document.getElementById('submit-button').textContent = 'Simpan';
                showModal();
            } else if (type === 'edit' && id) {
                fetch(`/admin/pegawai/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(d => {
                        if (d.message === "Pegawai tidak ditemukan") {
                            alert("Pegawai tidak ditemukan");
                            return;
                        }

                        document.getElementById('modal-title').textContent = 'Edit Pegawai';
                        document.getElementById('form-create-edit').action = `{{ url('/admin/pegawai') }}/${id}`;
                        document.getElementById('form-method').value = 'PUT';

                        // Isi data ke form
                        document.getElementById('id_role').value = d.id_role || '';
                        document.getElementById('nama_pegawai').value = d.nama_pegawai || '';
                        document.getElementById('nomor_telepon_pegawai').value = d.nomor_telepon_pegawai || '';
                        document.getElementById('email_pegawai').value = d.email_pegawai || '';

                        // Password tidak wajib diisi saat edit
                        document.getElementById('password_pegawai').required = false;
                        document.getElementById('password_pegawai').value = '';
                        document.getElementById('password_pegawai_confirmation').required = false;
                        document.getElementById('password_pegawai_confirmation').value = '';
                        document.getElementById('password-required').style.display = 'none';
                        document.getElementById('confirm-password-required').style.display = 'none';

                        document.getElementById('submit-button').textContent = 'Update';
                        document.getElementById('form-create-edit').style.display = 'block';
                        showModal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengambil data pegawai: ' + error.message);
                    });
            } else if (type === 'show' && id) {
                fetch(`/admin/pegawai/${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(d => {
                        if (d.message === "Pegawai tidak ditemukan") {
                            alert("Pegawai tidak ditemukan");
                            return;
                        }

                        document.getElementById('modal-title').textContent = 'Detail Pegawai';
                        document.getElementById('detail-id').textContent = d.id_pegawai || '-';
                        document.getElementById('detail-nama').textContent = d.nama_pegawai || '-';
                        document.getElementById('detail-telepon').textContent = d.nomor_telepon_pegawai || '-';
                        document.getElementById('detail-email').textContent = d.email_pegawai || '-';
                        document.getElementById('detail-role').textContent = d.role?.nama_role || '-';

                        document.getElementById('show-detail').style.display = 'block';
                        showModal();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Terjadi kesalahan saat mengambil data pegawai: ' + error.message);
                    });
            }
        }

        function showModal() {
            document.getElementById('modal').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        }

        function hideModal() {
            document.getElementById('modal').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
            clearErrors();
        }

        // Add form submit event listener
        document.getElementById('form-create-edit').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });

        // Real-time validation
        document.getElementById('nama_pegawai').addEventListener('input', function() {
            const nama = this.value.trim();
            const errorElement = document.getElementById('error-nama_pegawai');
            
            if (nama && !/^[a-zA-Z\s]+$/.test(nama)) {
                this.classList.add('input-error');
                errorElement.textContent = 'Nama pegawai hanya boleh mengandung huruf dan spasi.';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        });

        document.getElementById('nomor_telepon_pegawai').addEventListener('input', function() {
            const telepon = this.value.trim();
            const errorElement = document.getElementById('error-nomor_telepon_pegawai');
            
            if (telepon && !/^[0-9+\-\s]+$/.test(telepon)) {
                this.classList.add('input-error');
                errorElement.textContent = 'Format nomor telepon tidak valid.';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        });

        document.getElementById('email_pegawai').addEventListener('input', function() {
            const email = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const errorElement = document.getElementById('error-email_pegawai');
            
            if (email && !emailRegex.test(email)) {
                this.classList.add('input-error');
                errorElement.textContent = 'Format email tidak valid.';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        });

        document.getElementById('password_pegawai').addEventListener('input', function() {
            const password = this.value;
            const errorElement = document.getElementById('error-password_pegawai');
            
            if (password && !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/.test(password)) {
                this.classList.add('input-error');
                errorElement.textContent = 'Password harus mengandung huruf besar, huruf kecil, dan angka.';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        });

        document.getElementById('password_pegawai_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password_pegawai').value;
            const confirmPassword = this.value;
            const errorElement = document.getElementById('error-password_pegawai_confirmation');
            
            if (confirmPassword && password !== confirmPassword) {
                this.classList.add('input-error');
                errorElement.textContent = 'Konfirmasi password tidak cocok.';
                errorElement.style.display = 'block';
            } else {
                this.classList.remove('input-error');
                errorElement.style.display = 'none';
            }
        });

        // Auto-show modal if there are validation errors
        @if(session('modal_error'))
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('modal_error') === 'create')
                    showForm('create');
                @elseif(session('modal_error') === 'edit' && session('edit_id'))
                    showForm('edit', {{ session('edit_id') }});
                @endif

                // Show server-side validation errors
                @if($errors->any())
                    @foreach($errors->all() as $field => $error)
                        @if(is_string($field))
                            showError('{{ $field }}', '{{ $error }}');
                        @endif
                    @endforeach
                @endif
            });
        @endif

        // Show server validation errors if any
        @if($errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                @foreach($errors->keys() as $field)
                    @if($errors->has($field))
                        showError('{{ $field }}', '{{ $errors->first($field) }}');
                    @endif
                @endforeach
            });
        @endif

        // Focus pada search input jika ada fungsi cari lagi
        function focusSearchInput() {
            const searchInput = document.querySelector('input[name="keyword"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    </script>
@endsection
