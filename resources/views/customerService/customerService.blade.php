<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Service - Data Penitip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Data Penitip</h2>

    {{-- Pesan sukses/error --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Validasi Error --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Tambah / Edit Penitip --}}
    <div class="card mb-4">
        <div class="card-header">Form Penitip</div>
        <div class="card-body">
            <form id="penitipForm" method="POST" action="{{ route('penitip.store') }}">
                @csrf
                <input type="hidden" name="id_penitip" id="id_penitip" value="">
                <input type="hidden" name="_method" id="_method" value="">

                <div class="mb-3">
                    <label for="nama_penitip" class="form-label">Nama Penitip</label>
                    <input type="text" class="form-control @error('nama_penitip') is-invalid @enderror" id="nama_penitip" name="nama_penitip" required maxlength="50" value="{{ old('nama_penitip') }}">
                    @error('nama_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nik_penitip" class="form-label">NIK Penitip</label>
                    <input type="text" class="form-control @error('nik_penitip') is-invalid @enderror" id="nik_penitip" name="nik_penitip" required maxlength="16" value="{{ old('nik_penitip') }}">
                    <div class="form-text">NIK harus unik dan hanya satu akun per NIK.</div>
                    @error('nik_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nomor_telepon_penitip" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('nomor_telepon_penitip') is-invalid @enderror" id="nomor_telepon_penitip" name="nomor_telepon_penitip" required maxlength="50" value="{{ old('nomor_telepon_penitip') }}">
                    @error('nomor_telepon_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email_penitip" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email_penitip') is-invalid @enderror" id="email_penitip" name="email_penitip" required maxlength="50" value="{{ old('email_penitip') }}">
                    @error('email_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_penitip" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password_penitip') is-invalid @enderror" id="password_penitip" name="password_penitip" required minlength="8" maxlength="50">
                    @error('password_penitip')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
            </form>
        </div>
    </div>

    {{-- Form Pencarian --}}
    <form action="{{ route('penitip.index') }}" method="GET" class="mb-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NIK, telepon, email" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    {{-- Tabel Data Penitip --}}
    <table class="table table-bordered" id="penitipTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nama Penitip</th>
            <th>NIK</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
            @foreach($penitips as $penitip)
                <tr>
                    <td>{{ $penitip->id_penitip }}</td>
                    <td>{{ $penitip->nama_penitip }}</td>
                    <td>{{ $penitip->nik_penitip }}</td>
                    <td>{{ $penitip->nomor_telepon_penitip }}</td>
                    <td>{{ $penitip->email_penitip }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm editBtn"
                                data-id="{{ $penitip->id_penitip }}"
                                data-nama="{{ $penitip->nama_penitip }}"
                                data-nik="{{ $penitip->nik_penitip }}"
                                data-telp="{{ $penitip->nomor_telepon_penitip }}"
                                data-email="{{ $penitip->email_penitip }}">
                            Edit
                        </button>
                        <form method="POST" action="{{ route('penitip.destroy', $penitip->id_penitip) }}" style="display:inline;" onsubmit="return confirm('Hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
    const penitipForm = document.getElementById('penitipForm');
    const idInput = document.getElementById('id_penitip');
    const namaInput = document.getElementById('nama_penitip');
    const nikInput = document.getElementById('nik_penitip');
    const telpInput = document.getElementById('nomor_telepon_penitip');
    const emailInput = document.getElementById('email_penitip');
    const passwordInput = document.getElementById('password_penitip');
    const methodInput = document.getElementById('_method');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    function resetForm() {
        penitipForm.reset();
        idInput.value = '';
        methodInput.value = '';
        penitipForm.action = "{{ route('penitip.store') }}";
        submitBtn.textContent = 'Simpan';
        passwordInput.required = true;
    }

    resetBtn.addEventListener('click', resetForm);

    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const nik = button.getAttribute('data-nik');
            const telp = button.getAttribute('data-telp');
            const email = button.getAttribute('data-email');

            idInput.value = id;
            namaInput.value = nama;
            nikInput.value = nik;
            telpInput.value = telp;
            emailInput.value = email;

            penitipForm.action = `penitip/${id}`;
            methodInput.value = 'PUT';

            submitBtn.textContent = 'Update';
            passwordInput.value = '';
            passwordInput.required = false;
        });
    });

    resetForm();
</script>

</body>
</html>
