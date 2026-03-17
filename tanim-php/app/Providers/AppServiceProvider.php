<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Policies\CartItemPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(CartItem::class, CartItemPolicy::class);
    }
}
