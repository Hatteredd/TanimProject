<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function home(Request $request)
    {
        $featured = Product::where('is_active', true)
            ->where('stock', '>', 0)
            ->latest()
            ->limit(8)
            ->get();

        $categories = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        $brands = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');

        $types = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $searchResults = null;

        if ($request->filled('q')) {
            $searchResults = Product::search($request->string('q')->toString())
                ->query(fn (Builder $query) => $this->applyFilters($query, $request))
                ->paginate(12)
                ->withQueryString();
        }

        return view('home', compact('featured', 'searchResults', 'categories', 'brands', 'types'));
    }

    // ── Public marketplace ──────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)->where('stock', '>', 0);

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('category', 'like', $term)
                    ->orWhere('brand', 'like', $term)
                    ->orWhere('type', 'like', $term);
            });
        }

        $this->applyFilters($query, $request);

        match ($request->sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating' => $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        $brands = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('brand')
            ->where('brand', '!=', '')
            ->distinct()
            ->orderBy('brand')
            ->pluck('brand');
        $types = Product::where('is_active', true)
            ->whereNull('deleted_at')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        return view('marketplace', compact('products', 'categories', 'brands', 'types'));
    }

    public function show(Product $product)
    {
        $product->load(['reviews.user', 'photos']);

        $reviews = $product->reviews()->with('user')->latest()->get();
        $avgRating = round($reviews->avg('rating') ?? 0, 1);
        $reviewCount = $reviews->count();

        $userReview = Auth::check()
            ? $reviews->firstWhere('user_id', Auth::id())
            : null;

        $canReview = false;
        if (Auth::check()) {
            $canReview = Order::where('user_id', Auth::id())
                ->where('status', 'delivered')
                ->whereHas('items', fn (Builder $q) => $q->where('product_id', $product->id))
                ->exists();
        }

        $fromSameFarm = Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->limit(6)->get();

        return view('product-detail', compact('product', 'reviews', 'avgRating', 'reviewCount', 'fromSameFarm', 'userReview', 'canReview'));
    }

    private function applyFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (float) $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        return $query;
    }
}
