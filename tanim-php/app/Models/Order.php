<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const VAT_RATE = 0.12;
    public const SHIPPING_FEE = 100.00;

    protected $fillable = [
        'user_id', 'order_number', 'status',
        'shipping_address', 'contact_number', 'notes', 'paid_at', 'payment_method',
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

    public static function paymentMethods(): array
    {
        return [
            'cod' => [
                'label' => 'Cash on Delivery (COD)',
                'details' => 'Pay in cash once the order is delivered.',
            ],
            'gcash' => [
                'label' => 'GCash',
                'details' => 'Selection only; no automatic charge is performed in-app.',
            ],
            'bank_transfer' => [
                'label' => 'Bank Transfer',
                'details' => 'Selection only; transfer arrangement is handled outside checkout.',
            ],
        ];
    }

    public function paymentMethodLabel(): string
    {
        return self::paymentMethods()[$this->payment_method]['label'] ?? 'Not specified';
    }

    public function paymentMethodDetails(): ?string
    {
        return self::paymentMethods()[$this->payment_method]['details'] ?? null;
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

    public function scopeWithComputedTotal($query)
    {
        return $query->addSelect([
            'computed_total_amount' => OrderItem::query()
                ->selectRaw('COALESCE(SUM(unit_price * quantity), 0) * ? + ?', [1 + self::VAT_RATE, self::SHIPPING_FEE])
                ->whereColumn('order_id', 'orders.id'),
        ]);
    }

    public function getSubtotalAttribute(): float
    {
        if ($this->relationLoaded('items')) {
            return (float) $this->items->sum(fn (OrderItem $item) => ((float) $item->unit_price) * ((int) $item->quantity));
        }

        return (float) ($this->items()
            ->selectRaw('COALESCE(SUM(unit_price * quantity), 0) as total')
            ->value('total') ?? 0);
    }

    public function getVatAmountAttribute(): float
    {
        return $this->subtotal * self::VAT_RATE;
    }

    public function getShippingFeeAttribute(): float
    {
        return self::SHIPPING_FEE;
    }

    public function getCalculatedTotalAttribute(): float
    {
        return $this->subtotal + $this->vat_amount + $this->shipping_fee;
    }

    public function getTotalAmountAttribute($value): float
    {
        // If we have a stored value in the database and it's greater than 0, use it.
        // Otherwise, calculate it dynamically from line items.
        if ($value !== null && (float) $value > 0) {
            return (float) $value;
        }

        $computed = $this->attributes['computed_total_amount'] ?? null;
        if ($computed !== null && (float) $computed > 0) {
            return (float) $computed;
        }

        return $this->calculated_total;
    }

    public static function generateOrderNumber(): string
    {
        return 'TN-' . strtoupper(substr(uniqid(), -6)) . '-' . date('Ymd');
    }
}
