@extends('layouts.app')
@section('title', 'Form Produk')
@section('content')
<h3 class="mb-3">{{ $product->exists ? 'Edit' : 'Tambah' }} Produk</h3>
<form method="POST" class="card card-body shadow-sm" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}">
    @csrf
    @if ($product->exists) @method('PUT') @endif
    <div class="row">
        <div class="col-md-4 mb-3"><label class="form-label">SKU</label>
            <input name="sku" value="{{ old('sku', $product->sku) }}" class="form-control" required></div>
        <div class="col-md-8 mb-3"><label class="form-label">Nama</label>
            <input name="name" value="{{ old('name', $product->name) }}" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Kategori</label>
            <select name="category_id" class="form-select" required>
                <option value="">-- pilih --</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select></div>
        <div class="col-md-6 mb-3"><label class="form-label">Stok minimum</label>
            <input type="number" min="0" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock ?? 0) }}" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Harga beli</label>
            <input type="number" min="0" step="0.01" name="purchase_price" value="{{ old('purchase_price', $product->purchase_price) }}" class="form-control" required></div>
        <div class="col-md-6 mb-3"><label class="form-label">Harga jual</label>
            <input type="number" min="0" step="0.01" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" class="form-control" required></div>
        <div class="col-12 mb-3"><label class="form-label">Deskripsi Produk (Etalase Toko)</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Deskripsi singkat produk untuk pembeli">{{ old('description', $product->description) }}</textarea></div>
    </div>
    <p class="text-muted small">Stok awal 0 dan hanya berubah lewat transaksi pembelian/penjualan (dinamis & otomatis).</p>
    <div><button class="btn btn-primary">Simpan</button> <a href="{{ route('products.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
