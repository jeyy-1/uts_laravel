@extends('layouts.app')
@section('title', 'Masuk ke Sistem')

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
        background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, rgba(79, 70, 229, 0.03) 50%, transparent 70%);
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
        transition: box-shadow 0.25s ease;
    }

    .auth-brand-badge {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #3b82f6 100%);
        border-radius: 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.55rem;
        box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
    }


    .input-auth-group {
        position: relative;
    }

    .input-auth-group .form-control {
        height: 48px;
        border-radius: 12px;
        padding-left: 2.75rem;
        padding-right: 2.75rem;
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

    .password-toggle-btn {
        position: absolute;
        right: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px 6px;
        z-index: 5;
        border-radius: 6px;
        transition: color 0.15s ease;
    }

    .password-toggle-btn:hover {
        color: #475569;
    }

    .btn-auth-submit {
        height: 48px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
</style>
@endpush

@section('content')
<div class="auth-wrapper py-3">
    <!-- Ambient Blur Glow -->
    <div class="auth-bg-ambient"></div>

    <div class="container" style="max-width: 520px;">
        <div class="card auth-card border-0">
            <div class="card-body p-4 p-md-5">
                <!-- Brand Header -->
                <div class="text-center mb-4">
                    <div class="auth-brand-badge mx-auto mb-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="fw-bold text-dark mb-1" style="letter-spacing: -0.02em;">Masuk ke Akun</h3>
                    <p class="text-muted small mb-0">
                        Sistem Inventori Ritel Dinamis & POS Transaksi
                    </p>
                </div>

                <!-- Alert Pesan Error Login -->
                @if (session('error'))
                    <div class="alert alert-danger d-flex align-items-center gap-3 p-3 rounded-3 mb-4 shadow-xs"
                         style="background-color: #fff1f2; border: 1px solid #fecdd3; color: #be123c;" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
                        <div class="small fw-semibold">{{ session('error') }}</div>
                    </div>
                @endif

                <!-- Form Login -->
                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf

                    <!-- Email Input -->
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            Alamat Email <span class="text-danger">*</span>
                        </label>
                        <div class="input-auth-group">
                            <i class="bi bi-envelope input-auth-icon"></i>
                            <input type="email" name="email" id="loginEmail"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   required autofocus placeholder="nama@example.com">
                        </div>
                        @error('email')
                            <div class="text-danger small mt-1 d-flex align-items-center gap-1">
                                <i class="bi bi-x-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password Input dengan Toggle Show/Hide -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold text-dark mb-0">
                                Kata Sandi <span class="text-danger">*</span>
                            </label>
                            <a href="{{ route('register') }}" class="small text-primary text-decoration-none fw-semibold">
                                Daftar Akun Baru
                            </a>
                        </div>
                        <div class="input-auth-group">
                            <i class="bi bi-lock input-auth-icon"></i>
                            <input type="password" name="password" id="loginPassword"
                                   class="form-control @error('password') is-invalid @enderror"
                                   required placeholder="Ketik kata sandi">
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Tampilkan/sembunyikan kata sandi">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger small mt-1 d-flex align-items-center gap-1">
                                <i class="bi bi-x-circle-fill"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check small">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                            <label class="form-check-label text-muted" for="rememberMe">Ingat sesi saya</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-auth-submit shadow-sm">
                        <span>Masuk Sekarang</span>
                        <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle Show/Hide Password
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passwordInput = document.getElementById('loginPassword');
    const toggleIcon = document.getElementById('togglePasswordIcon');

    toggleBtn.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.className = 'bi bi-eye-slash';
        } else {
            passwordInput.type = 'password';
            toggleIcon.className = 'bi bi-eye';
        }
    });
</script>
@endpush

