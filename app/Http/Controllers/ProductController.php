<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\ActivityLogService;

class ProductController extends Controller
{
    /**
     * Display all products
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category_id');
        $showTrashed = $request->query('show_trashed', false);

        $query = Product::with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($showTrashed && auth()->user()->hasRole('superadmin')) {
            $query = $query->onlyTrashed();
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('management.products.index', compact('products', 'categories', 'categoryId', 'showTrashed'));
    }

    /**
     * Show create product form
     */
    public function create()
    {
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('management.products.create', compact('categories'));
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|max:100|unique:products,code',
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $product = Product::create($data);

        // Log activity
        ActivityLogService::log(
            action: 'create',
            description: "Created new product: {$product->name} (Rp " . number_format($product->price, 2) . ")",
            modelType: 'Product',
            modelId: $product->id,
            newValues: $data
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product created successfully', 'product' => $product]);
        }

        return redirect()->route('management.products.index')->with('success', 'Product created successfully');
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $categories = \App\Models\Category::orderBy('name')->get();
        return view('management.products.edit', compact('product', 'categories'));
    }

    /**
     * Update a product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'code' => 'required|string|max:100|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $oldData = [
            'code' => $product->code,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
        ];

        $product->update($data);

        // Log activity
        ActivityLogService::log(
            action: 'update',
            description: "Updated product: {$product->name}",
            modelType: 'Product',
            modelId: $product->id,
            oldValues: $oldData,
            newValues: $data
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product updated successfully', 'product' => $product]);
        }

        return redirect()->route('management.products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Delete a product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $productName = $product->name;
        $productPrice = $product->price;

        // Log activity before deletion
        ActivityLogService::log(
            action: 'delete',
            description: "Soft deleted product: {$productName} (Rp " . number_format($productPrice, 2) . ")",
            modelType: 'Product',
            modelId: $product->id,
            oldValues: [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
            ]
        );

        $product->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product soft-deleted successfully']);
        }

        return redirect()->route('management.products.index')->with('success', 'Product soft-deleted successfully');
    }

    public function restore($id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can restore products.');
        }

        $product = Product::withTrashed()->findOrFail($id);

        $product->restore();

        ActivityLogService::log(
            action: 'restore',
            description: "Restored product: {$product->name}",
            modelType: 'Product',
            modelId: $product->id
        );

        return redirect()->route('management.products.index', ['show_trashed' => 1])->with('success', 'Product restored successfully');
    }

    public function forceDelete($id)
    {
        if (!auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete products permanently.');
        }

        $product = Product::withTrashed()->findOrFail($id);
        $product->forceDelete();

        ActivityLogService::log(
            action: 'force_delete',
            description: "Permanently deleted product: {$product->name}",
            modelType: 'Product',
            modelId: $id
        );

        return redirect()->route('management.products.index', ['show_trashed' => 1])->with('success', 'Product permanently deleted');
    }

    /**
     * Get product details (AJAX)
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }
}
