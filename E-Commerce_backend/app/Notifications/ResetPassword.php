<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    use Queueable;

    public string $token;
    public string $email;

    public function __construct(string $token, string $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($notifiable instanceof User && $notifiable->role === User::ROLE_CUSTOMER) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $url = $frontendUrl . '/#/reset-password?token=' . $this->token . '&email=' . urlencode($this->email);
        } else {
            $url = route('password.reset', ['token' => $this->token, 'email' => $this->email]);
        }

        return (new MailMessage)
            ->subject('Reset Your Password')
            ->greeting('Hello!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $url)
            ->line('This password reset link will expire in 60 minutes.')
            ->line('If you did not request a password reset, no further action is required.');
    }
}
