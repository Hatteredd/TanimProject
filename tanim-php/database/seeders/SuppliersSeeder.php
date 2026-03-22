<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class SuppliersSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $specialties = ['Leafy vegetables', 'Highland vegetables', 'Rice and grains', 'Tropical fruits', 'Root crops'];

        for ($i = 0; $i < 8; $i++) {
            Employee::updateOrCreate(
                ['name' => $faker->unique()->company()],
                [
                    'location' => $faker->city() . ', ' . $faker->state(),
                    'specialty' => $faker->randomElement($specialties),
                    'contact_number' => '09' . $faker->numerify('#########'),
                    'status' => 'active',
                ]
            );
        }
    }
}
