@extends('layouts.gudang')

@section('content')
    <div class="container">
        <h2 class="mb-4">Tambah Penitipan Baru</h2>

        {{-- Form Tambah Penitipan --}}
        <form action="{{ route('penitipan.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="id_penitip" class="form-label">Nama Penitip</label>
                <select class="form-control" name="id_penitip" required>
                    <option value="">-- Pilih Penitip --</option>
                    @foreach ($penitipList as $penitip)
                        <option value="{{ $penitip->id_penitip }}">{{ $penitip->nama_penitip }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="id_barang[]" class="form-label">Pilih Barang Titipan</label>
                <select class="form-control" name="id_barang[]" multiple required>
                    @foreach ($barangList as $barang)
                        <option value="{{ $barang->id_barang }}">{{ $barang->nama_barang_titipan }}</option>
                    @endforeach
                </select>
                <small class="text-muted">Gunakan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu.</small>
            </div>

            <button type="submit" class="btn btn-success">Simpan Penitipan</button>
        </form>

        <hr>

        <h2 class="mt-5 mb-4">Daftar Penitipan</h2>

        {{-- Tabel Daftar Penitipan --}}
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Penitipan</th>
                    <th>ID Penitip</th>
                    <th>Barang Titipan</th>
                    <th>Aksi</th> {{-- Tambahkan kolom aksi --}}
                </tr>
            </thead>
            <tbody>
                @foreach ($penitipanList as $penitipan)
                    <tr>
                        <td>{{ $penitipan->id_penitipan }}</td>
                        <td>{{ $penitipan->id_penitip }}</td>
                        <td>
                            <ul>
                                @foreach ($detailPenitipan->where('id_penitipan', $penitipan->id_penitipan) as $detail)
                                    <li>{{ $detail->barang->nama_barang_titipan ?? 'Barang tidak ditemukan' }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                        <td>
                            @if (session('nota_path') && session('last_penitipan_id') == $penitipan->id_penitipan)
                                <a href="{{ asset('storage/nota/' . session('nota_path')) }}" target="_blank"
                                    class="btn btn-primary">Download Nota</a>
                            @endif
                        </td>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
