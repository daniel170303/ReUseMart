@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="text-center mb-4 font-weight-bold">🛒 Keranjang Belanja Anda</h2>

    {{-- Area untuk menampilkan pesan sukses atau error dari session --}}
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
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h5 class="alert-heading">Oops! Ada beberapa kesalahan:</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Countdown Timer Alert --}}
    <div id="countdownAlert" class="alert alert-warning alert-dismissible fade show" role="alert" style="display: none;">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock me-2"></i>
            <div>
                <strong>⏰ Waktu Pembayaran Terbatas!</strong><br>
                <span>Selesaikan pembayaran dalam: </span>
                <span id="countdownTimer" class="fw-bold text-danger fs-5">01:00</span>
                <br><small class="text-muted">Jika waktu habis, transaksi akan dibatalkan otomatis dan poin akan dikembalikan.</small>
            </div>
        </div>
    </div>

    @if($cart && count($cart) > 0)
        <div class="table-responsive shadow-sm rounded mb-4">
            <table class="table table-striped table-hover align-middle">
                <thead class="thead-dark">
                    <tr>
                        <th>📦 Nama Barang</th>
                        <th>🔢 Jumlah</th>
                        <th>💰 Harga</th>
                        <th>💵 Subtotal</th>
                        <th>⚙ Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $item)
                        @php 
                            $subtotal = $item['harga'] * $item['jumlah'];
                            $total += $subtotal;
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $item['nama'] }}</td>
                            <td>{{ $item['jumlah'] }}</td>
                            <td class="text-success">Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini dari keranjang?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-info">
                        <th colspan="3" class="text-end">Total Belanja:</th>
                        <th class="text-success fs-5">Rp{{ number_format($total, 0, ',', '.') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Bagian Checkout --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">🚚 Rincian Checkout & Pengiriman</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                    @csrf
                    {{-- Input tersembunyi untuk data yang akan dikirim ke server --}}
                    <input type="hidden" name="subtotal_barang" id="subtotalBarangInput" value="{{ $total }}">
                    <input type="hidden" name="ongkir" id="ongkirInput" value="0">
                    <input type="hidden" name="poin_ditebus" id="poinDitebusInput" value="0">
                    <input type="hidden" name="total_pembayaran" id="totalPembayaranInput" value="{{ $total }}">
                    
                    {{-- Bagian Metode Pengiriman --}}
                    <div class="mb-4">
                        <h5 class="mb-3">Pilih Metode Pengiriman:</h5>
                        <div class="row">
                            {{-- Opsi Kurir --}}
                            <div class="col-md-6 mb-3">
                                <div class="card border-2" id="kurirCard">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pengiriman" 
                                                   id="kurir" value="kurir" onchange="pilihMetodePengiriman()">
                                            <label class="form-check-label w-100" for="kurir">
                                                <div class="mt-2">
                                                    <i class="fas fa-truck fs-1 text-primary mb-2"></i>
                                                    <h5>🚛 Dikirim Kurir</h5>
                                                    <p class="text-muted">Pengiriman khusus wilayah Yogyakarta.</p>
                                                    <p class="text-success fw-bold" id="kurirFeeText">Biaya: Akan dihitung</p>
                                                    <small class="text-muted">Gratis ongkir untuk pembelian min. Rp 1.500.000.</small><br/>
                                                    <small class="text-muted">Estimasi: 1-2 hari kerja.</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Opsi Ambil Sendiri --}}
                            <div class="col-md-6 mb-3">
                                <div class="card border-2" id="ambilCard">
                                    <div class="card-body text-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="metode_pengiriman" 
                                                   id="ambil_sendiri" value="ambil_sendiri" onchange="pilihMetodePengiriman()">
                                            <label class="form-check-label w-100" for="ambil_sendiri">
                                                <div class="mt-2">
                                                    <i class="fas fa-store fs-1 text-success mb-2"></i>
                                                    <h5>🏪 Ambil Sendiri</h5>
                                                    <p class="text-muted">Ambil langsung di gudang kami.</p>
                                                    <p class="text-success fw-bold">Biaya: GRATIS</p>
                                                    <small class="text-muted">Jam Operasional: Senin-Sabtu, 08:00-17:00 WIB.</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Form Alamat (untuk Kurir) --}}
                    <div id="alamatSection" style="display: none;" class="mb-4 p-3 border rounded bg-light shadow-sm">
                        <h5 class="mb-3">📍 Alamat Pengiriman (Yogyakarta):</h5>
                        @php
                            $user = Auth::user();
                            $namaPembeli = '';
                            $noTeleponPembeli = '';
                            $alamatPembeli = '';
                            if ($user instanceof \App\Models\Pembeli) {
                                $namaPembeli = $user->nama_pembeli;
                                $noTeleponPembeli = $user->no_telepon_pembeli;
                                $alamatPembeli = $user->alamat_pembeli;
                            }
                        @endphp
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_penerima" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_penerima" name="nama_penerima" 
                                       placeholder="Masukkan nama lengkap penerima" value="{{ old('nama_penerima', $namaPembeli) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="no_telepon" class="form-label">No. Telepon Penerima <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="no_telepon" name="no_telepon" 
                                       placeholder="Contoh: 081234567890" value="{{ old('no_telepon', $noTeleponPembeli) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="alamat_lengkap" class="form-label">Alamat Lengkap Pengiriman <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" 
                                      placeholder="Contoh: Jl. Malioboro No. 10, RT 01 RW 02, Kel. Suryatmajan, Kec. Danurejan">{{ old('alamat_lengkap', $alamatPembeli) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="kota" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="kota" name="kota" 
                                       value="Yogyakarta" readonly>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                <input type="text" class="form-control" id="kode_pos" name="kode_pos" 
                                       placeholder="Contoh: 55213" value="{{ old('kode_pos') }}">
                            </div>
                        </div>
                         <small class="form-text text-muted">Pastikan alamat pengiriman Anda di Yogyakarta sudah benar.</small>
                    </div>

                    {{-- Bagian Informasi Gudang (untuk Ambil Sendiri) --}}
                    <div id="gudangSection" style="display: none;" class="mb-4">
                        <h5 class="mb-3">🏢 Informasi Pengambilan di Gudang:</h5>
                        <div class="alert alert-info">
                            <h6><i class="fas fa-map-marker-alt"></i> Alamat Gudang:</h6>
                            <p class="mb-2">Jl. ReUseMart No. 1, Condongcatur, Depok, Sleman, Yogyakarta 55283</p>
                            <h6><i class="fas fa-clock"></i> Jam Operasional Pengambilan:</h6>
                            <p class="mb-2">Senin - Sabtu: 08:00 - 17:00 WIB</p>
                            <p class="mb-0"><strong>Penting:</strong> Harap tunjukkan bukti pemesanan (email atau nomor transaksi) saat pengambilan barang.</p>
                        </div>
                    </div>
                    
                    {{-- Bagian Penukaran Poin --}}
                    @php
                        $poinDimiliki = 0;
                        if ($user instanceof \App\Models\Pembeli && $user->rewardPembeli) {
                            $poinDimiliki = $user->rewardPembeli->jumlah_poin_pembeli;
                        }
                    @endphp
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">🌟 Tukarkan Poin Rezeki Anda!</h5>
                        </div>
                        <div class="card-body">
                            <p>Anda memiliki: <strong id="poinSaatIniDisplay" data-poin="{{ $poinDimiliki }}">{{ number_format($poinDimiliki, 0, ',', '.') }}</strong> poin.</p>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" id="poinUntukDitebusDisplay" placeholder="Jumlah poin yang ingin ditukar" min="0" max="{{ $poinDimiliki }}" oninput="updatePoinDitebusInput()">
                                <button class="btn btn-outline-primary" type="button" id="applyPointsBtn" onclick="terapkanPoin()">Terapkan Poin</button>
                            </div>
                            <small class="form-text text-muted">Nilai tukar: 1 Poin = Rp {{ number_format(100, 0, ',', '.') }}.</small>
                                                        <div id="poinErrorMessage" class="text-danger mt-2" style="display: none;"></div>
                            <div id="sisaPoinInfo" class="alert alert-success mt-2" style="display: none;">
                                Sisa poin Anda setelah penukaran: <strong id="sisaPoinValue">0</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Bagian Ringkasan Pembayaran --}}
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="mb-3">💳 Ringkasan Pembayaran:</h5>
                            <div class="row mb-2">
                                <div class="col-7">Subtotal Barang:</div>
                                <div class="col-5 text-end" id="subtotalBarangText">Rp{{ number_format($total, 0, ',', '.') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-7">Ongkos Kirim:</div>
                                <div class="col-5 text-end" id="ongkirText">Rp 0</div>
                            </div>
                            <div class="row mb-2" id="poinDiscountRow" style="display: none;">
                                <div class="col-7">Diskon dari Poin:</div>
                                <div class="col-5 text-end text-danger" id="poinDiscountText">- Rp 0</div>
                            </div>
                            <hr>
                            <div class="row fw-bold mb-2">  
                                <div class="col-7 fs-5">Total Pembayaran:</div>
                                <div class="col-5 text-end text-success fs-4" id="totalPembayaranText">Rp{{ number_format($total, 0, ',', '.') }}</div>
                            </div>
                            <hr>
                             <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-muted">Anda akan mendapatkan <strong id="earnedPointsText">0</strong> poin dari transaksi ini.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tombol Lanjutkan ke Pembayaran --}}
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg" id="checkoutBtn" disabled>
                            <i class="fas fa-shield-alt"></i> Lanjutkan ke Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @else
        <div class="alert alert-warning text-center shadow-sm" role="alert">
            <h4>Keranjang belanja Anda masih kosong.</h4>
            <p>Yuk, temukan barang-barang menarik lainnya!</p>
            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
        </div>
    @endif
</div>

<script>
// Konstanta nilai tukar poin
const NILAI_TUKAR_POIN = 100; // 1 poin = Rp 100

// Mengambil subtotal barang dari input hidden
const subtotalBarang = parseFloat(document.getElementById('subtotalBarangInput').value) || 0;

// Variabel global untuk menyimpan status checkout saat ini
let ongkosKirimSaatIni = 0;
let poinDitebusUntukServer = 0;
let diskonDariPoinSaatIni = 0;

// Variabel untuk countdown timer
let countdownInterval = null;
let countdownStarted = false;
let timeRemaining = 60; // 1 menit dalam detik

// Fungsi untuk memformat angka menjadi format mata uang Rupiah
function formatRupiah(angka) {
    return 'Rp' + angka.toLocaleString('id-ID');
}

// Fungsi untuk menghitung poin yang akan diperoleh
function hitungPoinDiperoleh(totalBelanjaBarang) {
    if (totalBelanjaBarang <= 0) return 0;
    let poinDasar = Math.floor(totalBelanjaBarang / 10000); // 1 poin setiap Rp10.000
    let poinBonus = 0;
    if (totalBelanjaBarang > 500000) { // Bonus 20% jika belanja > Rp500.000
        poinBonus = Math.floor(poinDasar * 0.20);
    }
    return poinDasar + poinBonus;
}

// Fungsi untuk memperbarui ringkasan pembayaran di UI
function perbaruiRingkasanPembayaran() {
    const totalSebelumDiskon = subtotalBarang + ongkosKirimSaatIni;
    const totalSetelahDiskon = totalSebelumDiskon - diskonDariPoinSaatIni;

    document.getElementById('subtotalBarangText').textContent = formatRupiah(subtotalBarang);
    document.getElementById('ongkirText').textContent = formatRupiah(ongkosKirimSaatIni);
    document.getElementById('ongkirInput').value = ongkosKirimSaatIni;

    const barisDiskonPoin = document.getElementById('poinDiscountRow');
    const teksDiskonPoin = document.getElementById('poinDiscountText');
    if (diskonDariPoinSaatIni > 0) {
        teksDiskonPoin.textContent = '- ' + formatRupiah(diskonDariPoinSaatIni);
        barisDiskonPoin.style.display = 'flex';
    } else {
        barisDiskonPoin.style.display = 'none';
    }

    document.getElementById('poinDitebusInput').value = poinDitebusUntukServer;
    document.getElementById('totalPembayaranText').textContent = formatRupiah(totalSetelahDiskon > 0 ? totalSetelahDiskon : 0);
    document.getElementById('totalPembayaranInput').value = totalSetelahDiskon > 0 ? totalSetelahDiskon : 0;

    const poinDiperoleh = hitungPoinDiperoleh(subtotalBarang);
    document.getElementById('earnedPointsText').textContent = poinDiperoleh.toLocaleString('id-ID');

    // Update status tombol checkout
    const tombolCheckout = document.getElementById('checkoutBtn');
    const radioKurir = document.getElementById('kurir');
    const radioAmbilSendiri = document.getElementById('ambil_sendiri');
    
    console.log('Updating checkout button:', {
        kurirChecked: radioKurir ? radioKurir.checked : false,
        ambilChecked: radioAmbilSendiri ? radioAmbilSendiri.checked : false
    });
    
    if (tombolCheckout && radioKurir && radioAmbilSendiri) {
        tombolCheckout.disabled = !(radioKurir.checked || radioAmbilSendiri.checked);
        console.log('Tombol checkout disabled:', tombolCheckout.disabled);
    }
}

// Fungsi untuk menangani perubahan pilihan metode pengiriman
function pilihMetodePengiriman() {
    console.log('pilihMetodePengiriman() dipanggil');
    
    const radioKurir = document.getElementById('kurir');
    const radioAmbilSendiri = document.getElementById('ambil_sendiri');
    const bagianAlamat = document.getElementById('alamatSection');
    const bagianInfoGudang = document.getElementById('gudangSection');
    const kartuKurir = document.getElementById('kurirCard');
    const kartuAmbil = document.getElementById('ambilCard');
    const teksBiayaKurir = document.getElementById('kurirFeeText');

    // Reset styling
    if (kartuKurir) kartuKurir.classList.remove('border-primary', 'border-success', 'shadow');
    if (kartuAmbil) kartuAmbil.classList.remove('border-primary', 'border-success', 'shadow');

    if (radioKurir && radioKurir.checked) {
        console.log('Kurir dipilih');
        if (bagianAlamat) bagianAlamat.style.display = 'block';
        if (bagianInfoGudang) bagianInfoGudang.style.display = 'none';
        if (kartuKurir) kartuKurir.classList.add('border-primary', 'shadow');

        ongkosKirimSaatIni = (subtotalBarang >= 1500000) ? 0 : 100000;
        if (teksBiayaKurir) {
            teksBiayaKurir.innerHTML = (subtotalBarang >= 1500000) ?
                'Biaya: <span class="text-success fw-bold">GRATIS</span>' : 
                'Biaya: ' + formatRupiah(ongkosKirimSaatIni);
        }

    } else if (radioAmbilSendiri && radioAmbilSendiri.checked) {
        console.log('Ambil sendiri dipilih');
        if (bagianAlamat) bagianAlamat.style.display = 'none';
        if (bagianInfoGudang) bagianInfoGudang.style.display = 'block';
        if (kartuAmbil) kartuAmbil.classList.add('border-success', 'shadow');
        ongkosKirimSaatIni = 0;
    } else {
        console.log('Tidak ada yang dipilih');
        ongkosKirimSaatIni = 0;
        if (bagianAlamat) bagianAlamat.style.display = 'none';
        if (bagianInfoGudang) bagianInfoGudang.style.display = 'none';
    }
    
    perbaruiRingkasanPembayaran();
}

// Fungsi untuk menerapkan poin yang ditukar
function terapkanPoin() {
    const elPoinSaatIni = document.getElementById('poinSaatIniDisplay');
    const elInputPoinDisplay = document.getElementById('poinUntukDitebusDisplay');
    const elPesanErrorPoin = document.getElementById('poinErrorMessage');
    const elSisaPoinInfo = document.getElementById('sisaPoinInfo');
    const elSisaPoinValue = document.getElementById('sisaPoinValue');

    if (!elPoinSaatIni || !elInputPoinDisplay) return;

    const poinDimiliki = parseInt(elPoinSaatIni.dataset.poin, 10);
    let poinInginDitebus = parseInt(elInputPoinDisplay.value, 10);

    elPesanErrorPoin.style.display = 'none';
    elPesanErrorPoin.textContent = '';
    elSisaPoinInfo.style.display = 'none';

    if (isNaN(poinInginDitebus) || poinInginDitebus < 0) {
        poinInginDitebus = 0;
        elInputPoinDisplay.value = 0;
    }

    if (poinInginDitebus > poinDimiliki) {
        elPesanErrorPoin.textContent = 'Jumlah poin yang ingin Anda tukar melebihi poin yang Anda miliki.';
        elPesanErrorPoin.style.display = 'block';
        poinDitebusUntukServer = 0;
        diskonDariPoinSaatIni = 0;
    } else {
        poinDitebusUntukServer = poinInginDitebus;
        diskonDariPoinSaatIni = poinDitebusUntukServer * NILAI_TUKAR_POIN;

        const sisaPoin = poinDimiliki - poinInginDitebus;
        elSisaPoinValue.textContent = sisaPoin.toLocaleString('id-ID');
        elSisaPoinInfo.style.display = 'block';

        if (!countdownStarted) {
            startCountdown();
        }
    }
    perbaruiRingkasanPembayaran();
}

// Event listener utama
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Inisialisasi
    pilihMetodePengiriman();
    perbaruiRingkasanPembayaran();

    // Attach event listeners untuk radio buttons
    const radioKurir = document.getElementById('kurir');
    const radioAmbilSendiri = document.getElementById('ambil_sendiri');
    
    if (radioKurir) {
        radioKurir.addEventListener('change', function() {
            console.log('Event: Kurir radio changed');
            pilihMetodePengiriman();
        });
        console.log('Event listener attached to kurir radio');
    } else {
        console.error('Radio kurir tidak ditemukan');
    }
    
    if (radioAmbilSendiri) {
        radioAmbilSendiri.addEventListener('change', function() {
            console.log('Event: Ambil sendiri radio changed');
            pilihMetodePengiriman();
        });
        console.log('Event listener attached to ambil sendiri radio');
    } else {
        console.error('Radio ambil sendiri tidak ditemukan');
    }

    // Event listener untuk tombol terapkan poin
    const tombolTerapkanPoin = document.getElementById('applyPointsBtn');
    if (tombolTerapkanPoin) {
        tombolTerapkanPoin.addEventListener('click', terapkanPoin);
    }

    // Event listener untuk input poin
    const inputPoinDisplay = document.getElementById('poinUntukDitebusDisplay');
    if (inputPoinDisplay) {
        inputPoinDisplay.addEventListener('input', function() {
            let poinInginDitebus = parseInt(this.value, 10);
            if (isNaN(poinInginDitebus) || poinInginDitebus < 0) {
                poinInginDitebus = 0;
            }
            poinDitebusUntukServer = poinInginDitebus;
            document.getElementById('poinDitebusInput').value = poinDitebusUntukServer;
        });
    }
});

