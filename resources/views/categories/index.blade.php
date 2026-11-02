@extends('layouts.app')
@section('title', 'Kategori')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Kategori</h3>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">+ Tambah</a>
</div>
<div class="card shadow-sm">
<table class="table mb-0">
    <thead><tr><th>Nama</th><th>Jumlah Produk</th><th width="160"></th></tr></thead>
    <tbody>
    @forelse ($categories as $c)
        <tr>
            <td>{{ $c->name }}</td><td>{{ $c->products_count }}</td>
            <td class="text-end">
                <a href="{{ route('categories.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="{{ route('categories.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3" class="text-center text-muted">Belum ada data.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $categories->links() }}</div>
@endsection
