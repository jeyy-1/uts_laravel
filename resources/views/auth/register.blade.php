@extends('layouts.app')
@section('title', 'Daftar Akun Pelanggan')

@push('styles')
<style>
    .auth-wrapper {
        min-height: calc(100vh - 170px);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .auth-bg-ambient {
        position: absolute;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.12) 0%, rgba(5, 150, 105, 0.03) 50%, transparent 70%);
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        pointer-events: none;
        z-index: 0;
    }

    .auth-card {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 24px;
        box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.08), 0 0 0 1px rgba(15, 23, 42, 0.02);
        position: relative;
        z-index: 1;
        overflow: hidden;
    }

    .auth-brand-badge-success {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.55rem;
        box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.4);
    }

    .input-auth-group {
        position: relative;
    }

    .input-auth-group .form-control {
        height: 48px;
        border-radius: 12px;
        padding-left: 2.75rem;
        font-size: 0.925rem;
    }

    .input-auth-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.1rem;
        z-index: 4;
        pointer-events: none;
    }

    .btn-auth-submit {
        height: 48px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 700;
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper py-3">
    <div class="auth-bg-ambient"></div>

    <div class="container" style="max-width: 520px;">
        <div class="card auth-card border-0">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="auth-brand-badge-success mx-auto mb-3">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Buat Akun Pelanggan</h3>
                    <p class="text-muted small mb-0">
                        Daftar sekarang untuk kemudahan belanja multi-barang di RetailSmart
                    </p>
                </div>

                <form method="POST" action="{{ route('register.store') }}" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-auth-group">
                            <i class="bi bi-person input-auth-icon"></i>
                            <input type="text" name="name" value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   required autofocus placeholder="Contoh: Andi Pratama">
                        </div>
                        @error('name')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-auth-group">
                            <i class="bi bi-envelope input-auth-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required placeholder="andi@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Kata Sandi (Password) <span class="text-danger">*</span></label>
                        <div class="input-auth-group">
                            <i class="bi bi-lock input-auth-icon"></i>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required placeholder="Minimal 6 karakter">
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-1">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <div class="input-auth-group">
                            <i class="bi bi-shield-lock input-auth-icon"></i>
                            <input type="password" name="password_confirmation"
                                   class="form-control"
                                   required placeholder="Ketik ulang password">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100 btn-auth-submit shadow-sm mb-3">
                        <span>Daftar & Mulai Belanja</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </button>

                    <div class="text-center small text-muted pt-2 border-top">
                        Sudah punya akun? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-semibold">Masuk di sini</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
