<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    private function effectivePrice(\App\Models\Product $product): float
    {
        return $product->getDiscountPrice() ?? (float) $product->price;
    }

    public function checkout(Request $request): JsonResponse
    {
        $items = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json([
            'data' => $items,
            'total' => $items->sum(fn (Cart $item) => $this->effectivePrice($item->product) * $item->quantity),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
        ]);

        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        $order = DB::transaction(function () use ($request, $validated, $cartItems) {
            $total = $cartItems->sum(fn (Cart $item) => $this->effectivePrice($item->product) * $item->quantity);

            foreach ($cartItems as $cartItem) {
                if ($cartItem->quantity > $cartItem->product->stock) {
                    abort(422, "Not enough stock for {$cartItem->product->name}.");
                }
            }

            $order = Order::create([
                'user_id' => $request->user()->id,
                'total_amount' => $total,
                'status' => 'pending',
                'phone' => $validated['phone'],
                'shipping_address' => $validated['shipping_address'],
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'price' => $this->effectivePrice($cartItem->product),
                    'quantity' => $cartItem->quantity,
                ]);

                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            Cart::where('user_id', $request->user()->id)->delete();

            return $order->load('items.product');
        });

        User::where('role', 'admin')->get()->each->notify(new NewOrderNotification($order));

        return response()->json([
            'message' => 'Order placed successfully.',
            'data' => $order,
        ], 201);
    }
}
