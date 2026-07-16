<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $token;
    public string $resetUrl;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = $this->buildResetUrl();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset',
        );
    }

    private function buildResetUrl(): string
    {
        $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
        $role = $this->user->role;

        if ($role === User::ROLE_CUSTOMER) {
            return $frontendUrl . '/#/reset-password?token=' . $this->token . '&email=' . urlencode($this->user->email);
        }

        return $frontendUrl . '/#/admin/reset-password?token=' . $this->token . '&email=' . urlencode($this->user->email);
    }
}
