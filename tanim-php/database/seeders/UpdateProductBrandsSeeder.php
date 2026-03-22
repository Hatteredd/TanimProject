<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class UpdateProductBrandsSeeder extends Seeder
{
    public function run(): void
    {
        // Brand name mappings - change to company-style names
        $brandMappings = [
            // Current Brand => New Professional Brand Name
            'Green Valley Farms' => 'Green Valley Organics',
            'Highland Organic' => 'Highland Farms Co.',
            'Sweet Roots Co.' => 'Sweet Roots Harvest',
            'Veggie Fresh' => 'Fresh Veggie Co.',
            'Baguio Greens' => 'Baguio Mountain Greens',
            'Bean Harvest' => 'Harvest Beans Co.',
            'Davao Banana Farm' => 'Davao Banana Plantation',
            'Guimaras Mango Co.' => 'Guimaras Mango Estate',
            'Batangas Citrus' => 'Batangas Citrus Grove',
            'Tropical Fruits Inc.' => 'Tropical Fruits Philippines',
            'Nueva Ecija Rice Mills' => 'Nueva Ecija Rice Millers',
            'Cordillera Heritage' => 'Cordillera Heritage Grains',
            'Bukidnon Corn Co.' => 'Bukidnon Corn Products',
            'Root Crops PH' => 'Philippine Root Crops',
            'Mindanao Tuber Farms' => 'Mindanao Tuber Products',
            'Aromatic Herbs Co.' => 'Aromatic Herbs Philippines',
            'Flavor Leaves Inc.' => 'Flavor Leaves Company',
            'Spice Islands' => 'Spice Islands Trading',
            'Mountain Fresh Veggies' => 'Mountain Fresh Produce',
            'Citrus Grove' => 'Citrus Grove Farms',
            'Leafy Greens Co.' => 'Leafy Greens Supply',
            'Tropical Fruits PH' => 'Tropical Fruits Co.',
            'Root Harvest Farms' => 'Root Harvest Supply',
            'Summer Fruits Co.' => 'Summer Fruits Harvest',
            'NutriGreens' => 'NutriGreens Philippines',
        ];

        // Update all products with new brand names
        $updatedCount = 0;
        foreach ($brandMappings as $oldBrand => $newBrand) {
            $products = Product::where('brand', $oldBrand)->get();
            
            foreach ($products as $product) {
                $product->update(['brand' => $newBrand]);
                $updatedCount++;
                $this->command->info("Updated '{$product->name}': '{$oldBrand}' → '{$newBrand}'");
            }
        }

        $this->command->info("Brand update completed!");
        $this->command->info("Total products updated: {$updatedCount}");

        // Show final unique brands
        $uniqueBrands = Product::distinct()
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->pluck('brand')
            ->unique()
            ->sort()
            ->values();

        $this->command->info('Updated brand list:');
        foreach ($uniqueBrands as $brand) {
            $this->command->info("- {$brand}");
        }
    }
}
