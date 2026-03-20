<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        // Only buyers who purchased the product can review
        $hasPurchased = Order::where('user_id', Auth::id())
            ->where('status', 'delivered')
            ->whereHas('items', fn($q) => $q->where('product_id', $product->id))
            ->exists();

        if (!$hasPurchased) {
            return back()->withErrors(['review' => 'You can only review products you have purchased.']);
        }

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'product_id' => $product->id],
            $validated
        );

        return back()->with('success', 'Review submitted successfully.');
    }

    public function update(Request $request, Review $review)
    {
        abort_if($review->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'rating'  => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $review->update($validated);

        return back()->with('success', 'Review updated.');
    }

    public function destroy(Review $review)
    {
        $user = Auth::user();
        abort_if($review->user_id !== $user->id && $user->role !== 'admin', 403);
        $review->delete();
        return back()->with('success', 'Review deleted.');
    }
}
