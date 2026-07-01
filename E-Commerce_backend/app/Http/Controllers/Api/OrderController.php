<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $orders = Order::with('items.product')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order) => $this->orderPayload($order, $user)),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        $user = $request->user();
        abort_unless($order->user_id === $user->id, 403);

        return response()->json([
            'data' => $this->orderPayload($order->load('items.product'), $user),
        ]);
    }

    private function orderPayload(Order $order, User $user): array
    {
        return [
            'id' => $order->id,
            'user_id' => $order->user_id,
            'total_amount' => (float) $order->total_amount,
            'status' => $order->status,
            'phone' => $order->phone,
            'shipping_address' => $order->shipping_address,
            'created_at' => $order->created_at,
            'updated_at' => $order->updated_at,
            'items' => $order->items->map(fn (OrderItem $item) => $this->orderItemPayload($item, $user)),
        ];
    }

    private function orderItemPayload(OrderItem $item, User $user): array
    {
        return [
            'id' => $item->id,
            'order_id' => $item->order_id,
            'product_id' => $item->product_id,
            'price' => (float) $item->price,
            'quantity' => $item->quantity,
            'product' => $item->product ? [
                'id' => $item->product->id,
                'name' => $item->product->name,
                'slug' => $item->product->slug,
                'price' => (float) $item->product->price,
                'stock' => $item->product->stock,
                'image' => $item->product->image ? $this->publicUrl($item->product->image) : null,
            ] : null,
            'hasReviewed' => Review::where('user_id', $user->id)
                ->where('product_id', $item->product_id)
                ->exists(),
        ];
    }

    private function publicUrl(string $path): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/').Storage::url($path);
    }
}
