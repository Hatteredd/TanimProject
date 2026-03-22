<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        
        if (!$admin) {
            return;
        }

        // Create sample activity logs for the past 30 days
        $activities = [
            ['login', 'User authenticated', '2026-03-22 09:00:00'],
            ['create', 'Created new product', '2026-03-21 14:30:00'],
            ['update', 'Updated product information', '2026-03-20 11:15:00'],
            ['delete', 'Deleted expired product', '2026-03-19 16:45:00'],
            ['export', 'Exported sales report', '2026-03-18 10:30:00'],
            ['import', 'Imported product catalog', '2026-03-17 13:20:00'],
            ['login', 'User authenticated', '2026-03-16 08:45:00'],
            ['create', 'Created new order', '2026-03-15 10:20:00'],
            ['update', 'Updated order status', '2026-03-14 15:30:00'],
            ['delete', 'Cancelled fraudulent order', '2026-03-13 09:15:00'],
            ['export', 'Generated customer report', '2026-03-12 14:00:00'],
            ['login', 'User authenticated', '2026-03-11 07:30:00'],
            ['create', 'Added new expense', '2026-03-10 16:45:00'],
            ['update', 'Modified system settings', '2026-03-09 11:20:00'],
            ['delete', 'Removed old records', '2026-03-08 13:30:00'],
            ['export', 'Exported analytics data', '2026-03-07 09:15:00'],
        ];

        foreach ($activities as [$action, $description, $dateTime]) {
            ActivityLog::create([
                'user_id' => $admin->id,
                'action' => $action,
                'description' => $description,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'created_at' => Carbon::parse($dateTime),
                'updated_at' => Carbon::parse($dateTime),
            ]);
        }
    }
}
