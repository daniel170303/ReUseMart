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
            border: 1px solid red;
            background-color: #ffe6e6;
        }

        .text-error {
            color: red;
            font-size: 0.85em;
            margin-top: -8px;
            margin-bottom: 8px;
        }

        .info-button {
            background-color: #28a745;
        }

        .info-button:hover {
            background-color: #218838;
        }
    </style>

    <h1>👤 Manajemen Pegawai</h1>

    <button onclick="showForm('create')">+ Tambah Pegawai</button>

    <form action="{{ url ('/pegawai') }}" method="GET" style="margin-top: 16px; margin-bottom: 12px;">
        <input type="text" name="keyword" placeholder="🔍 Cari pegawai..." value="{{ request('keyword') }}"
            style="padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;">
        <button type="submit" style="padding: 8px 12px; margin-left: 4px;">Cari</button>

        @if (request('keyword'))
            <a href="{{ route('pegawai.index') }}"
                style="padding: 8px 12px; background-color: #6c757d; color: white; text-decoration: none; border-radius: 4px; margin-left: 8px;">
                Reset
            </a>
        @endif
    </form>

    @if (session('success'))
        <div class="success-message">{{ session('success') }}</div>
    @endif

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

    <div id="overlay" onclick="hideModal()"></div>

    {{-- Modal --}}
    <div id="modal">
        <h3 id="modal-title"></h3>

        {{-- Form Create/Edit --}}
        <form id="form-create-edit" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            {{-- Role --}}
            <label>Role Pegawai</label>
            <select name="id_role" id="id_role" class="{{ $errors->has('id_role') ? 'input-error' : '' }}" required>
                <option value="">-- Pilih Role --</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id_role }}" {{ old('id_role') == $role->id_role ? 'selected' : '' }}>
                        {{ $role->nama_role }}
                    </option>
                @endforeach
            </select>
            @error('id_role')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- Nama --}}
            <label>Nama Pegawai</label>
            <input type="text" name="nama_pegawai" id="nama_pegawai"
                class="{{ $errors->has('nama_pegawai') ? 'input-error' : '' }}" value="{{ old('nama_pegawai') }}"
                maxlength="50" required>
            @error('nama_pegawai')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- Telepon --}}
            <label>Nomor Telepon</label>
            <input type="text" name="nomor_telepon_pegawai" id="nomor_telepon_pegawai"
                class="{{ $errors->has('nomor_telepon_pegawai') ? 'input-error' : '' }}"
                value="{{ old('nomor_telepon_pegawai') }}" maxlength="50" required>
            @error('nomor_telepon_pegawai')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- Email --}}
            <label>Email</label>
            <input type="email" name="email_pegawai" id="email_pegawai"
                class="{{ $errors->has('email_pegawai') ? 'input-error' : '' }}" value="{{ old('email_pegawai') }}"
                maxlength="50" required>
            @error('email_pegawai')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- Password --}}
            <label>Password</label>
            <input type="password" name="password_pegawai" id="password_pegawai"
                class="{{ $errors->has('password_pegawai') ? 'input-error' : '' }}">
            @error('password_pegawai')
                <div class="text-error">{{ $message }}</div>
            @enderror

            {{-- Konfirmasi Password --}}
            <label>Konfirmasi Password</label>
            <input type="password" name="password_pegawai_confirmation" id="password_pegawai_confirmation"
                class="{{ $errors->has('password_pegawai_confirmation') ? 'input-error' : '' }}">
            @error('password_pegawai_confirmation')
                <div class="text-error">{{ $message }}</div>
            @enderror


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
        function showForm(type, id = null) {
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
                document.getElementById('form-create-edit').style.display = 'block';
                document.getElementById('submit-button').textContent = 'Simpan';
                showModal();
            } else if (type === 'edit' && id) {
                fetch(`/admin/pegawai/${id}`)
                    .then(response => response.json())
                    .then(d => {
                        if (d.message === "Pegawai tidak ditemukan") {
                            alert("Pegawai tidak ditemukan");
                            return;
                        }

                        document.getElementById('modal-title').textContent = 'Edit Pegawai';
                        document.getElementById('form-create-edit').action = `/admin/pegawai/${id}`;
                        document.getElementById('form-method').value = 'PUT';

                        // Isi data ke form
                        document.getElementById('id_role').value = d.id_role;
                        document.getElementById('nama_pegawai').value = d.nama_pegawai;
                        document.getElementById('nomor_telepon_pegawai').value = d.nomor_telepon_pegawai;
                        document.getElementById('email_pegawai').value = d.email_pegawai;

                        // Password tidak wajib diisi saat edit
                        document.getElementById('password_pegawai').required = false;
                        document.getElementById('password_pegawai').value = '';
                        document.getElementById('password_pegawai_confirmation').required = false;
                        document.getElementById('password_pegawai_confirmation').value = '';

                        document.getElementById('submit-button').textContent = 'Update';
                        document.getElementById('form-create-edit').style.display = 'block';
                        showModal();
                    });
            } else if (type === 'show' && id) {
                fetch(`/admin/pegawai/${id}`)
                    .then(response => response.json())
                    .then(d => {
                        if (d.message === "Pegawai tidak ditemukan") {
                            alert("Pegawai tidak ditemukan");
                            return;
                        }

                        document.getElementById('modal-title').textContent = 'Detail Pegawai';
                        document.getElementById('detail-id').textContent = d.id_pegawai;
                        document.getElementById('detail-nama').textContent = d.nama_pegawai;
                        document.getElementById('detail-telepon').textContent = d.nomor_telepon_pegawai;
                        document.getElementById('detail-email').textContent = d.email_pegawai;
                        document.getElementById('detail-role').textContent = d.role?.nama_role || '-';

                        document.getElementById('show-detail').style.display = 'block';
                        showModal();
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
        }
    </script>
@endsection
