{{-- resources/views/owner.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Owner - Terima Request & Kelola Donasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container mt-4">
    <h2>Daftar Request Donasi (Status Pending)</h2>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($requests->isEmpty())
        <div class="alert alert-info">Tidak ada request donasi dengan status pending.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Request</th>
                    <th>ID Organisasi</th>
                    <th>Nama Request Barang</th>
                    <th>Tanggal Request</th>
                    <th>Status Request</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $req)
                    <tr>
                        <td>{{ $req->id_request }}</td>
                        <td>{{ $req->id_organisasi }}</td>
                        <td>{{ $req->nama_request_barang }}</td>
                        <td>{{ $req->tanggal_request }}</td>
                        <td>{{ $req->status_request }}</td>
                        <td>
                            <form action="{{ route('request.terima', $req->id_request) }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">Terima</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <hr class="my-5">

    <h2>Daftar Donasi</h2>

    @if($donasis->isEmpty())
        <div class="alert alert-info">Belum ada data donasi.</div>
    @else
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Donasi</th>
                    <th>ID Barang</th>
                    <th>ID Request</th>
                    <th>Tanggal Donasi</th>
                    <th>Penerima Donasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($donasis as $donasi)
                <tr>
                    <td>{{ $donasi->id_donasi }}</td>
                    <td>{{ $donasi->id_barang }}</td>
                    <td>{{ $donasi->id_request }}</td>
                    <td>{{ $donasi->tanggal_donasi }}</td>
                    <td>{{ $donasi->penerima_donasi }}</td>
                    <td>
                        <button 
                            class="btn btn-primary btn-sm btn-edit-donasi"
                            data-bs-toggle="modal" 
                            data-bs-target="#editDonasiModal"
                            data-id="{{ $donasi->id_donasi }}"
                            data-id_barang="{{ $donasi->id_barang }}"
                            data-id_request="{{ $donasi->id_request }}"
                            data-tanggal_donasi="{{ $donasi->tanggal_donasi }}"
                            data-penerima_donasi="{{ $donasi->penerima_donasi }}"
                        >Edit</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif

</div>

<!-- Modal Edit Donasi -->
<div class="modal fade" id="editDonasiModal" tabindex="-1" aria-labelledby="editDonasiModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="editDonasiForm" method="POST" action="#">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editDonasiModalLabel">Edit Donasi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="id" id="donasiId" />
            <div class="mb-3">
                <label for="id_barang" class="form-label">ID Barang</label>
                <input type="number" class="form-control" id="id_barang" name="id_barang" required />
            </div>
            <div class="mb-3">
                <label for="id_request" class="form-label">ID Request</label>
                <input type="number" class="form-control" id="id_request" name="id_request" required />
            </div>
            <div class="mb-3">
                <label for="tanggal_donasi" class="form-label">Tanggal Donasi</label>
                <input type="date" class="form-control" id="tanggal_donasi" name="tanggal_donasi" required />
            </div>
            <div class="mb-3">
                <label for="penerima_donasi" class="form-label">Penerima Donasi</label>
                <input type="text" class="form-control" id="penerima_donasi" name="penerima_donasi" required />
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editDonasiModal = document.getElementById('editDonasiModal');
    editDonasiModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var id_barang = button.getAttribute('data-id_barang');
        var id_request = button.getAttribute('data-id_request');
        var tanggal_donasi = button.getAttribute('data-tanggal_donasi');
        var penerima_donasi = button.getAttribute('data-penerima_donasi');

        // Isi form dengan data yang diterima
        this.querySelector('#donasiId').value = id;
        this.querySelector('#id_barang').value = id_barang;
        this.querySelector('#id_request').value = id_request;
        this.querySelector('#tanggal_donasi').value = tanggal_donasi;
        this.querySelector('#penerima_donasi').value = penerima_donasi;

        // Set action form secara dinamis (pastikan route sudah benar)
        var form = this.querySelector('#editDonasiForm');
        form.action = "{{ url('/donasi') }}/" + id + "/update";
        console.log("Edit donasi id:", id);
    });
});
</script>

</body>
</html>
