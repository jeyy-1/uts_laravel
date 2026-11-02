<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RetailBusinessProblemsTest extends TestCase
{
    use RefreshDatabase;

    private function createRoleUser(string $role): User
    {
        return User::create([
            'name' => ucfirst($role) . ' User',
            'email' => "{$role}@example.com",
            'password' => 'password',
            'role' => $role,
        ]);
    }

    private function createProduct(string $name, int $stock, int $price, int $min = 2): Product
    {
        $cat = Category::firstOrCreate(['name' => 'Makanan']);
        return Product::create([
            'category_id' => $cat->id,
            'sku' => 'SKU-' . rand(1000, 9999),
            'name' => $name,
            'description' => "Deskripsi {$name}",
            'purchase_price' => $price * 0.7,
            'selling_price' => $price,
            'stock' => $stock,
            'minimum_stock' => $min,
        ]);
    }

    /**
     * Requirement: "index itu untuk halaman utama, jangan letakkan data sensitive di file index"
     */
    public function test_storefront_index_page_loads_with_safe_public_data(): void
    {
        $product = $this->createProduct('Biskuit Cokelat', 10, 8500);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Biskuit Cokelat');
        $response->assertSee('RetailSmart');
        // Pastikan tidak ada data sensitif bocor di index
        $response->assertDontSee('purchase_price');
        $response->assertDontSee('APP_KEY');
    }

    /**
     * Requirement: "user bisa beli multi barang & database diubah secara otomatis (dinamis)"
     */
    public function test_user_can_purchase_multi_items_and_stocks_decrement_automatically(): void
    {
        $prodA = $this->createProduct('Barang A', 20, 5000);
        $prodB = $this->createProduct('Barang B', 15, 10000);

        $response = $this->post('/store/checkout', [
            'customer_name' => 'Pembeli Multi Barang',
            'payment_method' => 'qris',
            'items' => [
                ['product_id' => $prodA->id, 'quantity' => 3],
                ['product_id' => $prodB->id, 'quantity' => 2],
            ]
        ]);

        $response->assertRedirect();

        // Stok barang A harus berkurang dari 20 -> 17
        $this->assertSame(17, $prodA->fresh()->stock);
        // Stok barang B harus berkurang dari 15 -> 13
        $this->assertSame(13, $prodB->fresh()->stock);

        // Total transaksi harus: (3 * 5000) + (2 * 10000) = 35000
        $this->assertDatabaseHas('sales', [
            'customer_name' => 'Pembeli Multi Barang',
            'total' => 35000,
            'status' => 'completed',
        ]);
    }

    /**
     * Requirement: "produk gaada tapi di display masih ada -> stok habis tidak boleh dibeli"
     */
    public function test_out_of_stock_product_cannot_be_purchased(): void
    {
        $emptyProduct = $this->createProduct('Barang Habis', 0, 10000);

        $response = $this->post('/store/checkout', [
            'customer_name' => 'Pembeli',
            'payment_method' => 'cash',
            'items' => [
                ['product_id' => $emptyProduct->id, 'quantity' => 1]
            ]
        ]);

        $response->assertSessionHasErrors('stock');
        $this->assertSame(0, $emptyProduct->fresh()->stock);
    }

    /**
     * Requirement: "alur pembatalan transaksi hanya owner, jadi saat pembeli udh co staf/admin
     * dan database taunya barang udh dibeli dan stok berkurang namun saat pembeli mau batalin
     * barangnya hanya owner yg bisa ngakses trs stoknya balek ke display lagi"
     */
    public function test_only_owner_can_cancel_sale_transaction_and_staff_is_forbidden(): void
    {
        $owner = $this->createRoleUser('owner');
        $staff = $this->createRoleUser('staff');

        $p = $this->createProduct('Barang Uji', 10, 5000);

        // Staf melayani penjualan (checkout)
        $this->actingAs($staff)->post('/sales', [
            'items' => [['product_id' => $p->id, 'quantity' => 4]],
            'payment_method' => 'cash',
        ]);
        $sale = Sale::first();

        // Stok berkurang menjadi 6
        $this->assertSame(6, $p->fresh()->stock);

        // Pembeli ingin membatalkan. Staf MENCOBA membatalkan -> Harus DITOLAK (403 Forbidden)
        $this->actingAs($staff)->post("/sales/{$sale->id}/cancel", [
            'cancellation_reason' => 'Staf mencoba membatalkan'
        ])->assertForbidden();

        // Stok MASIH 6 (tidak berubah karena staf tidak punya hak)
        $this->assertSame(6, $p->fresh()->stock);
        $this->assertSame('completed', $sale->fresh()->status);

        // Sekarang OWNER yang membatalkan transaksi
        $this->actingAs($owner)->post("/sales/{$sale->id}/cancel", [
            'cancellation_reason' => 'Pembeli batal belanja dan barang dikembalikan ke toko'
        ])->assertRedirect();

        // Status berubah jadi cancelled
        $this->assertSame('cancelled', $sale->fresh()->status);
        $this->assertSame($owner->id, $sale->fresh()->cancelled_by);

        // STOK BALIK KE DISPLAY LAGI (Kembali menjadi 10!)
        $this->assertSame(10, $p->fresh()->stock);
    }

    /**
     * Requirement: "login eror ada messagenya"
     */
    public function test_login_error_shows_message(): void
    {
        $this->createRoleUser('owner');

        $response = $this->post('/login', [
            'email' => 'owner@example.com',
            'password' => 'password_salah_123',
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertSessionHas('error');
    }

    /**
     * Requirement: "grafik langsung bisa berubah otomatis juga, datanya auto update"
     */
    public function test_dashboard_stats_api_returns_dynamic_chart_data(): void
    {
        $staff = $this->createRoleUser('staff');
        $this->createProduct('Mie Instan', 50, 3500);

        $response = $this->actingAs($staff)->get('/api/dashboard/stats');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'updated_at',
            'cards' => ['todaySales', 'monthSales', 'salesCount', 'lowStockCount'],
            'charts' => [
                'salesTrend' => ['labels', 'data'],
                'topProducts' => ['labels', 'data'],
                'categoryStock' => ['labels', 'data'],
            ]
        ]);
    }
}
