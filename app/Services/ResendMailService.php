<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResendMailService
{
    /**
     * Send email using Resend HTTPS API.
     */
    public static function send(string $to, string $subject, string $htmlContent): bool
    {
        try {
            $apiKey = env('RESEND_API_KEY');

            if (empty($apiKey)) {
                Log::warning("RESEND_API_KEY belum disetel di file .env");
                return false;
            }

            $response = Http::withToken($apiKey)
                ->timeout(10)
                ->post('https://api.resend.com/emails', [
                    'from' => 'Fajar Arena <onboarding@resend.dev>',
                    'to' => [$to],
                    'subject' => $subject,
                    'html' => $htmlContent,
                ]);

            if ($response->successful()) {
                Log::info("Resend email sent successfully to {$to}. ID: " . ($response->json('id') ?? ''));
                return true;
            }

            Log::error("Resend API returned non-200: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error("Resend send exception: " . $e->getMessage());
            return false;
        }
    }
}
