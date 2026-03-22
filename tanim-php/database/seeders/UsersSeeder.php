<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@tanim.ph'],
            [
                'name' => 'Admin Tanim',
                'role' => 'admin',
                'password' => Hash::make('admin123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $admin->forceFill([
            'role' => 'admin',
            'password' => Hash::make('admin123'),
            'is_active' => true,
            'email_verified_at' => $admin->email_verified_at ?: now(),
        ])->save();

        $customer = User::updateOrCreate(
            ['email' => 'customer@tanim.ph'],
            [
                'name' => 'Customer Tanim',
                'role' => 'buyer',
                'password' => Hash::make('customer123'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $customer->forceFill([
            'role' => 'buyer',
            'password' => Hash::make('customer123'),
            'is_active' => true,
            'email_verified_at' => $customer->email_verified_at ?: now(),
        ])->save();

        $faker = fake();

        for ($i = 0; $i < 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => $faker->unique()->safeEmail()],
                [
                    'name' => $faker->name(),
                    'role' => 'buyer',
                    'password' => Hash::make('password'),
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            if (!$user->role) {
                $user->role = 'buyer';
            }

            if (!$user->email_verified_at) {
                $user->email_verified_at = now();
            }

            if (!isset($user->is_active) || $user->is_active === null) {
                $user->is_active = true;
            }

            $user->save();
        }
    }
}
