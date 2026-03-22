<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'product.supplier'])->latest();

        if ($request->filled('search')) {
            $term = '%' . $request->search . '%';
            $query->where(function ($q) use ($term) {
                $q->whereHas('user', fn($uq) => $uq->where('name', 'like', $term))
                  ->orWhereHas('product', fn($pq) => $pq->where('name', 'like', $term));
            });
        }

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', (int) $request->product_id);
        }

        if ($request->filled('supplier_id')) {
            $supplierId = (int) $request->supplier_id;
            $query->whereHas('product', fn($pq) => $pq->where('supplier_id', $supplierId));
        }

        $reviews = $query->paginate(15)->withQueryString();

        $products = Product::whereHas('reviews')
            ->orderBy('name')
            ->get(['id', 'name']);

        $supplierIds = Product::whereHas('reviews')
            ->whereNotNull('supplier_id')
            ->distinct()
            ->pluck('supplier_id');

        $suppliers = Employee::whereIn('id', $supplierIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.reviews.index', compact('reviews', 'products', 'suppliers'));
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
