@extends('layouts.gudang')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Barang Titipan</h2>

            <!-- Switch Mode di pojok kanan dengan desain yang lebih menarik -->
            <div class="mode-switch">
                <div class="btn-group btn-group-toggle" data-toggle="buttons" role="group" aria-label="Mode Switch">
                    <label class="btn btn-outline-primary active">
                        <input type="radio" name="mode" id="mode-penitip" value="penitip" checked autocomplete="off">
                        <i class="fas fa-user mr-2"></i>Mode Penitip
                    </label>
                    <label class="btn btn-outline-success">
                        <input type="radio" name="mode" id="mode-hunter" value="hunter" autocomplete="off">
                        <i class="fas fa-search mr-2"></i>Mode Hunter
                    </label>
                </div>
            </div>
        </div>

        {{-- Alert untuk menampilkan pesan sukses atau error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        {{-- Form Tambah Barang --}}
        <div class="card mb-4" id="form-tambah-barang">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span id="form-title">Tambah Barang Titipan - Mode Penitip</span>
                        <small class="d-block mt-1 text-light">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="mode-info">Pegawai gudang akan tercatat sebagai penambah barang</span>
                        </small>
                    </div>
                    <div class="badge badge-light badge-pill px-3 py-2">
                        <i class="fas fa-user mr-1"></i>
                        <span id="current-mode">Penitip</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('gudang.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <!-- Hidden field untuk mode -->
                    <input type="hidden" name="mode" id="selected-mode" value="penitip">

                    {{-- Field Hunter (hidden by default) --}}
                    <div class="row mb-4" id="hunter-field" style="display: none;">
                        <div class="col-md-12">
                            <div class="alert alert-info border-left-success shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3">
                                        <i class="fas fa-search fa-2x text-success"></i>
                                    </div>
                                    <div>
                                        <h6 class="alert-heading mb-1">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            Mode Hunter Aktif
                                        </h6>
                                        <p class="mb-0">
                                            Pilih hunter yang akan dicatat untuk barang ini.
                                            Pegawai gudang (<strong>{{ session('user_name', 'Anda') }}</strong>) tetap akan
                                            tercatat sebagai yang menambahkan barang.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="hunter_id" class="font-weight-bold">
                                    <i class="fas fa-user-tie text-success mr-2"></i>
                                    Pilih Hunter <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-control-lg @error('hunter_id') is-invalid @enderror"
                                    name="hunter_id" id="hunter_id">
                                    <option value="">
                                        <i class="fas fa-hand-pointer"></i> -- Pilih Hunter --
                                    </option>
                                    @if (isset($hunters) && $hunters->count() > 0)
                                        @foreach ($hunters as $hunter)
                                            <option value="{{ $hunter->id_pegawai }}"
                                                {{ old('hunter_id') == $hunter->id_pegawai ? 'selected' : '' }}>
                                                <i class="fas fa-user"></i> {{ $hunter->nama_pegawai }} (ID:
                                                {{ $hunter->id_pegawai }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled class="text-muted">
                                            <i class="fas fa-exclamation-triangle"></i> Tidak ada hunter tersedia
                                        </option>
                                    @endif
                                </select>
                                @error('hunter_id')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror

                                {{-- Info hunter count --}}
                                <small class="form-text text-muted mt-2">
                                    <i class="fas fa-users mr-1"></i>
                                    <strong>{{ isset($hunters) ? $hunters->count() : 0 }}</strong> hunter tersedia
                                    @if (isset($hunters) && $hunters->count() == 0)
                                        <span class="text-warning">
                                            <i class="fas fa-exclamation-triangle ml-2"></i>
                                            Tidak ada hunter yang terdaftar dalam sistem
                                        </span>
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_barang_titipan">Nama Barang <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_barang_titipan') is-invalid @enderror"
                                name="nama_barang_titipan" id="nama_barang_titipan"
                                value="{{ old('nama_barang_titipan') }}" placeholder="Masukkan nama barang">
                            @error('nama_barang_titipan')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="harga_barang">Harga <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('harga_barang') is-invalid @enderror"
                                name="harga_barang" id="harga_barang" value="{{ old('harga_barang') }}" min="0"
                                step="0.01" placeholder="Masukkan harga barang">
                            @error('harga_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jenis_barang">Jenis Barang <span class="text-danger">*</span></label>
                            <select class="form-control @error('jenis_barang') is-invalid @enderror" name="jenis_barang"
                                id="jenis_barang">
                                <option value="">-- Pilih Jenis Barang --</option>
                                <option value="Elektronik & Gadget"
                                    {{ old('jenis_barang') == 'Elektronik & Gadget' ? 'selected' : '' }}>Elektronik &
                                    Gadget</option>
                                <option value="Pakaian & Aksesori"
                                    {{ old('jenis_barang') == 'Pakaian & Aksesori' ? 'selected' : '' }}>Pakaian & Aksesori
                                </option>
                                <option value="Perabotan Rumah Tangga"
                                    {{ old('jenis_barang') == 'Perabotan Rumah Tangga' ? 'selected' : '' }}>Perabotan Rumah
                                    Tangga</option>
                                <option value="Buku, Alat Tulis, & Peralatan Sekolah"
                                    {{ old('jenis_barang') == 'Buku, Alat Tulis, & Peralatan Sekolah' ? 'selected' : '' }}>
                                    Buku, Alat Tulis, & Peralatan Sekolah</option>
                                <option value="Hobi, Mainan, & Koleksi"
                                    {{ old('jenis_barang') == 'Hobi, Mainan, & Koleksi' ? 'selected' : '' }}>Hobi, Mainan,
                                    & Koleksi</option>
                                <option value="Perlengkapan Bayi & Anak"
                                    {{ old('jenis_barang') == 'Perlengkapan Bayi & Anak' ? 'selected' : '' }}>Perlengkapan
                                    Bayi & Anak</option>
                                <option value="Otomotif & Aksesori"
                                    {{ old('jenis_barang') == 'Otomotif & Aksesori' ? 'selected' : '' }}>Otomotif &
                                    Aksesori</option>
                                <option value="Perlengkapan Taman & Outdoor"
                                    {{ old('jenis_barang') == 'Perlengkapan Taman & Outdoor' ? 'selected' : '' }}>
                                    Perlengkapan Taman & Outdoor</option>
                                <option value="Peralatan Kantor & Industri"
                                    {{ old('jenis_barang') == 'Peralatan Kantor & Industri' ? 'selected' : '' }}>Peralatan
                                    Kantor & Industri</option>
                                <option value="Kosmetik & Perawatan Diri"
                                    {{ old('jenis_barang') == 'Kosmetik & Perawatan Diri' ? 'selected' : '' }}>Kosmetik &
                                    Perawatan Diri</option>
                            </select>
                            @error('jenis_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="garansi_barang">Garansi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('garansi_barang') is-invalid @enderror"
                                name="garansi_barang" id="garansi_barang" value="{{ old('garansi_barang') }}">
                            @error('garansi_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="berat_barang">Berat (kg) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('berat_barang') is-invalid @enderror"
                                name="berat_barang" id="berat_barang" value="{{ old('berat_barang') }}" min="1"
                                placeholder="Masukkan berat dalam gram">
                            @error('berat_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status_barang">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status_barang') is-invalid @enderror" name="status_barang"
                                id="status_barang">
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="dijual" {{ old('status_barang') == 'dijual' ? 'selected' : '' }}>Dijual
                                </option>
                                <option value="barang untuk donasi"
                                    {{ old('status_barang') == 'barang untuk donasi' ? 'selected' : '' }}>Barang untuk
                                    Donasi</option>
                            </select>
                            @error('status_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="deskripsi_barang">Deskripsi <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('deskripsi_barang') is-invalid @enderror" name="deskripsi_barang"
                                id="deskripsi_barang" rows="3" placeholder="Masukkan deskripsi barang...">{{ old('deskripsi_barang') }}</textarea>
                            @error('deskripsi_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gambar_barang">Gambar Utama</label>
                            <input type="file" class="form-control @error('gambar_barang') is-invalid @enderror"
                                name="gambar_barang" id="gambar_barang"
                                accept="image/jpeg,image/png,image/jpg,image/gif">
                            <small class="form-text text-muted">Format yang diizinkan: JPEG, PNG, JPG, GIF. Maksimal
                                2MB.</small>
                            @error('gambar_barang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="gambar">Gambar Tambahan</label>
                            <input type="file"
                                class="form-control @error('gambar') is-invalid @enderror @error('gambar.*') is-invalid @enderror"
                                name="gambar[]" id="gambar" accept="image/jpeg,image/png,image/jpg,image/gif"
                                multiple>
                            <small class="form-text text-muted">Anda dapat memilih beberapa gambar sekaligus. Format yang
                                diizinkan: JPEG, PNG, JPG, GIF. Maksimal 2MB per file.</small>
                            @error('gambar')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                            @error('gambar.*')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>Simpan Barang
                            </button>
                            <button type="reset" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-undo mr-2"></i>Reset Form
                            </button>
                        </div>
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-info-circle mr-1"></i>
                                Mode aktif: <span id="active-mode-display"
                                    class="font-weight-bold text-primary">Penitip</span>
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Form Pencarian Barang Titipan --}}
        <div class="card mb-4">
            <div class="card-header">Cari Barang Titipan</div>
            <div class="card-body">
                <form action="{{ route('gudang.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <input type="text" class="form-control" name="search" placeholder="Cari barang..."
                                value="{{ request()->get('search') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <button type="submit" class="btn btn-primary">Cari</button>
                            @if (request()->get('search'))
                                <a href="{{ route('gudang.index') }}" class="btn btn-secondary">Reset</a>
                            @endif
                        </div>
                    </div>
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
                                <td>{{ $barang->berat_barang }} kg</td>
                                <td>{{ ucfirst($barang->status_barang) }}</td>
                                <td>{{ $barang->garansi_barang ?? '' }}</td>
                                <td>
                                    <!-- Tombol Edit -->
                                    <button type="button" class="btn btn-primary btn-edit-barang"
                                        data-barang='@json($barang)'>
                                        Edit
                                    </button>
                                    <form action="{{ route('gudang.destroy', $barang->id_barang) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Yakin hapus barang ini?')"
                                            class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-info btn-detail-barang"
                                        data-barang='@json($barang)'>
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Modal Edit --}}
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form id="editForm" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit Barang Titipan</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <input type="hidden" name="id_barang" id="edit_id_barang">
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_nama_barang_titipan">Nama Barang <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="nama_barang_titipan"
                                        id="edit_nama_barang_titipan" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_harga_barang">Harga <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="harga_barang" id="edit_harga_barang"
                                        min="0" step="0.01" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_jenis_barang">Jenis Barang <span class="text-danger">*</span></label>
                                    <select class="form-control" name="jenis_barang" id="edit_jenis_barang" required>
                                        <option value="">-- Pilih Jenis Barang --</option>
                                        <option value="Elektronik & Gadget">Elektronik & Gadget</option>
                                        <option value="Pakaian & Aksesori">Pakaian & Aksesori</option>
                                        <option value="Perabotan Rumah Tangga">Perabotan Rumah Tangga</option>
                                        <option value="Buku, Alat Tulis, & Peralatan Sekolah">Buku, Alat Tulis, & Peralatan Sekolah</option>
                                        <option value="Hobi, Mainan, & Koleksi">Hobi, Mainan, & Koleksi</option>
                                        <option value="Perlengkapan Bayi & Anak">Perlengkapan Bayi & Anak</option>
                                        <option value="Otomotif & Aksesori">Otomotif & Aksesori</option>
                                        <option value="Perlengkapan Taman & Outdoor">Perlengkapan Taman & Outdoor</option>
                                        <option value="Peralatan Kantor & Industri">Peralatan Kantor & Industri</option>
                                        <option value="Kosmetik & Perawatan Diri">Kosmetik & Perawatan Diri</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_garansi_barang">Garansi <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" name="garansi_barang" id="edit_garansi_barang" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_berat_barang">Berat (kg) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="berat_barang" id="edit_berat_barang"
                                        min="1" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_status_barang">Status <span class="text-danger">*</span></label>
                                    <select class="form-control" name="status_barang" id="edit_status_barang" required>
                                        <option value="">-- Pilih Status --</option>
                                        <option value="dijual">Dijual</option>
                                        <option value="barang untuk donasi">Barang untuk Donasi</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="edit_deskripsi_barang">Deskripsi <span class="text-danger">*</span></label>
                                    <textarea class="form-control" name="deskripsi_barang" id="edit_deskripsi_barang" 
                                        rows="3" required placeholder="Masukkan deskripsi barang..."></textarea>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_gambar_barang">Gambar Utama (opsional)</label>
                                    <input type="file" class="form-control" name="gambar_barang" id="edit_gambar_barang"
                                        accept="image/jpeg,image/png,image/jpg,image/gif">
                                    <small class="form-text text-muted">Format yang diizinkan: JPEG, PNG, JPG, GIF. Maksimal 2MB.</small>
                                    
                                    {{-- Preview gambar utama saat ini --}}
                                    <div id="current_main_image" class="mt-2" style="display: none;">
                                        <label class="form-label">Gambar Utama Saat Ini:</label>
                                        <div>
                                            <img id="preview_main_image" src="" alt="Gambar Utama" class="img-thumbnail" width="100">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="edit_gambar_tambahan">Gambar Tambahan (opsional)</label>
                                    <input type="file" class="form-control" name="gambar[]" id="edit_gambar_tambahan"
                                        accept="image/jpeg,image/png,image/jpg,image/gif" multiple>
                                    <small class="form-text text-muted">Anda dapat memilih beberapa gambar sekaligus. Format yang diizinkan: JPEG, PNG, JPG, GIF. Maksimal 2MB per file.</small>
                                </div>
                                
                                <div class="col-12">
                                    <label class="form-label">Gambar Tambahan Saat Ini:</label>
                                    <div id="gambarTambahanContainer" class="d-flex flex-wrap">
                                        <!-- Gambar tambahan akan ditampilkan di sini -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                <i class="fas fa-times mr-1"></i>Batal
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Detail Barang --}}
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">Detail Barang Titipan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Menampilkan Detail Barang -->
                    <div class="form-group">
                        <label for="detail_nama_barang">Nama Barang</label>
                        <input type="text" class="form-control" id="detail_nama_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_harga_barang">Harga</label>
                        <input type="text" class="form-control" id="detail_harga_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_jenis_barang">Jenis Barang</label>
                        <input type="text" class="form-control" id="detail_jenis_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_garansi_barang">Garansi</label>
                        <input type="text" class="form-control" id="detail_garansi_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_berat_barang">Berat</label>
                        <input type="text" class="form-control" id="detail_berat_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_status_barang">Status</label>
                        <input type="text" class="form-control" id="detail_status_barang" disabled>
                    </div>
                    <div class="form-group">
                        <label for="detail_deskripsi_barang">Deskripsi</label>
                        <textarea class="form-control" id="detail_deskripsi_barang" rows="3" disabled></textarea>
                    </div>
                    <div class="form-group">
                        <label for="detail_gambar_barang">Gambar Utama</label>
                        <img id="detail_gambar_barang" width="100" alt="Gambar Utama">
                    </div>
                    <div class="form-group">
                        <label for="detail_gambar_tambahan">Gambar Tambahan</label>
                        <div id="gambarBarangTitipan">
                            @foreach ($barang->gambarBarangTitipan as $gambar)
                                <img src="{{ asset('storage/gambar_barang_titipan/' . $gambar->nama_file_gambar) }}"
                                    alt="Gambar Barang Titipan" class="img-thumbnail" width="100">
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Jadwalkan Pengiriman -->
        <div class="modal fade" id="scheduleModal" tabindex="-1" role="dialog" aria-labelledby="scheduleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form id="scheduleForm" method="POST" action="{{ route('gudang.penitipan.jadwalkanPengiriman') }}">
                        @csrf
                        <input type="hidden" name="id_penitipan" id="penitipanIdField">

                        <div class="modal-header">
                            <h5 class="modal-title" id="scheduleModalLabel">Jadwalkan Pengiriman</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">
                            <div class="form-group mb-3">
                                <label for="tanggal_pengiriman">Tanggal & Waktu Pengiriman</label>
                                <input type="datetime-local" class="form-control" name="tanggal_pengiriman"
                                    id="tanggal_pengiriman" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="kurir_id">Pilih Kurir</label>
                                <select class="form-control" name="kurir_id" id="kurir_id" required>
                                    <option value="" disabled selected>-- Pilih Kurir --</option>
                                    @foreach ($kurirs as $kurir)
                                        <option value="{{ $kurir->id_pegawai }}">{{ $kurir->nama_pegawai }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan Jadwal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editButtons = document.querySelectorAll('.btn-edit-barang');
                editButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const barang = JSON.parse(this.getAttribute('data-barang'));
                        const form = document.getElementById('editForm');

                        form.action = `/gudang/barang/${barang.id_barang}`;

                        // Set basic fields
                        document.getElementById('edit_id_barang').value = barang.id_barang;
                        document.getElementById('edit_nama_barang_titipan').value = barang.nama_barang_titipan;
                        document.getElementById('edit_harga_barang').value = barang.harga_barang;
                        document.getElementById('edit_berat_barang').value = barang.berat_barang;
                        document.getElementById('edit_deskripsi_barang').value = barang.deskripsi_barang;

                        // Set dropdown jenis barang
                        const jenisSelect = document.getElementById('edit_jenis_barang');
                        jenisSelect.value = barang.jenis_barang;

                        // Set dropdown status barang
                        const statusSelect = document.getElementById('edit_status_barang');
                        statusSelect.value = barang.status_barang;

                        // Set date garansi
                        const garansiInput = document.getElementById('edit_garansi_barang');
                        if (barang.garansi_barang) {
                            // Pastikan format tanggal sesuai dengan input date (YYYY-MM-DD)
                            const garansiDate = new Date(barang.garansi_barang);
                            const formattedDate = garansiDate.toISOString().split('T')[0];
                            garansiInput.value = formattedDate;
                        }

                        // Show current main image if exists
                        const currentMainImageDiv = document.getElementById('current_main_image');
                        const previewMainImage = document.getElementById('preview_main_image');
                        
                        if (barang.gambar_barang) {
                            previewMainImage.src = `/storage/${barang.gambar_barang}`;
                            currentMainImageDiv.style.display = 'block';
                        } else {
                            currentMainImageDiv.style.display = 'none';
                        }

                        // Handle additional images
                        const gambarContainer = document.getElementById('gambarTambahanContainer');
                        gambarContainer.innerHTML = '';
                        
                        if (barang.gambar_barang_titipan && barang.gambar_barang_titipan.length > 0) {
                            barang.gambar_barang_titipan.forEach((gambar, index) => {
                                const div = document.createElement('div');
                                div.classList.add('position-relative', 'm-1');
                                div.innerHTML = `
                                    <img src="/storage/gambar_barang_titipan/${gambar.nama_file_gambar}" 
                                         width="80" height="80" 
                                         class="img-thumbnail object-cover" 
                                         alt="Gambar ${index + 1}">
                                    <small class="d-block text-center text-muted mt-1">Gambar ${index + 1}</small>
                                `;
                                gambarContainer.appendChild(div);
                            });
                        } else {
                            gambarContainer.innerHTML = '<p class="text-muted">Tidak ada gambar tambahan.</p>';
                        }

                        // Show modal
                        $('#editModal').modal('show');
                    });
                });

                // Handle form submission validation
                const editForm = document.getElementById('editForm');
                if (editForm) {
                    editForm.addEventListener('submit', function(e) {
                        // Validate required fields
                        const requiredFields = [
                            'edit_nama_barang_titipan',
                            'edit_harga_barang', 
                            'edit_jenis_barang',
                            'edit_garansi_barang',
                            'edit_berat_barang',
                            'edit_status_barang',
                            'edit_deskripsi_barang'
                        ];

                        let isValid = true;
                        let firstInvalidField = null;

                        requiredFields.forEach(fieldId => {
                            const field = document.getElementById(fieldId);
                            if (!field.value.trim()) {
                                field.classList.add('is-invalid');
                                isValid = false;
                                if (!firstInvalidField) {
                                    firstInvalidField = field;
                                }
                            } else {
                                field.classList.remove('is-invalid');
                            }
                        });

                        if (!isValid) {
                            e.preventDefault();
                            if (firstInvalidField) {
                                firstInvalidField.focus();
                            }
                            alert('Mohon lengkapi semua field yang wajib diisi!');
                            return false;
                        }

                        // Validate file size if files are selected
                        const mainImageFile = document.getElementById('edit_gambar_barang').files[0];
                        const additionalImageFiles = document.getElementById('edit_gambar_tambahan').files;

                        if (mainImageFile && mainImageFile.size > 2 * 1024 * 1024) {
                            e.preventDefault();
                            alert('Ukuran gambar utama tidak boleh lebih dari 2MB!');
                            return false;
                        }

                        for (let i = 0; i < additionalImageFiles.length; i++) {
                            if (additionalImageFiles[i].size > 2 * 1024 * 1024) {
                                e.preventDefault();
                                alert(`Ukuran gambar tambahan ${i + 1} tidak boleh lebih dari 2MB!`);
                                return false;
                            }
                        }
                    });
                }

                // Clear validation on input change
                const formInputs = document.querySelectorAll('#editModal input, #editModal select, #editModal textarea');
                formInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    });
                });
            });

            document.addEventListener('DOMContentLoaded', function() {
                // Menangani tombol Detail
                const detailButtons = document.querySelectorAll('.btn-detail-barang');

                detailButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const barang = JSON.parse(this.getAttribute('data-barang'));

                        // Set data barang ke dalam modal
                        document.getElementById('detail_nama_barang').value = barang
                            .nama_barang_titipan;
                        document.getElementById('detail_harga_barang').value =
                            `Rp ${barang.harga_barang.toLocaleString()}`;
                        document.getElementById('detail_jenis_barang').value = barang.jenis_barang;
                        document.getElementById('detail_garansi_barang').value = barang
                            .garansi_barang ?? '-';
                        document.getElementById('detail_berat_barang').value = barang.berat_barang +
                            ' g';
                        document.getElementById('detail_status_barang').value = barang.status_barang;
                        document.getElementById('detail_deskripsi_barang').value = barang
                            .deskripsi_barang;

                        // Menampilkan gambar utama
                        const gambarBarang = barang.gambar_barang ? `/storage/${barang.gambar_barang}` :
                            '/path/to/default-image.jpg';
                        document.getElementById('detail_gambar_barang').src = gambarBarang;

                        const gambarBarangTitipanContainer = document.getElementById(
                            'gambarBarangTitipan');
                        gambarBarangTitipanContainer.innerHTML = ''; // Clear previous images

                        // Memeriksa apakah ada gambar barang titipan
                        if (barang.gambar_barang_titipan && barang.gambar_barang_titipan.length > 0) {
                            barang.gambar_barang_titipan.forEach(gambar => {
                                const gambarElement = document.createElement('img');
                                gambarElement.src =
                                    `/storage/gambar_barang_titipan/${gambar.nama_file_gambar}`; // Sesuaikan dengan folder gambar tambahan
                                gambarElement.alt = 'Gambar Barang Titipan';
                                gambarElement.classList.add(
                                    'img-thumbnail'); // Optional, menambahkan styling
                                gambarBarangTitipanContainer.appendChild(gambarElement);
                            });
                        } else {
                            gambarBarangTitipanContainer.innerHTML =
                                '<p>Tidak ada gambar tambahan.</p>'; // Jika tidak ada gambar tambahan
                        }

                        // Tampilkan modal detail
                        $('#detailModal').modal('show');
                    });
                });
            });

            function openScheduleModal(penitipanId) {
                $('#scheduleModal').modal('show');
                document.getElementById('penitipanIdField').value = penitipanId;
            }

            function closeScheduleModal() {
                $('#scheduleModal').modal('hide');
            }

            document.getElementById('scheduleForm').addEventListener('submit', function(e) {
                const jadwalInput = this.querySelector('[name="tanggal_pengiriman"]');
                const selectedDate = new Date(jadwalInput.value);
                const now = new Date();

                if (
                    selectedDate.toDateString() === now.toDateString() &&
                    selectedDate.getHours() >= 16
                ) {
                    e.preventDefault();
                    alert('Pengiriman di atas jam 4 sore tidak bisa dijadwalkan di hari yang sama.');
                }
            });

            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded'); // Debug

                // Handle mode switch
                const modeRadios = document.querySelectorAll('input[name="mode"]');
                const hunterField = document.getElementById('hunter-field');
                const hunterSelect = document.getElementById('hunter_id');
                const selectedModeInput = document.getElementById('selected-mode');
                const formTitle = document.getElementById('form-title');
                const modeInfo = document.getElementById('mode-info');
                const currentMode = document.getElementById('current-mode');
                const activeModeDisplay = document.getElementById('active-mode-display');

                console.log('Mode radios found:', modeRadios.length); // Debug
                console.log('Hunter field:', hunterField); // Debug
                console.log('Hunter select:', hunterSelect); // Debug

                modeRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        console.log('Mode changed to:', this.value); // Debug

                        const selectedMode = this.value;
                        selectedModeInput.value = selectedMode;

                        // Update UI elements
                        if (selectedMode === 'hunter') {
                            // Show hunter field dan buat required
                            hunterField.style.display = 'block';
                            hunterSelect.setAttribute('required', 'required');

                            // Update text elements
                            formTitle.textContent = 'Tambah Barang Titipan - Mode Hunter';
                            modeInfo.innerHTML =
                                '<i class="fas fa-info-circle mr-1"></i> Pegawai gudang dan hunter yang dipilih akan tercatat';
                            currentMode.innerHTML = '<i class="fas fa-search mr-1"></i>Hunter';
                            activeModeDisplay.textContent = 'Hunter';
                            activeModeDisplay.className = 'font-weight-bold text-success';

                            // Add animation
                            hunterField.style.opacity = '0';
                            hunterField.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                hunterField.style.transition = 'all 0.3s ease';
                                hunterField.style.opacity = '1';
                                hunterField.style.transform = 'translateY(0)';
                            }, 10);

                            console.log('Hunter field shown'); // Debug
                        } else {
                            // Hide hunter field dan hapus required
                            hunterField.style.transition = 'all 0.3s ease';
                            hunterField.style.opacity = '0';
                            hunterField.style.transform = 'translateY(-10px)';

                            setTimeout(() => {
                                hunterField.style.display = 'none';
                                hunterSelect.removeAttribute('required');
                                hunterSelect.value = ''; // Reset value
                            }, 300);

                            // Update text elements
                            formTitle.textContent = 'Tambah Barang Titipan - Mode Penitip';
                            modeInfo.innerHTML =
                                '<i class="fas fa-info-circle mr-1"></i> Pegawai gudang akan tercatat sebagai penambah barang';
                            currentMode.innerHTML = '<i class="fas fa-user mr-1"></i>Penitip';
                            activeModeDisplay.textContent = 'Penitip';
                            activeModeDisplay.className = 'font-weight-bold text-primary';

                            console.log('Hunter field hidden'); // Debug
                        }
                    });
                });

                // Form validation
                const form = document.querySelector('form[action="{{ route('gudang.store') }}"]');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const mode = selectedModeInput.value;

                        console.log('Form submitted with mode:', mode); // Debug

                        if (mode === 'hunter') {
                            const hunterId = hunterSelect.value;
                            if (!hunterId) {
                                e.preventDefault();

                                // Show better error message
                                const alertDiv = document.createElement('div');
                                alertDiv.className = 'alert alert-warning alert-dismissible fade show';
                                alertDiv.innerHTML = `
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle text-warning mr-3 fa-lg"></i>
                                        <div>
                                            <strong>Perhatian!</strong> Silakan pilih Hunter terlebih dahulu sebelum menyimpan barang.
                                        </div>
                                    </div>
                                    <button type="button" class="close" data-dismiss="alert">
                                        <span>&times;</span>
                                    </button>
                                `;

                                // Insert alert before form
                                form.parentNode.insertBefore(alertDiv, form);

                                // Focus on hunter select
                                hunterSelect.focus();
                                hunterSelect.classList.add('is-invalid');

                                // Remove alert after 5 seconds
                                setTimeout(() => {
                                    alertDiv.remove();
                                    hunterSelect.classList.remove('is-invalid');
                                }, 5000);

                                return false;
                            }
                        }
                    });
                }

                // Add hover effects to mode buttons
                const modeButtons = document.querySelectorAll('.btn-group-toggle .btn');
                modeButtons.forEach(button => {
                    button.addEventListener('mouseenter', function() {
                        if (!this.classList.contains('active')) {
                            this.style.transform = 'translateY(-2px)';
                            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
                        }
                    });

                    button.addEventListener('mouseleave', function() {
                        if (!this.classList.contains('active')) {
                            this.style.transform = 'translateY(0)';
                            this.style.boxShadow = 'none';
                        }
                    });
                });
            });
        </script>
    @endsection
