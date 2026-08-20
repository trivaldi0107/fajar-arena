<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $otpCode;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $otpCode)
    {
        $this->user = $user;
        $this->otpCode = $otpCode;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode OTP Verifikasi Akun Fajar Arena: ' . $this->otpCode,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
        );
    }

    /**
     * Send OTP email directly using verified Gmail SMTP with automatic server fallback.
     */
    public static function sendOtpDirect($user, $otpCode): void
    {
        $htmlContent = view('emails.otp', ['user' => $user, 'otpCode' => $otpCode])->render();
        $subject = 'Kode OTP Verifikasi Akun Fajar Arena: ' . $otpCode;
        $fromEmail = 'fajararenabadminton@gmail.com';
        $fromName = 'Fajar Arena';

        $email = (new \Symfony\Component\Mime\Email())
            ->from(new \Symfony\Component\Mime\Address($fromEmail, $fromName))
            ->to($user->email)
            ->subject($subject)
            ->html($htmlContent);

        // Tier 1: Coba kirim via Gmail SMTP
        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport('smtp.gmail.com', 587, false);
            $transport->setUsername('fajararenabadminton@gmail.com');
            $transport->setPassword('kczztgbfghwfqafa');

            $stream = $transport->getStream();
            if ($stream instanceof \Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream) {
                $stream->setStreamOptions([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
            }

            $mailer = new \Symfony\Component\Mailer\Mailer($transport);
            $mailer->send($email);
            return;
        } catch (\Throwable $e1) {
            \Illuminate\Support\Facades\Log::warning('Gmail SMTP 587 failed, trying server sendmail fallback: ' . $e1->getMessage());
        }

        // Tier 2: Fallback ke Sendmail bawaan server cPanel / Linux
        try {
            $sendmailTransport = new \Symfony\Component\Mailer\Transport\SendmailTransport();
            $sendmailMailer = new \Symfony\Component\Mailer\Mailer($sendmailTransport);
            $sendmailMailer->send($email);
            return;
        } catch (\Throwable $e2) {
            \Illuminate\Support\Facades\Log::warning('Sendmail fallback failed, trying PHP mail(): ' . $e2->getMessage());
        }

        // Tier 3: Fallback ke PHP Native mail() bawaan cPanel
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: {$fromName} <{$fromEmail}>\r\n";
        $headers .= "Reply-To: {$fromEmail}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $sent = @mail($user->email, $subject, $htmlContent, $headers);
        if (!$sent) {
            throw new \Exception('Gagal mengirim email OTP melalui semua metode server. Silakan hubungi admin.');
        }
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
