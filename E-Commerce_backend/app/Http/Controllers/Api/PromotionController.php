<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\User;
use App\Notifications\NewPromotionNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function index(): JsonResponse
    {
        $promotions = Promotion::withCount('products')->latest()->get();

        return response()->json([
            'data' => $promotions->map(fn (Promotion $promotion) => $this->payload($promotion)),
        ]);
    }

    public function active(): JsonResponse
    {
        Promotion::where('end_date', '<', now())->delete();

        $promotions = Promotion::active()
            ->with(['products' => fn ($q) => $q->with('category')])
            ->latest()
            ->get();

        return response()->json([
            'data' => $promotions->map(fn (Promotion $promotion) => $this->payload($promotion, true)),
        ]);
    }

    public function show(Promotion $promotion): JsonResponse
    {
        $promotion->load('products');

        return response()->json([
            'data' => $this->payload($promotion),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['nullable', 'boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $promotion = Promotion::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->boolean('status'),
        ]);

        if (! empty($validated['products'])) {
            $promotion->products()->sync($validated['products']);
        }

        $promotion->load('products');

        User::where('role', 'customer')->get()->each->notify(new NewPromotionNotification($promotion));

        return response()->json([
            'message' => 'Promotion created successfully.',
            'data' => $this->payload($promotion->load('products')),
        ], 201);
    }

    public function update(Request $request, Promotion $promotion): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'discount_type' => ['required', 'in:percentage,fixed'],
            'discount_value' => ['required', 'numeric', 'min:0'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['nullable', 'boolean'],
            'products' => ['nullable', 'array'],
            'products.*' => ['exists:products,id'],
        ]);

        $promotion->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => $request->boolean('status'),
        ]);

        $promotion->products()->sync($validated['products'] ?? []);

        return response()->json([
            'message' => 'Promotion updated successfully.',
            'data' => $this->payload($promotion->load('products')),
        ]);
    }

    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();

        return response()->json([
            'message' => 'Promotion deleted successfully.',
        ]);
    }

    private function payload(Promotion $promotion, bool $fullProducts = false): array
    {
        $products = null;

        if ($promotion->relationLoaded('products')) {
            $products = $promotion->products->map(fn (Product $product) => $fullProducts ? [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => (float) $product->price,
                'discount_price' => $product->getDiscountPrice(),
                'has_discount' => $product->getDiscountPrice() !== null,
                'stock' => $product->stock,
                'image' => $product->image ? url('storage/'.$product->image) : null,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
            ] : [
                'id' => $product->id,
                'name' => $product->name,
            ]);
        }

        return [
            'id' => $promotion->id,
            'name' => $promotion->name,
            'description' => $promotion->description,
            'discount_type' => $promotion->discount_type,
            'discount_value' => (float) $promotion->discount_value,
            'start_date' => $promotion->start_date,
            'end_date' => $promotion->end_date,
            'status' => (bool) $promotion->status,
            'products_count' => $products ? $products->count() : (int) $promotion->products_count,
            'products' => $products,
            'created_at' => $promotion->created_at,
        ];
    }
}
