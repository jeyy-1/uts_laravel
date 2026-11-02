<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Jika user adalah pelanggan/customer biasa, arahkan ke etalase/riwayat belanja
        if ($user && $user->role === 'user') {
            return redirect()->route('store.my-orders')
                ->with('info', 'Selamat datang! Anda masuk sebagai pelanggan. Berikut riwayat transaksi Anda.');
        }

        $completedSales = Sale::where('status', 'completed');

        // Statistik Penjualan
        $todaySales = (clone $completedSales)->whereDate('sold_at', today())->sum('total');
        $monthSales = (clone $completedSales)
            ->whereYear('sold_at', now()->year)
            ->whereMonth('sold_at', now()->month)
            ->sum('total');

        $completedCount = Sale::where('status', 'completed')->count();
        $cancelledCount = Sale::where('status', 'cancelled')->count();
        $purchasesCount = Purchase::count();

        // Estimasi Laba Kotor (Gross Profit) untuk Owner
        $grossProfit = 0;
        if ($user->role === 'owner') {
            $grossProfit = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed'))
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->sum(DB::raw('(sale_items.unit_price - products.purchase_price) * sale_items.quantity'));
        }

        // Low stock alerts
        $lowStock = Product::with('category')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock')
            ->limit(6)
            ->get();
        $lowStockCount = Product::whereColumn('stock', '<=', 'minimum_stock')->count();

        // Transaksi terakhir
        $recentSales = Sale::with('user')->latest('sold_at')->limit(8)->get();

        // Transaksi yang dibatalkan (khusus audit Owner)
        $cancelledSales = Sale::with(['user', 'cancelledBy'])
            ->where('status', 'cancelled')
            ->latest('cancelled_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'isOwner' => $user->role === 'owner',
            'totalProducts' => Product::count(),
            'salesToday' => $todaySales,
            'salesMonth' => $monthSales,
            'grossProfit' => $grossProfit,
            'salesCount' => $completedCount,
            'cancelledCount' => $cancelledCount,
            'purchasesCount' => $purchasesCount,
            'lowStock' => $lowStock,
            'lowStockCount' => $lowStockCount,
            'recentSales' => $recentSales,
            'cancelledSales' => $cancelledSales,
        ]);
    }

    /**
     * Endpoint API JSON untuk Grafik & Statistik Realtime (Auto-Update).
     * Dapat dipanggil via fetch() setiap interval detik secara otomatis.
     */
    public function stats(): JsonResponse
    {
        $user = auth()->user();

        // 1. Data Grafik Tren Penjualan 7 Hari Terakhir
        $salesTrendDays = [];
        $salesTrendValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = $date->translatedFormat('d M');

            $dailyTotal = Sale::where('status', 'completed')
                ->whereDate('sold_at', $date)
                ->sum('total');

            $salesTrendDays[] = $dayLabel;
            $salesTrendValues[] = (float) $dailyTotal;
        }

        // 2. Data Grafik Top 5 Produk Terlaris
        $topProducts = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed'))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with('product:id,name')
            ->get();

        $topProductLabels = [];
        $topProductQuantities = [];
        foreach ($topProducts as $tp) {
            $topProductLabels[] = $tp->product->name ?? 'Produk #' . $tp->product_id;
            $topProductQuantities[] = (int) $tp->total_qty;
        }

        // 3. Data Komposisi Stok per Kategori
        $categories = Category::withSum('products', 'stock')->get();
        $catLabels = [];
        $catStock = [];
        foreach ($categories as $cat) {
            $catLabels[] = $cat->name;
            $catStock[] = (int) ($cat->products_sum_stock ?? 0);
        }

        // 4. Counter Angka Terkini
        $completedSales = Sale::where('status', 'completed');
        $todaySales = (clone $completedSales)->whereDate('sold_at', today())->sum('total');
        $monthSales = (clone $completedSales)
            ->whereYear('sold_at', now()->year)
            ->whereMonth('sold_at', now()->month)
            ->sum('total');

        $grossProfit = 0;
        if ($user && $user->role === 'owner') {
            $grossProfit = SaleItem::whereHas('sale', fn ($q) => $q->where('status', 'completed'))
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->sum(DB::raw('(sale_items.unit_price - products.purchase_price) * sale_items.quantity'));
        }

        return response()->json([
            'status' => 'success',
            'updated_at' => now()->format('H:i:s') . ' WIB',
            'cards' => [
                'todaySales' => 'Rp ' . number_format($todaySales, 0, ',', '.'),
                'monthSales' => 'Rp ' . number_format($monthSales, 0, ',', '.'),
                'grossProfit' => 'Rp ' . number_format($grossProfit, 0, ',', '.'),
                'salesCount' => Sale::where('status', 'completed')->count(),
                'cancelledCount' => Sale::where('status', 'cancelled')->count(),
                'totalProducts' => Product::count(),
                'lowStockCount' => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
            ],
            'charts' => [
                'salesTrend' => [
                    'labels' => $salesTrendDays,
                    'data' => $salesTrendValues,
                ],
                'topProducts' => [
                    'labels' => $topProductLabels,
                    'data' => $topProductQuantities,
                ],
                'categoryStock' => [
                    'labels' => $catLabels,
                    'data' => $catStock,
                ]
            ]
        ]);
    }
}
