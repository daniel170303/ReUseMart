@extends('layouts.gudang') {{-- Pastikan layout ini sesuai dengan struktur proyekmu --}}

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Manajemen Barang Titipan</h2>

        {{-- Form Tambah Barang --}}
        <div class="card mb-4">
            <div class="card-header">Tambah Barang Titipan</div>
            <div class="card-body">
                <form action="{{ route('gudang.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang_titipan">Nama Barang</label>
                            <input type="text" class="form-control" name="nama_barang_titipan" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga_barang">Harga</label>
                            <input type="number" class="form-control" name="harga_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_barang">Jenis</label>
                            <input type="text" class="form-control" name="jenis_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="garansi_barang">Garansi (bulan)</label>
                            <input type="text" class="form-control" name="garansi_barang">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="berat_barang">Berat (gram)</label>
                            <input type="number" class="form-control" name="berat_barang" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_barang">Status</label>
                            <select class="form-control" name="status_barang" required>
                                <option value="ready">Ready</option>
                                <option value="terjual">Terjual</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deskripsi_barang">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi_barang" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gambar_barang">Gambar Barang</label>
                            <input type="file" class="form-control" name="gambar_barang" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </form>
            </div>
        </div>

        {{-- Tabel Daftar Barang --}}
        <div class="card">
            <div class="card-header">Daftar Barang Titipan</div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Harga</th>
                            <th>Jenis</th>
                            <th>Berat</th>
                            <th>Status</th>
                            <th>Garansi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($barangTitipan as $barang)
                            <tr>
                                <td>
                                    @if ($barang->gambar_barang && file_exists(public_path('storage/' . $barang->gambar_barang)))
                                        <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar"
                                            width="80">
                                    @else
                                        <span class="text-muted">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td>{{ $barang->nama_barang_titipan }}</td>
                                <td>Rp{{ number_format($barang->harga_barang, 0, ',', '.') }}</td>
                                <td>{{ $barang->jenis_barang }}</td>
                                <td>{{ $barang->berat_barang }} g</td>
                                <td>{{ ucfirst($barang->status_barang) }}</td>
                                <td>{{ $barang->garansi_barang ?? '-' }}</td>
                                <td>
                                    {{-- Edit --}}
                                    <a href="{{ route('gudang.edit', $barang->id_barang) }}"
                                        class="btn btn-sm btn-warning">Edit</a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('gudang.destroy', $barang->id_barang) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin hapus barang ini?')"
                                            class="btn btn-sm btn-danger">Hapus</button>
                                    </form>

                                    {{-- Detail --}}
                                    <a href="{{ route('barang.show', $barang->id_barang) }}"
                                        class="btn btn-sm btn-info">Detail</a>

                                    {{-- Konfirmasi Pengambilan
                                    @if (optional($barang->penitipan)->tanggal_pengambilan)
                                        <form
                                            action="{{ route('penitipan.konfirmasiPengambilan', $barang->penitipan->id_penitipan) }}"
                                            method="POST" style="display:inline-block;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Konfirmasi pengambilan barang ini?')">
                                                Konfirmasi Pengambilan
                                            </button>
                                        </form>
                                    @endif --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Tabel Barang dengan Jadwal Pengembalian --}}
        <div class="card mt-5">
            <div class="card-header bg-success text-white">Barang dengan Jadwal Pengembalian</div>
            <div class="card-body">
                @php
                    $barangDenganJadwal = $barangTitipan->filter(function ($barang) {
                        return optional($barang->penitipan)->tanggal_pengambilan !== null;
                    });
                @endphp

                @if ($barangDenganJadwal->count() > 0)
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama</th>
                                <th>Jenis</th>
                                <th>Status</th>
                                <th>Tanggal Pengambilan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangDenganJadwal as $barang)
                                <tr>
                                    <td>
                                        @if ($barang->gambar_barang && file_exists(public_path('storage/' . $barang->gambar_barang)))
                                            <img src="{{ asset('storage/' . $barang->gambar_barang) }}" alt="Gambar"
                                                width="80">
                                        @else
                                            <span class="text-muted">Tidak ada gambar</span>
                                        @endif
                                    </td>
                                    <td>{{ $barang->nama_barang_titipan }}</td>
                                    <td>{{ $barang->jenis_barang }}</td>
                                    <td>{{ ucfirst($barang->status_barang) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($barang->penitipan->tanggal_pengambilan)->format('d M Y') }}
                                    </td>
                                    <td>
                                        <form
                                            action="{{ route('penitipan.konfirmasiPengambilan', $barang->penitipan->id_penitipan) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm"
                                                onclick="return confirm('Yakin ingin mengonfirmasi pengambilan barang ini?')">
                                                Konfirmasi Pengambilan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-muted">Tidak ada barang yang dijadwalkan untuk pengambilan saat ini.</p>
                @endif
            </div>
        </div>

    </div>
@endsection
