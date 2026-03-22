<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Product;
use Illuminate\Database\Seeder;

class AssignSuppliersToProductsSeeder extends Seeder
{
    public function run(): void
    {
        // Get all active suppliers
        $suppliers = Employee::where('status', 'active')->get();
        
        if ($suppliers->isEmpty()) {
            $this->command->error('No active suppliers found!');
            return;
        }

        // Get all products without suppliers
        $products = Product::whereNull('supplier_id')->get();
        
        if ($products->isEmpty()) {
            $this->command->info('All products already have suppliers assigned!');
            return;
        }

        // Assign suppliers to products based on category and brand
        foreach ($products as $product) {
            $supplier = $this->selectSupplier($product, $suppliers);
            
            if ($supplier) {
                $product->update(['supplier_id' => $supplier->id]);
                $this->command->info("Assigned supplier '{$supplier->name}' to product '{$product->name}'");
            }
        }

        $this->command->info('Supplier assignment completed!');
        $this->command->info("Total products updated: {$products->count()}");
    }

    private function selectSupplier($product, $suppliers)
    {
        // Smart supplier assignment based on product characteristics
        
        // Match by brand name similarity
        $brandMatches = [
            'Green Valley Farms' => ['vegetables', 'leafy'],
            'Highland Organic' => ['vegetables', 'organic'],
            'Sweet Roots Co.' => ['root', 'cassava', 'kamote'],
            'Veggie Fresh' => ['vegetables', 'fresh'],
            'Baguio Greens' => ['vegetables', 'baguio'],
            'Bean Harvest' => ['beans', 'legumes'],
            'Davao Banana Farm' => ['banana', 'fruits'],
            'Guimaras Mango Co.' => ['mango', 'fruits'],
            'Batangas Citrus' => ['citrus', 'calamansi', 'dalandan'],
            'Tropical Fruits Inc.' => ['tropical', 'fruits'],
            'Nueva Ecija Rice Mills' => ['rice', 'grains'],
            'Cordillera Heritage' => ['rice', 'black', 'glutinous'],
            'Bukidnon Corn Co.' => ['corn', 'grains'],
            'Root Crops PH' => ['root', 'gabi', 'taro'],
            'Mindanao Tuber Farms' => ['cassava', 'tuber'],
            'Aromatic Herbs Co.' => ['herbs', 'lemongrass'],
            'Flavor Leaves Inc.' => ['pandan', 'flavor'],
            'Spice Islands' => ['turmeric', 'spices'],
            'Mountain Fresh Veggies' => ['vegetables', 'bell pepper'],
            'Citrus Grove' => ['citrus', 'calamansi'],
            'Leafy Greens Co.' => ['vegetables', 'spinach', 'malunggay'],
            'Tropical Fruits PH' => ['pineapple', 'tropical'],
            'Root Harvest Farms' => ['vegetables', 'carrots'],
            'Summer Fruits Co.' => ['fruits', 'melon', 'guyabano'],
            'NutriGreens' => ['vegetables', 'malunggay'],
        ];

        // Try to match by brand
        foreach ($brandMatches as $brand => $keywords) {
            if (stripos($product->brand ?? '', $brand) !== false) {
                $matchedSupplier = $suppliers->firstWhere('name', 'like', '%' . substr($brand, 0, 10) . '%');
                if ($matchedSupplier) return $matchedSupplier;
            }
        }

        // Try to match by product name/category
        $productName = strtolower($product->name);
        $category = strtolower($product->category);

        foreach ($brandMatches as $brand => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($productName, $keyword) !== false || stripos($category, $keyword) !== false) {
                    $matchedSupplier = $suppliers->firstWhere('name', 'like', '%' . substr($brand, 0, 10) . '%');
                    if ($matchedSupplier) return $matchedSupplier;
                }
            }
        }

        // Fallback: assign random supplier
        return $suppliers->random();
    }
}
