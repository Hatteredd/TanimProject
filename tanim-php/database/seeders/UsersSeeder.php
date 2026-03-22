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

        $buyers = [
            ['name' => 'John Llegado', 'email' => 'johnllegado20@gmail.com'],
            ['name' => 'Maria Santos', 'email' => 'buyer1@tanim.ph'],
            ['name' => 'Pedro Reyes', 'email' => 'buyer2@tanim.ph'],
            ['name' => 'Ana Buencamino', 'email' => 'buyer3@tanim.ph'],
            ['name' => 'Maria Santos Demo', 'email' => 'buyer@tanim.ph'],
        ];

        foreach ($buyers as $buyer) {
            $user = User::firstOrCreate(
                ['email' => $buyer['email']],
                [
                    'name' => $buyer['name'],
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
