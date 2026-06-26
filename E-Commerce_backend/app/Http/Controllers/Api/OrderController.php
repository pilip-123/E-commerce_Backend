<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with('items.product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'data' => $orders->getCollection()->map(fn (Order $order) => $this->orderPayload($order)),
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
        abort_unless($order->user_id === $request->user()->id, 403);

        return response()->json([
            'data' => $this->orderPayload($order->load('items.product')),
        ]);
    }

    private function orderPayload(Order $order): array
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
            'items' => $order->items->map(fn (OrderItem $item) => $this->orderItemPayload($item)),
        ];
    }

    private function orderItemPayload(OrderItem $item): array
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
        ];
    }

    private function publicUrl(string $path): string
    {
        return rtrim(request()->getSchemeAndHttpHost(), '/').Storage::url($path);
    }
}
