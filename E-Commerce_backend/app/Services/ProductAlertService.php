<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use App\Notifications\ProductAlertNotification;

class ProductAlertService
{
    public function checkLowStock(Product $product): void
    {
        if ($product->stock > 0 && $product->stock <= 3) {
            $this->sendAlerts($product, 'low_stock');
        }
    }

    public function checkOutOfStock(Product $product): void
    {
        if ($product->stock <= 0) {
            $this->sendAlerts($product, 'out_of_stock');
        }
    }

    protected function sendAlerts(Product $product, string $type): void
    {
        $users = User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_MANAGER])->get();

        if ($users->isEmpty()) {
            return;
        }

        $message = $type === 'out_of_stock'
            ? "{$product->name} — Out of stock."
            : "{$product->name} — Only {$product->stock} left in stock (below minimum of 3).";

        foreach ($users as $user) {
            $alreadyNotified = $user->notifications()
                ->where('type', ProductAlertNotification::class)
                ->where('data', 'like', "%{$product->slug}%{$type}%")
                ->where('created_at', '>=', now()->subDay())
                ->exists();

            if (! $alreadyNotified) {
                $user->notify(new ProductAlertNotification($product, $type, $message));
            }
        }

        if ($type === 'out_of_stock') {
            $product->load('category');
            app(TelegramService::class)->sendOutOfStockAlert($product);
        } else {
            $product->load('category');
            app(TelegramService::class)->sendLowStockAlert($product);
        }
    }
}
