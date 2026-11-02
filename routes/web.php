<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Hak akses / Authorization Gates
Gate::define('owner', fn ($user) => in_array($user->role, ['owner', 'admin']));
Gate::define('admin', fn ($user) => in_array($user->role, ['owner', 'admin']));
Gate::define('staff', fn ($user) => in_array($user->role, ['owner', 'admin', 'staff']));
Gate::define('customer', fn ($user) => $user->role === 'user');

// ---- 1. HALAMAN UTAMA (Storefront Ritel Publik & Belanja Multi-Barang) ----
// "index itu untuk halaman utama, jangan letakkan data sensitive di file index"
Route::get('/', [StoreController::class, 'index'])->name('home');
Route::post('/store/checkout', [StoreController::class, 'checkout'])->name('store.checkout');
Route::get('/store/receipt/{sale}', [StoreController::class, 'receipt'])->name('store.receipt');
Route::get('/my-orders', [StoreController::class, 'myOrders'])->name('store.my-orders');

// ---- 2. AUTENTIKASI (Login, Register, Logout) ----
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// ---- 3. AREA SISTEM TERPROTEKSI (Staff & Owner) ----
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // ---- 4. KHUSUS OWNER (Alur Pembatalan Transaksi & Manajemen Utama) ----
    // "alur pembatalan transaksi hanya owner, jadi saat pembeli udh co staf/admin
    //  dan database taunya barang udh dibeli dan stok berkurang namun saat pembeli mau batalin
    //  barangnya hanya owner yg bisa ngakses trs stoknya balek ke display lagi"
    // Ditaruh sebelum route resource show agar create route tidak tertimpa show route!
    Route::middleware('can:owner')->group(function () {
        // Pembatalan Penjualan & Pembelian (Mengembalikan stok ke etalase display secara otomatis)
        Route::post('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::post('/purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');

        // Manajemen Master Data Produk, Kategori, Supplier, & Pengguna
        Route::resource('products', ProductController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['show']);
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'destroy']);
    });

    // Dashboard & Operasional Staff/Kasir
    Route::middleware('can:staff')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/api/dashboard/stats', [DashboardController::class, 'stats'])->name('api.dashboard.stats');

        // Transaksi Penjualan (Kasir POS)
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

        // Stok Masuk (Stock In / Purchases)
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Pemantauan Stok & Produk
        Route::resource('products', ProductController::class)->only(['index', 'show']);
        Route::get('/low-stock', [ProductController::class, 'lowStock'])->name('products.low-stock');
    });
});
