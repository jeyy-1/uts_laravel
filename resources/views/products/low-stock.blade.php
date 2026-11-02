@extends('layouts.app')
@section('title', 'Peringatan Stok Rendah & Habis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Stok Rendah & Habis</h3>
        <p class="text-muted small mb-0">Daftar produk yang stoknya telah mencapai atau berada di bawah batas minimum stok.</p>
    </div>
    <a href="{{ route('purchases.create') }}" class="btn btn-success shadow-sm">
        <i class="bi bi-box-arrow-in-down me-1"></i> + Buat Stok Masuk (Restock)
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th class="text-center">Sisa Stok Saat Ini</th>
                    <th class="text-center">Batas Minimum</th>
                    <th class="text-end pe-3">Aksi Restock</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $p)
                    <tr>
                        <td class="ps-3 font-monospace fw-semibold">{{ $p->sku }}</td>
                        <td>
                            <div class="fw-bold">{{ $p->name }}</div>
                            <div class="small text-muted">{{ $p->category->name }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $p->category->name }}</span></td>
                        <td class="text-center">
                            @if ($p->stock == 0)
                                <span class="badge bg-danger fs-6 px-3 py-1">HABIS (0)</span>
                            @else
                                <span class="badge bg-warning text-dark fs-6 px-3 py-1 fw-bold">{{ $p->stock }} unit</span>
                            @endif
                        </td>
                        <td class="text-center text-muted fw-semibold">{{ $p->minimum_stock }} unit</td>
                        <td class="text-end pe-3">
                            <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus-lg me-1"></i> Input Stok Masuk
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            Kondisi inventori optimal. Tidak ada produk dengan stok di bawah batas minimum.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($products->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
