<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Riwayat Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .rating-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        .rating-stars {
            color: #ffc107;
        }
        .transaction-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: box-shadow 0.3s ease;
        }
        .transaction-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .transaction-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 8px 8px 0 0;
        }
        .transaction-body {
            padding: 15px;
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
        }
        .btn-rating {
            background: linear-gradient(45deg, #28a745, #20c997);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-rating:hover {
            background: linear-gradient(45deg, #218838, #1ea080);
            color: white;
            transform: translateY(-1px);
        }
        .rating-form {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 12px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="text-primary">
                    <i class="fas fa-history me-2"></i>Riwayat Transaksi Pembeli
                </h3>
                <div class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i>
                    {{ date('d F Y') }}
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(isset($transaksis) && $transaksis->count() > 0)
                @foreach ($transaksis as $transaksi)
                    <div class="transaction-card">
                        <div class="transaction-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-1">
                                        <a href="#" class="text-decoration-none transaksi-link"
                                           data-id="{{ $transaksi->id_transaksi }}"
                                           data-nama="{{ $transaksi->nama_barang }}"
                                           data-pesan="{{ $transaksi->tanggal_pemesanan }}"
                                           data-lunas="{{ $transaksi->tanggal_pelunasan }}"
                                           data-pengiriman="{{ $transaksi->jenis_pengiriman }}"
                                           data-tgl_kirim="{{ $transaksi->tanggal_pengiriman }}"
                                           data-tgl_ambil="{{ $transaksi->tanggal_pengambilan }}">
                                            <i class="fas fa-receipt me-2"></i>ID: {{ $transaksi->id_transaksi }}
                                        </a>
                                    </h5>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_pemesanan)->format('d M Y, H:i') }}
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    @if($transaksi->status_transaksi)
                                        @php
                                            $statusClass = 'secondary';
                                            switch(strtolower($transaksi->status_transaksi)) {
                                                case 'selesai':
                                                case 'completed':
                                                    $statusClass = 'success';
                                                    break;
                                                case 'pending':
                                                case 'menunggu':
                                                    $statusClass = 'warning';
                                                    break;
                                                case 'dibatalkan':
                                                case 'cancelled':
                                                    $statusClass = 'danger';
                                                    break;
                                                case 'diproses':
                                                case 'processing':
                                                    $statusClass = 'info';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge badge-{{ $statusClass }} status-badge">
                                            {{ ucfirst($transaksi->status_transaksi) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="transaction-body">
                            <div class="row">
                                @if(isset($transaksi->barangTitipan) && $transaksi->barangTitipan->gambar_barang)
                                    <div class="col-md-3">
                                        <img src="{{ asset('storage/' . $transaksi->barangTitipan->gambar_barang) }}"
                                            class="img-fluid rounded" alt="Gambar Barang" style="max-height: 150px; object-fit: cover;">
                                    </div>
                                @endif
                                <div class="col-md-{{ isset($transaksi->barangTitipan) && $transaksi->barangTitipan->gambar_barang ? '9' : '12' }}">
                                    <h5 class="text-dark mb-2">
                                        {{ $transaksi->nama_barang ?? $transaksi->barangTitipan->nama_barang_titipan ?? 'Nama barang tidak tersedia' }}
                                    </h5>
                                    
                                    @if(isset($transaksi->barangTitipan) && $transaksi->barangTitipan->harga_barang)
                                        <p class="mb-2">
                                            <strong class="text-success">
                                                <i class="fas fa-tag me-1"></i>
                                                Rp {{ number_format($transaksi->barangTitipan->harga_barang, 0, ',', '.') }}
                                            </strong>
                                        </p>
                                    @endif

                                    <div class="row text-sm">
                                        @if($transaksi->jenis_pengiriman)
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <i class="fas fa-shipping-fast me-1 text-info"></i>
                                                    <strong>Pengiriman:</strong> {{ $transaksi->jenis_pengiriman }}
                                                </p>
                                            </div>
                                        @endif
                                        @if($transaksi->tanggal_pelunasan)
                                            <div class="col-md-6">
                                                <p class="mb-1">
                                                    <i class="fas fa-check-circle me-1 text-success"></i>
                                                    <strong>Pelunasan:</strong> {{ \Carbon\Carbon::parse($transaksi->tanggal_pelunasan)->format('d M Y') }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Rating Section --}}
                                    @if(isset($transaksi->barangTitipan) && in_array(strtolower($transaksi->status_transaksi ?? ''), ['selesai', 'completed']))
                                        <div class="rating-section mt-3">
                                            <h6 class="mb-3">
                                                <i class="fas fa-star text-warning me-2"></i>Rating Produk
                                            </h6>
                                            
                                            {{-- Check if user already rated this item --}}
                                            @php
                                                $userRating = null;
                                                $userId = session('user_id') ?? auth()->id();
                                                $barangId = $transaksi->id_barang ?? $transaksi->barangTitipan->id_barang ?? null;
                                                
                                                // Check if Rating model exists and get user rating
                                                if (class_exists('\App\Models\Rating') && $userId && $barangId) {
                                                    $userRating = \App\Models\Rating::where('id_barang', $barangId)
                                                        ->where('id_pembeli', $userId)
                                                        ->first();
                                                }
                                            @endphp

                                            @if($userRating)
                                                {{-- Tampilkan rating yang sudah diberikan --}}
                                                <div class="alert alert-success border-left-success">
                                                    <div class="d-flex align-items-center">
                                                        <div class="mr-3">
                                                            <i class="fas fa-check-circle fa-2x text-success"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1">
                                                                <i class="fas fa-star text-warning me-1"></i>
                                                                Anda telah memberikan rating untuk produk ini
                                                            </h6>
                                                            <div class="rating-display mb-2">
                                                                <span class="rating-stars">
                                                                    @for($i = 1; $i <= 5; $i++)
                                                                        <i class="fas fa-star{{ $i <= $userRating->rating ? ' text-warning' : ' text-muted' }}"></i>
                                                                    @endfor
                                                                </span>
                                                                <span class="ml-2 font-weight-bold">{{ $userRating->rating }}/5</span>
                                                            </div>
                                                            @if($userRating->komentar)
                                                                <div class="mt-2">
                                                                    <small class="text-muted"><strong>Komentar Anda:</strong></small>
                                                                    <div class="bg-light p-2 rounded mt-1">
                                                                        <em>"{{ $userRating->komentar }}"</em>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <small class="text-muted">
                                                                <i class="fas fa-calendar me-1"></i>
                                                                Diberikan pada: {{ \Carbon\Carbon::parse($userRating->created_at)->format('d M Y, H:i') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                {{-- Pesan bahwa tidak bisa rating lagi --}}
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Informasi:</strong> Anda hanya dapat memberikan satu rating per transaksi. 
                                                    Rating yang telah diberikan tidak dapat diubah.
                                                </div>
                                            @else
                                                {{-- Form untuk memberikan rating baru --}}
                                                <div class="alert alert-warning">
                                                    <i class="fas fa-star me-2"></i>
                                                    <strong>Berikan Rating:</strong> Bagikan pengalaman Anda dengan produk ini untuk membantu pembeli lain.
                                                </div>
                                                
                                                <form method="POST" action="{{ route('rating.store') }}" class="rating-form" id="ratingForm_{{ $transaksi->id_transaksi }}">
                                                    @csrf
                                                    <input type="hidden" name="id_barang" value="{{ $barangId }}">
                                                    <input type="hidden" name="id_pembeli" value="{{ $userId }}">
                                                    <input type="hidden" name="id_transaksi" value="{{ $transaksi->id_transaksi }}">

                                                    <div class="row align-items-center mb-3">
                                                        <div class="col-auto">
                                                            <label for="rating_{{ $transaksi->id_transaksi }}" class="form-label mb-0 font-weight-bold">
                                                                Rating: <span class="text-danger">*</span>
                                                            </label>
                                                        </div>
                                                        <div class="col-auto">
                                                            <select name="rating" id="rating_{{ $transaksi->id_transaksi }}" 
                                                                    class="form-control form-control-sm rating-select" style="width: auto;" required>
                                                                <option value="">Pilih Rating</option>
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <option value="{{ $i }}">
                                                                        {{ $i }} ⭐ 
                                                                        @if($i == 1) Sangat Buruk
                                                                        @elseif($i == 2) Buruk
                                                                        @elseif($i == 3) Cukup
                                                                        @elseif($i == 4) Baik
                                                                        @elseif($i == 5) Sangat Baik
                                                                        @endif
                                                                    </option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-auto">
                                                            <button type="submit" class="btn btn-rating btn-sm" disabled id="submitRating_{{ $transaksi->id_transaksi }}">
                                                                <i class="fas fa-paper-plane me-1"></i> Kirim Rating
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="rating-preview mb-2" id="ratingPreview_{{ $transaksi->id_transaksi }}" style="display: none;">
                                                        <small class="text-muted">Preview: </small>
                                                        <span class="preview-stars"></span>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="komentar_{{ $transaksi->id_transaksi }}" class="form-label font-weight-bold">
                                                            Komentar (Opsional):
                                                        </label>
                                                        <textarea name="komentar" id="komentar_{{ $transaksi->id_transaksi }}" 
                                                                class="form-control form-control-sm" rows="3" 
                                                                placeholder="Bagikan pengalaman Anda dengan produk ini... (maksimal 500 karakter)"
                                                                maxlength="500"></textarea>
                                                        <small class="text-muted">
                                                            <span id="charCount_{{ $transaksi->id_transaksi }}">0</span>/500 karakter
                                                        </small>
                                                    </div>
                                                    
                                                    <div class="text-right">
                                                        <small class="text-muted">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                            <strong>Perhatian:</strong> Rating yang sudah dikirim tidak dapat diubah.
                                                        </small>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination if available --}}
                @if(method_exists($transaksis, 'links'))
                    <div class="d-flex justify-content-center mt-4">
                        {{ $transaksis->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-shopping-cart fa-4x text-muted"></i>
                    </div>
                    <h4 class="text-muted mb-3">Belum Ada Transaksi</h4>
                    <p class="text-muted mb-4">Anda belum memiliki riwayat transaksi pembelian.</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Mulai Berbelanja
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div class="modal fade" id="transaksiModal" tabindex="-1" role="dialog" aria-labelledby="transaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="transaksiModalLabel">
                    <i class="fas fa-receipt me-2"></i>Detail Transaksi
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informasi Transaksi
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>ID Transaksi:</strong></td>
                                <td><span id="modal-id" class="badge badge-secondary"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Nama Barang:</strong></td>
                                <td><span id="modal-nama"></span></td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pemesanan:</strong></td>
                                <td>
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    <span id="modal-pesan"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pelunasan:</strong></td>
                                <td>
                                    <i class="fas fa-credit-card me-1 text-success"></i>
                                    <span id="modal-lunas"></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">
                            <i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman
                        </h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Jenis Pengiriman:</strong></td>
                                <td>
                                    <i class="fas fa-truck me-1 text-info"></i>
                                    <span id="modal-pengiriman"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengiriman:</strong></td>
                                <td>
                                    <i class="fas fa-calendar-check me-1 text-warning"></i>
                                    <span id="modal-tgl-kirim"></span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengambilan:</strong></td>
                                <td>
                                    <i class="fas fa-hand-holding me-1 text-success"></i>
                                    <span id="modal-tgl-ambil"></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Rating Success -->
<div class="modal fade" id="ratingSuccessModal" tabindex="-1" role="dialog" aria-labelledby="ratingSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-3x text-success"></i>
                </div>
                <h5 class="mb-2">Rating Berhasil Dikirim!</h5>
                <p class="text-muted mb-3">Terima kasih atas feedback Anda.</p>
                <button type="button" class="btn btn-success btn-sm" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Handle rating select change
    document.querySelectorAll('.rating-select').forEach(select => {
        const transaksiId = select.id.split('_')[1];
        const submitBtn = document.getElementById(`submitRating_${transaksiId}`);
        const preview = document.getElementById(`ratingPreview_${transaksiId}`);
        
        select.addEventListener('change', function() {
            const rating = parseInt(this.value);
            
            if (rating) {
                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('btn-secondary');
                submitBtn.classList.add('btn-rating');
                
                // Show preview
                const stars = Array.from({length: 5}, (_, i) => 
                    `<i class="fas fa-star${i < rating ? ' text-warning' : ' text-muted'}"></i>`
                ).join(' ');
                
                preview.querySelector('.preview-stars').innerHTML = stars;
                preview.style.display = 'block';
            } else {
                // Disable submit button
                submitBtn.disabled = true;
                submitBtn.classList.remove('btn-rating');
                submitBtn.classList.add('btn-secondary');
                
                // Hide preview
                preview.style.display = 'none';
            }
        });
    });

    // Handle character count for comments
    document.querySelectorAll('textarea[name="komentar"]').forEach(textarea => {
        const transaksiId = textarea.id.split('_')[1];
        const charCount = document.getElementById(`charCount_${transaksiId}`);
        
        textarea.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count;
            
            if (count > 450) {
                charCount.style.color = '#dc3545';
            } else if (count > 400) {
                charCount.style.color = '#ffc107';
            } else {
                charCount.style.color = '#6c757d';
            }
        });
    });

    // Handle rating form submission with confirmation
    document.querySelectorAll('form[id^="ratingForm_"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const rating = this.querySelector('select[name="rating"]').value;
            const komentar = this.querySelector('textarea[name="komentar"]').value;
            
            // Confirmation dialog
            const confirmMessage = `Apakah Anda yakin ingin memberikan rating ${rating} bintang?\n\n` +
                                 `Rating yang sudah dikirim tidak dapat diubah lagi.\n\n` +
                                 `Klik OK untuk melanjutkan atau Cancel untuk membatalkan.`;
            
            if (!confirm(confirmMessage)) {
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable form and show loading
            this.querySelectorAll('input, select, textarea, button').forEach(el => {
                el.disabled = true;
            });
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengirim Rating...';
            
            // Submit rating
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Replace form with success message
                    const ratingSection = this.closest('.rating-section');
                    const stars = Array.from({length: 5}, (_, i) => 
                        `<i class="fas fa-star${i < rating ? ' text-warning' : ' text-muted'}"></i>`
                    ).join(' ');
                    
                    ratingSection.innerHTML = `
                        <div class="alert alert-success border-left-success">
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <i class="fas fa-star text-warning me-1"></i>
                                        Rating berhasil dikirim!
                                    </h6>
                                    <div class="rating-display mb-2">
                                        <span class="rating-stars">${stars}</span>
                                        <span class="ml-2 font-weight-bold">${rating}/5</span>
                                    </div>
                                    ${komentar ? `
                                        <div class="mt-2">
                                            <small class="text-muted"><strong>Komentar Anda:</strong></small>
                                            <div class="bg-light p-2 rounded mt-1">
                                                <em>"${komentar}"</em>
                                            </div>
                                        </div>
                                    ` : ''}
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        Diberikan pada: ${new Date().toLocaleDateString('id-ID')}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Terima kasih!</strong> Rating Anda telah tersimpan dan akan membantu pembeli lain.
                        </div>
                    `;
                    
                    // Show success modal
                    $('#ratingSuccessModal').modal('show');
                    
                    // Scroll to rating section
                    ratingSection.scrollIntoView({ behavior: 'smooth' });
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat mengirim rating');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim rating: ' + error.message + '\nSilakan coba lagi.');
                
                // Re-enable form
                this.querySelectorAll('input, select, textarea, button').forEach(el => {
                    el.disabled = false;
                });
                submitBtn.innerHTML = originalText;
            });
        });
    });

    // Prevent double submission
    let isSubmitting = false;
    document.querySelectorAll('form[id^="ratingForm_"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }
            isSubmitting = true;
            
            // Reset flag after 5 seconds
            setTimeout(() => {
                isSubmitting = false;
            }, 5000);
        });
    });
});
</script>

<style>
/* Additional animations and improvements */
.transaction-card {
    transition: all 0.3s ease;
}

.transaction-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.rating-stars i {
    transition: all 0.2s ease;
}

.rating-stars:hover i {
    transform: scale(1.2);
}

.btn-rating {
    position: relative;
    overflow: hidden;
}

.btn-rating::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-rating:hover::before {
    left: 100%;
}

.rating-form {
    transition: all 0.3s ease;
}

.rating-form:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.modal-content {
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    border-bottom: 2px solid rgba(255,255,255,0.2);
}

.badge {
    font-weight: 500;
    letter-spacing: 0.5px;
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-spin {
    animation: spin 1s linear infinite;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .transaction-header .col-md-6:last-child {
        text-align: left !important;
        margin-top: 10px;
    }
    
    .rating-form .row {
        flex-direction: column;
    }
    
    .rating-form .col-auto {
        margin-bottom: 10px;
    }
    
    .modal-dialog {
        margin: 10px;
    }
}

/* Print styles */
@media print {
    .btn, .modal, .rating-section {
        display: none !important;
    }
    
    .transaction-card {
        break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
}
</style>
</body>
</html>