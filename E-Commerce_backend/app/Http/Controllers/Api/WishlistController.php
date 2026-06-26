<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $items = Wishlist::with('product.category')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'data' => $items->map(fn (Wishlist $item) => $this->wishlistPayload($item)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
        ]);

        $item = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'product_id' => $validated['product_id'],
        ]);

        $item->load('product.category');

        return response()->json([
            'message' => 'Product added to wishlist.',
            'data' => $this->wishlistPayload($item),
        ], 201);
    }

    public function destroy(Request $request, Wishlist $wishlist): JsonResponse
    {
        abort_unless($wishlist->user_id === $request->user()->id, 403);

        $wishlist->delete();

        return response()->json(['message' => 'Wishlist item removed successfully.']);
    }

    private function wishlistPayload(Wishlist $item): array
    {
        return [
            'id' => $item->id,
            'product' => [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'price' => (float) $item->product->price,
                'stock' => $item->product->stock,
                'image' => $item->product->image ? asset('storage/'.$item->product->image) : null,
                'category' => $item->product->category?->name,
            ],
        ];
    }
}
