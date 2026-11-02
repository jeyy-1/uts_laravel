@extends('layouts.app')
@section('title', 'Produk')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Produk</h3>
    @can('owner')<a href="{{ route('products.create') }}" class="btn btn-primary">+ Tambah Produk</a>@endcan
</div>

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari SKU / nama"></div>
    <div class="col-md-4">
        <select name="category_id" class="form-select">
            <option value="">Semua kategori</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-primary">Cari</button> <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>

<div class="card shadow-sm border-0">
<table class="table mb-0 align-middle table-hover">
    <thead class="table-light"><tr><th class="ps-3">SKU</th><th>Nama Produk</th><th>Kategori</th><th class="text-end">Harga Jual</th><th class="text-center">Status Stok</th><th class="text-end pe-3" width="220">Aksi</th></tr></thead>
    <tbody>
    @forelse ($products as $p)
        <tr>
            <td class="ps-3 font-monospace fw-semibold">{{ $p->sku }}</td>
            <td>
                <div class="fw-bold">{{ $p->name }}</div>
                <div class="small text-muted">{{ Str::limit($p->description, 50) }}</div>
            </td>
            <td><span class="badge bg-light text-dark border">{{ $p->category->name }}</span></td>
            <td class="text-end fw-semibold">Rp {{ number_format($p->selling_price, 0, ',', '.') }}</td>
            <td class="text-center">
                @if ($p->stock == 0)
                    <span class="badge bg-danger">Habis (0)</span>
                @elseif ($p->isLowStock())
                    <span class="badge bg-warning text-dark">Menipis ({{ $p->stock }})</span>
                @else
                    <span class="badge bg-success">Tersedia ({{ $p->stock }})</span>
                @endif
            </td>
            <td class="text-end pe-3">
                <a href="{{ route('products.show', $p) }}" class="btn btn-sm btn-outline-primary">Detail</a>
                @can('owner')
                    <a href="{{ route('products.edit', $p) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                    <form method="POST" action="{{ route('products.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                        @csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Hapus</button>
                    </form>
                @endcan
            </td>
        </tr>
    @empty
        <tr><td colspan="6" class="text-center text-muted">Produk tidak ditemukan.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $products->links() }}</div>
@endsection
