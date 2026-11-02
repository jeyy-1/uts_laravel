@extends('layouts.app')
@section('title', 'Supplier')
@section('content')
<div class="d-flex justify-content-between mb-3">
    <h3>Supplier</h3>
    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">+ Tambah</a>
</div>
<div class="card shadow-sm">
<table class="table mb-0">
    <thead><tr><th>Nama</th><th>Telepon</th><th>Email</th><th width="160"></th></tr></thead>
    <tbody>
    @forelse ($suppliers as $s)
        <tr>
            <td>{{ $s->name }}</td><td>{{ $s->phone }}</td><td>{{ $s->email }}</td>
            <td class="text-end">
                <a href="{{ route('suppliers.edit', $s) }}" class="btn btn-sm btn-warning">Edit</a>
                <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Hapus supplier ini?')">
                    @csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="4" class="text-center text-muted">Belum ada data.</td></tr>
    @endforelse
    </tbody>
</table>
</div>
<div class="mt-3">{{ $suppliers->links() }}</div>
@endsection
