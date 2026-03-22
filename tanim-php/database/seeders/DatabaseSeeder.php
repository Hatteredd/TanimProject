<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\OrderSeeder;
use Database\Seeders\ProductBrandTypeSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,
            SuppliersSeeder::class,
            MarketplaceSeeder::class,
            OrderSeeder::class,
            ActivityLogSeeder::class,
            SystemSettingsSeeder::class,
        ]);
    }
}
