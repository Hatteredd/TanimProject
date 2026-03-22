<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductBrandTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Brand and Type mappings for fruits and vegetables
        $brandTypeData = [
            // Vegetables
            'Fresh Kangkong' => ['brand' => 'Green Valley Farms', 'type' => 'Leafy Green'],
            'Ampalaya (Bitter Gourd)' => ['brand' => 'Highland Organic', 'type' => 'Bitter Melon'],
            'Native Kamote' => ['brand' => 'Sweet Roots Co.', 'type' => 'Root Crop'],
            'Eggplant (Talong)' => ['brand' => 'Veggie Fresh', 'type' => 'Nightshade'],
            'Pechay Baguio' => ['brand' => 'Baguio Greens', 'type' => 'Leafy Green'],
            'Sitaw (String Beans)' => ['brand' => 'Bean Harvest', 'type' => 'Legume'],
            
            // Fruits
            'Lakatan Banana' => ['brand' => 'Davao Banana Farm', 'type' => 'Tropical Fruit'],
            'Philippine Mango (Carabao)' => ['brand' => 'Guimaras Mango Co.', 'type' => 'Tropical Fruit'],
            'Dalandan (Philippine Orange)' => ['brand' => 'Batangas Citrus', 'type' => 'Citrus Fruit'],
            'Papaya (Solo)' => ['brand' => 'Tropical Fruits Inc.', 'type' => 'Pawpaw'],
            
            // Grains & Rice
            'Dinorado Rice' => ['brand' => 'Nueva Ecija Rice Mills', 'type' => 'Premium Rice'],
            'Black Rice (Pirurutong)' => ['brand' => 'Cordillera Heritage', 'type' => 'Glutinous Rice'],
            'White Corn Kernels' => ['brand' => 'Bukidnon Corn Co.', 'type' => 'Feed Corn'],
            
            // Root Crops
            'Gabi (Taro Root)' => ['brand' => 'Root Crops PH', 'type' => 'Taro'],
            'Cassava (Kamoteng Kahoy)' => ['brand' => 'Mindanao Tuber Farms', 'type' => 'Cassava'],
            
            // Herbs & Spices
            'Lemongrass (Tanglad)' => ['brand' => 'Aromatic Herbs Co.', 'type' => 'Medicinal Herb'],
            'Pandan Leaves' => ['brand' => 'Flavor Leaves Inc.', 'type' => 'Flavoring'],
            'Turmeric Root (Luyang Dilaw)' => ['brand' => 'Spice Islands', 'type' => 'Rhizome Spice'],
        ];

        foreach ($brandTypeData as $productName => $data) {
            Product::where('name', $productName)->update([
                'brand' => $data['brand'],
                'type' => $data['type'],
            ]);
        }

        // Add some additional variety to existing products
        $additionalProducts = [
            [
                'user_id' => 1, // Admin user
                'name' => 'Red Bell Pepper',
                'category' => 'Vegetables',
                'price' => 85,
                'unit' => 'kg',
                'stock' => 60,
                'description' => 'Fresh red bell peppers perfect for stir-frying and roasting. Rich in vitamins A and C.',
                'farm_location' => 'Benguet, Cordillera',
                'harvest_date' => '2026-03-15',
                'brand' => 'Mountain Fresh Veggies',
                'type' => 'Sweet Pepper',
            ],
            [
                'user_id' => 1,
                'name' => 'Calamansi (Philippine Lime)',
                'category' => 'Fruits',
                'price' => 45,
                'unit' => 'kg',
                'stock' => 80,
                'description' => 'Small, green citrus fruits perfect for Filipino dishes and drinks. High in vitamin C.',
                'farm_location' => 'Southern Leyte',
                'harvest_date' => '2026-03-16',
                'brand' => 'Citrus Grove',
                'type' => 'Lime',
            ],
            [
                'user_id' => 1,
                'name' => 'Spinach',
                'category' => 'Vegetables',
                'price' => 55,
                'unit' => 'bundle',
                'stock' => 40,
                'description' => 'Tender spinach leaves rich in iron and vitamins. Perfect for salads and sautéing.',
                'farm_location' => 'Tarlac, Luzon',
                'harvest_date' => '2026-03-17',
                'brand' => 'Leafy Greens Co.',
                'type' => 'Leafy Green',
            ],
            [
                'user_id' => 1,
                'name' => 'Pineapple',
                'category' => 'Fruits',
                'price' => 95,
                'unit' => 'piece',
                'stock' => 30,
                'description' => 'Sweet and tangy pineapples from Mindanao. Perfect for desserts and juices.',
                'farm_location' => 'Davao del Sur, Mindanao',
                'harvest_date' => '2026-03-14',
                'brand' => 'Tropical Fruits PH',
                'type' => 'Tropical Fruit',
            ],
            [
                'user_id' => 1,
                'name' => 'Carrots',
                'category' => 'Vegetables',
                'price' => 50,
                'unit' => 'kg',
                'stock' => 100,
                'description' => 'Crunchy and sweet carrots perfect for soups, stews, and fresh eating.',
                'farm_location' => 'Benguet, Cordillera',
                'harvest_date' => '2026-03-18',
                'brand' => 'Root Harvest Farms',
                'type' => 'Root Vegetable',
            ],
            [
                'user_id' => 1,
                'name' => 'Guyabano (Melon)',
                'category' => 'Fruits',
                'price' => 75,
                'unit' => 'piece',
                'stock' => 25,
                'description' => 'Sweet and aromatic melon variety popular in the Philippines during summer.',
                'farm_location' => 'Pampanga, Luzon',
                'harvest_date' => '2026-03-19',
                'brand' => 'Summer Fruits Co.',
                'type' => 'Melon',
            ],
            [
                'user_id' => 1,
                'name' => 'Malunggay Leaves',
                'category' => 'Vegetables',
                'price' => 35,
                'unit' => 'bundle',
                'stock' => 60,
                'description' => 'Nutritious moringa leaves used in traditional Filipino soups like tinola.',
                'farm_location' => 'Bulacan, Luzon',
                'harvest_date' => '2026-03-20',
                'brand' => 'NutriGreens',
                'type' => 'Leafy Green',
            ],
        ];

        foreach ($additionalProducts as $product) {
            Product::create($product);
        }

        $this->command->info('Product brands and types have been updated!');
    }
}
