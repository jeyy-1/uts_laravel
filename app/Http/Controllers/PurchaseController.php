<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with(['supplier', 'user', 'cancelledBy'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('purchased_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('purchased_at', '<=', $d))
            ->when($request->q, fn ($q, $s) => $q->where('purchase_number', 'like', "%{$s}%"))
            ->latest('purchased_at')
            ->paginate(15)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        return view('purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'notes' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ], [
            'supplier_id.required' => 'Pilih supplier penyedia barang.',
            'items.required' => 'Pilih minimal satu barang yang masuk.',
            'items.*.quantity.min' => 'Jumlah barang masuk minimal 1.',
        ]);

        try {
            $purchase = DB::transaction(function () use ($request) {
                $purchase = Purchase::create([
                    'supplier_id' => $request->supplier_id,
                    'user_id' => auth()->id(),
                    'purchase_number' => 'PO-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(4)),
                    'total' => 0,
                    'notes' => $request->notes,
                    'status' => 'completed',
                    'purchased_at' => now(),
                ]);

                $total = 0;

                foreach ($request->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item['product_id']);
                    $qty = (int) $item['quantity'];
                    $cost = (float) $item['unit_cost'];
                    $subtotal = $cost * $qty;

                    $purchase->items()->create([
                        'product_id' => $product->id,
                        'quantity' => $qty,
                        'unit_cost' => $cost,
                        'subtotal' => $subtotal,
                    ]);

                    // Otomatis tambah stok produk di etalase dan database
                    $product->increment('stock', $qty);
                    $total += $subtotal;
                }

                $purchase->update(['total' => $total]);

                return $purchase;
            });

            return redirect()->route('purchases.show', $purchase)
                ->with('success', 'Stok Masuk (Pembelian) berhasil disimpan. Stok produk di display otomatis bertambah!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses stok masuk: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Purchase $purchase)
    {
        return view('purchases.show', [
            'purchase' => $purchase->load(['supplier', 'user', 'cancelledBy', 'items.product'])
        ]);
    }

    /**
     * Pembatalan Transaksi Pembelian (Hanya Owner)
     */
    public function cancel(Request $request, Purchase $purchase)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Akses Ditolak: Hanya Owner yang berhak membatalkan transaksi pembelian/stok masuk.');
        }

        if ($purchase->status !== 'completed') {
            return back()->withErrors(['cancel' => 'Transaksi sudah berstatus ' . $purchase->status]);
        }

        $request->validate([
            'cancellation_reason' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::transaction(function () use ($purchase, $request) {
                foreach ($purchase->items as $item) {
                    $product = Product::lockForUpdate()->find($item->product_id);

                    // Stok tidak boleh menjadi negatif jika barang sudah terlanjur dijual
                    if ($product && $product->stock < $item->quantity) {
                        throw ValidationException::withMessages([
                            'cancel' => "Tidak bisa dibatalkan: stok '{$product->name}' tersisa {$product->stock}, sedangkan barang masuk sebanyak {$item->quantity} sudah terlanjur terjual.",
                        ]);
                    }

                    if ($product) {
                        $product->decrement('stock', $item->quantity);
                    }
                }

                $purchase->update([
                    'status' => 'cancelled',
                    'cancellation_reason' => $request->cancellation_reason,
                    'cancelled_by' => auth()->id(),
                    'cancelled_at' => now(),
                ]);
            });

            return back()->with('success', 'Stok masuk berhasil dibatalkan oleh Owner. Stok produk telah dikurangi kembali.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->withErrors(['cancel' => 'Gagal membatalkan transaksi: ' . $e->getMessage()]);
        }
    }
}
