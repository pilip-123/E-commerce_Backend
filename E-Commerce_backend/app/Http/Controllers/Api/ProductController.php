<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewProductNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()
            ->with('category', 'activePromotions')
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest();

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->float('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->float('max_price'));
        }

        if ($request->has('status')) {
            $query->where('status', filter_var($request->input('status'), FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true);
        }

        $products = $query->paginate($request->integer('per_page', 12));

        return response()->json([
            'data' => $products->getCollection()->map(fn (Product $product) => $this->productPayload($product)),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load('category', 'reviews.user', 'activePromotions');

        return response()->json([
            'data' => $this->productPayload($product),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validateProduct($request);
        $data['image'] = $this->storeImage($request);

        $product = Product::create($data)->load('category');

        User::where('role', 'customer')->get()->each->notify(new NewProductNotification($product, 'created'));

        return response()->json([
            'message' => 'Product created successfully.',
            'data' => $this->productPayload($product),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $this->validateProduct($request, $product->id);
        $image = $this->storeImage($request);

        if ($image) {
            $this->deleteImage($product->image);
            $data['image'] = $image;
        }

        $product->update($data);
        $product->load('category');

        User::where('role', 'customer')->get()->each->notify(new NewProductNotification($product, 'updated'));

        return response()->json([
            'message' => 'Product updated successfully.',
            'data' => $this->productPayload($product),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->deleteImage($product->image);
        $product->delete();

        return response()->json([
            'message' => 'Product deleted successfully.',
        ]);
    }

    private function validateProduct(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        return [
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $ignoreId),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'stock' => $validated['stock'],
            'status' => $request->boolean('status'),
        ];
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $counter = 1;
        $query = Product::query()->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = Str::slug($name).'-'.(++$counter);
            $query = Product::query()->where('slug', $slug);

            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('products', 'public');
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function productPayload(Product $product): array
    {
        $avgRating = $product->reviews_avg_rating;
        $discountPrice = $product->getDiscountPrice();
        $promotion = $product->getBestPromotion();
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'discount_price' => $discountPrice,
            'has_discount' => $discountPrice !== null,
            'promotion' => $promotion ? [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'discount_type' => $promotion->discount_type,
                'discount_value' => (float) $promotion->discount_value,
            ] : null,
            'stock' => $product->stock,
            'status' => (bool) $product->status,
            'image' => $product->image ? $this->publicUrl($product->image) : null,
            'rating' => $avgRating ? round((float) $avgRating, 1) : null,
            'reviews_count' => (int) $product->reviews_count,
            'reviews' => $product->relationLoaded('reviews') ? $product->reviews->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'user' => $r->user ? [
                    'id' => $r->user->id,
                    'name' => $r->user->name,
                    'image_url' => $r->user->image_url ? $this->publicUrl($r->user->image_url) : null,
                ] : null,
                'created_at' => $r->created_at,
            ]) : [],
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $product->category->name,
                'slug' => $product->category->slug,
            ] : null,
        ];
    }

    private function publicUrl(string $path): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/').Storage::url($path);
    }
}
