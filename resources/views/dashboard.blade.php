@extends('layouts.app')
@section('title', 'Dashboard ' . ($isOwner ? 'Owner & Finansial' : 'Operasional Kasir'))

@section('content')
<!-- Header Dashboard & Auto-Update Toolbar -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
            <h3 class="fw-bold mb-0 text-dark">Dashboard {{ $isOwner ? 'Owner' : 'Staff Kasir' }}</h3>
            @if($isOwner)
                <span class="badge badge-role-owner px-3 py-1 rounded-pill"><i class="bi bi-crown-fill me-1"></i>Hak Akses Penuh & Pembatalan</span>
            @else
                <span class="badge badge-role-staff px-3 py-1 rounded-pill"><i class="bi bi-person-badge-fill me-1"></i>Kasir & Stok Masuk</span>
            @endif
        </div>
        <p class="text-muted small mb-0">
            Analitik penjualan real-time, sinkronisasi stok otomatis database, dan laporan finansial ritel.
        </p>
    </div>

    <!-- Live Auto-Update Control Badge -->
    <div class="d-flex align-items-center gap-2 bg-white px-3 py-2 rounded-4 shadow-sm border">
        <span class="live-dot" id="liveIndicator"></span>
        <span class="small fw-semibold text-secondary" id="liveStatusText">Live Sync (10s)</span>
        <button type="button" class="btn btn-sm btn-outline-primary py-1 px-2 rounded-2" id="manualRefreshBtn" title="Perbarui data sekarang">
            <i class="bi bi-arrow-clockwise me-1"></i> Sync
        </button>
        <div class="form-check form-switch ms-1 mb-0">
            <input class="form-check-input" type="checkbox" id="toggleAutoUpdate" checked title="Nyalakan/matikan auto update">
        </div>
        <span class="badge bg-light text-muted border small font-mono ms-1" id="lastUpdatedBadge">
            {{ now()->format('H:i:s') }} WIB
        </span>
    </div>
</div>

<!-- Quick Action Shortcuts -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <a href="{{ route('sales.create') }}" class="btn btn-primary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-cart-plus-fill fs-5"></i>
            <span class="fw-bold">+ Kasir POS Baru</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('purchases.create') }}" class="btn btn-success w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-box-arrow-in-down fs-5"></i>
            <span class="fw-bold">+ Input Stok Masuk</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('products.low-stock') }}" class="btn btn-outline-danger w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2 bg-white">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <span class="fw-bold">Cek Stok Rendah ({{ $lowStockCount }})</span>
        </a>
    </div>
    <div class="col-6 col-md-3">
        <a href="{{ route('home') }}" class="btn btn-outline-secondary w-100 py-3 shadow-sm rounded-3 d-flex align-items-center justify-content-center gap-2 bg-white" target="_blank">
            <i class="bi bi-shop fs-5"></i>
            <span class="fw-bold">Lihat Etalase Toko</span>
        </a>
    </div>
</div>

