@extends('layouts.owner')

@section('title', 'Profil Owner')

@section('content')
    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h4 class="mb-3 fw-semibold text-success">
            <i class="fas fa-user-circle me-2"></i> Profil Owner
        </h4>

        <div class="row g-3">
            <div class="col-sm-6">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Nama</small>
                    <div class="fw-semibold text-dark">{{ $owner->nama_pegawai }}</div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Email</small>
                    <div class="fw-semibold text-dark">{{ $owner->email_pegawai }}</div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">No. Telepon</small>
                    <div class="fw-semibold text-dark">{{ $owner->nomor_telepon_pegawai }}</div>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="border rounded p-3 bg-light">
                    <small class="text-muted">Role</small>
                    <div class="fw-semibold text-dark">Owner</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded shadow-sm p-4 mb-4">
        <h4 class="mb-3">💰 Total Komisi</h4>
        <p class="fs-4 fw-bold text-success">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</p>
    </div>

    <div class="row g-4">
        <!-- Komisi 20% -->
        <div class="col-md-6">
            <div class="bg-white rounded shadow-sm p-4 h-100">
                <h5 class="mb-3 text-primary">🔹 Komisi 20% (Tanpa Perpanjangan)</h5>
                <p class="text-muted mb-2">Total: <strong class="text-success">Rp
                        {{ number_format($totalKomisi20, 0, ',', '.') }}</strong></p>

                @forelse ($komisi20 as $item)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div class="fw-medium">{{ $item['nama'] }}</div>
                            <small class="text-muted">20% dari Rp {{ number_format($item['harga'], 0, ',', '.') }}</small>
                        </div>
                        <div class="text-success fw-semibold">+ Rp {{ number_format($item['komisi'], 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada barang.</p>
                @endforelse
            </div>
        </div>

        <!-- Komisi 30% -->
        <div class="col-md-6">
            <div class="bg-white rounded shadow-sm p-4 h-100">
                <h5 class="mb-3 text-warning">🔸 Komisi 30% (Perpanjangan Ya)</h5>
                <p class="text-muted mb-2">Total: <strong class="text-success">Rp
                        {{ number_format($totalKomisi30, 0, ',', '.') }}</strong></p>

                @forelse ($komisi30 as $item)
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <div>
                            <div class="fw-medium">{{ $item['nama'] }}</div>
                            <small class="text-muted">30% dari Rp {{ number_format($item['harga'], 0, ',', '.') }}</small>
                        </div>
                        <div class="text-success fw-semibold">+ Rp {{ number_format($item['komisi'], 0, ',', '.') }}</div>
                    </div>
                @empty
                    <p class="text-muted">Tidak ada barang.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
