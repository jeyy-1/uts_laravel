<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StoreController extends Controller
{
    /**
     * Halaman Utama Ritel (Storefront / Display Produk Publik).
     * Aman: TIDAK memuat data sensitif (tanpa harga modal/beli, tanpa data supplier, tanpa kredensial).
     */
    public function index(Request $request)
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        $query = Product::with('category')
            ->select('id', 'category_id', 'sku', 'name', 'description', 'selling_price', 'stock', 'minimum_stock')
            ->orderByRaw('stock > 0 DESC') // Tampilkan produk berstok terlebih dahulu
            ->orderBy('name');

        if ($request->filled('q')) {
            $s = $request->q;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('sku', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->paginate(12)->withQueryString();

        return view('store.index', compact('products', 'categories'));
    }

    /**
     * Checkout Multi-Barang oleh Pelanggan (User).
     * Database diubah secara otomatis (stok berkurang seketika dengan locking atomik).
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'customer_name' => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,qris,transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'customer_name.required' => 'Mohon masukkan nama pembeli.',
            'items.required' => 'Keranjang belanja masih kosong.',
            'items.min' => 'Pilih minimal satu barang untuk checkout.',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $saleNumber = 'SL-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4));

                // Jika user sedang login (misal peran 'user' atau 'staff'), hubungkan ID-nya, jika tidak fallback ke user publik/pertama
                $userId = auth()->check() ? auth()->id() : (Product::first()->category->id ?? 1);

                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'payment_method' => $request->payment_method,
                    'sale_number' => $saleNumber,
                    'total' => 0,
                    'status' => 'completed',
                    'sold_at' => now(),
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    // Lock baris produk di DB untuk mencegah overselling / race condition
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];

                    // Validasi ketersediaan stok
                    if ($product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'stock' => "Maaf, stok untuk '{$product->name}' tidak mencukupi (tersedia: {$product->stock}, diminta: {$qty}).",
                        ]);
                    }

                    $subtotal = $product->selling_price * $qty;

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Stok dikurangi secara otomatis dan langsung terupdate di database
                    $product->decrement('stock', $qty);
                    $total += $subtotal;
                }

                $sale->update(['total' => $total]);

                return $sale;
            });

            return redirect()->route('store.receipt', $sale->id)
                ->with('success', 'Checkout berhasil! Stok telah otomatis terpotong dari sistem toko.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['checkout' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Tampilan Struk / Nota Pembelian Digital.
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['items.product', 'user']);
        return view('store.receipt', compact('sale'));
    }

    /**
     * Riwayat Pesanan untuk Pelanggan yang Login.
     */
    public function myOrders()
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('info', 'Silakan login untuk melihat riwayat pesanan Anda.');
        }

        $sales = Sale::with('items.product')
            ->where('user_id', auth()->id())
            ->orWhere('customer_name', auth()->user()->name)
            ->latest('sold_at')
            ->paginate(10);

        return view('store.my-orders', compact('sales'));
    }
}
