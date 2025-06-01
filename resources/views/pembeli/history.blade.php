@extends('layouts.pembeli')

@section('title', 'Riwayat Pembelian')

@section('content')
    <div class="mb-4">
        <h2 class="h4 fw-bold">Riwayat Pembelian - {{ $pembeli->nama }}</h2>
    </div>

    @forelse($riwayat as $transaksi)
        <div class="card mb-4">
            <div class="row g-0">
                @if ($transaksi->barangTitipan->gambar_barang)
                    <div class="col-md-3">
                        <img src="{{ asset('storage/' . $transaksi->barangTitipan->gambar_barang) }}"
                            class="img-fluid rounded-start" alt="...">
                    </div>
                @endif
                <div class="col-md-9">
                    <div class="card-body">
                        <h5 class="card-title">{{ $transaksi->barangTitipan->nama_barang_titipan }}</h5>
                        <p class="card-text mb-1">Harga: <strong>Rp
                                {{ number_format($transaksi->barangTitipan->harga_barang, 0, ',', '.') }}</strong></p>
                        <p class="card-text"><small class="text-muted">Status: {{ $transaksi->status_transaksi }}</small>
                        </p>

                        <form method="POST" action="{{ route('rating.store') }}" class="mt-3">
                            @csrf
                            <input type="hidden" name="id_barang" value="{{ $transaksi->id_barang }}">
                            <input type="hidden" name="id_pembeli" value="{{ $pembeli->id_pembeli }}">

                            <div class="row g-2 align-items-center">
                                <div class="col-auto">
                                    <label for="rating" class="col-form-label">Beri Rating:</label>
                                </div>
                                <div class="col-auto">
                                    <select name="rating" id="rating" class="form-select form-select-sm">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}">{{ $i }} ⭐</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-paper-plane me-1"></i> Kirim Rating
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">Belum ada transaksi pembelian.</div>
    @endforelse
@endsection
