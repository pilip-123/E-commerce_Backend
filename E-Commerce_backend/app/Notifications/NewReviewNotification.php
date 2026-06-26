<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReviewNotification extends Notification
{
    use Queueable;

    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'bi-star-fill',
            'title' => 'New Review',
            'message' => $this->review->user->name . ' rated "' . $this->review->product->name . '" ' . $this->review->rating . '/5' . ($this->review->comment ? ': "' . $this->review->comment . '"' : ''),
            'url' => route('admin.reviews.index'),
        ];
    }
}
