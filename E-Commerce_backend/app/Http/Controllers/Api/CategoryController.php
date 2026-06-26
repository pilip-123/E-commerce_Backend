<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('products')->orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('products');

        return response()->json([
            'data' => $category,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $slug = $this->uniqueSlug($validated['name']);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ])->loadCount('products');

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $category->update([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $category->id),
            'description' => $validated['description'] ?? null,
        ]);

        $category->loadCount('products');

        return response()->json([
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->load('products');

        foreach ($category->products as $product) {
            $this->deleteProductAssets($product);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully.',
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $counter = 1;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = Str::slug($name).'-'.(++$counter);
        }

        return $slug;
    }

    private function deleteProductAssets(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
    }
}
