@extends('layouts.admin')

@section('content')
    <style>
        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        button,
        input[type="submit"] {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        button:hover,
        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        .danger-button {
            background-color: #dc3545;
        }

        .danger-button:hover {
            background-color: #a71d2a;
        }

        .info-button {
            background-color: #17a2b8;
        }

        .info-button:hover {
            background-color: #117a8b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: white;
        }

        table th,
        table td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
        }

        #modal {
            display: none;
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            padding: 24px;
            border-radius: 8px;
            width: 400px;
            z-index: 1001;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        form label {
            display: block;
            margin-top: 12px;
            font-weight: bold;
        }

        form input {
            width: 100%;
            padding: 8px;
            margin-top: 4px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 4px;
            margin: 16px 0;
        }
    </style>

    <h1>📦 Manajemen Penitip</h1>

    <button onclick="showForm('create')">+ Tambah Penitip</button>

    <form action="{{ route('admin.penitip.index') }}" method="GET" style="margin: 16px 0;">
        <input type="text" name="keyword" placeholder="🔍 Cari penitip..." value="{{ request('keyword') }}"
            style="padding: 8px; width: 250px; border-radius: 4px; border: 1px solid #ccc;">
        <button type="submit">Cari</button>
        @if (request('keyword'))
            <a href="{{ route('admin.penitip.index') }}"
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
                <th>NIK</th>
                <th>Telepon</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($penitip as $p)
                <tr>
                    <td>{{ $p->id_penitip }}</td>
                    <td>{{ $p->nama_penitip }}</td>
                    <td>{{ $p->nik_penitip }}</td>
                    <td>{{ $p->nomor_telepon_penitip }}</td>
                    <td>{{ $p->email_penitip }}</td>
                    <td>
                        <button class="info-button" onclick="showForm('show', {{ $p->id_penitip }})">Detail</button>
                        <button onclick="showForm('edit', {{ $p->id_penitip }})">Edit</button>
                        <form action="{{ route('admin.penitip.destroy', $p->id_penitip) }}" method="POST"
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

        <form id="form-create-edit" method="POST">
            @csrf
            <input type="hidden" name="_method" id="form-method" value="POST">

            <label>Nama Penitip</label>
            <input type="text" name="nama_penitip" id="nama_penitip" required>

            <label>NIK</label>
            <input type="text" name="nik_penitip" id="nik_penitip" required>

            <label>Nomor Telepon</label>
            <input type="text" name="nomor_telepon_penitip" id="nomor_telepon_penitip" required>

            <label>Email</label>
            <input type="email" name="email_penitip" id="email_penitip" required>

            <label>Password</label>
            <input type="password" name="password_penitip" id="password_penitip">

            <label>Konfirmasi Password</label>
            <input type="password" name="password_penitip_confirmation" id="password_penitip_confirmation">

            <button type="submit" id="submit-button">Simpan</button>
            <button type="button" onclick="hideModal()">Batal</button>
        </form>

        <div id="show-detail" style="display:none;">
            <p><strong>ID:</strong> <span id="detail-id"></span></p>
            <p><strong>Nama:</strong> <span id="detail-nama"></span></p>
            <p><strong>NIK:</strong> <span id="detail-nik"></span></p>
            <p><strong>Telepon:</strong> <span id="detail-telepon"></span></p>
            <p><strong>Email:</strong> <span id="detail-email"></span></p>
            <button type="button" onclick="hideModal()">Tutup</button>
        </div>
    </div>

    <script>
        function showForm(type, id = null) {
            document.getElementById('form-create-edit').reset();
            document.getElementById('form-create-edit').style.display = 'none';
            document.getElementById('show-detail').style.display = 'none';
            document.getElementById('form-method').value = 'POST';

            if (type === 'create') {
                document.getElementById('modal-title').textContent = 'Tambah Penitip';
                document.getElementById('form-create-edit').action = "{{ route('admin.penitip.store') }}";
                document.getElementById('password_penitip').required = true;
                document.getElementById('password_penitip_confirmation').required = true;
                document.getElementById('submit-button').textContent = 'Simpan';
                document.getElementById('form-create-edit').style.display = 'block';
                showModal();
            } else if (type === 'edit' && id) {
                fetch(`/admin/penitip/${id}`)
                    .then(res => res.json())
                    .then(d => {
                        if (d.message) return alert(d.message);
                        document.getElementById('modal-title').textContent = 'Edit Penitip';
                        document.getElementById('form-create-edit').action = `/admin/penitip/${id}`;
                        document.getElementById('form-method').value = 'PUT';

                        document.getElementById('nama_penitip').value = d.nama_penitip;
                        document.getElementById('nik_penitip').value = d.nik_penitip;
                        document.getElementById('nomor_telepon_penitip').value = d.nomor_telepon_penitip;
                        document.getElementById('email_penitip').value = d.email_penitip;

                        document.getElementById('password_penitip').required = false;
                        document.getElementById('password_penitip').value = '';
                        document.getElementById('password_penitip_confirmation').required = false;
                        document.getElementById('password_penitip_confirmation').value = '';

                        document.getElementById('submit-button').textContent = 'Update';
                        document.getElementById('form-create-edit').style.display = 'block';
                        showModal();
                    });
            } else if (type === 'show' && id) {
                fetch(`/admin/penitip/${id}`)
                    .then(res => res.json())
                    .then(d => {
                        if (d.message) return alert(d.message);

                        document.getElementById('modal-title').textContent = 'Detail Penitip';
                        document.getElementById('detail-id').textContent = d.id_penitip;
                        document.getElementById('detail-nama').textContent = d.nama_penitip;
                        document.getElementById('detail-nik').textContent = d.nik_penitip;
                        document.getElementById('detail-telepon').textContent = d.nomor_telepon_penitip;
                        document.getElementById('detail-email').textContent = d.email_penitip;

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
