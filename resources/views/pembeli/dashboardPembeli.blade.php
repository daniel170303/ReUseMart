@extends('layouts.pembeli')

@section('content')
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px;">Dashboard Pembeli</h2>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="font-size: 16px; color: #343a40;">
            Selamat datang kembali, <strong>{{ session('user_name') ?? ($pembeli->nama_pembeli ?? 'Pembeli') }}</strong>!
        </p>
        <p style="font-size: 14px; color: #6c757d; margin-top: 10px;">
            Di sini Anda dapat menjelajahi produk-produk berkualitas, mengelola keranjang belanja, melihat riwayat pembelian, dan mengubah informasi profil Anda.
        </p>
        
        @if(isset($rewardPoints))
        <div class="mt-3 p-3" style="background-color: #f8f9fa; border-radius: 6px;">
            <p class="mb-0" style="font-size: 14px; color: #495057;">
                <i class="fas fa-star text-warning me-1"></i>
                Total Poin Reward: <strong>{{ $rewardPoints ?? 0 }}</strong>
            </p>
        </div>
        @endif
    </div>
@endsection