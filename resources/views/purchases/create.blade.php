@extends('layouts.app')
@section('title', 'Input Stok Masuk (Stock-in / Pembelian)')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1"><i class="bi bi-box-arrow-in-down me-2 text-success"></i>Input Stok Masuk Baru</h3>
                <p class="text-muted small mb-0">Catat penerimaan barang dari supplier; stok produk di display akan otomatis bertambah secara real-time.</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat
            </a>
        </div>

        <form method="POST" action="{{ route('purchases.store') }}" class="card shadow-sm border-0 p-4">
            @csrf

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Supplier Penyedia Barang <span class="text-danger">*</span></label>
                    <select name="supplier_id" class="form-select" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold small">Catatan / No. Surat Jalan (Opsional)</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Contoh: Pengiriman DO #SJ-8890 batch reguler">
                </div>
            </div>

            <h6 class="fw-bold mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-boxes text-success"></i>
                <span>Barang Masuk (Multi-Item)</span>
            </h6>

            @include('partials.items-form', ['withCost' => true])

            <div class="alert alert-light border small text-muted my-3 d-flex align-items-center gap-2">
                <i class="bi bi-info-circle-fill text-success fs-5"></i>
                <div>
                    Ketika Anda mengklik <strong>Simpan & Tambah Stok</strong>, sistem akan secara atomik menambah kuantitas stok di database dan langsung memperbarui ketersediaan produk di etalase display.
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2">
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
                    <i class="bi bi-plus-circle me-1"></i> Simpan & Tambah Stok Otomatis
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
