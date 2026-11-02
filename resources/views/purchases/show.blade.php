@extends('layouts.app')
@section('title', 'Detail Stok Masuk #' . $purchase->purchase_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Top Navigation / Breadcrumb -->
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('purchases.index') }}" class="text-decoration-none">Stok Masuk</a></li>
                        <li class="breadcrumb-item active text-muted" aria-current="page">{{ $purchase->purchase_number }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <h3 class="fw-bold mb-0 text-dark">Penerimaan Barang</h3>
                    <span class="font-mono text-primary fw-semibold px-2 py-1 bg-primary-subtle rounded-2 small">
                        {{ $purchase->purchase_number }}
                    </span>
                    @if ($purchase->status === 'completed')
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
                <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Kembali ke Riwayat
                </a>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak Bukti Masuk
                </button>
                @can('owner')
                    @if ($purchase->status === 'completed')
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelPurchaseModal">
                            <i class="bi bi-x-circle"></i> Batalkan Pembelian
                        </button>
                    @endif
                @endcan
            </div>
        </div>

        <!-- Alert Jika Transaksi Dibatalkan -->
        @if ($purchase->status === 'cancelled')
            <div class="card border-danger mb-4 shadow-sm" style="background-color: #fff1f2; border-left: 5px solid #f43f5e !important;">
                <div class="card-body p-4 d-flex gap-3 align-items-start">
                    <div class="p-3 bg-white rounded-3 shadow-sm text-danger fs-3">
                        <i class="bi bi-shield-x"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-danger mb-1">Transaksi Stok Masuk Telah Dibatalkan</h6>
                        <p class="small text-muted mb-2">
                            Dibatalkan oleh <strong>{{ $purchase->cancelledBy->name ?? 'Owner' }}</strong> pada
                            <span class="text-dark fw-semibold">{{ $purchase->cancelled_at ? $purchase->cancelled_at->translatedFormat('d F Y, H:i') : '-' }} WIB</span>.
                        </p>
                        <div class="p-2 bg-white rounded-2 border small mb-2">
                            <strong>Alasan Pembatalan:</strong> <em>"{{ $purchase->cancellation_reason ?: 'Tidak ada alasan tertulis' }}"</em>
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
                <!-- Info Supplier & Petugas Card -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Supplier Penyedia</div>
                                <h5 class="fw-bold text-dark mt-1 mb-1">{{ $purchase->supplier->name }}</h5>
                                <div class="small text-muted"><i class="bi bi-telephone me-1"></i> {{ $purchase->supplier->phone ?: 'Tidak ada no. telp' }}</div>
                                <div class="small text-muted"><i class="bi bi-envelope me-1"></i> {{ $purchase->supplier->email ?: 'Tidak ada email' }}</div>
                            </div>
                            <div class="col-sm-6 border-start-sm ps-sm-4">
                                <div class="text-muted small text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Informasi Masuk</div>
                                <div class="mt-1 small">
                                    <span class="text-muted">Tanggal:</span> <strong class="text-dark">{{ $purchase->purchased_at->translatedFormat('d F Y, H:i') }} WIB</strong>
                                </div>
                                <div class="small">
                                    <span class="text-muted">Diterima oleh:</span> <strong class="text-dark">{{ $purchase->user->name }}</strong>
                                </div>
                                @if($purchase->notes)
                                    <div class="small mt-1 p-2 bg-light rounded-2 text-secondary">
                                        <i class="bi bi-card-text me-1"></i> {{ $purchase->notes }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Barang Masuk -->
                <div class="card border-0 shadow-sm overflow-hidden mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <i class="bi bi-boxes text-success me-2"></i>Daftar Barang yang Diterima
                        </h6>
                        <span class="badge bg-light text-secondary border">{{ $purchase->items->count() }} Jenis Produk</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-custom align-middle">
                            <thead>
                                <tr>
                                    <th>SKU & Nama Produk</th>
                                    <th class="text-center">Qty Masuk</th>
                                    <th class="text-end">Harga Beli</th>
                                    <th class="text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->items as $it)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $it->product->name ?? 'Produk Dihapus' }}</div>
                                            <span class="font-mono text-muted small">SKU: {{ $it->product->sku ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-subtle-success fs-6 px-3 py-1">
                                                +{{ $it->quantity }} unit
                                            </span>
                                        </td>
                                        <td class="text-end font-mono">
                                            Rp {{ number_format($it->unit_cost, 0, ',', '.') }}
                                        </td>
                                        <td class="text-end pe-4 fw-bold font-mono text-dark">
                                            Rp {{ number_format($it->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-light">
                                <tr>
                                    <td colspan="3" class="text-end fw-bold py-3 fs-6">Total Biaya Pembelian:</td>
                                    <td class="text-end pe-4 fw-bold fs-5 text-success font-mono py-3">
                                        Rp {{ number_format($purchase->total, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        @can('owner')
            @if ($purchase->status === 'completed')
                <!-- Modal Konfirmasi Pembatalan -->
                <div class="modal fade" id="cancelPurchaseModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('purchases.cancel', $purchase) }}" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            @csrf
                            <div class="modal-header bg-danger text-white py-3 px-4">
                                <h5 class="modal-title fw-bold">Konfirmasi Pembatalan Stok Masuk</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4">
                                <p class="mb-3">
                                    Apakah Anda yakin ingin membatalkan transaksi stok masuk <strong>{{ $purchase->purchase_number }}</strong>?
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
</div>
@endsection
