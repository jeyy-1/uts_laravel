<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 3 Akun dengan peran berbeda
        $owner = User::create([
            'name' => 'Budi Santoso (Owner)',
            'email' => 'owner@example.com',
            'password' => 'password',
            'role' => 'owner'
        ]);

        $staff = User::create([
            'name' => 'Siti Kasir (Staff)',
            'email' => 'staff@example.com',
            'password' => 'password',
            'role' => 'staff'
        ]);

        $customer = User::create([
            'name' => 'Andi Pratama (Pelanggan)',
            'email' => 'user@example.com',
            'password' => 'password',
            'role' => 'user'
        ]);

        // Kategori Ritel
        $food = Category::create(['name' => 'Makanan Ringan']);
        $drink = Category::create(['name' => 'Minuman Segar']);
        $stat = Category::create(['name' => 'Alat Tulis Kantor']);
        $household = Category::create(['name' => 'Kebutuhan Rumah']);

        // Supplier
        $sup1 = Supplier::create([
            'name' => 'PT Sumber Makmur Sejahtera',
            'phone' => '081299887766',
            'email' => 'sales@sumbermakmur.id'
        ]);
        $sup2 = Supplier::create([
            'name' => 'CV Maju Jaya Abadi',
            'phone' => '081377889900',
            'email' => 'order@majujaya.id'
        ]);

        // Daftar Produk Ritel dengan stok dinamis
        $productsData = [
            [$food, 'MKN-001', 'Mie Instan Goreng Spesial', 'Mie goreng instan rasa lezat dengan bumbu lengkap dan bawang goreng gurih.', 2500, 3500, 85, 20],
            [$food, 'MKN-002', 'Biskuit Cokelat Crispy', 'Kue biskuit renyah salut cokelat Belgia berkualitas premium.', 6000, 8500, 42, 15],
            [$food, 'MKN-003', 'Keripik Singkong Balado', 'Keripik singkong pedas manis asli olahan nusantara.', 5000, 7500, 0, 10], // Stok habis untuk display
            [$drink, 'MNM-001', 'Air Mineral Pegunungan 600ml', 'Air mineral murni dari mata air pegunungan alami.', 2000, 3500, 110, 30],
            [$drink, 'MNM-002', 'Teh Melati Botol 350ml', 'Seduhan teh hijau aroma melati yang menyegarkan dahaga.', 3500, 5000, 6, 15], // Stok rendah
            [$drink, 'MNM-003', 'Kopi Susu Gula Aren 250ml', 'Perpaduan biji kopi pilihan dengan susu segar dan gula aren asli.', 7000, 10000, 30, 10],
            [$stat, 'ATK-001', 'Buku Tulis Bergaris 38 Lembar', 'Buku tulis kertas putih tebal berkualitas tanpa tembus tinta.', 3000, 4500, 50, 15],
            [$stat, 'ATK-002', 'Pulpen Gel Hitam 0.5mm', 'Pena tinta gel hitam pekat anti macet dan cepat kering.', 1500, 2500, 4, 10], // Stok rendah
            [$stat, 'ATK-003', 'Penghapus Pensil Bersih', 'Penghapus karet bebas debu tidak merusak permukaan kertas.', 1000, 2000, 45, 10],
            [$household, 'RUM-001', 'Sabun Cuci Piring Jeruk Nipis 750ml', 'Cairan pencuci piring formula anti lemak tangguh aroma jeruk nipis.', 8500, 12000, 25, 8],
            [$household, 'RUM-002', 'Tisu Wajah 200 Sheets 2 Ply', 'Tisu wajah lembut higienis aman untuk segala jenis kulit.', 7500, 10500, 38, 12],
        ];

        $createdProducts = [];
        foreach ($productsData as [$cat, $sku, $name, $desc, $buy, $sell, $stock, $min]) {
            $createdProducts[] = Product::create([
                'category_id' => $cat->id,
                'sku' => $sku,
                'name' => $name,
                'description' => $desc,
                'purchase_price' => $buy,
                'selling_price' => $sell,
                'stock' => $stock,
                'minimum_stock' => $min,
            ]);
        }

        // Buat sample transaksi historis (7 hari terakhir) agar grafik langsung hidup dan berwarna
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->setHour(rand(9, 19))->setMinute(rand(0, 59));
            $salesCountForDay = rand(2, 4);

            for ($j = 0; $j < $salesCountForDay; $j++) {
                $saleTime = (clone $date)->addMinutes($j * 45);
                $sale = Sale::create([
                    'user_id' => $staff->id,
                    'customer_name' => 'Pelanggan Walk-in #'.rand(10, 99),
                    'payment_method' => ['cash', 'qris', 'transfer'][rand(0, 2)],
                    'sale_number' => 'SL-'.$saleTime->format('YmdHi').'-'.Str::upper(Str::random(3)),
                    'total' => 0,
                    'status' => 'completed',
                    'sold_at' => $saleTime,
                ]);

                $total = 0;
                // Pilih 1 - 3 random produk
                $randomKeys = (array) array_rand($createdProducts, rand(1, 3));
                foreach ($randomKeys as $k) {
                    $prod = $createdProducts[$k];
                    $qty = rand(1, 3);
                    $subtotal = $prod->selling_price * $qty;

                    $sale->items()->create([
                        'product_id' => $prod->id,
                        'quantity' => $qty,
                        'unit_price' => $prod->selling_price,
                        'subtotal' => $subtotal,
                    ]);
                    $total += $subtotal;
                }

                $sale->update(['total' => $total]);
            }
        }

        // Buat 1 sample transaksi yang dibatalkan oleh Owner untuk demonstrasi audit trail
        $cancelledSale = Sale::create([
            'user_id' => $staff->id,
            'customer_name' => 'Ahmad (Batal Beli)',
            'payment_method' => 'cash',
            'sale_number' => 'SL-'.now()->subHours(3)->format('YmdHi').'-CNCL',
            'total' => 17000,
            'status' => 'cancelled',
            'cancellation_reason' => 'Pembeli keliru memilih produk dan barang dikembalikan dalam kondisi utuh.',
            'cancelled_by' => $owner->id,
            'cancelled_at' => now()->subHours(2),
            'sold_at' => now()->subHours(3),
        ]);

        $cancelledSale->items()->create([
            'product_id' => $createdProducts[1]->id, // Biskuit Cokelat
            'quantity' => 2,
            'unit_price' => $createdProducts[1]->selling_price,
            'subtotal' => 17000,
        ]);

        // Sample Pembelian (Stok Masuk)
        $purchase1 = \App\Models\Purchase::create([
            'supplier_id' => $sup1->id,
            'user_id' => $staff->id,
            'purchase_number' => 'PO-' . now()->subDays(2)->format('YmdHi') . '-SM01',
            'total' => 250000,
            'status' => 'completed',
            'notes' => 'Pengiriman DO #DO-7781 dari PT Sumber Makmur',
            'purchased_at' => now()->subDays(2),
        ]);

        $purchase1->items()->create([
            'product_id' => $createdProducts[0]->id, // Mie Instan
            'quantity' => 100,
            'unit_cost' => 2500,
            'subtotal' => 250000,
        ]);

        // Sample Pembelian yang dibatalkan oleh Owner
        $purchase2 = \App\Models\Purchase::create([
            'supplier_id' => $sup2->id,
            'user_id' => $staff->id,
            'purchase_number' => 'PO-' . now()->subDays(1)->format('YmdHi') . '-MJ02',
            'total' => 120000,
            'status' => 'cancelled',
            'notes' => 'Pengiriman barang cacat kemasan',
            'cancellation_reason' => 'Barang diretur kembali ke supplier karena kemasan sobek saat penerimaan.',
            'cancelled_by' => $owner->id,
            'cancelled_at' => now()->subHours(12),
            'purchased_at' => now()->subDays(1),
        ]);

        $purchase2->items()->create([
            'product_id' => $createdProducts[1]->id, // Biskuit Cokelat
            'quantity' => 20,
            'unit_cost' => 6000,
            'subtotal' => 120000,
        ]);
    }
}
