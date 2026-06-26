<?php

namespace App\Notifications;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewProductNotification extends Notification
{
    use Queueable;

    public Product $product;
    public string $action;

    public function __construct(Product $product, string $action = 'created')
    {
        $this->product = $product;
        $this->action = $action;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $actionText = $this->action === 'created' ? 'New Product' : 'Product Updated';

        return [
            'icon' => 'bi-box-seam',
            'title' => $actionText,
            'message' => $this->product->name . ' has been ' . $this->action . ' — $' . number_format($this->product->price, 2),
            'url' => null,
        ];
    }
}
