<?php

namespace App\Services;

use App\Mail\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetService
{
    public const EXPIRY_MINUTES = 30;

    /**
     * Create a reset token for the given email.
     *
     * @return array{user: User, token: string}|null
     */
    public function createToken(string $email, ?string $role = null): ?array
    {
        $query = User::where('email', $email);

        if ($role) {
            $query->where('role', $role);
        }

        $user = $query->first();

        if (!$user) {
            return null;
        }

        $plainToken = Str::random(64);
        $tokenHash = hash('sha256', $plainToken);

        PasswordResetToken::where('user_id', $user->id)->delete();

        PasswordResetToken::create([
            'user_id' => $user->id,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
        ]);

        return ['user' => $user, 'token' => $plainToken];
    }

    public function sendResetLink(string $email, ?string $role = null): void
    {
        $result = $this->createToken($email, $role);

        if (!$result) {
            return;
        }

        Mail::to($result['user']->email)->send(
            new PasswordResetMail($result['user'], $result['token'])
        );
    }

    public function resetPassword(string $email, string $plainToken, string $newPassword): array
    {
        $tokenHash = hash('sha256', $plainToken);

        $record = PasswordResetToken::where('token_hash', $tokenHash)
            ->whereHas('user', fn($q) => $q->where('email', $email))
            ->first();

        if (!$record) {
            return ['success' => false, 'message' => 'Invalid or expired password reset token.', 'status' => 422];
        }

        if ($record->isExpired()) {
            $record->delete();
            return ['success' => false, 'message' => 'This password reset link has expired.', 'status' => 422];
        }

        $user = $record->user;
        $user->update(['password' => $newPassword]);

        $record->delete();

        return ['success' => true, 'message' => 'Password reset successfully.', 'status' => 200];
    }

    public function validateToken(string $email, string $plainToken): bool
    {
        $tokenHash = hash('sha256', $plainToken);

        $record = PasswordResetToken::where('token_hash', $tokenHash)
            ->whereHas('user', fn($q) => $q->where('email', $email))
            ->first();

        if (!$record || $record->isExpired()) {
            return false;
        }

        return true;
    }
}
