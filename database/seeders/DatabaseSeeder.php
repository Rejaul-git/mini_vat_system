<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ReturnItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        // Create report viewer user
        User::create([
            'name' => 'Report Viewer',
            'email' => 'viewer@example.com',
            'role' => 'report_viewer',
            'password' => Hash::make('viewer123'),
        ]);

        // Create sample products
    $products = [
    ['name' => 'Men’s Cotton Shirt',   'sku' => 'SHIRT-001', 'unit' => 'Pcs', 'vat_rate' => 15.00],
    ['name' => 'Ladies Denim Jeans',   'sku' => 'JEANS-002', 'unit' => 'Pcs', 'vat_rate' => 15.00],
    ['name' => 'Sunflower Cooking Oil','sku' => 'OIL-003',   'unit' => 'Ltr', 'vat_rate' => 15.00],
    ['name' => 'Basmati Rice 5kg',     'sku' => 'RICE-004',  'unit' => 'Kg',  'vat_rate' => 15.00],
    ['name' => 'Detergent Powder 1kg', 'sku' => 'DETG-005',  'unit' => 'Kg',  'vat_rate' => 15.00],
];


        foreach ($products as $product) {
            Product::create($product);
        }

        // Create sample purchases
        for ($i = 1; $i <= 20; $i++) {
            $purchase = Purchase::create([
                'date' => now()->subDays(rand(1, 90)),
                'supplier_name' => 'Supplier ' . chr(64 + $i),
                'note' => 'Purchase note ' . $i,
            ]);

            // Add purchase items
            for ($j = 0; $j < rand(2, 4); $j++) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => rand(1, 5),
                    'qty' => rand(10, 100),
                    'unit_price' => rand(50, 500),
                    'vat_rate' => 15.00,
                ]);
            }
        }

        // Create sample sales
        for ($i = 1; $i <= 15; $i++) {
            $sale = Sale::create([
                'date' => now()->subDays(rand(1, 60)),
                'customer_name' => 'Customer ' . chr(64 + $i),
                'note' => 'Sale note ' . $i,
            ]);

            // Add sale items
            for ($j = 0; $j < rand(1, 3); $j++) {
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => rand(1, 5),
                    'qty' => rand(5, 30),
                    'unit_price' => rand(100, 800),
                    'vat_rate' => 15.00,
                ]);

                // Add some returns (30% chance)
                if (rand(1, 10) <= 3) {
                    ReturnItem::create([
                        'sale_item_id' => $saleItem->id,
                        'qty' => rand(1, 5),
                        'date' => now()->subDays(rand(1, 30)),
                        'reason' => 'Defective product',
                    ]);
                }
            }
        }
    }
}