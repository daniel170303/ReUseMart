<!-- resources/views/organisasi/dashboard.blade.php -->
@extends('layouts.organisasi')

@section('title', 'Dashboard Organisasi')

@section('content')
<div class="container-fluid">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px;">Dashboard Organisasi</h2>

    <!-- Welcome Section - Style seperti Owner -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <p style="font-size: 16px; color: #343a40;">
            Selamat datang kembali, <strong>{{ session('user_name') ?? ($organisasi->nama_organisasi ?? 'Organisasi') }}</strong>!
        </p>
        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
            Di sini Anda dapat mengelola permintaan donasi, melihat riwayat donasi yang diterima, dan mengubah informasi profil organisasi Anda.
        </p>
    </div>
</div>
@endsection