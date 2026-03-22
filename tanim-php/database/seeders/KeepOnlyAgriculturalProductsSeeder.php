<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class KeepOnlyAgriculturalProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Define specific non-agricultural products to remove (exact matches)
        $productsToRemove = [
            // Beverages
            'San Miguel Beer',
            'Magnolia Ice Cream', 
            'Nescafé Classic',
            'Milo Chocolate Drink',
            'CDO Pineapple Juice',
            'CDO Mango Nectar',
            
            // Packaged/Food Products
            'Lucky Me Pancit Canton',
            'Mama Sita Noodles',
            'UFC Spaghetti',
            'UFC Tomato Sauce',
            'Chocolate Mousse Cake',
            'Black Forest Cake',
            'Jolly Spaghetti',
            'Chickenjoy',
            'Max\'s Fried Chicken',
            'Max\'s Rice Meals',
            
            // Seafood (canned)
            'Liwayway Sardines',
            'Liwayway Mackerel',
            'Century Tuna',
            'Century Bangus',
            
            // Sugar
            'White Sugar',
            'Brown Sugar',
        ];

        // Define agricultural products to add back
        $agriculturalProducts = [
            [
                'name' => 'Fresh Kangkong',
                'category' => 'Vegetables',
                'price' => 45.00,
                'unit' => 'bundle',
                'stock' => 80,
                'type' => 'Leafy Green',
                'description' => 'Fresh water spinach, perfect for sinigang and salads.',
                'farm_location' => 'Pampanga',
                'harvest_date' => '2026-03-20',
                'brand' => 'Green Valley Organics'
            ],
            [
                'name' => 'Ampalaya',
                'category' => 'Vegetables', 
                'price' => 55.00,
                'unit' => 'kg',
                'stock' => 60,
                'type' => 'Bitter Melon',
                'description' => 'Fresh bitter gourd, excellent for diabetes management.',
                'farm_location' => 'Benguet',
                'harvest_date' => '2026-03-18',
                'brand' => 'Highland Farms Co.'
            ],
            [
                'name' => 'Native Sweet Potato',
                'category' => 'Root Crops',
                'price' => 40.00,
                'unit' => 'kg',
                'stock' => 100,
                'type' => 'Root Crop',
                'description' => 'Sweet purple sweet potatoes, rich in antioxidants.',
                'farm_location' => 'Leyte',
                'harvest_date' => '2026-03-19',
                'brand' => 'Sweet Roots Harvest'
            ],
            [
                'name' => 'Eggplant',
                'category' => 'Vegetables',
                'price' => 65.00,
                'unit' => 'kg',
                'stock' => 50,
                'type' => 'Nightshade',
                'description' => 'Fresh Chinese eggplant, perfect for grilling and frying.',
                'farm_location' => 'Pangasinan',
                'harvest_date' => '2026-03-17',
                'brand' => 'Fresh Veggie Co.'
            ],
            [
                'name' => 'Baguio Pechay',
                'category' => 'Vegetables',
                'price' => 35.00,
                'unit' => 'bundle',
                'stock' => 90,
                'type' => 'Leafy Green',
                'description' => 'Crisp Baguio pechay, grown in cool mountain climate.',
                'farm_location' => 'Baguio',
                'harvest_date' => '2026-03-21',
                'brand' => 'Baguio Mountain Greens'
            ],
            [
                'name' => 'String Beans',
                'category' => 'Vegetables',
                'price' => 50.00,
                'unit' => 'kg',
                'stock' => 70,
                'type' => 'Legume',
                'description' => 'Fresh string beans, perfect for sinigang and ginataan.',
                'farm_location' => 'Bulacan',
                'harvest_date' => '2026-03-16',
                'brand' => 'Harvest Beans Co.'
            ],
            [
                'name' => 'Lakatan Banana',
                'category' => 'Fruits',
                'price' => 60.00,
                'unit' => 'bunch',
                'stock' => 120,
                'type' => 'Tropical Fruit',
                'description' => 'Sweet lakatan bananas, perfect for banana cue.',
                'farm_location' => 'Davao',
                'harvest_date' => '2026-03-15',
                'brand' => 'Davao Banana Plantation'
            ],
            [
                'name' => 'Carabao Mango',
                'category' => 'Fruits',
                'price' => 120.00,
                'unit' => 'kg',
                'stock' => 40,
                'type' => 'Tropical Fruit',
                'description' => 'World-famous sweet carabao mangoes from Guimaras.',
                'farm_location' => 'Guimaras',
                'harvest_date' => '2026-03-14',
                'brand' => 'Guimaras Mango Estate'
            ],
            [
                'name' => 'Calamansi',
                'category' => 'Fruits',
                'price' => 80.00,
                'unit' => 'kg',
                'stock' => 150,
                'type' => 'Citrus Fruit',
                'description' => 'Fresh calamansi, essential for Filipino cooking.',
                'farm_location' => 'Batangas',
                'harvest_date' => '2026-03-18',
                'brand' => 'Batangas Citrus Grove'
            ],
            [
                'name' => 'Papaya',
                'category' => 'Fruits',
                'price' => 45.00,
                'unit' => 'piece',
                'stock' => 60,
                'type' => 'Pawpaw',
                'description' => 'Sweet solo papaya with orange flesh.',
                'farm_location' => 'Mindanao',
                'harvest_date' => '2026-03-20',
                'brand' => 'Tropical Fruits Philippines'
            ],
            [
                'name' => 'Dinorado Rice',
                'category' => 'Grains & Rice',
                'price' => 55.00,
                'unit' => 'kg',
                'stock' => 200,
                'type' => 'Premium Rice',
                'description' => 'Premium quality dinorado rice, fragrant and fluffy.',
                'farm_location' => 'Nueva Ecija',
                'harvest_date' => '2026-03-12',
                'brand' => 'Nueva Ecija Rice Millers'
            ],
            [
                'name' => 'Black Rice',
                'category' => 'Grains & Rice',
                'price' => 75.00,
                'unit' => 'kg',
                'stock' => 80,
                'type' => 'Glutinous Rice',
                'description' => 'Glutinous black rice, perfect for suman and kakanin.',
                'farm_location' => 'Cordillera',
                'harvest_date' => '2026-03-15',
                'brand' => 'Cordillera Heritage Grains'
            ],
            [
                'name' => 'White Corn',
                'category' => 'Grains & Rice',
                'price' => 35.00,
                'unit' => 'kg',
                'stock' => 150,
                'type' => 'Feed Corn',
                'description' => 'Sweet white corn kernels, perfect for boiling.',
                'farm_location' => 'Bukidnon',
                'harvest_date' => '2026-03-19',
                'brand' => 'Bukidnon Corn Products'
            ],
            [
                'name' => 'Gabi',
                'category' => 'Root Crops',
                'price' => 40.00,
                'unit' => 'kg',
                'stock' => 60,
                'type' => 'Taro',
                'description' => 'Fresh gabi taro root, perfect for ginataan.',
                'farm_location' => 'Pampanga',
                'harvest_date' => '2026-03-17',
                'brand' => 'Philippine Root Crops'
            ],
            [
                'name' => 'Cassava',
                'category' => 'Root Crops',
                'price' => 30.00,
                'unit' => 'kg',
                'stock' => 100,
                'type' => 'Cassava',
                'description' => 'Fresh cassava, great for making cassava cake.',
                'farm_location' => 'Quezon',
                'harvest_date' => '2026-03-16',
                'brand' => 'Mindanao Tuber Products'
            ],
            [
                'name' => 'Lemongrass',
                'category' => 'Herbs & Spices',
                'price' => 25.00,
                'unit' => 'bundle',
                'stock' => 80,
                'type' => 'Medicinal Herb',
                'description' => 'Fresh lemongrass, aromatic and medicinal.',
                'farm_location' => 'Laguna',
                'harvest_date' => '2026-03-21',
                'brand' => 'Aromatic Herbs Philippines'
            ],
            [
                'name' => 'Turmeric',
                'category' => 'Herbs & Spices',
                'price' => 150.00,
                'unit' => 'kg',
                'stock' => 40,
                'type' => 'Rhizome Spice',
                'description' => 'Fresh turmeric root, anti-inflammatory properties.',
                'farm_location' => 'Ilocos',
                'harvest_date' => '2026-03-18',
                'brand' => 'Spice Islands Trading'
            ],
            [
                'name' => 'Fresh Chicken Eggs',
                'category' => 'Livestock',
                'price' => 110.00,
                'unit' => 'tray',
                'stock' => 50,
                'type' => 'Poultry',
                'description' => 'Farm-fresh chicken eggs with rich yellow yolks.',
                'farm_location' => 'Batangas',
                'harvest_date' => '2026-03-20',
                'brand' => 'Magnolia Fresh'
            ],
            [
                'name' => 'Fresh Milk',
                'category' => 'Dairy',
                'price' => 90.00,
                'unit' => 'liter',
                'stock' => 30,
                'type' => 'Dairy',
                'description' => 'Pure fresh cow milk, pasteurized and ready.',
                'farm_location' => 'Cavite',
                'harvest_date' => '2026-03-19',
                'brand' => 'Magnolia Fresh'
            ],
            [
                'name' => 'Bangus',
                'category' => 'Seafood',
                'price' => 200.00,
                'unit' => 'kg',
                'stock' => 25,
                'type' => 'Fish',
                'description' => 'Fresh bangus milkfish, Filipino breakfast favorite.',
                'farm_location' => 'Manila Bay',
                'harvest_date' => '2026-03-16',
                'brand' => 'Manila Fresh Seafood'
            ],
            [
                'name' => 'Tilapia',
                'category' => 'Seafood',
                'price' => 120.00,
                'unit' => 'kg',
                'stock' => 60,
                'type' => 'Fish',
                'description' => 'Fresh tilapia, perfect for grilling and frying.',
                'farm_location' => 'Laguna',
                'harvest_date' => '2026-03-17',
                'brand' => 'Laguna Aqua Farms'
            ],
        ];

        // Remove specific non-agricultural products
        $removedCount = 0;
        foreach ($productsToRemove as $productName) {
            $products = Product::where('name', $productName)->get();
            foreach ($products as $product) {
                $product->delete();
                $removedCount++;
                $this->command->info("Removed: {$productName}");
            }
        }

        // Add back agricultural products
        $addedCount = 0;
        foreach ($agriculturalProducts as $productData) {
            // Check if product already exists
            $existingProduct = Product::where('name', $productData['name'])->first();
            
            if (!$existingProduct) {
                Product::create(array_merge($productData, [
                    'user_id' => 1, // Admin user
                ]));
                $addedCount++;
                $this->command->info("Added: {$productData['name']} ({$productData['category']})");
            }
        }

        $this->command->info("Agricultural products cleanup completed!");
        $this->command->info("Products removed: {$removedCount}");
        $this->command->info("Products added: {$addedCount}");

        // Show final summary
        $finalProducts = Product::all();
        $categoryCounts = [];
        
        foreach ($finalProducts as $product) {
            $category = $product->category;
            if (!isset($categoryCounts[$category])) {
                $categoryCounts[$category] = 0;
            }
            $categoryCounts[$category]++;
        }

        $this->command->info("Final products by category:");
        foreach ($categoryCounts as $category => $count) {
            $this->command->info("- {$category}: {$count}");
        }
    }
}
