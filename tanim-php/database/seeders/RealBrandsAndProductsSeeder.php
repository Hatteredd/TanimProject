<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class RealBrandsAndProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Real Philippine agricultural companies and their products
        $brandProducts = [
            'Del Monte Philippines' => [
                ['name' => 'Pineapple Chunks', 'category' => 'Fruits', 'price' => 85.00, 'unit' => 'can', 'stock' => 45, 'type' => 'Tropical Fruit', 'description' => 'Premium quality pineapple chunks in syrup, perfect for desserts and halo-halo.', 'farm_location' => 'Bukidnon', 'harvest_date' => '2026-03-15'],
                ['name' => 'Whole Corn Kernels', 'category' => 'Vegetables', 'price' => 55.00, 'unit' => 'can', 'stock' => 60, 'type' => 'Vegetable', 'description' => 'Sweet corn kernels, ready to eat or cook. High quality canned corn.', 'farm_location' => 'Laguna', 'harvest_date' => '2026-03-10'],
            ],
            'Magnolia Fresh' => [
                ['name' => 'Fresh Chicken Eggs', 'category' => 'Dairy', 'price' => 120.00, 'unit' => 'tray', 'stock' => 30, 'type' => 'Poultry', 'description' => 'Farm-fresh chicken eggs, high quality with rich yellow yolks.', 'farm_location' => 'Batangas', 'harvest_date' => '2026-03-18'],
                ['name' => 'Fresh Milk', 'category' => 'Dairy', 'price' => 95.00, 'unit' => 'liter', 'stock' => 25, 'type' => 'Dairy', 'description' => 'Pure fresh cow milk, pasteurized and ready for consumption.', 'farm_location' => 'Cavite', 'harvest_date' => '2026-03-16'],
            ],
            'San Miguel Corporation' => [
                ['name' => 'San Miguel Beer', 'category' => 'Beverages', 'price' => 65.00, 'unit' => 'bottle', 'stock' => 100, 'type' => 'Alcoholic Beverage', 'description' => 'The original Filipino beer, brewed with the finest ingredients.', 'farm_location' => 'Manila', 'harvest_date' => '2026-03-20'],
                ['name' => 'Magnolia Ice Cream', 'category' => 'Dairy', 'price' => 150.00, 'unit' => 'tub', 'stock' => 40, 'type' => 'Frozen Dessert', 'description' => 'Classic Filipino ice cream, creamy and delicious in various flavors.', 'farm_location' => 'Quezon City', 'harvest_date' => '2026-03-12'],
            ],
            'Universal Robina Corporation' => [
                ['name' => 'White Sugar', 'category' => 'Other', 'price' => 45.00, 'unit' => 'kg', 'stock' => 80, 'type' => 'Sweetener', 'description' => 'Refined white sugar, perfect for cooking and baking.', 'farm_location' => 'Batangas', 'harvest_date' => '2026-03-14'],
                ['name' => 'Brown Sugar', 'category' => 'Other', 'price' => 48.00, 'unit' => 'kg', 'stock' => 75, 'type' => 'Sweetener', 'description' => 'Natural brown sugar with molasses content, great for coffee.', 'farm_location' => 'Batangas', 'harvest_date' => '2026-03-13'],
            ],
            'Nestlé Philippines' => [
                ['name' => 'Nescafé Classic', 'category' => 'Beverages', 'price' => 125.00, 'unit' => 'jar', 'stock' => 35, 'type' => 'Coffee', 'description' => 'Premium instant coffee, rich aroma and smooth taste.', 'farm_location' => 'Cavite', 'harvest_date' => '2026-03-17'],
                ['name' => 'Milo Chocolate Drink', 'category' => 'Beverages', 'price' => 85.00, 'unit' => 'pack', 'stock' => 60, 'type' => 'Chocolate Drink', 'description' => 'Energy chocolate drink with vitamins and minerals for active lifestyle.', 'farm_location' => 'Laguna', 'harvest_date' => '2026-03-15'],
            ],
            'CDO Foodsphere' => [
                ['name' => 'CDO Pineapple Juice', 'category' => 'Beverages', 'price' => 95.00, 'unit' => 'liter', 'stock' => 40, 'type' => 'Fruit Juice', 'description' => '100% pure pineapple juice, no added sugar or preservatives.', 'farm_location' => 'Davao', 'harvest_date' => '2026-03-16'],
                ['name' => 'CDO Mango Nectar', 'category' => 'Beverages', 'price' => 105.00, 'unit' => 'liter', 'stock' => 35, 'type' => 'Fruit Juice', 'description' => 'Sweet mango nectar made from ripe Philippine mangoes.', 'farm_location' => 'Davao', 'harvest_date' => '2026-03-14'],
            ],
            'Monde Nissin' => [
                ['name' => 'Lucky Me Pancit Canton', 'category' => 'Other', 'price' => 35.00, 'unit' => 'pack', 'stock' => 100, 'type' => 'Noodles', 'description' => 'Classic Filipino pancit canton noodles, perfect for stir-frying.', 'farm_location' => 'Laguna', 'harvest_date' => '2026-03-18'],
                ['name' => 'Mama Sita Noodles', 'category' => 'Other', 'price' => 28.00, 'unit' => 'pack', 'stock' => 120, 'type' => 'Noodles', 'description' => 'Traditional Filipino noodles for soup dishes.', 'farm_location' => 'Laguna', 'harvest_date' => '2026-03-19'],
            ],
            'UFC Philippines' => [
                ['name' => 'UFC Spaghetti', 'category' => 'Other', 'price' => 45.00, 'unit' => 'pack', 'stock' => 80, 'type' => 'Pasta', 'description' => 'Italian-style spaghetti pasta, al dente texture.', 'farm_location' => 'Cavite', 'harvest_date' => '2026-03-17'],
                ['name' => 'UFC Tomato Sauce', 'category' => 'Other', 'price' => 55.00, 'unit' => 'can', 'stock' => 60, 'type' => 'Sauce', 'description' => 'Rich tomato sauce perfect for pasta dishes.', 'farm_location' => 'Cavite', 'harvest_date' => '2026-03-16'],
            ],
            'Red Ribbon Bakeshop' => [
                ['name' => 'Chocolate Mousse Cake', 'category' => 'Other', 'price' => 450.00, 'unit' => 'whole', 'stock' => 15, 'type' => 'Bakery', 'description' => 'Decadent chocolate mousse cake, perfect for celebrations.', 'farm_location' => 'Quezon City', 'harvest_date' => '2026-03-20'],
                ['name' => 'Black Forest Cake', 'category' => 'Other', 'price' => 550.00, 'unit' => 'whole', 'stock' => 10, 'type' => 'Bakery', 'description' => 'Classic Black Forest cake with layers of chocolate and cream.', 'farm_location' => 'Quezon City', 'harvest_date' => '2026-03-19'],
            ],
            'Jollibee Foods Corporation' => [
                ['name' => 'Jolly Spaghetti', 'category' => 'Other', 'price' => 125.00, 'unit' => 'meal', 'stock' => 50, 'type' => 'Fast Food', 'description' => 'Filipino-style spaghetti with sweet tomato sauce.', 'farm_location' => 'Pasig', 'harvest_date' => '2026-03-18'],
                ['name' => 'Chickenjoy', 'category' => 'Other', 'price' => 135.00, 'unit' => 'meal', 'stock' => 45, 'type' => 'Fast Food', 'description' => 'Crispy fried chicken with savory gravy.', 'farm_location' => 'Pasig', 'harvest_date' => '2026-03-17'],
            ],
            'Max\'s Restaurant' => [
                ['name' => 'Max\'s Fried Chicken', 'category' => 'Other', 'price' => 140.00, 'unit' => 'meal', 'stock' => 40, 'type' => 'Fast Food', 'description' => 'Signature fried chicken with crispy skin and juicy meat.', 'farm_location' => 'Quezon City', 'harvest_date' => '2026-03-16'],
                ['name' => 'Max\'s Rice Meals', 'category' => 'Other', 'price' => 95.00, 'unit' => 'meal', 'stock' => 35, 'type' => 'Fast Food', 'description' => 'Complete rice meals with Filipino favorites.', 'farm_location' => 'Quezon City', 'harvest_date' => '2026-03-15'],
            ],
            'Liwayway Marketing' => [
                ['name' => 'Liwayway Sardines', 'category' => 'Seafood', 'price' => 35.00, 'unit' => 'can', 'stock' => 150, 'type' => 'Canned Fish', 'description' => 'Premium sardines in tomato sauce, rich in omega-3.', 'farm_location' => 'General Santos', 'harvest_date' => '2026-03-14'],
                ['name' => 'Liwayway Mackerel', 'category' => 'Seafood', 'price' => 42.00, 'unit' => 'can', 'stock' => 120, 'type' => 'Canned Fish', 'description' => 'Mackerel in oil, perfect for Filipino dishes.', 'farm_location' => 'General Santos', 'harvest_date' => '2026-03-13'],
            ],
            'Century Pacific Food' => [
                ['name' => 'Century Tuna', 'category' => 'Seafood', 'price' => 65.00, 'unit' => 'can', 'stock' => 100, 'type' => 'Canned Fish', 'description' => 'Premium canned tuna in vegetable oil.', 'farm_location' => 'General Santos', 'harvest_date' => '2026-03-16'],
                ['name' => 'Century Bangus', 'category' => 'Seafood', 'price' => 55.00, 'unit' => 'can', 'stock' => 90, 'type' => 'Canned Fish', 'description' => 'Milkfish in brine, Filipino breakfast favorite.', 'farm_location' => 'General Santos', 'harvest_date' => '2026-03-15'],
            ],
        ];

        // Update existing products with new brand names and add new products
        $updatedCount = 0;
        $addedCount = 0;

        foreach ($brandProducts as $brand => $products) {
            foreach ($products as $productData) {
                // Check if product already exists
                $existingProduct = Product::where('name', $productData['name'])->first();
                
                if ($existingProduct) {
                    // Update existing product with new brand
                    $existingProduct->update([
                        'brand' => $brand,
                        'type' => $productData['type'],
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                    ]);
                    $updatedCount++;
                    $this->command->info("Updated: {$productData['name']} -> {$brand}");
                } else {
                    // Add new product
                    Product::create(array_merge($productData, [
                        'user_id' => 1, // Admin user
                        'brand' => $brand,
                    ]));
                    $addedCount++;
                    $this->command->info("Added: {$productData['name']} ({$brand})");
                }
            }
        }

        $this->command->info("Brand and product update completed!");
        $this->command->info("Products updated: {$updatedCount}");
        $this->command->info("Products added: {$addedCount}");

        // Show final brand list
        $uniqueBrands = Product::distinct()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->pluck('brand')
            ->unique()
            ->sort()
            ->values();

        $this->command->info('Final brand list:');
        foreach ($uniqueBrands as $brand) {
            $this->command->info("- {$brand}");
        }
    }
}
