<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\ProductAlertService;
use App\Services\TelegramService;
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
            'phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['nullable', 'string', 'max:1000'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Your cart is empty.'], 422);
        }

        // Apply discount if code provided
        $discountCode = null;
        $discountAmount = 0;
        if ($request->discount_code) {
            // Require $500+ spending today to use a VIP code
            $todayTotal = Order::where('user_id', $request->user()->id)
                ->whereDate('created_at', today())
                ->sum('total_amount');

            if ($todayTotal < 500) {
                return response()->json(['message' => 'VIP codes require $500+ in orders today.'], 422);
            }

            $discountCode = DiscountCode::where('code', strtoupper(trim($request->discount_code)))->first();
            if ($discountCode) {
                if (!$discountCode->isValidForUser($request->user())) {
                    $msg = $discountCode->isValid()
                        ? 'You have already used this discount code.'
                        : 'This discount code has already been used.';
                    return response()->json(['message' => $msg], 422);
                }
                $rawTotal = $cartItems->sum(fn (Cart $item) => $this->effectivePrice($item->product) * $item->quantity);
                $discountAmount = $discountCode->discount_type === 'percentage'
                    ? $rawTotal * ($discountCode->discount_value / 100)
                    : min($discountCode->discount_value, $rawTotal);
            }
        }

        $order = DB::transaction(function () use ($request, $validated, $cartItems, $discountAmount) {
            $total = $cartItems->sum(fn (Cart $item) => $this->effectivePrice($item->product) * $item->quantity);
            $total = max(0, $total - $discountAmount);

            foreach ($cartItems as $cartItem) {
                if ($cartItem->quantity > $cartItem->product->stock) {
                    abort(422, "Not enough stock for {$cartItem->product->name}.");
                }
            }

            $user = $request->user();
            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $total,
                'status' => 'pending',
                'phone' => $validated['phone'] ?? $user->phone ?? '',
                'shipping_address' => $validated['shipping_address'] ?? $user->address ?? '',
            ]);

            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'price' => $this->effectivePrice($cartItem->product),
                    'quantity' => $cartItem->quantity,
                ]);

                $cartItem->product->decrement('stock', $cartItem->quantity);

                $product = $cartItem->product->fresh();
                app(ProductAlertService::class)->checkLowStock($product);
                app(ProductAlertService::class)->checkOutOfStock($product);
            }

            Cart::where('user_id', $request->user()->id)->delete();

            return $order->load('items.product');
        });

        if ($discountCode) {
            $discountCode->markUsedBy($request->user());
        }

        User::where('role', 'admin')->get()->each->notify(new NewOrderNotification($order));

        app(TelegramService::class)->sendOrderNotification($order);

        return response()->json([
            'message' => 'Order placed successfully.',
            'data' => $order,
        ], 201);
    }
}
