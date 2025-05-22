<!-- resources/views/pembeli/dashboard.blade.php -->
@extends('layouts.pembeli')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4">Welcome back, {{ Auth::user()->name }}!</h1>
    
    <div class="row">
        <!-- Account Summary Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Account Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/100" alt="Profile" class="rounded-circle me-3" width="100" height="100">
                        <div>
                            <h5 class="mb-1">{{ Auth::user()->name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="fas fa-envelope me-1"></i> {{ Auth::user()->email }}
                            </p>
                            <a href="{{ route('profile.show') }}" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="fas fa-user-edit me-1"></i> Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders Card -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Recent Orders</h5>
                    <a href="{{ route('pembeli.transactions') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Replace with actual data -->
                                <tr>
                                    <td>#1001</td>
                                    <td>15 May 2025</td>
                                    <td>Rp 350.000</td>
                                    <td><span class="badge bg-success">Completed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Featured Products -->
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Featured Products</h5>
            <a href="{{ route('pembeli.products') }}" class="btn btn-sm btn-outline-primary">Browse All</a>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Example product cards - replace with actual data -->
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/150" alt="Product" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Recycled Chair</h5>
                            <p class="card-text text-success">Rp 250.000</p>
                            <a href="#" class="btn btn-primary btn-sm d-block">View Details</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/150" alt="Product" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Eco Lamp</h5>
                            <p class="card-text text-success">Rp 175.000</p>
                            <a href="#" class="btn btn-primary btn-sm d-block">View Details</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/150" alt="Product" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Upcycled Table</h5>
                            <p class="card-text text-success">Rp 450.000</p>
                            <a href="#" class="btn btn-primary btn-sm d-block">View Details</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card h-100">
                        <img src="https://via.placeholder.com/150" alt="Product" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title">Recycled Vase</h5>
                            <p class="card-text text-success">Rp 120.000</p>
                            <a href="#" class="btn btn-primary btn-sm d-block">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection