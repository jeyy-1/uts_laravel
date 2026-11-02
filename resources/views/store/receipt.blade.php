@extends('layouts.app')
@section('title', 'Nota Pembelian ' . $sale->sale_number)

@push('styles')
<style>
    @media print {
        body { background-color: #ffffff !important; }
        .navbar-main, footer, .no-print { display: none !important; }
        main { padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .receipt-card { width: 100% !important; max-width: 100% !important; }
    }
    .receipt-cut {
        border-top: 2px dashed #cbd5e1;
        position: relative;
    }
</style>
@endpush

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden mb-4 receipt-card">
            <!-- Header Struk -->
            <div class="p-4 text-center text-white" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                <div class="p-2 bg-success text-white rounded-circle d-inline-flex mb-2">
                    <i class="bi bi-check2 fs-3"></i>
                </div>
                <h4 class="fw-bold mb-1">Bukti Pembayaran Belanja</h4>
                <div class="font-mono small text-white-50">NO: {{ $sale->sale_number }}</div>
            </div>

            <div class="card-body p-4 p-md-5">
                <!-- Info Toko & Pembeli -->
                <div class="d-flex justify-content-between align-items-start pb-3 border-bottom mb-4">
                    <div>
                        <div class="fw-bold text-dark fs-5">RetailSmart Store</div>
                        <div class="text-muted small">Pencatatan Ritel Otomatis</div>
                        <div class="text-muted small font-mono mt-1">{{ $sale->sold_at->translatedFormat('d F Y, H:i') }} WIB</div>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-light text-dark border text-uppercase mb-1">{{ $sale->payment_method }}</span>
                        <div class="small text-muted">Pelanggan:</div>
                        <div class="fw-bold text-dark">{{ $sale->customer_name ?: 'Pelanggan Umum' }}</div>
                    </div>
                </div>

                <!-- Status Badge -->
                <div class="mb-4 text-center">
                    @if ($sale->status === 'completed')
                        <span class="badge badge-subtle-success fs-6 px-3 py-2">
                            <i class="bi bi-patch-check-fill me-1"></i> STATUS: LUNAS
                        </span>
                    @else
                        <span class="badge badge-subtle-danger fs-6 px-3 py-2">
                            <i class="bi bi-x-octagon-fill me-1"></i> STATUS: DIBATALKAN
                        </span>
                    @endif
                </div>

                <!-- Rincian Barang yang Dibeli -->
                <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-receipt me-1 text-primary"></i> Rincian Barang Belanja:</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-custom align-middle">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Harga</th>
                                <th class="text-end pe-3">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $item->product->name ?? 'Produk' }}</div>
                                        <span class="font-mono text-muted small">SKU: {{ $item->product->sku ?? '-' }}</span>
                                    </td>
                                    <td class="text-center font-mono">{{ $item->quantity }}</td>
                                    <td class="text-end font-mono">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                    <td class="text-end pe-3 font-mono fw-bold text-dark">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-light">
                            <tr>
                                <th colspan="3" class="text-end fs-6 py-3">Total Belanja:</th>
                                <th class="text-end pe-3 fs-5 fw-bold text-primary font-mono py-3">Rp {{ number_format($sale->total, 0, ',', '.') }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="receipt-cut pt-3 mb-4">
                    <div class="p-3 bg-light rounded-3 small text-muted text-center">
                        <div class="fw-semibold text-dark mb-1">Terima kasih telah berbelanja di RetailSmart!</div>
                        <div>Barang yang telah dibeli dapat ditukar sesuai syarat & ketentuan toko. Simpan struk ini sebagai bukti pembayaran yang sah.</div>
                    </div>
                </div>

                <!-- Tombol Aksi (Tidak Ikut Ter-print) -->
                <div class="d-flex flex-wrap gap-2 justify-content-between pt-2 no-print">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Toko
                    </a>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="window.print()">
                            <i class="bi bi-printer me-1"></i> Cetak Struk
                        </button>
                        @auth
                            @if(in_array(auth()->user()->role, ['owner', 'staff', 'admin']))
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-dark">
                                    <i class="bi bi-gear me-1"></i> Panel Kasir
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
