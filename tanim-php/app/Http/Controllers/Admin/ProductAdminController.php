<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ProductsImport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProductAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::withTrashed()->with('photos');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('category', 'like', '%' . $request->search . '%')
                    ->orWhere('brand', 'like', '%' . $request->search . '%')
                    ->orWhere('type', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('status')) {
            if ($request->status === 'trashed') $query->onlyTrashed();
            elseif ($request->status === 'inactive') $query->where('is_active', false)->whereNull('deleted_at');
            else $query->where('is_active', true)->whereNull('deleted_at');
        }

        $products   = $query->latest()->get();
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
            'category'      => 'required|string|max:120',
            'brand'         => 'nullable|string|max:120',
            'type'          => 'nullable|string|max:120',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'farm_location' => 'nullable|string|max:200',
            'harvest_date'  => 'nullable|date',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'photos'        => 'nullable|array|max:3',
            'photos.*'      => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', true);
        $data['user_id']   = Auth::id(); // admin owns all products

        $product = Product::create($data);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $index => $photo) {
                $path = $photo->store('products/photos', 'public');
                $product->photos()->create([
                    'path' => $path,
                    'is_primary' => $index === 0,
                    'sort_order' => $index,
                ]);

                if (empty($product->image) && $index === 0) {
                    $product->image = $path;
                    $product->save();
                }
            }
        }

        ActivityLog::record('create', "Admin created product: {$product->name}", $product);

        return redirect()->route('admin.products.index')->with('success', "Product '{$product->name}' created.");
    }

    public function edit(Product $product)
    {
        $product->load('photos');
        $categories = ['Vegetables', 'Fruits', 'Grains & Rice', 'Root Crops', 'Herbs & Spices', 'Livestock', 'Seafood', 'Dairy', 'Other'];
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:200',
            'category'      => 'required|string|max:120',
            'brand'         => 'nullable|string|max:120',
            'type'          => 'nullable|string|max:120',
            'description'   => 'nullable|string',
            'price'         => 'required|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'stock'         => 'required|integer|min:0',
            'farm_location' => 'nullable|string|max:200',
            'harvest_date'  => 'nullable|date',
            'is_active'     => 'boolean',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'photos'        => 'nullable|array|max:3',
            'photos.*'      => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }
        $data['is_active'] = $request->boolean('is_active', true);

        $product->update($data);

        if ($request->hasFile('photos')) {
            $existingCount = $product->photos()->count();
            $incomingCount = count($request->file('photos'));
            if (($existingCount + $incomingCount) > 3) {
                return back()
                    ->withErrors(['photos' => 'You can only keep up to 3 gallery photos per product.'])
                    ->withInput();
            }

            $nextOrder = ((int) $product->photos()->max('sort_order')) + 1;
            $hasPrimary = $product->photos()->where('is_primary', true)->exists();

            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('products/photos', 'public');
                $product->photos()->create([
                    'path' => $path,
                    'is_primary' => !$hasPrimary,
                    'sort_order' => $nextOrder++,
                ]);

                if (!$hasPrimary) {
                    $hasPrimary = true;
                }
            }
        }

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
        foreach ($product->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
        }
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->forceDelete();
        ActivityLog::record('delete', "Admin permanently deleted product: {$name}");
        return back()->with('success', "Product '{$name}' permanently deleted.");
    }

    public function destroyPhoto(Product $product, ProductPhoto $photo)
    {
        if ($photo->product_id !== $product->id) {
            abort(404);
        }

        $wasPrimary = $photo->is_primary;
        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        if ($wasPrimary || $product->image === $photo->path) {
            $nextPhoto = $product->photos()->orderBy('sort_order')->first();
            if ($nextPhoto) {
                $nextPhoto->update(['is_primary' => true]);
                $product->update(['image' => $nextPhoto->path]);
            } else {
                $product->update(['image' => null]);
            }
        }

        ActivityLog::record('delete', "Admin removed product photo: {$product->name}", $product);

        return response()->json(['success' => true, 'message' => 'Photo deleted']);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $import = new ProductsImport((int) Auth::id());

        try {
            Excel::import($import, $request->file('file'));
        } catch (Throwable $e) {
            return back()->withErrors([
                'file' => 'Import failed. Ensure the worksheet has headings: name, category, description, price, unit, stock, farm_location, harvest_date, brand, type.',
            ]);
        }

        ActivityLog::record('create', "Admin imported {$import->imported} products from worksheet.");

        return back()->with('success', "Imported {$import->imported} products successfully.");
    }
}
