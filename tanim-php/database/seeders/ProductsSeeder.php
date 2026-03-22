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

        $suppliers = Employee::where('status', 'active')->get();
        if ($suppliers->isEmpty()) {
            return;
        }

        $faker = fake();
        $categories = ['Vegetables', 'Fruits', 'Grains & Rice'];
        $types = ['Organic', 'Leafy', 'Premium', 'Tropical', 'Local'];
        $units = ['kg', 'bundle', 'pack', 'box'];

        for ($i = 0; $i < 20; $i++) {
            $supplier = $suppliers->random();
            $name = ucfirst($faker->unique()->words(2, true));

            $product = Product::updateOrCreate(
                ['name' => $name],
                [
                    'user_id' => $admin->id,
                    'supplier_id' => $supplier->id,
                    'name' => $name,
                    'category' => $faker->randomElement($categories),
                    'brand' => ucfirst($faker->word()),
                    'type' => $faker->randomElement($types),
                    'description' => $faker->sentence(),
                    'price' => $faker->randomFloat(2, 20, 250),
                    'unit' => $faker->randomElement($units),
                    'stock' => $faker->numberBetween(20, 500),
                    'farm_location' => $supplier->location,
                    'harvest_date' => $faker->dateTimeBetween('-14 days', 'now')->format('Y-m-d'),
                    'is_active' => true,
                ]
            );

            if ($buyers->isEmpty()) {
                continue;
            }

            foreach ($buyers->shuffle()->take(2) as $buyer) {
                Review::firstOrCreate(
                    ['user_id' => $buyer->id, 'product_id' => $product->id],
                    [
                        'rating' => $faker->numberBetween(4, 5),
                        'comment' => $faker->sentence(),
                    ]
                );
            }
        }
    }
}
