<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $userId = session('pending_otp_user_id');

        if (!$userId) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi telah berakhir. Silakan daftar kembali.');
        }

        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('register')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($user->email_verified_at !== null) {
            Auth::login($user);
            return redirect()->route('portal')->with('success', 'Email Anda sudah terverifikasi.');
        }

        $remainingSeconds = 0;
        if ($user->otp_expires_at) {
            $remainingSeconds = max(0, (int) now()->diffInSeconds($user->otp_expires_at, false));
        }

        return view('auth.verify-otp', compact('user', 'remainingSeconds'));
    }

    /**
     * Verify the 6-digit OTP code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $userId = session('pending_otp_user_id');

        if (!$userId) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi telah berakhir.');
        }

        $user = User::findOrFail($userId);

        // Concatenate 6 digits if sent as individual input fields or single string
        $otpEntered = $request->input('otp_code');
        if (is_array($otpEntered)) {
            $otpEntered = implode('', $otpEntered);
        }

        $request->validate([
            'otp_code' => 'required',
        ], [
            'otp_code.required' => 'Silakan masukkan 6 digit kode OTP.',
        ]);

        if (trim($otpEntered) !== (string) $user->otp_code) {
            return back()->with('error', 'Kode OTP yang Anda masukkan salah. Silakan periksa kembali email Anda.');
        }

        if ($user->otp_expires_at && now()->greaterThan($user->otp_expires_at)) {
            return back()->with('error', 'Kode OTP telah kadaluarsa. Silakan klik "Kirim Ulang Kode OTP".');
        }

        // Successfully verified
        $user->email_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        session()->forget('pending_otp_user_id');

        Auth::login($user);

        return redirect()->route('portal')->with('success', 'Selamat! Registrasi & Verifikasi 2FA Email Anda Berhasil.');
    }

    /**
     * Resend a new OTP code to the user's email.
     */
    public function resend(Request $request): RedirectResponse
    {
        $userId = session('pending_otp_user_id');

        if (!$userId) {
            return redirect()->route('register')->with('error', 'Sesi verifikasi telah berakhir.');
        }

        $user = User::findOrFail($userId);

        $otpCode = sprintf('%06d', mt_rand(100000, 999999));
        $user->otp_code = $otpCode;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new OtpVerificationMail($user, $otpCode));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed resending OTP email: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email OTP. Silakan coba beberapa saat lagi (' . $e->getMessage() . ')');
        }

        return back()->with('success', 'Kode OTP baru telah berhasil dikirimkan ke email Anda.');
    }
}
