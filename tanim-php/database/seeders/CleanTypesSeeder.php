<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class CleanTypesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all products and clean up their types
        $products = Product::whereNotNull('type')->where('type', '!=', '')->get();
        
        foreach ($products as $product) {
            $cleanType = $this->cleanType($product->type);
            if ($cleanType !== $product->type) {
                $product->update(['type' => $cleanType]);
            }
        }

        // Show final unique types
        $uniqueTypes = Product::distinct()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->pluck('type')
            ->unique()
            ->sort()
            ->values();

        $this->command->info('Types have been cleaned!');
        $this->command->info('Final unique types: ' . $uniqueTypes->implode(', '));
    }

    private function cleanType($type)
    {
        // Standardize the type
        return trim(ucwords(strtolower($type)));
    }
}
