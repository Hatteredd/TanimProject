<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class RemoveNonAgriculturalProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Define non-agricultural product keywords to remove
        $nonAgriculturalKeywords = [
            'beer', 'wine', 'liquor', 'alcohol', 'whiskey', 'gin', 'rum', 'brandy',
            'cigarette', 'tobacco', 'vape', 'smoking',
            'medicine', 'drug', 'pharmaceutical', 'vitamin', 'supplement',
            'cosmetic', 'makeup', 'beauty', 'skincare', 'lotion', 'cream',
            'electronics', 'phone', 'gadget', 'computer', 'appliance', 'device',
            'clothing', 'shirt', 'pants', 'dress', 'shoes', 'fashion',
            'furniture', 'chair', 'table', 'bed', 'sofa', 'cabinet',
            'toy', 'game', 'playstation', 'xbox', 'nintendo', 'lego',
            'car', 'motorcycle', 'vehicle', 'tire', 'battery', 'oil',
            'book', 'magazine', 'newspaper', 'paper', 'office',
            'cleaning', 'soap', 'detergent', 'bleach', 'disinfectant',
            'construction', 'cement', 'steel', 'nail', 'hammer', 'paint',
            'jewelry', 'gold', 'silver', 'diamond', 'ring', 'necklace',
            'service', 'repair', 'delivery', 'shipping', 'consulting',
        ];

        // Agricultural categories to keep
        $agriculturalCategories = [
            'Vegetables', 'Fruits', 'Grains & Rice', 'Root Crops', 'Herbs & Spices', 'Livestock', 'Seafood', 'Dairy'
        ];

        // Get all products
        $products = Product::all();
        $removedCount = 0;
        $keptCount = 0;

        foreach ($products as $product) {
            $productName = strtolower($product->name);
            $productCategory = strtolower($product->category ?? '');
            
            // Check if product contains non-agricultural keywords
            $isNonAgricultural = false;
            foreach ($nonAgriculturalKeywords as $keyword) {
                if (strpos($productName, $keyword) !== false) {
                    $isNonAgricultural = true;
                    break;
                }
            }

            // Also check if category is non-agricultural
            $isValidCategory = in_array($productCategory, $agriculturalCategories);

            // Remove if non-agricultural or invalid category
            if ($isNonAgricultural || !$isValidCategory) {
                $product->delete();
                $removedCount++;
                $this->command->info("Removed: {$product->name} ({$product->category}) - Non-agricultural");
            } else {
                $keptCount++;
            }
        }

        $this->command->info("Non-agricultural products cleanup completed!");
        $this->command->info("Products removed: {$removedCount}");
        $this->command->info("Products kept: {$keptCount}");

        // Show remaining products by category
        $remainingProducts = Product::all();
        $categoryCounts = [];
        
        foreach ($remainingProducts as $product) {
            $category = $product->category;
            if (!isset($categoryCounts[$category])) {
                $categoryCounts[$category] = 0;
            }
            $categoryCounts[$category]++;
        }

        $this->command->info("Remaining products by category:");
        foreach ($categoryCounts as $category => $count) {
            $this->command->info("- {$category}: {$count}");
        }
    }
}
