<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Laguna Greens Cooperative', 'location' => 'Laguna, Luzon', 'specialty' => 'Leafy vegetables', 'contact_number' => '09171234567', 'status' => 'active'],
            ['name' => 'Benguet Highland Farms', 'location' => 'Benguet, Cordillera', 'specialty' => 'Highland vegetables', 'contact_number' => '09182345678', 'status' => 'active'],
            ['name' => 'Nueva Ecija Grain Hub', 'location' => 'Nueva Ecija, Central Luzon', 'specialty' => 'Rice and grains', 'contact_number' => '09193456789', 'status' => 'active'],
            ['name' => 'Mindanao Tropics', 'location' => 'Davao del Sur, Mindanao', 'specialty' => 'Tropical fruits', 'contact_number' => '09204567890', 'status' => 'active'],
            ['name' => 'Visayas Orchard Group', 'location' => 'Guimaras, Western Visayas', 'specialty' => 'Mangoes and citrus', 'contact_number' => '09215678901', 'status' => 'active'],
        ];

        foreach ($suppliers as $supplier) {
            Employee::updateOrCreate(
                ['name' => $supplier['name']],
                $supplier
            );
        }
    }
}
