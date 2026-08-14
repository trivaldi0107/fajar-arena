<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Jika email belum terverifikasi OTP, kirim OTP baru dan arahkan ke verifikasi OTP
        if ($user->email_verified_at === null && $user->role !== 'admin') {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $otpCode = sprintf('%06d', mt_rand(100000, 999999));
            $user->otp_code = $otpCode;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\OtpVerificationMail($user, $otpCode));
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Failed sending OTP email on login: ' . $e->getMessage());
            }

            session(['pending_otp_user_id' => $user->id]);

            return redirect()->route('register.verify_otp')->with('success', 'Email Anda belum terverifikasi. Kode OTP verifikasi baru telah dikirimkan ke email Anda.');
        }

        if ($user->role === 'admin') {
            $request->session()->forget('url.intended');
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('portal');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
