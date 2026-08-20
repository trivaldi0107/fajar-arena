<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\OtpVerificationMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $existingUser = User::where('email', $request->email)->first();

        // Jika email sudah terdaftar DAN sudah terverifikasi, baru munculkan error unique
        if ($existingUser && $existingUser->email_verified_at !== null) {
            $request->validate([
                'email' => ['unique:'.User::class],
            ], [
                'email.unique' => 'Email ini telah terdaftar.',
            ]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.regex' => 'Format email tidak valid. Wajib menyertakan @ dan domain seperti .com (contoh: nama@gmail.com)',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $otpCode = sprintf('%06d', mt_rand(100000, 999999));

        if ($existingUser && $existingUser->email_verified_at === null) {
            // Update akun yang belum terverifikasi dengan data & OTP baru
            $existingUser->update([
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
            ]);
            $user = $existingUser;
        } else {
            // Buat akun baru
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'otp_code' => $otpCode,
                'otp_expires_at' => now()->addMinutes(10),
                'email_verified_at' => null,
            ]);
        }

        try {
            OtpVerificationMail::sendOtpDirect($user, $otpCode);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed sending OTP email: ' . $e->getMessage());
        }

        session(['pending_otp_user_id' => $user->id]);

        return redirect()->route('register.verify_otp')->with('success', 'Kode OTP verifikasi telah dikirimkan ke email Anda. Silakan periksa kotak masuk/spam email.');
    }
}
