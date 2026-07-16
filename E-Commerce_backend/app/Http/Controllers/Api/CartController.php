<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Cart::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $items->map(fn (Cart $item) => $this->cartPayload($item)),
            'total' => $items->sum(fn (Cart $item) => $this->itemEffectivePrice($item) * $item->quantity),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $quantity = $validated['quantity'] ?? 1;

        $item = DB::transaction(function () use ($request, $validated, $quantity) {
            $existing = Cart::withTrashed()
                ->where('user_id', $request->user()->id)
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if ($existing) {
                if ($existing->trashed()) {
                    $existing->restore();
                    $existing->quantity = $quantity;
                } else {
                    $existing->increment('quantity', $quantity);
                }
                return $existing->fresh();
            }

            return Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $validated['product_id'],
                'quantity' => $quantity,
            ]);
        });

        $item->load('product.category');

        return response()->json([
            'message' => 'Product added to cart.',
            'data' => $this->cartPayload($item),
        ], 201);
    }

    public function update(Request $request, Cart $cart): JsonResponse
    {
        abort_unless($cart->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart->update($validated);
        $cart->load('product.category');

        return response()->json([
            'message' => 'Cart updated successfully.',
            'data' => $this->cartPayload($cart),
        ]);
    }

    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        abort_unless($cart->user_id === $request->user()->id, 403);

        $cart->delete();

        return response()->json(['message' => 'Cart item removed successfully.']);
    }

    public function clear(Request $request): JsonResponse
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json(['message' => 'Cart cleared successfully.']);
    }

    private function cartPayload(Cart $item): array
    {
        $discountPrice = $item->product->getDiscountPrice();
        return [
            'id' => $item->id,
            'quantity' => $item->quantity,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'price' => (float) $item->product->price,
                'discount_price' => $discountPrice,
                'has_discount' => $discountPrice !== null,
                'stock' => $item->product->stock,
                'image' => $item->product->image ? asset('storage/'.$item->product->image) : null,
                'category' => $item->product->category?->name,
            ],
        ];
    }

    private function itemEffectivePrice(Cart $item): float
    {
        return $item->product->getDiscountPrice() ?? (float) $item->product->price;
    }
}
