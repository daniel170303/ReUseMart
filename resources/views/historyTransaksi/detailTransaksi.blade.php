@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Detail Transaksi</h2>
                
                <a href="{{ route('pembeli.history') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
                </a>
                
            </div>
            <hr>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informasi Transaksi -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Informasi Transaksi</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nomor Transaksi:</div>
                        <div class="col-md-8">{{ $transaksi->nomor_nota }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Tanggal Pemesanan:</div>
                        <div class="col-md-8">{{ $transaksi->tanggal_pemesanan->format('d F Y, H:i') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status Transaksi:</div>
                        <div class="col-md-8">
                            @if($transaksi->status_transaksi == 'menunggu_pembayaran')
                                <span class="badge bg-warning text-dark">Menunggu Pembayaran</span>
                                @if($countdown > 0)
                                    <div class="mt-2">
                                        <small class="text-danger">
                                            Sisa waktu pembayaran: <span id="countdown">{{ $countdown }}</span> detik
                                        </small>
                                    </div>
                                @endif
                            @elseif($transaksi->status_transaksi == 'menunggu_verifikasi')
                                <span class="badge bg-info">Menunggu Verifikasi</span>
                            @elseif($transaksi->status_transaksi == 'disiapkan')
                                <span class="badge bg-primary">Sedang Disiapkan</span>
                            @elseif($transaksi->status_transaksi == 'dikirim')
                                <span class="badge bg-info">Dikirim</span>
                            @elseif($transaksi->status_transaksi == 'selesai')
                                <span class="badge bg-success">Selesai</span>
                            @elseif($transaksi->status_transaksi == 'dibatalkan')
                                <span class="badge bg-danger">Dibatalkan</span>
                            @else
                                <span class="badge bg-secondary">{{ $transaksi->status_transaksi }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Metode Pengiriman:</div>
                        <div class="col-md-8">
                            {{ $transaksi->metode_pengiriman == 'kurir' ? 'Kurir' : 'Ambil Sendiri' }}
                        </div>
                    </div>
                    @if($transaksi->metode_pengiriman == 'kurir')
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Alamat Pengiriman:</div>
                        <div class="col-md-8">
                            {{ $transaksi->nama_penerima }}<br>
                            {{ $transaksi->telepon_penerima }}<br>
                            {{ $transaksi->alamat_pengiriman_lengkap }}<br>
                            Kode Pos: {{ $transaksi->kode_pos_pengiriman ?? '-' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detail Barang -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Detail Barang</h4>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th class="text-center">Jumlah</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->detailTransaksis as $detail)
                                <tr>
                                    <td>{{ $detail->nama_barang }}</td>
                                    <td class="text-center">{{ $detail->jumlah }}</td>
                                    <td class="text-end">Rp{{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp{{ number_format($detail->subtotal_item, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Ringkasan Pembayaran</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-8 text-end">Subtotal Barang:</div>
                        <div class="col-md-4 text-end">Rp{{ number_format($transaksi->subtotal_barang, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-8 text-end">Ongkos Kirim:</div>
                        <div class="col-md-4 text-end">Rp{{ number_format($transaksi->ongkir, 0, ',', '.') }}</div>
                    </div>
                    @if($transaksi->diskon_poin > 0)
                    <div class="row mb-2">
                        <div class="col-md-8 text-end">Diskon Poin ({{ number_format($transaksi->poin_ditebus, 0, ',', '.') }} poin):</div>
                        <div class="col-md-4 text-end text-danger">-Rp{{ number_format($transaksi->diskon_poin, 0, ',', '.') }}</div>
                    </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-md-8 text-end fw-bold">Total Pembayaran:</div>
                        <div class="col-md-4 text-end fw-bold fs-5 text-success">Rp{{ number_format($transaksi->total_pembayaran, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <small class="text-muted">
                                Anda mendapatkan <strong>{{ number_format($transaksi->poin_diperoleh, 0, ',', '.') }}</strong> poin dari transaksi ini.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pembayaran dan Status -->
        <div class="col-md-4">
            <!-- Informasi Rekening -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">Informasi Pembayaran</h4>
                </div>
                <div class="card-body">
                    <p class="fw-bold">Silakan transfer ke rekening berikut:</p>
                    <div class="alert alert-light border">
                        <p class="mb-1"><strong>Bank BCA</strong></p>
                        <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                        <p class="mb-0">Atas Nama: <strong>PT ReUseMart Indonesia</strong></p>
                    </div>
                    <p class="text-danger small">
                        <i class="fas fa-info-circle"></i> Harap transfer tepat sampai 3 digit terakhir untuk memudahkan verifikasi.
                    </p>
                </div>
            </div>

            <!-- Upload Bukti Pembayaran -->
            @if($transaksi->status_transaksi == 'menunggu_pembayaran')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Upload Bukti Pembayaran</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('transaksi.upload-bukti', $transaksi->id_transaksi) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/*" required>
                            <small class="form-text text-muted">Format: JPG, PNG, atau JPEG. Maks: 2MB</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload"></i> Upload Bukti Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            <!-- Bukti Pembayaran yang Diupload -->
            @if($transaksi->bukti_pembayaran_path)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Bukti Pembayaran</h4>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $transaksi->bukti_pembayaran_path) }}" alt="Bukti Pembayaran" class="img-fluid rounded mb-3" style="max-height: 300px;">
                    <p class="mb-0">
                        @if($transaksi->status_transaksi == 'menunggu_verifikasi')
                            <span class="badge bg-info">Menunggu Verifikasi Admin</span>
                        @elseif($transaksi->status_transaksi == 'disiapkan')
                            <span class="badge bg-success">Pembayaran Terverifikasi</span>
                        @endif
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($transaksi->status_transaksi == 'menunggu_pembayaran' && $countdown > 0)
<script>
    // Countdown timer
    let timeLeft = {{ $countdown }};
    const countdownElement = document.getElementById('countdown');
    
    const countdownTimer = setInterval(function() {
        timeLeft--;
        countdownElement.textContent = timeLeft;
        
        if (timeLeft <= 0) {
            clearInterval(countdownTimer);
            alert('Waktu pembayaran telah habis. Halaman akan dimuat ulang.');
            location.reload();
        }
    }, 1000);
</script>
@endif
@endsection
