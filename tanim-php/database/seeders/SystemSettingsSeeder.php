<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (SystemSetting::defaults() as $d) {
            SystemSetting::firstOrCreate(['key' => $d['key']], $d);
        }
    }
}
