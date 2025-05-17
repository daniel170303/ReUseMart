@extends('layouts.app')

@section('content')
<h1>Manajemen Pegawai</h1>

{{-- Tombol Tambah Pegawai --}}
<button onclick="showForm('create')">Tambah Pegawai</button>

{{-- Notifikasi --}}
@if(session('success'))
    <div style="color: green; margin-top:10px;">{{ session('success') }}</div>
@endif

{{-- List Pegawai --}}
<table (border="1" cellpadding="8" cellspacing="0" width="100%" style="margin-top: 10px;")>
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
        @foreach($pegawai as $p)
        <tr>
            <td>{{ $p->id_pegawai }}</td>
            <td>{{ $p->nama_pegawai }}</td>
            <td>{{ $p->nomor_telepon_pegawai }}</td>
            <td>{{ $p->email_pegawai }}</td>
            <td>{{ $p->role->nama_role ?? '-' }}</td>
            <td>
                <button onclick="showForm('show', {{ $p->id_pegawai }})">Detail</button>
                <button onclick="showForm('edit', {{ $p->id_pegawai }})">Edit</button>
                <form action="{{ route('pegawai.destroy', $p->id_pegawai) }}" method="POST" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:red;cursor:pointer">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Modal Form / Detail --}}
<div id="modal" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%); background:#fff; border:1px solid #ccc; padding:20px; z-index:100; width:400px;">
    <h3 id="modal-title"></h3>
    <form id="form-create-edit" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="form-method" value="POST">

        <label>Role Pegawai:</label><br>
        <select name="id_role" id="id_role" required>
            <option value="">-- Pilih Role --</option>
            @foreach($roles as $role)
                <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
            @endforeach
        </select><br><br>

        <label>Nama Pegawai:</label><br>
        <input type="text" name="nama_pegawai" id="nama_pegawai" maxlength="50" required><br><br>

        <label>Nomor Telepon:</label><br>
        <input type="text" name="nomor_telepon_pegawai" id="nomor_telepon_pegawai" maxlength="50" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email_pegawai" id="email_pegawai" maxlength="50" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password_pegawai" id="password_pegawai"><br><br>

        <label>Konfirmasi Password:</label><br>
        <input type="password" name="password_pegawai_confirmation" id="password_pegawai_confirmation"><br><br>

        <button type="submit" id="submit-button">Simpan</button>
        <button type="button" onclick="hideModal()">Batal</button>
    </form>

    <div id="show-detail" style="display:none;">
        <p><strong>ID:</strong> <span id="detail-id"></span></p>
        <p><strong>Nama:</strong> <span id="detail-nama"></span></p>
        <p><strong>Nomor Telepon:</strong> <span id="detail-telepon"></span></p>
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

        if(type === 'create') {
            document.getElementById('modal-title').textContent = 'Tambah Pegawai';
            document.getElementById('form-method').value = 'POST';
            document.getElementById('form-create-edit').action = "{{ route('pegawai.store') }}";
            document.getElementById('password_pegawai').required = true;
            document.getElementById('password_pegawai_confirmation').required = true;
            document.getElementById('form-create-edit').style.display = 'block';
            document.getElementById('submit-button').textContent = 'Simpan';
            showModal();
        } 
        else if(type === 'edit' && id) {
            fetch("{{ url('pegawai') }}/" + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal-title').textContent = 'Edit Pegawai';
                document.getElementById('form-method').value = 'PUT';
                document.getElementById('form-create-edit').action = "{{ url('pegawai') }}/" + id;
                document.getElementById('id_role').value = data[0].id_role;
                document.getElementById('nama_pegawai').value = data[0].nama_pegawai;
                document.getElementById('nomor_telepon_pegawai').value = data[0].nomor_telepon_pegawai;
                document.getElementById('email_pegawai').value = data[0].email_pegawai;
                document.getElementById('password_pegawai').required = false;
                document.getElementById('password_pegawai_confirmation').required = false;
                document.getElementById('form-create-edit').style.display = 'block';
                document.getElementById('submit-button').textContent = 'Update';
                showModal();
            });
        } 
        else if(type === 'show' && id) {
            fetch("{{ url('pegawai') }}/" + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('modal-title').textContent = 'Detail Pegawai';
                document.getElementById('detail-id').textContent = data[0].id_pegawai;
                document.getElementById('detail-nama').textContent = data[0].nama_pegawai;
                document.getElementById('detail-telepon').textContent = data[0].nomor_telepon_pegawai;
                document.getElementById('detail-email').textContent = data[0].email_pegawai;
                // Cari nama role dari list roles
                const role = @json($roles).find(r => r.id_role === data[0].id_role);
                document.getElementById('detail-role').textContent = role ? role.nama_role : '-';
                document.getElementById('show-detail').style.display = 'block';
                showModal();
            });
        }
    }

    function showModal() {
        document.getElementById('modal').style.display = 'block';
    }
    function hideModal() {
        document.getElementById('modal').style.display = 'none';
    }
</script>

@endsection
