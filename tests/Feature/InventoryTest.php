<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role = 'staff'): User
    {
        return User::create([
            'name' => ucfirst($role), 'email' => "$role@test.com",
            'password' => 'password', 'role' => $role,
        ]);
    }

    private function product(int $stock = 10, string $sku = 'SKU-1'): Product
    {
        $cat = Category::firstOrCreate(['name' => 'Umum']);

        return Product::create([
            'category_id' => $cat->id, 'sku' => $sku, 'name' => "Produk $sku",
            'purchase_price' => 1000, 'selling_price' => 2000,
            'stock' => $stock, 'minimum_stock' => 2,
        ]);
    }

    public function test_guest_cannot_open_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->user();
        $this->post('/login', ['email' => 'staff@test.com', 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_duplicate_sku_and_negative_price_are_rejected(): void
    {
        $admin = $this->user('admin');
        $p = $this->product();

        $this->actingAs($admin)->post('/products', [
            'sku' => $p->sku, 'name' => 'X', 'category_id' => $p->category_id,
            'purchase_price' => -1, 'selling_price' => 100, 'minimum_stock' => 1,
        ])->assertSessionHasErrors(['sku', 'purchase_price']);
    }

    public function test_purchase_increases_stock(): void
    {
        $staff = $this->user();
        $p = $this->product(10);
        $s = Supplier::create(['name' => 'Sup']);

        $this->actingAs($staff)->post('/purchases', [
            'supplier_id' => $s->id,
            'items' => [['product_id' => $p->id, 'quantity' => 5, 'unit_cost' => 1000]],
        ])->assertRedirect();

        $this->assertSame(15, $p->fresh()->stock);
    }

    public function test_sale_decreases_stock_and_total_is_calculated_by_server(): void
    {
        $staff = $this->user();
        $p = $this->product(10);

        $this->actingAs($staff)->post('/sales', [
            'items' => [['product_id' => $p->id, 'quantity' => 3, 'unit_price' => 1]], // unit_price dari browser diabaikan
        ])->assertRedirect();

        $this->assertSame(7, $p->fresh()->stock);
        $this->assertDatabaseHas('sales', ['total' => 6000]);
    }

    public function test_sale_with_insufficient_stock_is_rejected_without_changes(): void
    {
        $staff = $this->user();
        $p = $this->product(4);

        $this->actingAs($staff)->post('/sales', [
            'items' => [['product_id' => $p->id, 'quantity' => 6]],
        ])->assertSessionHasErrors('items');

        $this->assertSame(4, $p->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_failed_item_rolls_back_whole_transaction(): void
    {
        $staff = $this->user();
        $a = $this->product(10, 'A');
        $b = $this->product(1, 'B');

        $this->actingAs($staff)->post('/sales', [
            'items' => [
                ['product_id' => $a->id, 'quantity' => 2],
                ['product_id' => $b->id, 'quantity' => 5], // gagal
            ],
        ])->assertSessionHasErrors('items');

        $this->assertSame(10, $a->fresh()->stock);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
    }

    public function test_staff_cannot_access_admin_functions(): void
    {
        $staff = $this->user();

        $this->actingAs($staff)->get('/users')->assertForbidden();
        $this->actingAs($staff)->get('/categories')->assertForbidden();
        $this->actingAs($staff)->get('/products/create')->assertForbidden();
    }

    public function test_cancelling_sale_restores_stock(): void
    {
        $admin = $this->user('admin');
        $p = $this->product(10);

        $this->actingAs($admin)->post('/sales', ['items' => [['product_id' => $p->id, 'quantity' => 4]]]);
        $sale = \App\Models\Sale::first();
        $this->assertSame(6, $p->fresh()->stock);

        $this->actingAs($admin)->post("/sales/{$sale->id}/cancel");
        $this->assertSame(10, $p->fresh()->stock);
    }

    public function test_product_search_and_pagination(): void
    {
        $staff = $this->user();
        foreach (range(1, 12) as $i) {
            $this->product(5, "ITEM-$i");
        }

        $this->actingAs($staff)->get('/products?q=ITEM-1')->assertOk()->assertSee('ITEM-1');
        $this->actingAs($staff)->get('/products?page=2')->assertOk();
    }
}
