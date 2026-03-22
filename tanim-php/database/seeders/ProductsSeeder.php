<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@tanim.ph')->first();
        if (!$admin) {
            return;
        }

        $buyers = User::where('role', 'buyer')->get();

        $products = [
            ['name' => 'Fresh Kangkong', 'category' => 'Vegetables', 'type' => 'Leafy', 'price' => 25, 'unit' => 'bundle', 'stock' => 100, 'description' => 'Freshly harvested water spinach.', 'supplier' => 'Laguna Greens Cooperative'],
            ['name' => 'Sitaw (String Beans)', 'category' => 'Vegetables', 'type' => 'Organic', 'price' => 40, 'unit' => 'bundle', 'stock' => 70, 'description' => 'Tender and crunchy long beans.', 'supplier' => 'Laguna Greens Cooperative'],
            ['name' => 'Ampalaya (Bitter Gourd)', 'category' => 'Vegetables', 'type' => 'Organic', 'price' => 60, 'unit' => 'kg', 'stock' => 80, 'description' => 'Organically grown bitter gourd.', 'supplier' => 'Benguet Highland Farms'],
            ['name' => 'Pechay Baguio', 'category' => 'Vegetables', 'type' => 'Leafy', 'price' => 30, 'unit' => 'kg', 'stock' => 120, 'description' => 'Crisp highland pechay.', 'supplier' => 'Benguet Highland Farms'],
            ['name' => 'Lakatan Banana', 'category' => 'Fruits', 'type' => 'Tropical', 'price' => 80, 'unit' => 'kg', 'stock' => 200, 'description' => 'Sweet Lakatan bananas.', 'supplier' => 'Mindanao Tropics'],
            ['name' => 'Philippine Mango (Carabao)', 'category' => 'Fruits', 'type' => 'Premium', 'price' => 120, 'unit' => 'kg', 'stock' => 60, 'description' => 'World-famous Carabao mango.', 'supplier' => 'Visayas Orchard Group'],
            ['name' => 'Dinorado Rice', 'category' => 'Grains & Rice', 'type' => 'Premium', 'price' => 65, 'unit' => 'kg', 'stock' => 500, 'description' => 'Premium Dinorado white rice.', 'supplier' => 'Nueva Ecija Grain Hub'],
        ];

        foreach ($products as $seed) {
            $supplier = Employee::where('name', $seed['supplier'])->first();
            if (!$supplier) {
                continue;
            }

            $product = Product::updateOrCreate(
                ['name' => $seed['name']],
                [
                    'user_id' => $admin->id,
                    'supplier_id' => $supplier->id,
                    'name' => $seed['name'],
                    'category' => $seed['category'],
                    'brand' => $seed['name'],
                    'type' => $seed['type'],
                    'description' => $seed['description'],
                    'price' => $seed['price'],
                    'unit' => $seed['unit'],
                    'stock' => $seed['stock'],
                    'farm_location' => $supplier->location,
                    'harvest_date' => now()->toDateString(),
                    'is_active' => true,
                ]
            );

            if ($buyers->isEmpty()) {
                continue;
            }

            foreach ($buyers->shuffle()->take(2) as $buyer) {
                Review::firstOrCreate(
                    ['user_id' => $buyer->id, 'product_id' => $product->id],
                    ['rating' => rand(4, 5), 'comment' => 'Fresh quality produce.']
                );
            }
        }
    }
}
