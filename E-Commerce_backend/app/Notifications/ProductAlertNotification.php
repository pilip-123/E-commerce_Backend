<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProductAlertNotification extends Notification
{
    use Queueable;

    public Product $product;
    public string $type;
    public string $message;

    public function __construct(Product $product, string $type, string $message)
    {
        $this->product = $product;
        $this->type = $type;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $icon = match ($this->type) {
            'low_stock' => 'bi-exclamation-triangle',
            'out_of_stock' => 'bi-x-circle',
            default => 'bi-clock-history',
        };

        $title = match ($this->type) {
            'low_stock' => 'Low Stock Alert',
            'out_of_stock' => 'Out of Stock Alert',
            default => 'Product Expiring Soon',
        };

        return [
            'icon' => $icon,
            'title' => $title,
            'message' => $this->message,
            'url' => route('admin.products.index'),
        ];
    }
}
