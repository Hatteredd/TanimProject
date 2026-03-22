<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixDuplicateTypesSeeder extends Seeder
{
    public function run(): void
    {
        // Get all products with types and standardize them
        $typeMapping = [
            'Bitter Melon' => 'Bitter Melon',
            'bitter melon' => 'Bitter Melon',
            'BITTER MELON' => 'Bitter Melon',
            'Leafy Green' => 'Leafy Green',
            'leafy green' => 'Leafy Green',
            'LEAFY GREEN' => 'Leafy Green',
            'Root Crop' => 'Root Crop',
            'root crop' => 'Root Crop',
            'ROOT CROP' => 'Root Crop',
            'Tropical Fruit' => 'Tropical Fruit',
            'tropical fruit' => 'Tropical Fruit',
            'TROPICAL FRUIT' => 'Tropical Fruit',
            'Citrus Fruit' => 'Citrus Fruit',
            'citrus fruit' => 'Citrus Fruit',
            'CITRUS FRUIT' => 'Citrus Fruit',
            'Nightshade' => 'Nightshade',
            'nightshade' => 'Nightshade',
            'NIGHTSHADE' => 'Nightshade',
            'Legume' => 'Legume',
            'legume' => 'Legume',
            'LEGUME' => 'Legume',
            'Premium Rice' => 'Premium Rice',
            'premium rice' => 'Premium Rice',
            'PREMIUM RICE' => 'Premium Rice',
            'Glutinous Rice' => 'Glutinous Rice',
            'glutinous rice' => 'Glutinous Rice',
            'GLUTINOUS RICE' => 'Glutinous Rice',
            'Feed Corn' => 'Feed Corn',
            'feed corn' => 'Feed Corn',
            'FEED CORN' => 'Feed Corn',
            'Taro' => 'Taro',
            'taro' => 'Taro',
            'TARO' => 'Taro',
            'Cassava' => 'Cassava',
            'cassava' => 'Cassava',
            'CASSAVA' => 'Cassava',
            'Medicinal Herb' => 'Medicinal Herb',
            'medicinal herb' => 'Medicinal Herb',
            'MEDICINAL HERB' => 'Medicinal Herb',
            'Flavoring' => 'Flavoring',
            'flavoring' => 'Flavoring',
            'FLAVORING' => 'Flavoring',
            'Rhizome Spice' => 'Rhizome Spice',
            'rhizome spice' => 'Rhizome Spice',
            'RHIZOME SPICE' => 'Rhizome Spice',
            'Sweet Pepper' => 'Sweet Pepper',
            'sweet pepper' => 'Sweet Pepper',
            'SWEET PEPPER' => 'Sweet Pepper',
            'Lime' => 'Lime',
            'lime' => 'Lime',
            'LIME' => 'Lime',
            'Root Vegetable' => 'Root Vegetable',
            'root vegetable' => 'Root Vegetable',
            'ROOT VEGETABLE' => 'Root Vegetable',
            'Melon' => 'Melon',
            'melon' => 'Melon',
            'MELON' => 'Melon',
            'Pawpaw' => 'Pawpaw',
            'pawpaw' => 'Pawpaw',
            'PAWPAW' => 'Pawpaw',
        ];

        // Update all products with standardized types
        foreach ($typeMapping as $from => $to) {
            Product::where('type', $from)->update(['type' => $to]);
        }

        // Show current unique types after fixing
        $uniqueTypes = Product::distinct()
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->pluck('type')
            ->unique()
            ->sort()
            ->values();

        $this->command->info('Duplicate types have been fixed!');
        $this->command->info('Unique types: ' . $uniqueTypes->implode(', '));
    }
}
