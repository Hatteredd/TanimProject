<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::all()->groupBy('group');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $inputs = $request->except(['_token', '_method']);

        foreach ($inputs as $key => $value) {
            SystemSetting::set($key, $value);
        }

        // Handle unchecked booleans (checkboxes not submitted = false)
        $boolKeys = SystemSetting::where('type', 'boolean')->pluck('key');
        foreach ($boolKeys as $key) {
            if (!array_key_exists($key, $inputs)) {
                SystemSetting::set($key, '0');
            }
        }

        ActivityLog::record('update', 'Admin updated system settings');
        return back()->with('success', 'Settings saved successfully.');
    }
}
