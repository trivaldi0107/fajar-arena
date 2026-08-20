<?php

namespace App\Providers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
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
        if ($this->app->environment('production') || (config('app.url') && str_starts_with(config('app.url'), 'https://'))) {
            URL::forceScheme('https');
        }

        // Fix cPanel / Hosting SSL certificate mismatch for Gmail SMTP
        Mail::extend('smtp', function (array $config = []) {
            $port = (int) ($config['port'] ?? env('MAIL_PORT', 587));
            $isDirectSsl = ($port === 465 || strtolower($config['encryption'] ?? env('MAIL_ENCRYPTION', '')) === 'ssl');
            
            $transport = new EsmtpTransport(
                $config['host'] ?? env('MAIL_HOST', 'smtp.gmail.com'),
                $port,
                $isDirectSsl
            );

            if (!empty($config['username'])) {
                $transport->setUsername($config['username']);
            }
            if (!empty($config['password'])) {
                $transport->setPassword($config['password']);
            }

            $stream = $transport->getStream();
            if ($stream instanceof SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
            }

            return $transport;
        });
    }
}
