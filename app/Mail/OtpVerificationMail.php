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
            from: new \Illuminate\Mail\Mailables\Address('fajararenabadminton@gmail.com', 'Fajar Arena Badminton'),
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
     * Send OTP email using Resend HTTPS API with fallback.
     */
    public static function sendToUser($user, $otpCode): void
    {
        $html = view('emails.otp', ['user' => $user, 'otpCode' => $otpCode])->render();
        $subject = 'Kode OTP Verifikasi Akun Fajar Arena: ' . $otpCode;

        // Kirim via Resend API (HTTPS Port 443 - 100% tembus firewall & langsung masuk Primary Inbox)
        $sent = \App\Services\ResendMailService::send($user->email, $subject, $html);

        if (!$sent) {
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new self($user, $otpCode));
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
