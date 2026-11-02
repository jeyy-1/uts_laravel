@extends('layouts.app')
@section('title', 'Kelola Pengguna Sistem')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Kelola Pengguna Sistem</h3>
        <p class="text-muted small mb-0">Manajemen akun Owner, Staff Kasir, dan Pelanggan Ritel.</p>
    </div>
    <a href="{{ route('users.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> + Tambah Pengguna Baru
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Nama</th>
                    <th>Email</th>
                    <th>Hak Akses / Peran</th>
                    <th>Terdaftar Sejak</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $u)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">
                            {{ $u->name }}
                            @if ($u->id === auth()->id())
                                <span class="badge bg-light text-primary border ms-1">Akun Anda</span>
                            @endif
                        </td>
                        <td>{{ $u->email }}</td>
                        <td>
                            @if ($u->role === 'owner')
                                <span class="badge badge-role-owner px-2 py-1"><i class="bi bi-crown me-1"></i>Owner</span>
                            @elseif (in_array($u->role, ['staff', 'admin']))
                                <span class="badge badge-role-staff px-2 py-1"><i class="bi bi-person-badge me-1"></i>Staff Kasir</span>
                            @else
                                <span class="badge badge-role-user px-2 py-1"><i class="bi bi-person me-1"></i>Pelanggan</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $u->created_at ? $u->created_at->translatedFormat('d M Y') : '-' }}</td>
                        <td class="text-end pe-3">
                            @if ($u->id !== auth()->id())
                                <form method="POST" action="{{ route('users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Hapus pengguna {{ $u->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </form>
                            @else
                                <span class="text-muted small fst-italic">Sedang Aktif</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