<!-- KPI Cards Grid (Dynamic Auto-Updating) -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="stat-widget shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Penjualan Hari Ini</span>
                    <div class="fs-4 fw-bold text-primary font-mono mt-1" id="kpiTodaySales">
                        Rp {{ number_format($salesToday, 0, ',', '.') }}
                    </div>
                </div>
                <div class="stat-icon-wrapper stat-icon-primary shadow-xs">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
            </div>
            <div class="small text-muted d-flex align-items-center gap-1">
                <span class="badge badge-subtle-success small"><i class="bi bi-arrow-up-right"></i> Hari Ini</span>
                <span>Transaksi lunas</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-3">
        <div class="stat-widget shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Penjualan Bulan Ini</span>
                    <div class="fs-4 fw-bold text-success font-mono mt-1" id="kpiMonthSales">
                        Rp {{ number_format($salesMonth, 0, ',', '.') }}
                    </div>
                </div>
                <div class="stat-icon-wrapper stat-icon-success shadow-xs">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <div class="small text-muted d-flex align-items-center gap-1">
                <span class="badge badge-subtle-success small">{{ now()->translatedFormat('M Y') }}</span>
                <span>Total omset bulan ini</span>
            </div>
        </div>
    </div>

    @if($isOwner)
        <div class="col-sm-6 col-lg-3">
            <div class="stat-widget shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Estimasi Laba Kotor (Owner)</span>
                        <div class="fs-4 fw-bold font-mono mt-1" style="color: #7c3aed;" id="kpiGrossProfit">
                            Rp {{ number_format($grossProfit, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-purple shadow-xs">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                </div>
                <div class="small text-muted">
                    <span class="badge badge-subtle-primary small">Margin</span>
                    <span>Harga Jual &minus; Harga Beli</span>
                </div>
            </div>
        </div>
    @else
        <div class="col-sm-6 col-lg-3">
            <div class="stat-widget shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Total Transaksi Selesai</span>
                        <div class="fs-4 fw-bold text-info font-mono mt-1" id="kpiSalesCount">
                            {{ $salesCount }} Transaksi
                        </div>
                    </div>
                    <div class="stat-icon-wrapper stat-icon-cyan shadow-xs">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
                <div class="small text-muted">
                    <span class="badge badge-subtle-success small">Sukses</span>
                    <span>Stok otomatis terpotong</span>
                </div>
            </div>
        </div>
    @endif

    <div class="col-sm-6 col-lg-3">
        <div class="stat-widget shadow-sm h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase" style="font-size: 0.72rem; letter-spacing: 0.05em;">Peringatan Stok Rendah</span>
                    <div class="fs-4 fw-bold text-danger font-mono mt-1" id="kpiLowStock">
                        {{ $lowStockCount }} Produk
                    </div>
                </div>
                <div class="stat-icon-wrapper stat-icon-danger shadow-xs">
                    <i class="bi bi-shield-alert"></i>
                </div>
            </div>
            <div class="small text-muted d-flex align-items-center gap-1">
                @if($lowStockCount > 0)
                    <span class="badge badge-subtle-danger small">Perlu Restock</span>
                    <span>Stok &le; batas minimum</span>
                @else
                    <span class="badge badge-subtle-success small">Aman</span>
                    <span>Semua stok cukup</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 3 GRAFIK DINAMIS REAL-TIME (CHART.JS) -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Tren Penjualan 7 Hari Terakhir -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-graph-up text-primary me-2"></i>Tren Penjualan 7 Hari Terakhir (Dinamis)</h6>
                    <small class="text-muted">Grafik otomatis berubah saat ada transaksi penjualan baru atau pembatalan</small>
                </div>
                <span class="badge badge-subtle-primary">Live Sync</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="salesTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart 2: Komposisi Stok per Kategori -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-pie-chart-fill text-success me-2"></i>Komposisi Stok per Kategori</h6>
                    <small class="text-muted">Distribusi jumlah stok barang yang ada di toko</small>
                </div>
                <span class="badge badge-subtle-success">Kategori</span>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="categoryStockChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Chart 3: Top 5 Produk Terlaris -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy-fill text-warning me-2"></i>Top 5 Produk Paling Laris</h6>
                <small class="text-muted">Berdasarkan total unit yang berhasil terjual di kasir & toko</small>
            </div>
            <div class="card-body p-4">
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Peringatan Stok Menipis & Habis (Solusi Bisnis: Produk gaada tapi di display masih ada) -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100 overflow-hidden">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Status Kritis Stok Barang</h6>
                    <small class="text-muted">Produk yang stoknya &le; batas minimum stok</small>
                </div>
                <a href="{{ route('products.low-stock') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3">SKU & Produk</th>
                            <th>Kategori</th>
                            <th class="text-center">Sisa Stok</th>
                            <th class="text-center">Min</th>
                            <th class="text-end pe-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="lowStockTableBody">
                        @forelse ($lowStock as $p)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-dark">{{ $p->name }}</div>
                                    <span class="font-mono text-muted small">{{ $p->sku }}</span>
                                </td>
                                <td><span class="badge bg-light text-secondary border">{{ $p->category->name }}</span></td>
                                <td class="text-center">
                                    @if ($p->stock == 0)
                                        <span class="badge badge-subtle-danger">HABIS (0)</span>
                                    @else
                                        <span class="badge badge-subtle-warning font-mono fw-bold">{{ $p->stock }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-muted font-mono">{{ $p->minimum_stock }}</td>
                                <td class="text-end pe-3">
                                    <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-success py-1 px-2 rounded-2" title="Stok Masuk">
                                        <i class="bi bi-plus-lg"></i> Restock
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-check-circle-fill text-success me-1"></i> Semua stok produk dalam keadaan aman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- KHUSUS OWNER: AUDIT TRANSAKSI YANG DIBATALKAN -->
@if($isOwner)
<div class="card shadow-sm border-0 mb-4 overflow-hidden">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-danger">
                <i class="bi bi-shield-slash-fill me-2"></i>Log Audit Pembatalan Transaksi (Khusus Owner)
            </h6>
            <small class="text-muted">
                Riwayat transaksi yang dibatalkan oleh Owner beserta catatan alasan dan pengembalian stok ke display.
            </small>
        </div>
        <span class="badge badge-subtle-danger">
            Total Dibatalkan: <span id="kpiCancelledCount" class="font-mono fw-bold">{{ $cancelledCount }}</span>
        </span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">No. Transaksi</th>
                    <th>Pembeli</th>
                    <th>Total</th>
                    <th>Dibatalkan Oleh</th>
                    <th>Alasan Pembatalan</th>
                    <th>Waktu Pembatalan</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cancelledSales as $cs)
                    <tr>
                        <td class="ps-3 fw-bold font-mono text-danger">{{ $cs->sale_number }}</td>
                        <td>{{ $cs->customer_name ?: 'Pelanggan Umum' }}</td>
                        <td class="fw-bold font-mono">Rp {{ number_format($cs->total, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-role-owner rounded-pill px-2 py-1" style="font-size: 0.72rem;">
                                <i class="bi bi-crown-fill me-1"></i>{{ $cs->cancelledBy->name ?? 'Owner' }}
                            </span>
                        </td>
                        <td class="small text-muted" style="max-width: 220px;">
                            {{ Str::limit($cs->cancellation_reason ?: 'Dibatalkan oleh Owner', 60) }}
                        </td>
                        <td class="small text-muted font-mono">
                            {{ $cs->cancelled_at ? $cs->cancelled_at->translatedFormat('d M Y, H:i') : '-' }}
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('sales.show', $cs) }}" class="btn btn-sm btn-outline-secondary rounded-2">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            Belum ada transaksi yang dibatalkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- TRANSAKSI TERAKHIR (STAFF & OWNER) -->
<div class="card shadow-sm border-0 mb-4 overflow-hidden">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-2 text-primary"></i>Transaksi Penjualan Terkini</h6>
            <small class="text-muted">Pencatatan kasir dan toko ritel secara otomatis</small>
        </div>
        <a href="{{ route('sales.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="table table-custom align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-3">No. Transaksi</th>
                    <th>Waktu</th>
                    <th>Pelanggan</th>
                    <th>Petugas Kasir</th>
                    <th>Total Belanja</th>
                    <th>Metode</th>
                    <th class="text-center">Status</th>
                    <th class="text-end pe-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentSales as $sale)
                    <tr>
                        <td class="ps-3 fw-bold font-mono text-primary">{{ $sale->sale_number }}</td>
                        <td class="small text-muted font-mono">{{ $sale->sold_at->translatedFormat('d M Y, H:i') }}</td>
                        <td class="fw-semibold">{{ $sale->customer_name ?: 'Pelanggan Walk-in' }}</td>
                        <td class="small text-secondary">{{ $sale->user->name ?? 'Sistem' }}</td>
                        <td class="fw-bold font-mono text-dark">Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                        <td><span class="badge bg-light text-secondary border text-uppercase">{{ $sale->payment_method }}</span></td>
                        <td class="text-center">
                            @if ($sale->status === 'completed')
                                <span class="badge badge-subtle-success"><i class="bi bi-check-circle-fill me-1"></i>Selesai</span>
                            @else
                                <span class="badge badge-subtle-danger"><i class="bi bi-x-circle-fill me-1"></i>Dibatalkan</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-primary rounded-2">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi penjualan tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let salesTrendChartInstance = null;
    let categoryStockChartInstance = null;
    let topProductsChartInstance = null;
    let autoUpdateInterval = null;

    document.addEventListener('DOMContentLoaded', () => {
        initCharts();
        fetchLiveStats();
        startAutoUpdate();

        const toggleSwitch = document.getElementById('toggleAutoUpdate');
        toggleSwitch.addEventListener('change', function() {
            if (this.checked) {
                startAutoUpdate();
                document.getElementById('liveStatusText').innerText = 'Live Sync (10s)';
                document.getElementById('liveIndicator').style.display = 'inline-block';
            } else {
                stopAutoUpdate();
                document.getElementById('liveStatusText').innerText = 'Live Sync: Mati';
                document.getElementById('liveIndicator').style.display = 'none';
            }
        });

        document.getElementById('manualRefreshBtn').addEventListener('click', () => {
            fetchLiveStats(true);
        });
    });

    function startAutoUpdate() {
        if (autoUpdateInterval) clearInterval(autoUpdateInterval);
        autoUpdateInterval = setInterval(() => {
            fetchLiveStats(false);
        }, 10000);
    }

    function stopAutoUpdate() {
        if (autoUpdateInterval) clearInterval(autoUpdateInterval);
        autoUpdateInterval = null;
    }

    function initCharts() {
        // 1. Chart Tren Penjualan
        const ctxSales = document.getElementById('salesTrendChart').getContext('2d');
        const salesGradient = ctxSales.createLinearGradient(0, 0, 0, 260);
        salesGradient.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
        salesGradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

        salesTrendChartInstance = new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Pendapatan (Rp)',
                    data: [],
                    borderColor: '#4f46e5',
                    backgroundColor: salesGradient,
                    fill: true,
                    tension: 0.35,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#4f46e5',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4.5,
                    pointHoverRadius: 6.5,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { family: 'Plus Jakarta Sans', size: 13 },
                        bodyFont: { family: 'JetBrains Mono', size: 12 },
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' Pendapatan: Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { family: 'JetBrains Mono', size: 11 },
                            callback: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + ' Jt';
                                if (val >= 1000) return (val / 1000).toFixed(0) + ' Rb';
                                return val;
                            }
                        },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    x: {
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });

        // 2. Chart Komposisi Kategori
        const ctxCategory = document.getElementById('categoryStockChart').getContext('2d');
        categoryStockChartInstance = new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#4f46e5', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#f43f5e'
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { family: 'Plus Jakarta Sans', size: 12 }, padding: 14 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': ' + ctx.raw + ' unit stok';
                            }
                        }
                    }
                },
                cutout: '68%'
            }
        });

        // 3. Chart Top Produk Terlaris
        const ctxTop = document.getElementById('topProductsChart').getContext('2d');
        topProductsChartInstance = new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Unit Terjual',
                    data: [],
                    backgroundColor: 'rgba(245, 158, 11, 0.85)',
                    borderColor: '#d97706',
                    borderWidth: 1,
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' Terjual: ' + ctx.raw + ' unit';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { precision: 0, font: { family: 'JetBrains Mono', size: 11 } },
                        grid: { color: 'rgba(226, 232, 240, 0.6)' }
                    },
                    y: {
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11 } },
                        grid: { display: false }
                    }
                }
            }
        });
    }

    function fetchLiveStats(showIndicator = false) {
        const refreshBtn = document.getElementById('manualRefreshBtn');
        if (showIndicator) refreshBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sync...';

        fetch("{{ route('api.dashboard.stats') }}", {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                updateDashboardDOM(data);
            }
        })
        .catch(err => {
            console.error('Auto-update sync error:', err);
        })
        .finally(() => {
            if (showIndicator) refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i> Sync';
        });
    }

    function updateDashboardDOM(data) {
        const cards = data.cards;
        if (cards) {
            const kpiToday = document.getElementById('kpiTodaySales');
            const kpiMonth = document.getElementById('kpiMonthSales');
            const kpiProfit = document.getElementById('kpiGrossProfit');
            const kpiSales = document.getElementById('kpiSalesCount');
            const kpiLow = document.getElementById('kpiLowStock');
            const kpiCancelled = document.getElementById('kpiCancelledCount');

            if (kpiToday) kpiToday.innerText = cards.todaySales;
            if (kpiMonth) kpiMonth.innerText = cards.monthSales;
            if (kpiProfit) kpiProfit.innerText = cards.grossProfit;
            if (kpiSales) kpiSales.innerText = cards.salesCount + ' Transaksi';
            if (kpiLow) kpiLow.innerText = cards.lowStockCount + ' Produk';
            if (kpiCancelled) kpiCancelled.innerText = cards.cancelledCount;
        }

        document.getElementById('lastUpdatedBadge').innerText = data.updated_at;

        if (salesTrendChartInstance && data.charts.salesTrend) {
            salesTrendChartInstance.data.labels = data.charts.salesTrend.labels;
            salesTrendChartInstance.data.datasets[0].data = data.charts.salesTrend.data;
            salesTrendChartInstance.update();
        }

        if (categoryStockChartInstance && data.charts.categoryStock) {
            categoryStockChartInstance.data.labels = data.charts.categoryStock.labels;
            categoryStockChartInstance.data.datasets[0].data = data.charts.categoryStock.data;
            categoryStockChartInstance.update();
        }

        if (topProductsChartInstance && data.charts.topProducts) {
            topProductsChartInstance.data.labels = data.charts.topProducts.labels;
            topProductsChartInstance.data.datasets[0].data = data.charts.topProducts.data;
            topProductsChartInstance.update();
        }
    }
</script>
@endpush
