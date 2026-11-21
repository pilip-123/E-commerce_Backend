<?php

namespace App\Providers;

use App\Support\CaBundle;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->useWorkingCaBundle();
    }

    /**
     * If PHP's default CA bundle is missing, point every outgoing TLS client at
     * the system bundle so HTTPS and SMTP keep verifying certificates.
     */
    private function useWorkingCaBundle(): void
    {
        $bundle = CaBundle::fallbackPath();
        if (! $bundle) {
            return;
        }

        // Laravel HTTP client (Telegram alerts, etc.).
        Http::globalOptions(['verify' => $bundle]);

        // Socialite builds its own Guzzle client from services.{provider}.guzzle.
        foreach (['google', 'github', 'facebook'] as $provider) {
            config(["services.{$provider}.guzzle.verify" => $bundle]);
        }

        // SMTP (password reset emails via Gmail) uses a raw TLS socket.
        Event::listen(MessageSending::class, function () use ($bundle) {
            $transport = Mail::mailer()->getSymfonyTransport();
            if ($transport instanceof SmtpTransport && ($stream = $transport->getStream()) instanceof SocketStream) {
                $options = $stream->getStreamOptions();
                $options['ssl']['cafile'] = $bundle;
                $stream->setStreamOptions($options);
            }
        });
    }
}
