@extends('layouts.owner')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clock mr-2"></i>
                            Laporan Barang yang Masa Penitipannya Sudah Habis
                        </h3>
                    </div>

                    <div class="card-body">
                        {{-- Tombol Download PDF --}}
                        <div class="row mb-4">
                            <div class="col-md-12 text-right">
                                <a href="{{ route('owner.laporanMasaPenitipanHabisPDF') }}" class="btn btn-danger">
                                    <i class="fas fa-download mr-1"></i>Download PDF
                                </a>
                            </div>
                        </div>

                        {{-- Info Alert --}}
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Informasi:</strong> Laporan ini menampilkan barang-barang yang masa penitipannya sudah
                            selesai (melewati tanggal selesai penitipan) dan belum diambil oleh penitip.
                            <ul class="mb-0 mt-2">
                                <li><strong>Status:</strong> Barang dengan status selain "sudah diambil penitip" dan "sudah
                                    didonasikan"</li>
                                <li><strong>Masa Habis:</strong> Tanggal selesai penitipan sudah terlewat dari hari ini</li>
                                <li><strong>Tindakan:</strong> Barang-barang ini perlu ditindaklanjuti (kontak penitip atau
                                    proses donasi)</li>
                            </ul>
                        </div>

                        {{-- Summary Cards --}}
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card bg-danger text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4>{{ number_format($totalBarang) }}</h4>
                                                <p class="mb-0">Total Barang Masa Habis</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-box fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h4>{{ number_format($totalPenitip) }}</h4>
                                                <p class="mb-0">Total Penitip Terlibat</p>
                                            </div>
                                            <div class="align-self-center">
                                                <i class="fas fa-users fa-2x"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tabel Laporan --}}
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="thead-dark">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th class="text-center">Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">ID Penitip</th>
                                        <th>Nama Penitip</th>
                                        <th class="text-center">Tanggal Masuk</th>
                                        <th class="text-center">Tanggal Akhir</th>
                                        <th class="text-center">Batas Ambil</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($penitipanHabis as $index => $data)
                                        @php
                                            $hariTerlambat = now()->diffInDays(
                                                \Carbon\Carbon::parse($data->tanggal_akhir),
                                            );
                                        @endphp
                                        <tr class="text-dark">
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-primary">{{ $data->kode_produk }}</span>
                                            </td>
                                            <td>{{ $data->nama_produk }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-info">{{ $data->id_penitip }}</span>
                                            </td>
                                            <td>{{ $data->nama_penitip }}</td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($data->tanggal_masuk)->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge badge-danger">
                                                    {{ \Carbon\Carbon::parse($data->tanggal_akhir)->format('d/m/Y') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                {{ \Carbon\Carbon::parse($data->batas_ambil)->format('d/m/Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center text-muted">
                                                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                                <br>Tidak ada barang yang masa penitipannya sudah habis
                                                <br><small>Semua barang masih dalam masa penitipan yang valid</small>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Rekomendasi Tindakan --}}
                        @if ($totalBarang > 0)
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning">
                                            <h5 class="card-title mb-0">
                                                <i class="fas fa-lightbulb mr-2"></i>
                                                Rekomendasi Tindakan
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-phone text-primary mr-2"></i>Kontak Penitip</h6>
                                                    <ul class="list-unstyled">
                                                        <li>• Hubungi penitip untuk konfirmasi pengambilan</li>
                                                        <li>• Berikan perpanjangan waktu jika diperlukan</li>
                                                        <li>• Informasikan konsekuensi jika tidak diambil</li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6">
                                                    <h6><i class="fas fa-heart text-danger mr-2"></i>Proses Donasi</h6>
                                                    <ul class="list-unstyled">
                                                        <li>• Barang yang tidak diambil > 7 hari setelah masa habis dapat
                                                            didonasikan</li>
                                                        <li>• Update status barang menjadi "sudah didonasikan"</li>
                                                        <li>• Dokumentasikan proses donasi</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
