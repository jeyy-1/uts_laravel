@extends('layouts.app')
@section('title', 'Detail Penjualan #' . $sale->sale_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Top Navigation / Breadcrumb -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}" class="text-decoration-none">Penjualan</a></li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">{{ $sale->sale_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold mb-0 text-dark">Invoice Transaksi Kasir</h3>
                    <span class="font-mono text-primary fw-semibold px-2 py-1 bg-primary-subtle rounded-2 small">
                        {{ $sale->sale_number }}
                    </span>
                    @if ($sale->status === 'completed')
                        <span class="badge badge-subtle-success fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Selesai
                        </span>
                    @else
                        <span class="badge badge-subtle-danger fs-6">
                            <i class="bi bi-x-circle-fill me-1"></i> Dibatalkan
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                </a>
                <a href="{{ route('store.receipt', $sale) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="bi bi-receipt"></i> Struk Belanja Pelanggan
                </a>
                @can('owner')
                    @if ($sale->status === 'completed')
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelSaleModal">
                            <i class="bi bi-x-circle"></i> Batalkan Penjualan
                        </button>
                    @endif
                @endcan
            </div>
        </div>

        <!-- Info Jika Transaksi Telah Dibatalkan -->
        @if ($sale->status === 'cancelled')
            <div class="card border-danger mb-4 shadow-sm" style="background-color: #fff1f2; border-left: 5px solid #f43f5e !important;">
                <div class="card-body p-4 d-flex gap-3 align-items-start">
                    <div class="p-3 bg-white rounded-3 shadow-sm text-danger fs-3">
                        <i class="bi bi-shield-x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Transaksi Ini Telah Dibatalkan</h6>
                        <p class="small text-muted mb-2">
                            Dibatalkan oleh: <strong>{{ $sale->cancelledBy->name ?? 'Owner Toko' }}</strong> pada
                            <span class="text-dark fw-semibold">{{ $sale->cancelled_at ? $sale->cancelled_at->translatedFormat('d F Y, H:i') : '-' }} WIB</span>.
                        </p>
                        <div class="p-2 bg-white rounded-2 border small mb-2">
                            <strong>Alasan Pembatalan:</strong> <em>"{{ $sale->cancellation_reason ?: 'Permintaan pembatalan dari pelanggan via Owner' }}"</em>
                        </div>
                        <span class="badge bg-secondary text-white small">
                            <i class="bi bi-info-circle me-1"></i> Transaksi tidak berlaku.
                        </span>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <!-- Info Pelanggan & Petugas Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Informasi Pembeli</div>
                                <h5 class="fw-bold text-dark mt-1 mb-1">{{ $sale->customer_name ?: 'Pelanggan Umum' }}</h5>
                                <div class="small text-muted">
                                    Metode Pembayaran: <span class="badge bg-light text-dark border text-uppercase">{{ $sale->payment_method }}</span>
                                </div>
                            </div>
                            <div class="col-sm-6 border-start-sm ps-sm-4">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Waktu & Kasir</div>
                                <div class="mt-1 small">
                                    <span class="text-muted">Tanggal:</span> <strong class="text-dark">{{ $sale->sold_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                                </div>
                                <div class="small">
                                    <span class="text-muted">Kasir/Petugas:</span> <strong class="text-dark">{{ $sale->user->name ?? 'Sistem Ritel' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Barang yang Dijual -->
                <div class="card border-0 shadow-sm overflow-hidden mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-cart-check text-primary me-2"></i>Rincian Barang yang Dijual
                        </h6>
                        <span class="badge bg-light text-secondary border">{{ $sale->items->count() }} Jenis Produk</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>SKU & Nama Produk</th>
                                    <th class="text-center">Kuantitas</th>
                                    <th class="text-end">Harga Satuan</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sale->items as $it)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $it->product->name ?? 'Produk Dihapus' }}</div>
                                            <span class="font-mono text-muted small">SKU: {{ $it->product->sku ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark border fs-6 px-3 py-1 font-mono">
                                                {{ $it->quantity }} unit
                                            </span>
                                        </td>
                                        <td class="text-end font-mono">
                                            Rp {{ number_format($it->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end pe-4 fw-bold font-mono text-dark">
                                            Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 fs-6">Total Pembayaran:</td>
                                    <td class="text-end pe-4 fw-bold fs-5 text-primary font-mono py-3">
                                        Rp {{ number_format($sale->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
        </div>

        @can('owner')
            @if ($sale->status === 'completed')
                <!-- Modal Konfirmasi Pembatalan Khusus Owner -->
                <div class="modal fade" id="cancelSaleModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('sales.cancel', $sale) }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            @csrf
                            <div class="modal-header bg-danger text-white py-3 px-4">
                                <h5 class="modal-title fw-bold">Konfirmasi Pembatalan Transaksi</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Apakah Anda yakin ingin membatalkan transaksi <strong>{{ $sale->sale_number }}</strong> sejumlah <strong>Rp {{ number_format($sale->total, 0, ',', '.') }}</strong>?
                                </p>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Alasan Pembatalan <span class="text-danger">*</span></label>
                                    <textarea name="cancellation_reason" class="form-control" rows="3" required
                                              placeholder="Tulis alasan pembatalan..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer bg-light px-4 py-3">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button type="submit" class="btn btn-danger fw-bold">
                                    <i class="bi bi-x-circle me-1"></i> Konfirmasi Pembatalan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endcan
    </div>
</div>
@endsection
