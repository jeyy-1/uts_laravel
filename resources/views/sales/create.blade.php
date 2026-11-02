@extends('layouts.app')
@section('title', 'Transaksi Penjualan Baru (POS Kasir)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-cart-check me-2 text-primary"></i>Kasir Penjualan Baru</h3>
                <p class="text-muted small mb-0">Input penjualan produk ritel; stok produk akan otomatis terpotong dari sistem.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
            </a>
        </div>

        <form method="POST" action="{{ route('sales.store') }}" class="card shadow-sm border-0 p-4">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Nama Pembeli / Pelanggan</label>
                    <input type="text" name="customer_name" class="form-control"
                           value="{{ old('customer_name', 'Pelanggan Umum') }}"
                           placeholder="Contoh: Budi Santoso / Meja #02">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Metode Pembayaran <span class="text-danger">*</span></label>
                    <select name="payment_method" class="form-select" required>
                        <option value="cash" @selected(old('payment_method') === 'cash')>Tunai (Cash di Kasir)</option>
                        <option value="qris" @selected(old('payment_method') === 'qris')>QRIS Dinamis</option>
                        <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer Bank</option>
                    </select>
                </div>
            </div>

            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-boxes text-primary"></i>
                <span>Barang yang Dibeli (Multi-Barang)</span>
            </h6>

            @include('partials.items-form', ['withCost' => false])

            <div class="alert alert-light border small text-muted my-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-primary fs-5"></i>
                <div>
                    Harga satuan dan ketersediaan stok diambil otomatis dari database secara real-time. Total akhir dihitung oleh server dengan validasi atomik transaksi.
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Simpan & Potong Stok Otomatis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
