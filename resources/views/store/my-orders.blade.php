@extends('layouts.app')
@section('title', 'Pesanan Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-bag-check me-2 text-primary"></i>Riwayat Pesanan Saya</h3>
        <p class="text-muted small mb-0">Daftar transaksi belanja ritel yang telah Anda lakukan.</p>
    </div>
    <a href="{{ route('home') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-cart-plus me-1"></i> Belanja Lagi
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Barang yang Dibeli</th>
                    <th>Total</th>
                    <th>Metode</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $sale)
                    <tr>
                        <td class="fw-bold font-monospace text-primary">{{ $sale->sale_number }}</td>
                        <td class="small">{{ $sale->sold_at->translatedFormat('d M Y, H:i') }}</td>
                        <td>
                            <ul class="list-unstyled mb-0 small">
                                @foreach ($sale->items as $item)
                                    <li>&bull; {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }}x)</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="fw-bold">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $sale->payment_method }}</span></td>
                        <td>
                            @if ($sale->status === 'completed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('store.receipt', $sale) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-receipt me-1"></i> Nota
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                            Belum ada riwayat pesanan. Mari mulai belanja di etalase toko kami!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($sales->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $sales->links() }}
        </div>
    @endif
</div>
@endsection
