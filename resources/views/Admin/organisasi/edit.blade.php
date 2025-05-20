<!-- resources/views/admin/organisasi/edit.blade.php -->
@extends('layouts.admin')

@section('title', 'Edit Organization')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Organization</h1>
        <a href="{{ route('admin.organisasi.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left fa-sm mr-2"></i>Back to List
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Edit Organization Information</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.organisasi.update', $organisasi->id_organisasi) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nama_organisasi" class="form-label">Organization Name</label>
                        <input type="text" class="form-control @error('nama_organisasi') is-invalid @enderror" 
                               id="nama_organisasi" name="nama_organisasi" value="{{ old('nama_organisasi', $organisasi->nama_organisasi) }}" required>
                        @error('nama_organisasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email_organisasi" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email_organisasi') is-invalid @enderror" 
                               id="email_organisasi" name="email_organisasi" value="{{ old('email_organisasi', $organisasi->email_organisasi) }}" required>
                        @error('email_organisasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nomor_telepon_organisasi" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('nomor_telepon_organisasi') is-invalid @enderror" 
                               id="nomor_telepon_organisasi" name="nomor_telepon_organisasi" value="{{ old('nomor_telepon_organisasi', $organisasi->nomor_telepon_organisasi) }}" required>
                        @error('nomor_telepon_organisasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label for="password_organisasi" class="form-label">Password</label>
                        <input type="password" class="form-control @error('password_organisasi') is-invalid @enderror" 
                               id="password_organisasi" name="password_organisasi" placeholder="Leave blank to keep current password">
                        <small class="form-text text-muted">Leave blank if you don't want to change the password</small>
                        @error('password_organisasi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="alamat_organisasi" class="form-label">Address</label>
                    <textarea class="form-control @error('alamat_organisasi') is-invalid @enderror" 
                              id="alamat_organisasi" name="alamat_organisasi" rows="3" required>{{ old('alamat_organisasi', $organisasi->alamat_organisasi) }}</textarea>
                    @error('alamat_organisasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="{{ route('admin.organisasi.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Organization</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection