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

        // Override SMTP transport to disable SSL/STARTTLS peer certificate checks on cPanel
        Mail::extend('smtp', function (array $config = []) {
            $transport = new EsmtpTransport('smtp.gmail.com', 587, false);
            $transport->setUsername('fajararenabadminton@gmail.com');
            $transport->setPassword('kczztgbfghwfqafa');

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