// Form validation
document.getElementById('checkoutForm').addEventListener('submit', function(event) {
    const radioKurir = document.getElementById('kurir');
    const radioAmbilSendiri = document.getElementById('ambil_sendiri');

    if (!radioKurir.checked && !radioAmbilSendiri.checked) {
        event.preventDefault();
        alert('Silakan pilih metode pengiriman terlebih dahulu.');
        return;
    }

    if (radioKurir.checked) {
        const namaPenerima = document.getElementById('nama_penerima').value.trim();
        const noTelepon = document.getElementById('no_telepon').value.trim();
        const alamatLengkap = document.getElementById('alamat_lengkap').value.trim();

        let pesanErrorAlamat = [];
        if (!namaPenerima) pesanErrorAlamat.push('Nama Penerima');
        if (!noTelepon) pesanErrorAlamat.push('No. Telepon Penerima');
        if (!alamatLengkap) pesanErrorAlamat.push('Alamat Lengkap Pengiriman');

        if (pesanErrorAlamat.length > 0) {
            event.preventDefault();
            alert('Mohon lengkapi data berikut untuk pengiriman:\n- ' + pesanErrorAlamat.join('\n- '));
            return;
        }
    }

    // Disable tombol submit untuk mencegah double-submit
    document.getElementById('checkoutBtn').disabled = true;
    document.getElementById('checkoutBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
});

// Fungsi countdown dan lainnya (tambahkan fungsi yang hilang)
function startCountdown() {
    // Implementasi countdown jika diperlukan
    console.log('Countdown started');
}
</script>


{{-- CSS untuk efek blink --}}
<style>
@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.blink {
    animation: blink 1s infinite;
}

#countdownAlert {
    border-left: 5px solid #ffc107;
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
}

.alert-danger#countdownAlert {
    border-left: 5px solid #dc3545;
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
}

#countdownTimer {
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}
</style>

@endsection