@extends('layouts.organisasi')

@section('title', 'Profile Organisasi')

@section('content')
    <div class="container-fluid">

        <div class="row">
            <!-- Profile Information Card -->
            <div class="col-xl-4 col-lg-5">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-building me-2"></i>Informasi Organisasi
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                                style="width: 80px; height: 80px;">
                                <i class="fas fa-building fa-2x text-white"></i>
                            </div>
                        </div>
                        <h5 class="font-weight-bold">{{ $organisasi->nama_organisasi }}</h5>
                        <p class="text-muted">{{ $organisasi->email_organisasi }}</p>
                        <p class="text-muted">
                            <i class="fas fa-phone me-1"></i>{{ $organisasi->nomor_telepon_organisasi }}
                        </p>
                        <p class="text-muted">
                            <i class="fas fa-map-marker-alt me-1"></i>{{ $organisasi->alamat_organisasi }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Form -->
            <div class="col-xl-8 col-lg-7">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-user-edit me-2"></i>Edit Profile
                        </h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('organisasi.profile.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nama_organisasi" class="form-label">Nama Organisasi <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nama_organisasi"
                                            name="nama_organisasi"
                                            value="{{ old('nama_organisasi', $organisasi->nama_organisasi) }}" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email_organisasi" class="form-label">Email <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email_organisasi"
                                            name="email_organisasi"
                                            value="{{ old('email_organisasi', $organisasi->email_organisasi) }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nomor_telepon_organisasi" class="form-label">Nomor Telepon <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nomor_telepon_organisasi"
                                            name="nomor_telepon_organisasi"
                                            value="{{ old('nomor_telepon_organisasi', $organisasi->nomor_telepon_organisasi) }}"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="alamat_organisasi" class="form-label">Alamat <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" id="alamat_organisasi" name="alamat_organisasi" rows="3" required>{{ old('alamat_organisasi', $organisasi->alamat_organisasi) }}</textarea>
                            </div>

                            <hr>
                            <h6 class="text-primary">Ubah Password (Opsional)</h6>
                            <p class="text-muted small">Kosongkan jika tidak ingin mengubah password</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_organisasi" class="form-label">Password Baru</label>
                                        <input type="password" class="form-control" id="password_organisasi"
                                            name="password_organisasi">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_organisasi_confirmation" class="form-label">Konfirmasi Password
                                            Baru</label>
                                        <input type="password" class="form-control" id="password_organisasi_confirmation"
                                            name="password_organisasi_confirmation">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('organisasi.dashboard') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
