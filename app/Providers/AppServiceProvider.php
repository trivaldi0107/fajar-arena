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

            $username = !empty($config['username']) ? $config['username'] : (config('mail.mailers.smtp.username') ?: env('MAIL_USERNAME', 'fajararenabadminton@gmail.com'));
            $password = !empty($config['password']) ? $config['password'] : (config('mail.mailers.smtp.password') ?: env('MAIL_PASSWORD', 'fckrphmivvbdnjjx'));

            $username = trim($username, " \t\n\r\0\x0B'\"");
            $password = trim(str_replace(' ', '', $password), " \t\n\r\0\x0B'\"");

            $transport->setUsername($username);
            $transport->setPassword($password);

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
