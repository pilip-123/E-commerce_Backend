<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.frontend_url' => 'http://localhost:5173']);
        Mail::fake();
    }

    public function test_shop_reset_email_links_to_the_vue_reset_page(): void
    {
        $user = User::factory()->create(['email' => 'shopper@example.com']);

        $this->postJson('/api/auth/forgot-password', ['email' => $user->email])->assertOk();

        Mail::assertSent(PasswordResetMail::class, function (PasswordResetMail $mail) use ($user) {
            return str_starts_with($mail->resetUrl, 'http://localhost:5173/#/reset-password?')
                && str_contains($mail->resetUrl, 'email=' . urlencode($user->email))
                && str_contains($mail->resetUrl, 'token=' . $mail->token);
        });
    }

    public function test_admin_app_reset_email_links_to_the_vue_admin_reset_page(): void
    {
        $admin = User::factory()->create(['email' => 'boss@example.com', 'role' => User::ROLE_ADMIN]);

        $this->postJson('/api/admin/auth/forgot-password', ['email' => $admin->email, 'role' => 'admin'])->assertOk();

        Mail::assertSent(PasswordResetMail::class, fn (PasswordResetMail $mail) => str_starts_with($mail->resetUrl, 'http://localhost:5173/#/admin/reset-password?'));
    }

    public function test_the_emailed_token_resets_the_password(): void
    {
        $user = User::factory()->create(['email' => 'shopper@example.com']);
        $this->postJson('/api/auth/forgot-password', ['email' => $user->email]);

        $token = null;
        Mail::assertSent(PasswordResetMail::class, function (PasswordResetMail $mail) use (&$token) {
            $token = $mail->token;
            return true;
        });

        $this->postJson('/api/auth/reset-password', [
            'email' => $user->email,
            'token' => $token,
            'password' => 'NewSecret123!',
            'password_confirmation' => 'NewSecret123!',
        ])->assertOk();

        $this->postJson('/api/auth/login', ['email' => $user->email, 'password' => 'NewSecret123!'])->assertOk();
    }
}
