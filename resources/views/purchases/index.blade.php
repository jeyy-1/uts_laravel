@extends('layouts.app')
@section('title', 'Riwayat Stok Masuk (Pembelian)')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-box-arrow-in-down me-2 text-success"></i>Riwayat Stok Masuk (Pembelian)</h3>
        <p class="text-muted small mb-0">Penerimaan barang dari supplier; stok produk otomatis bertambah di etalase display.</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-success shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> + Input Stok Masuk Baru
    </a>
</div>

<div class="card shadow-sm border-0 mb-4 p-3 bg-white">
    <form method="GET" action="{{ route('purchases.index') }}" class="row g-2 align-items-center">
        <div class="col-md-3">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari No. PO pembelian...">
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
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary" title="Reset filter"><i class="bi bi-x-lg"></i></a>
        </div>
    </form>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">No. Transaksi Masuk</th>
                    <th>Waktu Masuk</th>
                    <th>Supplier</th>
                    <th>Petugas Penerima</th>
                    <th class="text-end">Total Biaya Beli</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($purchases as $p)
                    <tr>
                        <td class="ps-3 fw-bold font-monospace text-success">{{ $p->purchase_number }}</td>
                        <td class="small text-muted">{{ $p->purchased_at->translatedFormat('d M Y, H:i') }}</td>
                        <td class="fw-semibold">{{ $p->supplier->name }}</td>
                        <td class="small">{{ $p->user->name }}</td>
                        <td class="text-end fw-bold text-dark">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if ($p->status === 'completed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                            @else
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('purchases.show', $p) }}" class="btn btn-sm btn-outline-primary">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                            Belum ada catatan stok masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($purchases->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $purchases->links() }}
        </div>
    @endif
</div>
@endsection
