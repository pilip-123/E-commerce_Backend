<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VipDiscountNotification extends Notification
{
    use Queueable;

    public string $code;
    public string $discountType;
    public string $discountValue;

    public function __construct(string $code, string $discountType, string $discountValue)
    {
        $this->code = $code;
        $this->discountType = $discountType;
        $this->discountValue = $discountValue;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        $discountText = $this->discountType === 'percentage'
            ? $this->discountValue . '%'
            : '$' . number_format((float) $this->discountValue, 2);

        return [
            'icon' => 'bi-lock-fill',
            'title' => 'Exclusive VIP Discount!',
            'message' => 'You received a VIP code: ' . $this->code . ' — ' . $discountText . ' off your next order!',
            'url' => '/promotions',
        ];
    }
}
