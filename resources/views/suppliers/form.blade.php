@extends('layouts.app')
@section('title', 'Form Supplier')
@section('content')
<h3 class="mb-3">{{ $supplier->exists ? 'Edit' : 'Tambah' }} Supplier</h3>
<form method="POST" class="card card-body shadow-sm" action="{{ $supplier->exists ? route('suppliers.update', $supplier) : route('suppliers.store') }}">
    @csrf
    @if ($supplier->exists) @method('PUT') @endif
    <div class="mb-3"><label class="form-label">Nama</label>
        <input name="name" value="{{ old('name', $supplier->name) }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Telepon</label>
        <input name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Email</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="form-control"></div>
    <div><button class="btn btn-primary">Simpan</button> <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
