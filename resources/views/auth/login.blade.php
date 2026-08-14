<x-guest-layout>
<style>
    /* Sembunyikan icon mata bawaan browser Edge */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
<canvas id="particles" class="absolute inset-0 z-0 opacity-30 pointer-events-none"></canvas>

@php
    $customAuthBg = active_arena()->auth_bg_image ?? null;
@endphp

@if($customAuthBg)
<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950"
     style="
        background:
        linear-gradient(rgba(10,20,40,0.65), rgba(10,20,40,0.85)),
        url('{{ asset('storage/' . $customAuthBg) }}');
        background-size: cover;
        background-position: center;
     ">
<!-- VIGNETTE -->
<div class="absolute inset-0 pointer-events-none"
     style="background: radial-gradient(circle at center, rgba(255,255,255,0.05) 0%, rgba(15,23,42,0.4) 40%, rgba(9,13,22,0.95) 100%);">
</div>
@else
<!-- DESAIN POLOS MODERN (AMBIENT GLOW & CLEAN DARK) -->
<div class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[#090d16] text-white">
    <!-- Ambient Mesh Glowing Orbs -->
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/25 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-600/10 rounded-full blur-[150px] pointer-events-none"></div>
    
    <!-- Polos Radial Dot Pattern -->
    <div class="absolute inset-0 bg-[radial-gradient(#1e293b_1px,transparent_1px)] [background-size:24px_24px] opacity-30 pointer-events-none"></div>
@endif

<div class="relative z-10 w-full max-w-6xl px-6">

    <!-- CARD -->
    <div class="backdrop-blur-2xl bg-white/10 border border-white/20 shadow-[0_8px_40px_rgba(0,0,0,0.35)] rounded-3xl flex flex-col md:flex-row overflow-hidden">

        <!-- ================= LEFT ================= -->
        <div class="hidden md:flex md:w-1/2 relative overflow-hidden">

            <div class="absolute inset-0 bg-black/40 z-10"></div>

            <div class="relative z-20 flex flex-col justify-end h-full p-10 text-white">

                <a href="/" class="text-sm text-white/80 hover:text-white mb-6">
                    ← Back to Website
                </a>

                <h1 class="text-5xl font-bold leading-tight">
                    Booking Lapangan
                    Olahraga Lebih Mudah.
                </h1>

                <p class="mt-4 text-white/80 text-lg leading-relaxed">
                    Reservasi lapangan Fajar Arena secara cepat,
                    modern, dan nyaman.
                </p>

            </div>

        </div>
        <!-- ================= END LEFT ================= -->


        <!-- ================= RIGHT ================= -->
        <div class="w-full md:w-1/2 p-8 text-white">

            <h2 class="text-xl font-semibold mb-6 text-center">Login</h2>

            @if(session('error'))
                <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-lg p-4 text-sm text-red-200 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-semibold text-red-300">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            @if(session('success') || session('status'))
                <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-lg p-4 text-sm text-green-200 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-semibold text-green-400">{{ session('success') ?? session('status') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 bg-red-500/20 border border-red-500/50 rounded-lg p-4 text-sm text-red-200 shadow-lg backdrop-blur-sm">
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-semibold text-red-400">Oops! Gagal Login</span>
                    </div>
                    <ul class="list-disc pl-7 text-red-300">
                        @php
                            $translations = [
                                'These credentials do not match our records.' => 'Email atau password yang Anda masukkan salah.',
                                'The email field is required.' => 'Kolom email tidak boleh kosong.',
                                'The password field is required.' => 'Kolom password tidak boleh kosong.',
                                'validation.recaptchav3' => 'Verifikasi keamanan reCAPTCHA gagal.',
                            ];
                        @endphp
                        @foreach ($errors->all() as $error)
                            <li>{{ $translations[$error] ?? $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
                @csrf

                <!-- EMAIL -->
                <div>
                    <label class="text-sm text-blue-100">Email</label>
                    <input id="email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           autofocus
                           autocomplete="username"
                           placeholder="Masukkan email (contoh: nama@gmail.com)"
                           class="w-full mt-1 p-2 rounded bg-white/10 border border-white/20 text-white placeholder:text-blue-200">
                </div>

                <!-- PASSWORD -->
                <div class="mt-4">
                    <label class="text-sm text-blue-100">Password</label>
                    <div style="position: relative; margin-top: 0.25rem;">
                        <input id="password" type="password"
                            name="password"
                            style="width: 100%; padding-right: 2.75rem !important;"
                            class="w-full p-2.5 rounded bg-white/10 border border-white/20 text-white placeholder:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan password">

                        <button type="button" onclick="togglePassword('password', this)"
                            style="position: absolute; right: 12px; top: 0; bottom: 0; height: 100%; display: flex; align-items: center; justify-content: center; background: transparent; border: none; padding: 0; margin: 0; z-index: 20; cursor: pointer;"
                            class="text-blue-200 hover:text-white transition-colors"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- LUPA PASSWORD -->
                <div class="mt-2 text-right">
                    <a href="#" onclick="goToForgotPassword(event)" class="text-sm text-blue-200 hover:text-white transition-colors duration-200">
                        Lupa kata sandi?
                    </a>
                </div>

                <!-- RECAPTCHA V3 -->
                {!! RecaptchaV3::field('login') !!}
                @if ($errors->has('g-recaptcha-response'))
                    <span class="text-sm text-red-500 font-bold block mt-2">
                        Verifikasi keamanan reCAPTCHA gagal. Silakan coba lagi.
                    </span>
                @endif

                <!-- BUTTON -->
                <button type="submit"
                        class="mt-6 w-full bg-blue-600 py-2 rounded hover:bg-blue-700 transition">
                    Login
                </button>
                <p class="text-center text-sm text-blue-100 mt-4">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="underline hover:text-white">
                        Daftar
                    </a>
                </p>

                <!-- Google reCAPTCHA Disclaimer -->
                <p class="text-center text-[11px] text-blue-300 mt-6 leading-tight">
                    Situs ini dilindungi oleh reCAPTCHA. <br>
                    <a href="https://policies.google.com/privacy" target="_blank" class="underline hover:text-white">Kebijakan Privasi</a> dan
                    <a href="https://policies.google.com/terms" target="_blank" class="underline hover:text-white">Persyaratan Layanan</a> Google berlaku.
                </p>

            </form>

        </div>
        <!-- ================= END RIGHT ================= -->

    </div>
    <!-- END CARD -->

</div>
</div>

<!-- PARTICLES -->

<script>
const canvas = document.getElementById('particles');
const ctx = canvas.getContext('2d');

let particles = [];
let mouse = { x: null, y: null };

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

window.addEventListener('mousemove', function(e) {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
});

function initParticles() {
    particles = [];
    for (let i = 0; i < 120; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            size: Math.random() * 2 + 1
        });
    }
}
initParticles();

function distance(x1, y1, x2, y2) {
    return Math.sqrt((x1 - x2)**2 + (y1 - y2)**2);
}

function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);

    particles.forEach(p => {
        let opacity = 0.05;

        if (mouse.x && mouse.y) {
            let dist = distance(p.x, p.y, mouse.x, mouse.y);
            if (dist < 200) opacity = 1 - (dist / 200);
        }

        ctx.beginPath();
        ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
        ctx.fillStyle = `rgba(147,197,253,${opacity})`;
        ctx.fill();
    });

    requestAnimationFrame(animate);
}
animate();
</script>

<script>
function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (!input) return;

    const btn = el.tagName === 'BUTTON' ? el : (el.closest('button') || el);

    if (input.type === "password") {
        input.type = "text";
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                <line x1="1" y1="1" x2="23" y2="23"></line>
            </svg>
        `;
    } else {
        input.type = "password";
        btn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        `;
    }
}

function goToForgotPassword(e) {
    e.preventDefault();
    const email = document.getElementById('email').value;
    const baseUrl = "{{ route('password.request') }}";
    if (email) {
        window.location.href = baseUrl + '?email=' + encodeURIComponent(email);
    } else {
        window.location.href = baseUrl;
    }
}
</script>

</x-guest-layout>
