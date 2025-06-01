@extends('layouts.owner')

@section('content')
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px;">Dashboard Owner</h2>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #343a40;">
            Selamat datang kembali, <strong>{{ session('user_name') }}</strong>!
        </p>
        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
            Di sini Anda dapat mengelola data bisnis Anda, melihat riwayat transaksi, dan mengubah informasi profil Anda.
        </p>
    </div>
@endsection
