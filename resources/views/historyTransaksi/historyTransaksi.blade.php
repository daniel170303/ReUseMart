<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Riwayat Transaksi Pembeli</h3>
    <table class="table table-bordered mt-3">
        <thead class="thead-dark">
            <tr>
                <th>ID Transaksi</th>
                <th>Nama Barang</th>
                <th>Tanggal Pemesanan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksis as $transaksi)
                <tr>
                    <td>
                        <a href="#"
                           class="transaksi-link"
                           data-id="{{ $transaksi->id_transaksi }}"
                           data-nama="{{ $transaksi->nama_barang }}"
                           data-pesan="{{ $transaksi->tanggal_pemesanan }}"
                           data-lunas="{{ $transaksi->tanggal_pelunasan }}"
                           data-pengiriman="{{ $transaksi->jenis_pengiriman }}"
                           data-tgl_kirim="{{ $transaksi->tanggal_pengiriman }}"
                           data-tgl_ambil="{{ $transaksi->tanggal_pengambilan }}">
                            {{ $transaksi->id_transaksi }}
                        </a>
                    </td>
                    <td>{{ $transaksi->nama_barang }}</td>
                    <td>{{ $transaksi->tanggal_pemesanan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="transaksiModal" tabindex="-1" role="dialog" aria-labelledby="transaksiModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transaksiModalLabel">Detail Transaksi</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><strong>ID:</strong> <span id="modal-id"></span></p>
        <p><strong>Nama Barang:</strong> <span id="modal-nama"></span></p>
        <p><strong>Tanggal Pemesanan:</strong> <span id="modal-pesan"></span></p>
        <p><strong>Tanggal Pelunasan:</strong> <span id="modal-lunas"></span></p>
        <p><strong>Jenis Pengiriman:</strong> <span id="modal-pengiriman"></span></p>
        <p><strong>Tanggal Pengiriman:</strong> <span id="modal-tgl-kirim"></span></p>
        <p><strong>Tanggal Pengambilan:</strong> <span id="modal-tgl-ambil"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.transaksi-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            document.getElementById('modal-id').textContent = this.dataset.id;
            document.getElementById('modal-nama').textContent = this.dataset.nama;
            document.getElementById('modal-pesan').textContent = this.dataset.pesan;
            document.getElementById('modal-lunas').textContent = this.dataset.lunas || '-';
            document.getElementById('modal-pengiriman').textContent = this.dataset.pengiriman || '-';
            document.getElementById('modal-tgl-kirim').textContent = this.dataset.tgl_kirim || '-';
            document.getElementById('modal-tgl-ambil').textContent = this.dataset.tgl_ambil || '-';

            $('#transaksiModal').modal('show');
        });
    });
});
</script>
</body>
</html>