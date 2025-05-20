<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Alamat Pembeli</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- ... HEAD TIDAK BERUBAH ... -->

<!-- Form Tambah / Edit Alamat Pembeli -->
<div class="card mb-4">
    <div class="card-header">Form Alamat Pembeli</div>
    <div class="card-body">
        <form id="alamatForm" method="POST" action="{{ route('Admin.alamat_pembeli.store') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="">
            <input type="hidden" name="_method" id="_method" value="">

            <div class="mb-3">
                <label for="id_pembeli" class="form-label">ID Pembeli</label>
                <input type="number" class="form-control" id="id_pembeli" name="id_pembeli" required value="{{ old('id_pembeli') }}">
            </div>

            <div class="mb-3">
                <label for="nama_penerima" class="form-label">Nama Penerima</label>
                <input type="text" class="form-control @error('nama_penerima') is-invalid @enderror" id="nama_penerima" name="nama_penerima" required maxlength="255" value="{{ old('nama_penerima') }}">
                @error('nama_penerima')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="alamat_lengkap" class="form-label">Alamat Lengkap</label>
                <input type="text" class="form-control @error('alamat_lengkap') is-invalid @enderror" id="alamat_lengkap" name="alamat_lengkap" required maxlength="255" value="{{ old('alamat_lengkap') }}">
                @error('alamat_lengkap')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kota" class="form-label">Kota</label>
                <input type="text" class="form-control @error('kota') is-invalid @enderror" id="kota" name="kota" required maxlength="100" value="{{ old('kota') }}">
                @error('kota')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="provinsi" class="form-label">Provinsi</label>
                <input type="text" class="form-control @error('provinsi') is-invalid @enderror" id="provinsi" name="provinsi" required maxlength="100" value="{{ old('provinsi') }}">
                @error('provinsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="kode_pos" class="form-label">Kode Pos</label>
                <input type="text" class="form-control @error('kode_pos') is-invalid @enderror" id="kode_pos" name="kode_pos" required maxlength="10" value="{{ old('kode_pos') }}">
                @error('kode_pos')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
            <button type="button" class="btn btn-secondary" id="resetBtn">Reset</button>
        </form>
    </div>
</div>

<!-- Tabel -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>ID Pembeli</th>
            <th>Nama Penerima</th>
            <th>Alamat</th>
            <th>Kota</th>
            <th>Provinsi</th>
            <th>Kode Pos</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alamat as $item)
        <tr>
            <td>{{ $item->id }}</td>
            <td>{{ $item->id_pembeli }}</td>
            <td>{{ $item->nama_penerima }}</td>
            <td>{{ $item->alamat_lengkap }}</td>
            <td>{{ $item->kota }}</td>
            <td>{{ $item->provinsi }}</td>
            <td>{{ $item->kode_pos }}</td>
            <td>
                <button class="btn btn-warning btn-sm editBtn"
                        data-id="{{ $item->id }}"
                        data-id_pembeli="{{ $item->id_pembeli }}"
                        data-nama="{{ $item->nama_penerima }}"
                        data-alamat="{{ $item->alamat_lengkap }}"
                        data-kota="{{ $item->kota }}"
                        data-provinsi="{{ $item->provinsi }}"
                        data-kodepos="{{ $item->kode_pos }}">
                    Edit
                </button>
                <form method="POST" action="{{ route('alamat_pembeli.destroy', $item->id) }}" style="display:inline;" onsubmit="return confirm('Hapus alamat ini?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Script -->
<script>
    const form = document.getElementById('alamatForm');
    const idInput = document.getElementById('id');
    const idPembeliInput = document.getElementById('id_pembeli');
    const namaPenerimaInput = document.getElementById('nama_penerima');
    const alamatInput = document.getElementById('alamat_lengkap');
    const kotaInput = document.getElementById('kota');
    const provinsiInput = document.getElementById('provinsi');
    const kodePosInput = document.getElementById('kode_pos');
    const methodInput = document.getElementById('_method');
    const submitBtn = document.getElementById('submitBtn');
    const resetBtn = document.getElementById('resetBtn');

    function resetForm() {
        form.reset();
        idInput.value = '';
        methodInput.value = '';
        form.action = "{{ route('Admin.alamat_pembeli.store') }}";
        submitBtn.textContent = 'Simpan';
        idPembeliInput.required = true;
    }

    resetBtn.addEventListener('click', resetForm);

    document.querySelectorAll('.editBtn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.getAttribute('data-id');
            const idPembeli = button.getAttribute('data-id_pembeli');
            const nama = button.getAttribute('data-nama');
            const alamat = button.getAttribute('data-alamat');
            const kota = button.getAttribute('data-kota');
            const provinsi = button.getAttribute('data-provinsi');
            const kodepos = button.getAttribute('data-kodepos');

            idInput.value = id;
            idPembeliInput.value = idPembeli;
            namaPenerimaInput.value = nama;
            alamatInput.value = alamat;
            kotaInput.value = kota;
            provinsiInput.value = provinsi;
            kodePosInput.value = kodepos;

            form.action = `alamat-pembeli/${id}`;
            methodInput.value = 'PUT';

            submitBtn.textContent = 'Update';
            idPembeliInput.required = false;
        });
    });

    resetForm();
</script>
</body>
</html>
