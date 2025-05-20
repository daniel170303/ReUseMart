<!-- resources/views/admin/organisasi/show.blade.php -->
@extends('layouts.admin')

@section('title', 'Organization Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Organization Details</h1>
        <div>
            <a href="{{ route('admin.organisasi.edit', $organisasi->id_organisasi) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit fa-sm mr-2"></i>Edit
            </a>
            <a href="{{ route('admin.organisasi.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm mr-2"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Organization Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px;">Organization ID</th>
                                <td>{{ $organisasi->id_organisasi }}</td>
                            </tr>
                            <tr>
                                <th>Name</th>
                                <td>{{ $organisasi->nama_organisasi }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $organisasi->email_organisasi }}</td>
                            </tr>
                            <tr>
                                <th>Phone Number</th>
                                <td>{{ $organisasi->nomor_telepon_organisasi }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $organisasi->alamat_organisasi }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.organisasi.edit', $organisasi->id_organisasi) }}" class="btn btn-warning btn-block mb-2">
                            <i class="fas fa-edit fa-sm mr-2"></i>Edit Organization
                        </a>
                        <button type="button" class="btn btn-danger btn-block" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash fa-sm mr-2"></i>Delete Organization
                        </button>
                    </div>
                </div>
            </div>

            <!-- Donation Requests Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Donation Requests Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4>{{ $requestsCount }} Requests</h4>
                    </div>
                    <div class="row text-center">
                        <div class="col-sm-6 mb-2">
                            <div class="card bg-info text-white">
                                <div class="card-body py-2">
                                    <h5>{{ $pendingRequests }} Pending</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <div class="card bg-success text-white">
                                <div class="card-body py-2">
                                    <h5>{{ $completedRequests }} Completed</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.requests.index', ['organisasi_id' => $organisasi->id_organisasi]) }}" class="btn btn-primary btn-sm btn-block">
                            View All Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong>{{ $organisasi->nama_organisasi }}</strong>? This action cannot be undone and will also delete all related donation requests.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('admin.organisasi.destroy', $organisasi->id_organisasi) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection