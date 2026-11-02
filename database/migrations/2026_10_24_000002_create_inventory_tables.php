<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $t) {
            $t->id();
            $t->string('name')->unique();
            $t->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('phone')->nullable();
            $t->string('email')->nullable();
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->constrained()->restrictOnDelete();
            $t->string('sku', 50)->unique();
            $t->string('name', 150);
            $t->text('description')->nullable();
            $t->string('image')->nullable();
            $t->decimal('purchase_price', 12, 2);
            $t->decimal('selling_price', 12, 2);
            $t->unsignedInteger('stock')->default(0);
            $t->unsignedInteger('minimum_stock')->default(0);
            $t->timestamps();
            $t->index('name');
        });

        Schema::create('purchases', function (Blueprint $t) {
            $t->id();
            $t->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $t->foreignId('user_id')->constrained()->restrictOnDelete();
            $t->string('purchase_number')->unique();
            $t->decimal('total', 14, 2)->default(0);
            $t->string('status')->default('completed'); // completed | cancelled
            $t->text('notes')->nullable();
            $t->text('cancellation_reason')->nullable();
            $t->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('cancelled_at')->nullable();
            $t->dateTime('purchased_at')->useCurrent();
            $t->timestamps();
            $t->index('status');
            $t->index('purchased_at');
        });

        Schema::create('purchase_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('quantity');
            $t->decimal('unit_cost', 12, 2);
            $t->decimal('subtotal', 14, 2);
            $t->timestamps();
        });

        Schema::create('sales', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $t->string('sale_number')->unique();
            $t->string('customer_name')->nullable();
            $t->string('payment_method')->default('cash'); // cash | qris | transfer
            $t->decimal('total', 14, 2)->default(0);
            $t->string('status')->default('completed'); // completed | cancelled
            $t->text('cancellation_reason')->nullable();
            $t->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('cancelled_at')->nullable();
            $t->dateTime('sold_at')->useCurrent();
            $t->timestamps();
            $t->index('status');
            $t->index('sold_at');
        });

        Schema::create('sale_items', function (Blueprint $t) {
            $t->id();
            $t->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $t->foreignId('product_id')->constrained()->restrictOnDelete();
            $t->unsignedInteger('quantity');
            $t->decimal('unit_price', 12, 2);
            $t->decimal('subtotal', 14, 2);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
        Schema::dropIfExists('purchase_items');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
