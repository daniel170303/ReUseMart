<!-- resources/views/admin/pegawai/create.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Tambah Pegawai</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.pegawai.index') }}">Pegawai</a></li>
        <li class="breadcrumb-item active">Tambah</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i> Form Tambah Pegawai
        </div>
        <div class="card-body">
            <form action="{{ route('admin.pegawai.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="id_role" class="form-label">Role</label>
                    <select class="form-select @error('id_role') is-invalid @enderror" id="id_role" name="id_role" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_role }}">{{ $role->nama_role }}</option>
                        @endforeach
                    </select>
                    @error('id_role')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="nama_pegawai" class="form-label">Nama Pegawai</label>
                    <input type="text" class="form-control @error('nama_pegawai') is-invalid @enderror" id="nama_pegawai" name="nama_pegawai" value="{{ old('nama_pegawai') }}" required>
                    @error('nama_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="nomor_telepon_pegawai" class="form-label">Nomor Telepon</label>
                    <input type="text" class="form-control @error('nomor_telepon_pegawai') is-invalid @enderror" id="nomor_telepon_pegawai" name="nomor_telepon_pegawai" value="{{ old('nomor_telepon_pegawai') }}" required>
                    @error('nomor_telepon_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="email_pegawai" class="form-label">Email</label>
                    <input type="email" class="form-control @error('email_pegawai') is-invalid @enderror" id="email_pegawai" name="email_pegawai" value="{{ old('email_pegawai') }}" required>
                    @error('email_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="password_pegawai" class="form-label">Password</label>
                    <input type="password" class="form-control @error('password_pegawai') is-invalid @enderror" id="password_pegawai" name="password_pegawai" required>
                    @error('password_pegawai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection