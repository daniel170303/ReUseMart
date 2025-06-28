@extends('layouts.penitip')

@section('content')
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px;">Dashboard Penitip</h2>

    @if(session('notifikasi_penitip'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>📢 {{ session('notifikasi_penitip') }}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #343a40;">
            Selamat datang kembali, <strong>{{ session('user_name') }}</strong>!
        </p>
        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
            Di sini Anda dapat mengelola data barang titipan Anda, melihat riwayat penitipan, dan mengubah informasi profil Anda.
        </p>
    </div>
@endsection