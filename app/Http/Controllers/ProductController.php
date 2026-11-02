<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    private function rules(?Product $product = null): array
    {
        return [
            'sku' => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:150'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ];
    }

    public function index(Request $request)
    {
        $products = Product::with('category')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('sku', 'like', "%{$s}%")->orWhere('name', 'like', "%{$s}%")))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function lowStock()
    {
        return view('products.low-stock', [
            'products' => Product::with('category')
                ->whereColumn('stock', '<=', 'minimum_stock')
                ->orderBy('stock')->paginate(10),
        ]);
    }

    public function show(Product $product)
    {
        return view('products.show', ['product' => $product->load('category')]);
    }

    public function create()
    {
        return view('products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules());
        $data['stock'] = 0; // stok awal 0; bertambah lewat transaksi pembelian

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk ditambahkan.');
    }

    public function edit(Product $product)
    {
        return view('products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->update($request->validate($this->rules($product)));

        return redirect()->route('products.index')->with('success', 'Produk diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->withErrors(['delete' => 'Produk sudah dipakai transaksi, tidak bisa dihapus.']);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk dihapus.');
    }
}
