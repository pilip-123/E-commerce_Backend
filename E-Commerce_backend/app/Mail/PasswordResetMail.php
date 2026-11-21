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

    /**
     * @param  string|null  $resetUrl  Link to put in the email; defaults to the backend reset page.
     */
    public function __construct(User $user, string $token, ?string $resetUrl = null)
    {
        $this->user = $user;
        $this->token = $token;
        $this->resetUrl = $resetUrl ?? $this->buildResetUrl();
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
        return route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->email,
        ]);
    }
}
