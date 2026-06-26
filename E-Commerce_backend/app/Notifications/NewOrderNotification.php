<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;

    public Order $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'bi-receipt',
            'title' => 'New Order #' . $this->order->id,
            'message' => 'Order #' . $this->order->id . ' placed by ' . ($this->order->user->name ?? 'Customer') . ' — $' . number_format($this->order->total_amount, 2),
            'url' => route('admin.orders.index'),
        ];
    }
}
