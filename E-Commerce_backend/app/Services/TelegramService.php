<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage(string $message): bool
    {
        if (empty($this->botToken) || empty($this->chatId)) {
            Log::warning('Telegram credentials not configured.');
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$this->botToken}/sendMessage", [
            'chat_id' => $this->chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);

        if ($response->failed()) {
            Log::error('Telegram notification failed: ' . $response->body());
            return false;
        }

        return true;
    }

    public function sendOrderNotification(\App\Models\Order $order): bool
    {
        $items = $order->items->map(fn ($item) =>
            "• {$item->product->name} x{$item->quantity} — \$" . number_format($item->price * $item->quantity, 2)
        )->implode("\n");

        $message = "<b>🛒 New Order #{$order->id}</b>\n"
            . "─────────────────────\n"
            . "<b>Customer:</b> {$order->user->name}\n"
            . "<b>Email:</b> {$order->user->email}\n"
            . "<b>Phone:</b> {$order->phone}\n"
            . "<b>Address:</b> {$order->shipping_address}\n"
            . "─────────────────────\n"
            . "<b>Items:</b>\n{$items}\n"
            . "─────────────────────\n"
            . "<b>Total:</b> \$" . number_format($order->total_amount, 2);

        return $this->sendMessage($message);
    }
}
