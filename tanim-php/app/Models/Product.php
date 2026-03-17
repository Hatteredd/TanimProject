<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'category', 'description',
        'price', 'unit', 'stock', 'image', 'is_active',
        'farm_location', 'harvest_date',
    ];

    protected $casts = [
        'price'        => 'decimal:2',
        'is_active'    => 'boolean',
        'harvest_date' => 'date',
    ];

    public function farmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function avgRating(): float
    {
        return round($this->reviews()->avg('rating') ?? 0, 1);
    }

    public function primaryPhoto(): ?string
    {
        $primary = $this->photos()->where('is_primary', true)->first();
        if ($primary) return asset('storage/' . $primary->path);
        $first = $this->photos()->first();
        if ($first) return asset('storage/' . $first->path);
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
