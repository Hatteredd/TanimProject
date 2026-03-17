<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    // ── Public marketplace ──────────────────────────────────────────
    public function index(Request $request)
    {

        $query = Product::where('is_active', true)->where('stock', '>', 0);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('description', 'like', $term)
                  ->orWhere('category', 'like', $term);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        match ($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = Product::where('is_active', true)->whereNull('deleted_at')->distinct()->pluck('category');

        return view('marketplace', compact('products', 'categories'));
    }

    public function show(Product $product)
    {

        $product->load(['reviews.user', 'photos']);

        $reviews     = $product->reviews()->with('user')->latest()->get();
        $avgRating   = round($reviews->avg('rating') ?? 0, 1);
        $reviewCount = $reviews->count();

        $userReview = Auth::check()
            ? $reviews->firstWhere('user_id', Auth::id())
            : null;

        $fromSameFarm = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(6)->get();

        return view('product-detail', compact('product', 'reviews', 'avgRating', 'reviewCount', 'fromSameFarm', 'userReview'));
    }
}
