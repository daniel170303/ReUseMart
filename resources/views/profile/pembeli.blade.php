<!-- resources/views/profile/pembeli.blade.php -->
@extends('layouts.pembeli')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">My Profile</h1>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="https://via.placeholder.com/150" alt="Profile" class="rounded-circle mb-3" width="150" height="150">
                    <h5>{{ $profileData->nama_pembeli }}</h5>
                    <p class="text-muted">Customer</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="nama_pembeli" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="nama_pembeli" name="nama_pembeli" value="{{ $profileData->nama_pembeli }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="email_pembeli" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email_pembeli" value="{{ $profileData->email_pembeli }}" disabled>
                            <div class="form-text">Email cannot be changed</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="alamat_pembeli" class="form-label">Address</label>
                            <textarea class="form-control" id="alamat_pembeli" name="alamat_pembeli" rows="3">{{ $profileData->alamat_pembeli }}</textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nomor_telepon_pembeli" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="nomor_telepon_pembeli" name="nomor_telepon_pembeli" value="{{ $profileData->nomor_telepon_pembeli }}">
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="form-text">Leave blank if you don't want to change the password</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection