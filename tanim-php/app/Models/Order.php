<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'status', 'total_amount',
        'shipping_address', 'contact_number', 'notes', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public static function statuses(): array
    {
        return ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
    }

    public static function statusColors(): array
    {
        return [
            'pending'    => '#d97706',
            'confirmed'  => '#2563eb',
            'processing' => '#7c3aed',
            'shipped'    => '#0891b2',
            'delivered'  => '#16a34a',
            'cancelled'  => '#dc2626',
        ];
    }

    public static function statusBgColors(): array
    {
        return [
            'pending'    => 'rgba(217,119,6,0.12)',
            'confirmed'  => 'rgba(37,99,235,0.12)',
            'processing' => 'rgba(124,58,237,0.12)',
            'shipped'    => 'rgba(8,145,178,0.12)',
            'delivered'  => 'rgba(22,163,74,0.12)',
            'cancelled'  => 'rgba(220,38,38,0.12)',
        ];
    }

    public function statusBg(): string
    {
        return self::statusBgColors()[$this->status] ?? 'rgba(107,114,128,0.12)';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusColor(): string
    {
        return self::statusColors()[$this->status] ?? '#6b7280';
    }

    public static function generateOrderNumber(): string
    {
        return 'TN-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');
    }
}
