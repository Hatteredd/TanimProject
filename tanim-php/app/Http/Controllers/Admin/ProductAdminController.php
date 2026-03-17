<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withTrashed();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status')) {
            if ($request->status === 'trashed') $query->onlyTrashed();
            elseif ($request->status === 'inactive') $query->where('is_active', false)->whereNull('deleted_at');
            else $query->where('is_active', true)->whereNull('deleted_at');
        }

        $products   = $query->latest()->paginate(20)->withQueryString();
        $categories = Product::distinct()->pluck('category');

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ['Vegetables', 'Fruits', 'Grains & Rice', 'Root Crops', 'Herbs & Spices', 'Livestock', 'Seafood', 'Dairy', 'Other'];
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'category'      => 'required|string',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'farm_location' => 'nullable|string|max:200',
            'harvest_date'  => 'nullable|date',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $data['user_id']   = Auth::id(); // admin owns all products

        $product = Product::create($data);
        ActivityLog::record('create', "Admin created product: {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created.");
    }

    public function edit(Product $product)
    {
        $categories = ['Vegetables', 'Fruits', 'Grains & Rice', 'Root Crops', 'Herbs & Spices', 'Livestock', 'Seafood', 'Dairy', 'Other'];
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'category'      => 'required|string',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'farm_location' => 'nullable|string|max:200',
            'harvest_date'  => 'nullable|date',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);
        ActivityLog::record('update', "Admin updated product: {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' updated.");
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        ActivityLog::record('delete', "Admin soft-deleted product: {$name}");
        return back()->with('success', "Product '{$name}' deleted.");
    }

    public function restore(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();
        ActivityLog::record('update', "Admin restored product: {$product->name}", $product);
        return back()->with('success', "Product '{$product->name}' restored.");
    }

    public function forceDelete(int $id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $name = $product->name;
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->forceDelete();
        ActivityLog::record('delete', "Admin permanently deleted product: {$name}");
        return back()->with('success', "Product '{$name}' permanently deleted.");
    }
}
