<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $sales = Sale::with(['user', 'cancelledBy'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('sold_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('sold_at', '<=', $d))
            ->when($request->q, fn ($q, $s) => $q->where(function ($query) use ($s) {
                $query->where('sale_number', 'like', "%{$s}%")
                      ->orWhere('customer_name', 'like', "%{$s}%");
            }))
            ->latest('sold_at')
            ->paginate(15)
            ->withQueryString();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        // Hanya tampilkan produk yang memiliki stok > 0 untuk POS Kasir
        return view('sales.create', [
            'products' => Product::where('stock', '>', 0)->orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['nullable', 'in:cash,qris,transfer'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ], [
            'items.required' => 'Pilih minimal satu produk untuk transaksi.',
            'items.*.quantity.min' => 'Jumlah barang minimal 1 unit.',
        ]);

        try {
            $sale = DB::transaction(function () use ($request) {
                $sale = Sale::create([
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name ?: 'Pelanggan Walk-in',
                    'payment_method' => $request->payment_method ?: 'cash',
                    'sale_number' => 'SL-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                    'total' => 0,
                    'status' => 'completed',
                    'sold_at' => now(),
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    // Kunci baris produk dengan lockForUpdate untuk mencegah race condition
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];

                    if ($product->stock < $qty) {
                        throw ValidationException::withMessages([
                            'items' => "Stok '{$product->name}' tidak cukup! Tersedia: {$product->stock}, Diminta: {$qty}.",
                        ]);
                    }

                    // Ambil harga asli dari database (mencegah manipulasi harga dari client)
                    $subtotal = $product->selling_price * $qty;

                    $sale->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ]);

                    // Otomatis kurangi stok produk di database
                    $product->decrement('stock', $qty);
                    $total += $subtotal;
                }

                $sale->update(['total' => $total]);

                return $sale;
            });

            return redirect()->route('sales.show', $sale)
                ->with('success', 'Transaksi penjualan berhasil disimpan. Stok produk telah otomatis terpotong!');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses transaksi penjualan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Sale $sale)
    {
        return view('sales.show', [
            'sale' => $sale->load(['user', 'cancelledBy', 'items.product'])
        ]);
    }

    /**
     * ALUR PEMBATALAN TRANSAKSI:
     * "adapun juga alur pembatalan transaksi hanya owner, jadi saat pembeli udh co staf/admin
     * dan database taunya barang udh dibeli dan stok berkurang namun saat pembeli mau batalin
     * barangnya hanya owner yg bisa ngakses trs stoknya balek ke display lagi"
     */
    public function cancel(Request $request, Sale $sale)
    {
        // Proteksi tingkat server: HANYA OWNER / ADMIN yang berwenang
        if (! in_array(auth()->user()->role, ['owner', 'admin'])) {
            abort(403, 'Akses Ditolak: Hanya Owner yang berhak membatalkan transaksi dan mengembalikan stok barang.');
        }

        if ($sale->status !== 'completed') {
            return back()->withErrors(['cancel' => 'Transaksi ini sudah berstatus ' . $sale->status . ' dan tidak dapat dibatalkan lagi.']);
        }

        $request->validate([
            'cancellation_reason' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($sale, $request) {
                // Kembalikan semua stok barang yang ada pada transaksi ini secara otomatis
                foreach ($sale->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }

                // Catat metadata pembatalan untuk audit trail
                $sale->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $request->cancellation_reason ?: 'Dibatalkan oleh Owner',
                    'cancelled_by' => auth()->id(),
                    'cancelled_at' => now(),
                ]);
            });

            return back()->with('success', 'Transaksi ' . $sale->sale_number . ' BERHASIL DIBATALKAN oleh Owner. Semua stok barang telah otomatis dikembalikan ke etalase display!');
        } catch (\Exception $e) {
            return back()->withErrors(['cancel' => 'Terjadi kesalahan saat membatalkan transaksi: ' . $e->getMessage()]);
        }
    }
}
