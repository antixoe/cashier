<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Services\ActivityLogService;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(12);
        return view('management.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create($data);
        ActivityLogService::log(
            action: 'create',
            description: "Created category: {$category->name}",
            modelType: 'Category',
            modelId: $category->id,
            newValues: $data
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category created successfully', 'category' => $category]);
        }

        return redirect()->route('management.categories')->with('success', 'Category created successfully');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $old = $category->only(['name', 'description']);
        $category->update($data);

        ActivityLogService::log(
            action: 'update',
            description: "Updated category: {$category->name}",
            modelType: 'Category',
            modelId: $category->id,
            oldValues: $old,
            newValues: $data
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category updated successfully', 'category' => $category]);
        }

        return redirect()->route('management.categories')->with('success', 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        ActivityLogService::log(
            action: 'delete',
            description: "Deleted category: {$category->name}",
            modelType: 'Category',
            modelId: $category->id,
            oldValues: $category->only(['name', 'description'])
        );

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
        }

        return redirect()->route('management.categories')->with('success', 'Category deleted successfully');
    }
}