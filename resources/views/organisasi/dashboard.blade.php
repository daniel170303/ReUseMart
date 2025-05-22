<!-- resources/views/organisasi/dashboard.blade.php -->
@extends('layouts.organisasi')

@section('title', 'Dashboard Organisasi')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Dashboard Organisasi</h1>
        <p class="text-muted mb-0">Welcome, {{ $organisasi->nama_organisasi }}</p>
    </div>
    
    <div class="row">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Requests</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingRequests }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pause-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Organisasi Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Organisasi Information</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> {{ $organisasi->nama_organisasi }}</p>
                    <p><strong>Email:</strong> {{ $organisasi->email_organisasi }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Address:</strong> {{ $organisasi->alamat_organisasi }}</p>
                    <p><strong>Phone:</strong> {{ $organisasi->nomor_telepon_organisasi }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
        </div>
        <div class="card-body">
            <a href="{{ route('organisasi.requests.create') }}" class="btn btn-primary mr-2">
                <i class="fas fa-plus mr-1"></i> Create New Request
            </a>
            <a href="{{ route('organisasi.requests') }}" class="btn btn-info">
                <i class="fas fa-list mr-1"></i> View All Requests
            </a>
        </div>
    </div>
</div>
@endsection