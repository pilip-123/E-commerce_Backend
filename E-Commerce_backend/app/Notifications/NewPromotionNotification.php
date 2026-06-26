<?php

namespace App\Notifications;

use App\Models\Promotion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPromotionNotification extends Notification
{
    use Queueable;

    public Promotion $promotion;

    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $discountText = $this->promotion->discount_type === 'percentage'
            ? $this->promotion->discount_value . '% off'
            : '$' . number_format($this->promotion->discount_value, 2) . ' off';

        $productNames = $this->promotion->relationLoaded('products')
            ? $this->promotion->products->take(3)->pluck('name')->implode(', ')
            : null;

        $extra = $productNames
            ? ' on: ' . $productNames . ($this->promotion->products->count() > 3 ? ' and more' : '')
            : ' on selected products';

        return [
            'icon' => 'bi-percent',
            'title' => $this->promotion->name,
            'message' => $discountText . $extra,
            'url' => '/promotions',
        ];
    }
}
