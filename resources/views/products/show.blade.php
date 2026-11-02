@extends('layouts.app')
@section('title', 'Detail Produk')
@section('content')
<h3 class="mb-3">{{ $product->name }}</h3>
<div class="card card-body shadow-sm">
    <dl class="row mb-0">
        <dt class="col-sm-3">SKU</dt><dd class="col-sm-9">{{ $product->sku }}</dd>
        <dt class="col-sm-3">Kategori</dt><dd class="col-sm-9">{{ $product->category->name }}</dd>
        <dt class="col-sm-3">Harga beli</dt><dd class="col-sm-9">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</dd>
        <dt class="col-sm-3">Harga jual</dt><dd class="col-sm-9">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</dd>
        <dt class="col-sm-3">Stok</dt><dd class="col-sm-9">{{ $product->stock }} @if($product->isLowStock())<span class="badge bg-danger">Rendah</span>@endif</dd>
        <dt class="col-sm-3">Stok minimum</dt><dd class="col-sm-9">{{ $product->minimum_stock }}</dd>
    </dl>
</div>
<a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">Kembali</a>
@endsection
