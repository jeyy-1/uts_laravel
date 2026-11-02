@extends('layouts.app')
@section('title', 'Form Kategori')
@section('content')
<h3 class="mb-3">{{ $category->exists ? 'Edit' : 'Tambah' }} Kategori</h3>
<form method="POST" class="card card-body shadow-sm" action="{{ $category->exists ? route('categories.update', $category) : route('categories.store') }}">
    @csrf
    @if ($category->exists) @method('PUT') @endif
    <div class="mb-3">
        <label class="form-label">Nama</label>
        <input name="name" value="{{ old('name', $category->name) }}" class="form-control" required>
    </div>
    <div><button class="btn btn-primary">Simpan</button> <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
