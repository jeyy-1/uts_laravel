<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RetailSmart') &mdash; Sistem Ritel & Inventori Cerdas</title>

    <!-- Google Fonts: Plus Jakarta Sans & Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --brand-primary: #4f46e5;
            --brand-primary-hover: #4338ca;
            --brand-primary-light: #eef2ff;
            --brand-primary-rgb: 79, 70, 229;
            --brand-slate-dark: #0f172a;
            --brand-slate-surface: #1e293b;
            --brand-bg: #f8fafc;
            --brand-card: #ffffff;
            --brand-border: #e2e8f0;
            --brand-border-subtle: #f1f5f9;
            --brand-success: #10b981;
            --brand-success-light: #ecfdf5;
            --brand-warning: #f59e0b;
            --brand-warning-light: #fffbeb;
            --brand-danger: #f43f5e;
            --brand-danger-light: #fff1f2;
            --brand-purple: #8b5cf6;
            --brand-purple-light: #f5f3ff;
            --brand-cyan: #06b6d4;
            --brand-cyan-light: #ecfeff;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--brand-bg);
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
        }

        /* Navbar Glassmorphism */
        .navbar-main {
            background: rgba(15, 23, 42, 0.94) !important;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.12);
        }

        .navbar-brand {
            font-weight: 800;
            letter-spacing: -0.03em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.15rem;
        }

        .brand-logo-mark {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #3b82f6 100%);
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.35);
        }

        .nav-link {
            font-weight: 600;
            font-size: 0.885rem;
            color: #cbd5e1 !important;
            padding: 0.55rem 0.95rem !important;
            border-radius: 8px;
            transition: all 0.18s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link:hover, .nav-link:focus {
            color: #ffffff !important;
            background-color: rgba(255, 255, 255, 0.08);
        }

        .nav-link.active {
            color: #ffffff !important;
            background: rgba(99, 102, 241, 0.22);
            border: 1px solid rgba(99, 102, 241, 0.4);
        }

        /* Cards & Elevations */
        .card {
            border: 1px solid var(--brand-border);
            border-radius: 16px;
            background-color: var(--brand-card);
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.02), 0 2px 6px -1px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .card-header {
            background-color: transparent;
            border-bottom: 1px solid var(--brand-border-subtle);
            padding: 1.15rem 1.4rem;
        }

        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.04);
        }

        /* Buttons */
        .btn {
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0.55rem 1.15rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.18s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: 1px solid #4338ca;
            box-shadow: 0 2px 8px rgba(79, 70, 229, 0.28);
        }

        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border-color: #3730a3;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(79, 70, 229, 0.38);
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border: 1px solid #047857;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        }

        .btn-danger {
            background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
            border: 1px solid #be123c;
            box-shadow: 0 2px 8px rgba(244, 63, 94, 0.25);
        }

        /* Form Controls */
        .form-control, .form-select {
            border: 1.5px solid var(--brand-border);
            border-radius: 10px;
            padding: 0.6rem 0.95rem;
            font-size: 0.9rem;
            transition: all 0.15s ease;
            background-color: #ffffff;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 4px rgba(var(--brand-primary-rgb), 0.12);
        }

        /* Pill Badges */
        .badge {
            font-weight: 600;
            letter-spacing: 0.01em;
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
        }

        .badge-subtle-success {
            background-color: var(--brand-success-light);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-subtle-warning {
            background-color: var(--brand-warning-light);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        .badge-subtle-danger {
            background-color: var(--brand-danger-light);
            color: #be123c;
            border: 1px solid rgba(244, 63, 94, 0.25);
        }

        .badge-subtle-primary {
            background-color: var(--brand-primary-light);
            color: #4338ca;
            border: 1px solid rgba(79, 70, 229, 0.25);
        }

        .badge-role-owner {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(109, 40, 217, 0.3);
        }

        .badge-role-staff {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
        }

        .badge-role-user {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
        }

        /* Stat Widget Cards */
        .stat-widget {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--brand-border);
            padding: 1.35rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .stat-icon-primary { background: #eef2ff; color: #4f46e5; }
        .stat-icon-success { background: #ecfdf5; color: #059669; }
        .stat-icon-warning { background: #fffbeb; color: #d97706; }
        .stat-icon-danger  { background: #fff1f2; color: #e11d48; }
        .stat-icon-purple  { background: #f5f3ff; color: #7c3aed; }
        .stat-icon-cyan    { background: #ecfeff; color: #0891b2; }

        /* Tables */
        .table-custom {
            margin-bottom: 0;
        }

        .table-custom thead th {
            font-size: 0.775rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            background-color: #f8fafc;
            border-bottom: 1.5px solid var(--brand-border);
            padding: 0.9rem 1.1rem;
        }

        .table-custom tbody td {
            padding: 1rem 1.1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }

        .table-custom tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Live Indicator */
        .live-dot {
            width: 8px;
            height: 8px;
            background-color: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: pulse-ring 1.8s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        }

        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { transform: scale(1.1); box-shadow: 0 0 0 7px rgba(16, 185, 129, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Monospace Numbers */
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Footer */
        footer {
            margin-top: auto;
            border-top: 1px solid var(--brand-border);
            background: #ffffff;
            font-size: 0.85rem;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 7px;
            height: 7px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Header Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-main sticky-top py-2">
    <div class="container-fluid px-lg-4">
        <a class="navbar-brand" href="{{ route('home') }}">
            <span class="brand-logo-mark"><i class="bi bi-shop"></i></span>
            <span class="d-flex flex-column leading-none">
                <span class="text-white fw-bold">Retail<span class="text-primary-emphasis" style="color: #818cf8 !important;">Smart</span></span>
                <span class="text-white-50 small" style="font-size: 0.68rem; letter-spacing: 0.05em; font-weight: 500;">INVENTORY & SALES</span>
            </span>
        </a>

        <button class="navbar-toggler border-0 p-2" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <!-- Halaman Utama Ritel Publik -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="bi bi-grid-fill"></i> Etalase Display
                    </a>
                </li>

                @auth
                    @if (in_array(auth()->user()->role, ['owner', 'staff', 'admin']))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('sales.*') ? 'active' : '' }}" href="{{ route('sales.index') }}">
                                <i class="bi bi-receipt"></i> Penjualan (POS)
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                                <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('products.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-boxes"></i> Inventori
                            </a>
                            <ul class="dropdown-menu shadow-lg border-0 rounded-3 mt-2 py-2">
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('products.index') }}"><i class="bi bi-list-ul me-2 text-primary"></i>Katalog Semua Produk</a></li>
                                <li><a class="dropdown-item py-2 fw-semibold text-danger" href="{{ route('products.low-stock') }}"><i class="bi bi-exclamation-triangle-fill me-2"></i>Peringatan Stok Rendah & Habis</a></li>
                                @can('owner')
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item py-2 fw-semibold text-success" href="{{ route('products.create') }}"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Produk Baru</a></li>
                                @endcan
                            </ul>
                        </li>
                    @endif

                    @can('owner')
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle {{ request()->routeIs('categories.*', 'suppliers.*', 'users.*') ? 'active' : '' }}" href="#" data-bs-toggle="dropdown" style="color: #c4b5fd !important;">
                                <i class="bi bi-shield-lock-fill"></i> Panel Owner
                            </a>
                            <ul class="dropdown-menu shadow-lg border-0 rounded-3 mt-2 py-2">
                                <li><div class="px-3 py-1 text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">Master Data</div></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('categories.index') }}"><i class="bi bi-tags-fill me-2 text-info"></i>Kategori Produk</a></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('suppliers.index') }}"><i class="bi bi-truck-front-fill me-2 text-warning"></i>Data Supplier</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('users.index') }}"><i class="bi bi-people-fill me-2 text-purple" style="color: #8b5cf6;"></i>Kelola Pengguna & Staf</a></li>
                            </ul>
                        </li>
                    @endcan

                    @if (auth()->user()->role === 'user')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('store.my-orders') ? 'active' : '' }}" href="{{ route('store.my-orders') }}">
                                <i class="bi bi-bag-check-fill"></i> Pesanan Saya
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Sisi Kanan: Akun & Status Login -->
            <div class="d-flex align-items-center gap-2">
                @auth
                    @php
                        $roleClass = match(auth()->user()->role) {
                            'owner' => 'badge-role-owner',
                            'staff', 'admin' => 'badge-role-staff',
                            default => 'badge-role-user',
                        };
                        $roleIcon = match(auth()->user()->role) {
                            'owner' => 'bi-crown-fill',
                            'staff', 'admin' => 'bi-person-badge-fill',
                            default => 'bi-person-fill',
                        };
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-outline-light border-secondary-subtle btn-sm dropdown-toggle d-flex align-items-center gap-2 py-1 px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                            <i class="bi {{ $roleIcon }}"></i>
                            <span class="fw-semibold">{{ auth()->user()->name }}</span>
                            <span class="badge {{ $roleClass }} rounded-pill" style="font-size: 0.72rem;">{{ ucfirst(auth()->user()->role) }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2 py-2" style="min-width: 230px;">
                            <li class="px-3 py-2 border-bottom">
                                <div class="text-muted small" style="font-size: 0.75rem;">Login sebagai:</div>
                                <div class="fw-bold text-dark text-truncate">{{ auth()->user()->email }}</div>
                            </li>
                            @if (in_array(auth()->user()->role, ['owner', 'staff', 'admin']))
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</a></li>
                            @else
                                <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('store.my-orders') }}"><i class="bi bi-bag-check me-2 text-success"></i>Pesanan Saya</a></li>
                            @endif
                            <li><a class="dropdown-item py-2 fw-semibold" href="{{ route('home') }}"><i class="bi bi-shop me-2 text-info"></i>Katalog Toko</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button class="dropdown-item py-2 text-danger fw-semibold" type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i>Keluar (Logout)
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    @if(!request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk (Login)
                        </a>
                    @endif
                    @if(!request()->routeIs('register'))
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                            <i class="bi bi-person-plus-fill"></i> Daftar Akun
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Main Container -->
<main class="flex-grow-1 py-4">
    <div class="container-fluid px-lg-4">
        <!-- Flash Alert Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-3 mb-4 rounded-3 p-3" role="alert" style="background-color: #ecfdf5; border-left: 5px solid #10b981 !important;">
                <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                <div class="flex-grow-1 fw-semibold text-dark">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-3 mb-4 rounded-3 p-3" role="alert" style="background-color: #fff1f2; border-left: 5px solid #f43f5e !important;">
                <i class="bi bi-exclamation-octagon-fill fs-4 text-danger"></i>
                <div class="flex-grow-1 fw-semibold text-dark">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 d-flex align-items-center gap-3 mb-4 rounded-3 p-3" role="alert" style="background-color: #f0f9ff; border-left: 5px solid #0284c7 !important;">
                <i class="bi bi-info-circle-fill fs-4 text-info"></i>
                <div class="flex-grow-1 fw-semibold text-dark">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4 rounded-3 p-3" role="alert" style="background-color: #fff1f2; border-left: 5px solid #f43f5e !important;">
                <div class="d-flex align-items-center gap-2 mb-2 fw-bold text-danger">
                    <i class="bi bi-shield-x fs-5"></i> Mohon Periksa Input Formulir:
                </div>
                <ul class="mb-0 ps-4 small text-danger fw-medium">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- Footer -->
<footer class="py-3 text-muted">
    <div class="container-fluid px-lg-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div>
            <strong class="text-dark">RetailSmart POS & Inventory</strong> &copy; {{ date('Y') }}.
        </div>
        <div class="small text-muted">
            Solusi Manajemen Ritel & Penjualan
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
