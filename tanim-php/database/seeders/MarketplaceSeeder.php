<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing products, reviews, and orders to start fresh
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Review::truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('cart_items')->truncate();
        Product::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $admin = User::where('email', 'admin@tanim.ph')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@tanim.ph',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]);
        }

        $buyers = User::where('role', 'buyer')->get();
        $suppliers = Employee::all();
        
        if ($suppliers->isEmpty()) {
            $this->call(SuppliersSeeder::class);
            $suppliers = Employee::all();
        }

        $faker = fake();

        $products = [
            // Category: Plants & Seeds
            [
                'name' => 'Sunflower Seeds (Premium)',
                'category' => 'Plants & Seeds',
                'brand' => 'Davao Seeds Co.',
                'type' => 'Seeds',
                'description' => 'High-quality sunflower seeds, perfect for oil production or garden planting.',
                'price' => 150.00,
                'unit' => 'pack',
                'stock' => 100,
            ],
            [
                'name' => 'Tomato Seeds (Hybrid)',
                'category' => 'Plants & Seeds',
                'brand' => 'Manila Greenery',
                'type' => 'Seeds',
                'description' => 'Hybrid tomato seeds resistant to common pests. High yield guaranteed.',
                'price' => 85.00,
                'unit' => 'pack',
                'stock' => 200,
            ],
            [
                'name' => 'Aloe Vera Plant (Potted)',
                'category' => 'Plants & Seeds',
                'brand' => 'Cebu Garden Supplies',
                'type' => 'Potted Plant',
                'description' => 'Mature Aloe Vera plant in a ceramic pot. Great for skin care and air purification.',
                'price' => 250.00,
                'unit' => 'piece',
                'stock' => 45,
            ],
            [
                'name' => 'Snake Plant (Sansevieria)',
                'category' => 'Plants & Seeds',
                'brand' => 'Manila Greenery',
                'type' => 'Potted Plant',
                'description' => 'Resilient snake plant, ideal for indoor decoration and air filtering.',
                'price' => 350.00,
                'unit' => 'piece',
                'stock' => 30,
            ],
            [
                'name' => 'Sweet Basil Seeds',
                'category' => 'Plants & Seeds',
                'brand' => 'Davao Seeds Co.',
                'type' => 'Seeds',
                'description' => 'Aromatic sweet basil seeds. Easy to grow in pots or gardens.',
                'price' => 65.00,
                'unit' => 'pack',
                'stock' => 150,
            ],

            // Category: Vegetables
            [
                'name' => 'Fresh Kangkong',
                'category' => 'Vegetables',
                'brand' => 'Luzon Fresh',
                'type' => 'Leafy Green',
                'description' => 'Freshly harvested water spinach, perfect for local Filipino dishes.',
                'price' => 35.00,
                'unit' => 'bundle',
                'stock' => 120,
            ],
            [
                'name' => 'Ampalaya (Bitter Gourd)',
                'category' => 'Vegetables',
                'brand' => 'Baguio Organic',
                'type' => 'Gourd',
                'description' => 'Nutritious bitter gourd from the highlands of Baguio.',
                'price' => 75.00,
                'unit' => 'kg',
                'stock' => 80,
            ],
            [
                'name' => 'Native Eggplant',
                'category' => 'Vegetables',
                'brand' => 'Mindanao Harvest',
                'type' => 'Nightshade',
                'description' => 'Long purple eggplants, versatile for frying, grilling, or stewing.',
                'price' => 60.00,
                'unit' => 'kg',
                'stock' => 95,
            ],
            [
                'name' => 'Baguio Pechay',
                'category' => 'Vegetables',
                'brand' => 'Baguio Organic',
                'type' => 'Leafy Green',
                'description' => 'Crisp and fresh Pechay from Benguet. Essential for soups.',
                'price' => 45.00,
                'unit' => 'bundle',
                'stock' => 110,
            ],
            [
                'name' => 'Red Onions',
                'category' => 'Vegetables',
                'brand' => 'Luzon Fresh',
                'type' => 'Bulb',
                'description' => 'Pungent and fresh red onions from Nueva Ecija farms.',
                'price' => 180.00,
                'unit' => 'kg',
                'stock' => 300,
            ],

            // Category: Fruits
            [
                'name' => 'Carabao Mango (Sweet)',
                'category' => 'Fruits',
                'brand' => 'Guimaras Sweets',
                'type' => 'Tropical Fruit',
                'description' => 'World-famous sweet Carabao mangoes from Guimaras Island.',
                'price' => 150.00,
                'unit' => 'kg',
                'stock' => 150,
            ],
            [
                'name' => 'Lakatan Banana',
                'category' => 'Fruits',
                'brand' => 'Davao Tropical',
                'type' => 'Tropical Fruit',
                'description' => 'Sweet and creamy Lakatan bananas, high in potassium.',
                'price' => 85.00,
                'unit' => 'kg',
                'stock' => 200,
            ],
            [
                'name' => 'Pineapple (Solo)',
                'category' => 'Fruits',
                'brand' => 'Davao Tropical',
                'type' => 'Tropical Fruit',
                'description' => 'Small, sweet pineapples perfect for individual consumption.',
                'price' => 45.00,
                'unit' => 'piece',
                'stock' => 80,
            ],
            [
                'name' => 'Calamansi',
                'category' => 'Fruits',
                'brand' => 'Batangas Citrus',
                'type' => 'Citrus',
                'description' => 'Fresh Philippine lime, essential for sawsawan and juice.',
                'price' => 50.00,
                'unit' => 'kg',
                'stock' => 250,
            ],
            [
                'name' => 'Papaya (Solo)',
                'category' => 'Fruits',
                'brand' => 'Davao Tropical',
                'type' => 'Tropical Fruit',
                'description' => 'Ripe and sweet solo papaya, perfect for breakfast.',
                'price' => 65.00,
                'unit' => 'piece',
                'stock' => 60,
            ],
        ];

        foreach ($products as $pData) {
            $supplier = $suppliers->random();
            $product = Product::create(array_merge($pData, [
                'user_id' => $admin->id,
                'supplier_id' => $supplier->id,
                'farm_location' => $supplier->location,
                'harvest_date' => $faker->dateTimeBetween('-14 days', 'now')->format('Y-m-d'),
                'is_active' => true,
            ]));

            // Add 3 different pictures for each product as requested
            $categories = [
                'Plants & Seeds' => 'nature',
                'Vegetables' => 'vegetables',
                'Fruits' => 'fruits'
            ];
            $keyword = $categories[$pData['category']] ?? 'farm';
            
            for ($i = 1; $i <= 3; $i++) {
                \App\Models\ProductPhoto::create([
                    'product_id' => $product->id,
                    // Use different random images from picsum.photos for each photo
                    'path' => "https://picsum.photos/seed/" . md5($product->name . $i) . "/800/600",
                    'is_primary' => $i === 1,
                    'sort_order' => $i,
                ]);
            }

            if ($buyers->isNotEmpty()) {
                foreach ($buyers->shuffle()->take(2) as $buyer) {
                    Review::create([
                        'user_id' => $buyer->id,
                        'product_id' => $product->id,
                        'rating' => $faker->numberBetween(4, 5),
                        'comment' => $faker->sentence(),
                    ]);
                }
            }
        }
    }
}
