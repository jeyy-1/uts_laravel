@extends('layouts.app')
@section('title', 'Riwayat Transaksi Penjualan')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-receipt me-2 text-primary"></i>Riwayat Transaksi Penjualan</h3>
        <p class="text-muted small mb-0">Semua catatan transaksi kasir ritel & status pengembalian stok saat pembatalan.</p>
    </div>
    <a href="{{ route('sales.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> + Transaksi Kasir Baru
    </a>
</div>

<!-- Filter Toolbar -->
<div class="card shadow-sm border-0 mb-4 p-3 bg-white">
    <form method="GET" action="{{ route('sales.index') }}" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari No. transaksi / pembeli...">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" title="Dari tanggal">
        </div>
        <div class="col-md-2">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" title="Sampai tanggal">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="completed" @selected(request('status') === 'completed')>Selesai</option>
                <option value="cancelled" @selected(request('status') === 'cancelled')>Dibatalkan</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-funnel me-1"></i> Filter</button>
            <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<!-- Table Card -->
<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. Transaksi</th>
                    <th>Waktu</th>
                    <th>Pelanggan</th>
                    <th>Petugas / Kasir</th>
                    <th>Metode</th>
                    <th class="text-end">Total</th>
                    <th class="text-center">Status Transaksi</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($sales as $s)
                    <tr>
                        <td class="ps-3 fw-bold font-monospace text-primary">{{ $s->sale_number }}</td>
                        <td class="small text-muted">{{ $s->sold_at->translatedFormat('d M Y, H:i') }}</td>
                        <td class="fw-semibold">{{ $s->customer_name ?: 'Pelanggan Walk-in' }}</td>
                        <td class="small">{{ $s->user->name ?? 'Sistem' }}</td>
                        <td><span class="badge bg-light text-dark border text-uppercase">{{ $s->payment_method }}</span></td>
                        <td class="text-end fw-bold">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if ($s->status === 'completed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                            @else
                                <span class="badge bg-danger" title="{{ $s->cancellation_reason }}">
                                    <i class="bi bi-x-circle me-1"></i>Dibatalkan Owner
                                </span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('sales.show', $s) }}" class="btn btn-sm btn-outline-primary">
                                Detail & Otorisasi
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                            Tidak ada transaksi penjualan ditemukan.
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
