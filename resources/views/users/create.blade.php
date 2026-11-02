@extends('layouts.app')
@section('title', 'Tambah Pengguna')
@section('content')
<h3 class="mb-3">Tambah Pengguna</h3>
<form method="POST" action="{{ route('users.store') }}" class="card card-body shadow-sm">
    @csrf
    <div class="mb-3"><label class="form-label">Nama</label><input name="name" value="{{ old('name') }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password (min. 8 karakter)</label><input type="password" name="password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Role</label>
        <select name="role" class="form-select" required>
            <option value="owner" @selected(old('role') === 'owner')>Owner (Akses Penuh & Pembatalan)</option>
            <option value="staff" @selected(old('role') === 'staff')>Staff (Kasir & Stok Masuk)</option>
            <option value="user" @selected(old('role') === 'user')>User (Pelanggan Ritel)</option>
        </select></div>
    <div><button class="btn btn-primary">Simpan</button> <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a></div>
</form>
@endsection
