<!-- resources/views/admin/pegawai/show.blade.php -->
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Detail Pegawai</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.pegawai.index') }}">Pegawai</a></li>
        <li class="breadcrumb-item active">Detail</li>
    </ol>

    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user me-1"></i> Informasi Pegawai
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">ID Pegawai</th>
                        <td>{{ $pegawai->id_pegawai }}</td>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <td>{{ $pegawai->nama_pegawai }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $pegawai->email_pegawai }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Telepon</th>
                        <td>{{ $pegawai->nomor_telepon_pegawai }}</td>
                    </tr>
                    <tr>
                        <th>Role</th>
                        <td>{{ $pegawai->id_role }}</td>
                    </tr>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary me-2">Kembali</a>
                <a href="{{ route('admin.pegawai.edit', $pegawai->id_pegawai) }}" class="btn btn-warning me-2">Edit</a>
                <form action="{{ route('admin.pegawai.destroy', $pegawai->id_pegawai) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection