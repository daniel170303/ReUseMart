<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Service - Data Organisasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Data Organisasi</h2>

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

    {{-- Form Tambah / Edit Organisasi --}}
    <div class="card mb-4">
        <div class="card-header">Form Organisasi</div>
        <div class="card-body">
            <form id="organisasiForm" method="POST" action="{{ route('admin.organisasi.store') }}">
                @csrf
                <input type="hidden" name="id_organisasi" id="id_organisasi" value="">
                <input type="hidden" name="_method" id="_method" value="">

                <div class="mb-3">
                    <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
                    <input type="text" class="form-control @error('nama_organisasi') is-invalid @enderror" id="nama_organisasi" name="nama_organisasi" required maxlength="50" value="{{ old('nama_organisasi') }}">
                    @error('nama_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="alamat_organisasi" class="form-label">Alamat Organisasi</label>
                    <input type="text" class="form-control @error('alamat_organisasi') is-invalid @enderror" id="alamat_organisasi" name="alamat_organisasi" required maxlength="50" value="{{ old('alamat_organisasi') }}">
                    @error('alamat_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="nomor_telepon_organisasi" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('nomor_telepon_organisasi') is-invalid @enderror" id="nomor_telepon_organisasi" name="nomor_telepon_organisasi" required maxlength="50" value="{{ old('nomor_telepon_organisasi') }}">
                    @error('nomor_telepon_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email_organisasi" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email_organisasi') is-invalid @enderror" id="email_organisasi" name="email_organisasi" required maxlength="50" value="{{ old('email_organisasi') }}">
                    @error('email_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_organisasi" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password_organisasi') is-invalid @enderror" id="password_organisasi" name="password_organisasi" required maxlength="50">
                    @error('password_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
            </form>
        </div>
    </div>

    {{-- Form Pencarian --}}
    <form action="{{ route('admin.organisasi.index') }}" method="GET" class="mb-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, alamat, telepon, email" class="form-control" />
        <button type="submit" class="btn btn-primary mt-2">Cari</button>
    </form>

    {{-- Tabel Data Organisasi --}}
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Telepon</th>
            <th>Email</th>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        @foreach($organisasi as $item)
            <tr>
                <td>{{ $item->id_organisasi }}</td>
                <td>{{ $item->nama_organisasi }}</td>
                <td>{{ $item->alamat_organisasi }}</td>
                <td>{{ $item->nomor_telepon_organisasi }}</td>
                <td>{{ $item->email_organisasi }}</td>
                <td>
                    <button class="btn btn-warning btn-sm editBtn"
                            data-id="{{ $item->id_organisasi }}"
                            data-nama="{{ $item->nama_organisasi }}"
                            data-alamat="{{ $item->alamat_organisasi }}"
                            data-telp="{{ $item->nomor_telepon_organisasi }}"
                            data-email="{{ $item->email_organisasi }}">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.organisasi.destroy', $item->id_organisasi) }}" style="display:inline;" onsubmit="return confirm('Hapus data ini?')">
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
    const form = document.getElementById('organisasiForm');
    const idInput = document.getElementById('id_organisasi');
    const namaInput = document.getElementById('nama_organisasi');
    const alamatInput = document.getElementById('alamat_organisasi');
    const telpInput = document.getElementById('nomor_telepon_organisasi');
    const emailInput = document.getElementById('email_organisasi');
    const passwordInput = document.getElementById('password_organisasi');
    const methodInput = document.getElementById('_method');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    function resetForm() {
        form.reset();
        idInput.value = '';
        methodInput.value = '';
        form.action = "{{ route('admin.organisasi.store') }}";
        submitBtn.textContent = 'Simpan';
        passwordInput.required = true;
    }

    resetBtn.addEventListener('click', resetForm);

    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const alamat = button.getAttribute('data-alamat');
            const telp = button.getAttribute('data-telp');
            const email = button.getAttribute('data-email');

            idInput.value = id;
            namaInput.value = nama;
            alamatInput.value = alamat;
            telpInput.value = telp;
            emailInput.value = email;

            form.action = `organisasi/${id}`;
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
