<x-guest-layout>

@php
    $customAuthBg = active_arena()->auth_bg_image ?? null;
@endphp

@if($customAuthBg)
<div class="relative min-h-screen flex items-center justify-center overflow-hidden py-12 px-4 sm:px-6 lg:px-8 bg-slate-950"
     style="
        background:
        linear-gradient(rgba(10,20,40,0.65), rgba(10,20,40,0.85)),
        url('{{ asset('storage/' . $customAuthBg) }}');
        background-size: cover;
        background-position: center;
     ">
<!-- VIGNETTE OVERLAY -->
<div class="absolute inset-0 pointer-events-none"
     style="background: radial-gradient(circle at center, rgba(255,255,255,0.05) 0%, rgba(15,23,42,0.4) 40%, rgba(9,13,22,0.95) 100%);">
</div>
@else
<!-- DESAIN POLOS MODERN (AMBIENT GLOW & CLEAN DARK) -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden py-12 px-4 sm:px-6 lg:px-8 bg-[#090d16] text-white">
    <!-- Ambient Mesh Glowing Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/25 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[150px] pointer-events-none"></div>
    
    <!-- Polos Radial Dot Pattern -->
    <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-30 pointer-events-none"></div>
@endif

    <!-- MAIN CARD -->
    <div class="relative z-10 w-full max-w-md bg-white/10 backdrop-blur-2xl border border-white/20 rounded-3xl shadow-[0_8px_40px_rgba(0,0,0,0.45)] p-8 sm:p-10 text-white">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-500/20 border border-blue-400/30 text-blue-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                🔑
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Verifikasi Kode OTP</h2>
            <p class="mt-2 text-sm text-slate-300">
                Kami telah mengirimkan 6 digit kode OTP ke email:
            </p>
            <p class="mt-2 text-sm font-semibold text-blue-300 bg-blue-500/20 border border-blue-400/30 py-1.5 px-4 rounded-full inline-block">
                {{ substr($user->email, 0, 3) . '***' . strstr($user->email, '@') }}
            </p>
        </div>

        <!-- Alert Error / Success -->
        @if(session('error'))
            <div class="mb-6 bg-red-500/20 border border-red-500/40 p-4 rounded-2xl text-sm text-red-200 backdrop-blur-md">
                {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 bg-emerald-500/20 border border-emerald-500/40 p-4 rounded-2xl text-sm text-emerald-200 backdrop-blur-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- OTP Verification Form -->
        <form action="{{ route('register.verify_otp') }}" method="POST" id="otpForm" class="space-y-6">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider text-center mb-3">
                    Masukkan 6 Digit Kode OTP
                </label>

                <div class="flex justify-between items-center gap-2 sm:gap-3" id="otpBoxContainer">
                    @for($i = 0; $i < 6; $i++)
                        <input type="text"
                               maxlength="1"
                               pattern="[0-9]*"
                               inputmode="numeric"
                               data-index="{{ $i }}"
                               class="otp-input w-11 h-14 sm:w-12 sm:h-14 text-center text-2xl font-black text-white bg-white/10 border border-white/20 rounded-xl focus:bg-white/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 transition-all outline-none"
                               required />
                    @endfor
                </div>

                <!-- Hidden full OTP string -->
                <input type="hidden" name="otp_code" id="fullOtpInput">
            </div>

            <!-- Expiration Countdown -->
            <div class="text-center text-xs text-slate-300">
                Kode berlaku dalam: <span id="countdownTimer" class="font-bold text-amber-400">10:00</span>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    id="submitBtn"
                    class="w-full py-4 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 hover:shadow-blue-500/50 transition-all duration-200 active:scale-[0.98]">
                Verifikasi & Lanjutkan
            </button>
        </form>

        <!-- Resend OTP Section -->
        <div class="mt-8 pt-6 border-t border-white/10 text-center">
            <p class="text-xs text-slate-300 mb-2">Tidak menerima kode email?</p>
            <form action="{{ route('register.resend_otp') }}" method="POST" id="resendForm">
                @csrf
                <button type="submit"
                        id="resendBtn"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-400 hover:text-blue-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    Kirim Ulang Kode OTP
                </button>
            </form>
            <span id="cooldownText" class="block mt-1 text-xs text-slate-400 hidden">
                Kirim ulang tersedia dalam <span id="cooldownSeconds">60</span> detik
            </span>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-input');
    const fullInput = document.getElementById('fullOtpInput');
    const otpForm = document.getElementById('otpForm');

    if (inputs.length > 0) inputs[0].focus();

    function updateFullOtp() {
        let code = '';
        inputs.forEach(input => code += input.value);
        fullInput.value = code;
    }

    inputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            const value = e.target.value;
            if (!/^[0-9]$/.test(value)) {
                e.target.value = '';
                return;
            }

            updateFullOtp();

            if (value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(pastedData)) {
                pastedData.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                updateFullOtp();
                inputs[5].focus();
            }
        });
    });

    otpForm.addEventListener('submit', function(e) {
        updateFullOtp();
        if (fullInput.value.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
        }
    });

    let totalSeconds = {{ $remainingSeconds ?? 600 }};
    const timerDisplay = document.getElementById('countdownTimer');
    
    function updateTimerUI() {
        if (totalSeconds <= 0) {
            timerDisplay.textContent = 'Kadaluarsa';
            timerDisplay.className = 'font-bold text-red-400';
            return false;
        }
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        timerDisplay.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        return true;
    }

    if (updateTimerUI()) {
        const interval = setInterval(() => {
            totalSeconds--;
            if (!updateTimerUI()) {
                clearInterval(interval);
            }
        }, 1000);
    }

    const resendBtn = document.getElementById('resendBtn');
    const cooldownText = document.getElementById('cooldownText');
    const cooldownSecs = document.getElementById('cooldownSeconds');
    
    document.getElementById('resendForm').addEventListener('submit', function() {
        resendBtn.disabled = true;
        cooldownText.classList.remove('hidden');
        let cd = 60;
        const cdInterval = setInterval(() => {
            cd--;
            cooldownSecs.textContent = cd;
            if (cd <= 0) {
                clearInterval(cdInterval);
                resendBtn.disabled = false;
                cooldownText.classList.add('hidden');
            }
        }, 1000);
    });
});
</script>
</x-guest-layout>
