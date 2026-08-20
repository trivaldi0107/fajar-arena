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
     * Send OTP email directly using verified Symfony Mailer transport.
     */
    public static function sendOtpDirect($user, $otpCode): void
    {
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
        $htmlContent = view('emails.otp', ['user' => $user, 'otpCode' => $otpCode])->render();

        $email = (new \Symfony\Component\Mime\Email())
            ->from(new \Symfony\Component\Mime\Address('fajararenabadminton@gmail.com', 'Fajar Arena'))
            ->to($user->email)
            ->subject('Kode OTP Verifikasi Akun Fajar Arena: ' . $otpCode)
            ->html($htmlContent);

        $mailer->send($email);
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
