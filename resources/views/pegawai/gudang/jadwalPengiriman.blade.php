@extends('layouts.gudang')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4 fw-bold text-dark">Daftar Transaksi</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <table class="table table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID Transaksi</th>
                <th>Nama Barang</th>
                <th>Jenis Pengiriman</th>
                <th>Status</th>
                <th>Detail</th>
                <th>Jadwalkan</th>
                <th>Cetak PDF</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaksi as $item)
                @php
                    $status = strtolower(trim($item->status_transaksi));
                    $jenis = strtolower(trim($item->jenis_pengiriman));
                    $sudah_dijadwalkan = !is_null($item->tanggal_pengiriman) || !is_null($item->tanggal_pengambilan);
                @endphp
                <tr>
                    <td>{{ $item->id_transaksi }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ ucfirst($item->jenis_pengiriman) }}</td>
                    <td>{{ ucfirst($item->status_transaksi) }}</td>
                    <td>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id_transaksi }}">
                            Lihat
                        </button>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal{{ $item->id_transaksi }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Transaksi #{{ $item->id_transaksi }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>ID Barang:</strong> {{ $item->id_barang }}</li>
                                            <li class="list-group-item"><strong>ID Pembeli:</strong> {{ $item->id_pembeli }}</li>
                                            <li class="list-group-item"><strong>Nama Barang:</strong> {{ $item->nama_barang }}</li>
                                            <li class="list-group-item"><strong>Tanggal Pemesanan:</strong> {{ $item->tanggal_pemesanan }}</li>
                                            <li class="list-group-item"><strong>Tanggal Pelunasan:</strong> {{ $item->tanggal_pelunasan ?? '-' }}</li>
                                            <li class="list-group-item"><strong>Jenis Pengiriman:</strong> {{ $item->jenis_pengiriman }}</li>
                                            <li class="list-group-item"><strong>Tanggal Pengiriman:</strong> {{ $item->tanggal_pengiriman ?? '-' }}</li>
                                            <li class="list-group-item"><strong>Tanggal Pengambilan:</strong> {{ $item->tanggal_pengambilan ?? '-' }}</li>
                                            @if(strtolower($item->jenis_pengiriman) == 'pengantaran' && $item->tanggal_pengiriman && $item->nama_kurir)
                                                <li class="list-group-item"><strong>Kurir:</strong> {{ $item->nama_kurir }}</li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if(($status === 'dikirim' && in_array($jenis, ['pengantaran'])) || $status === 'diambil pembeli')
                            @if($sudah_dijadwalkan)
                                <button class="btn btn-secondary btn-sm" disabled>Sudah Dijadwalkan</button>
                            @else
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#jadwalModal{{ $item->id_transaksi }}">
                                    Jadwalkan Pengiriman
                                </button>

                                <!-- Modal Jadwal -->
                                <div class="modal fade" id="jadwalModal{{ $item->id_transaksi }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form method="POST" action="{{ route('gudang.penitipan.prosesJadwalkanPengiriman') }}">
                                            @csrf
                                            <input type="hidden" name="id_transaksi" value="{{ $item->id_transaksi }}">

                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ $status === 'diambil pembeli' ? 'Jadwalkan Pengambilan' : 'Jadwalkan Pengiriman' }}
                                                        #{{ $item->id_transaksi }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Tanggal Pengiriman</label>
                                                        <input type="date" name="tanggal_pengiriman" class="form-control" value="{{ old('tanggal_pengiriman') ?? '' }}">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Waktu Pengiriman</label>
                                                        <input type="time" name="waktu_pengiriman" class="form-control" value="{{ old('waktu_pengiriman') ?? '09:00' }}">
                                                        @if ($jenis == 'pengantaran')
                                                            <div class="form-text">Pengiriman maksimal pukul 16:00 jika untuk hari ini.</div>
                                                        @endif
                                                    </div>

                                                    @if ($jenis == 'pengantaran')
                                                        <div class="mb-3">
                                                            <label class="form-label">Pilih Kurir</label>
                                                            <select name="id_kurir" class="form-select">
                                                                <option value="" disabled selected>-- Pilih Kurir --</option>
                                                                @foreach ($kurirs as $kurir)
                                                                    <option value="{{ $kurir->id_pegawai }}" {{ old('id_kurir') == $kurir->id_pegawai ? 'selected' : '' }}>
                                                                        {{ $kurir->nama_pegawai }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @else
                            <span class="text-muted">Tidak bisa dijadwalkan</span>
                        @endif
                    </td>
                    <td>
                        @if($sudah_dijadwalkan)
                            @if($jenis == 'pengantaran')
                                <a href="{{ route('gudang.transaksi.cetakPDF', $item->id_transaksi) }}" class="btn btn-danger btn-sm" target="_blank" title="Cetak Nota Pengantaran">
                                    <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                                </a>
                            @elseif($jenis == 'ambil sendiri')
                                <a href="{{ route('gudang.transaksi.cetakPDFAmbil', $item->id_transaksi) }}" class="btn btn-danger btn-sm" target="_blank" title="Cetak Nota Ambil Sendiri">
                                    <i class="bi bi-file-earmark-pdf"></i> Cetak PDF
                                </a>
                            @endif
                        @else
                            <span class="text-muted">Belum Dijadwalkan</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection