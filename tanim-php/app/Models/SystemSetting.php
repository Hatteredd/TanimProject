<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type', 'label'];

    public static function get(string $key, $default = null)
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function defaults(): array
    {
        return [
            // General
            ['key' => 'site_name',        'value' => 'Tanim',                        'group' => 'general',  'type' => 'text',     'label' => 'Site Name'],
            ['key' => 'site_tagline',     'value' => 'Farm to Table, Direct to You', 'group' => 'general',  'type' => 'text',     'label' => 'Site Tagline'],
            ['key' => 'maintenance_mode', 'value' => '0',                            'group' => 'general',  'type' => 'boolean',  'label' => 'Maintenance Mode'],
            ['key' => 'allow_register',   'value' => '1',                            'group' => 'general',  'type' => 'boolean',  'label' => 'Allow New Registrations'],
            // Email
            ['key' => 'mail_from_name',   'value' => 'Tanim',                        'group' => 'email',    'type' => 'text',     'label' => 'Mail From Name'],
            ['key' => 'mail_from_email',  'value' => 'noreply@tanim.ph',             'group' => 'email',    'type' => 'text',     'label' => 'Mail From Email'],
            ['key' => 'order_notify',     'value' => '1',                            'group' => 'email',    'type' => 'boolean',  'label' => 'Send Order Notifications'],
            // Security
            ['key' => 'require_verify',   'value' => '1',                            'group' => 'security', 'type' => 'boolean',  'label' => 'Require Email Verification'],
            ['key' => 'session_lifetime', 'value' => '120',                          'group' => 'security', 'type' => 'text',     'label' => 'Session Lifetime (minutes)'],
        ];
    }
}
