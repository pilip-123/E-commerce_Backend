<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminAnnouncementNotification extends Notification
{
    use Queueable;

    public string $title;
    public string $message;

    public function __construct(string $title, string $message)
    {
        $this->title = $title;
        $this->message = $message;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'icon' => 'bi-megaphone-fill',
            'title' => $this->title,
            'message' => $this->message,
            'url' => null,
        ];
    }
}
