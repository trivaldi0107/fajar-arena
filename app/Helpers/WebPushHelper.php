<?php

namespace App\Helpers;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\Vapid;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Log;

class WebPushHelper
{
    /**
     * Get or create VAPID keys for Web Push Notifications.
     */
    public static function getVapidKeys(): array
    {
        $publicKey = env('VAPID_PUBLIC_KEY');
        $privateKey = env('VAPID_PRIVATE_KEY');

        if (!$publicKey || !$privateKey) {
            $keys = Vapid::createVapidKeys();
            $publicKey = $keys['publicKey'];
            $privateKey = $keys['privateKey'];

            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $envContent = file_get_contents($envPath);
                if (!str_contains($envContent, 'VAPID_PUBLIC_KEY')) {
                    $envContent .= "\n\nVAPID_PUBLIC_KEY={$publicKey}\n";
                    $envContent .= "VAPID_PRIVATE_KEY={$privateKey}\n";
                    $envContent .= "VAPID_SUBJECT=mailto:adminfajararena@gmail.com\n";
                    file_put_contents($envPath, $envContent);
                }
            }
        }

        return [
            'publicKey' => $publicKey,
            'privateKey' => $privateKey,
        ];
    }

    /**
     * Send Web Push Notification to registered Admin users.
     */
    public static function sendToAdmins(string $title, string $body, ?string $url = null): bool
    {
        try {
            $keys = self::getVapidKeys();

            $auth = [
                'VAPID' => [
                    'subject' => env('VAPID_SUBJECT', 'mailto:adminfajararena@gmail.com'),
                    'publicKey' => $keys['publicKey'],
                    'privateKey' => $keys['privateKey'],
                ],
            ];

            $webPush = new WebPush($auth);

            $subscriptions = PushSubscription::all();

            if ($subscriptions->isEmpty()) {
                Log::info('WebPush: No push subscriptions found.');
                return false;
            }

            $payload = json_encode([
                'title' => $title,
                'body' => $body,
                'icon' => asset('favicon.ico'),
                'url' => $url ?: route('admin.pemesanan'),
            ]);

            foreach ($subscriptions as $sub) {
                $subscription = Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'publicKey' => $sub->public_key,
                    'authToken' => $sub->auth_token,
                ]);

                $webPush->queueNotification($subscription, $payload);
            }

            foreach ($webPush->flush() as $report) {
                $endpoint = $report->getRequest()->getUri()->__toString();

                if (!$report->isSuccess()) {
                    Log::warning("WebPush send failed for {$endpoint}: " . $report->getReason());
                    if ($report->isSubscriptionExpired()) {
                        PushSubscription::where('endpoint_hash', md5($endpoint))->delete();
                    }
                }
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('WebPush Exception: ' . $e->getMessage());
            return false;
        }
    }
}
