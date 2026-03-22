<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_name', 'unit_price', 'quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function getSubtotalAttribute($value): float
    {
        if ($value !== null) {
            return (float) $value;
        }

        return ((float) $this->unit_price) * ((int) $this->quantity);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
