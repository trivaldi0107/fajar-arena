<x-guest-layout>
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

<div class="relative z-10 w-full max-w-xl px-6">

    <!-- CARD -->
    <div class="backdrop-blur-2xl bg-white/10 border border-white/20 shadow-[0_8px_40px_rgba(0,0,0,0.35)] rounded-3xl p-8 text-white">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold mb-2">Lupa Kata Sandi?</h2>
            <p class="text-blue-100 text-sm">
                Jangan khawatir. Masukkan alamat email Anda yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-6 bg-green-500/20 border border-green-500/50 rounded-lg p-4 text-sm text-green-200 shadow-lg backdrop-blur-sm">
                @if(session('status') == __('passwords.sent') || session('status') == 'We have emailed your password reset link.')
                    Tautan untuk mengatur ulang kata sandi telah dikirim. Silakan cek email Anda.
                @else
                    {{ session('status') }}
                @endif
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label class="text-sm text-blue-100">Email</label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ request('email') ?? old('email') }}"
                       required
                       autofocus
                       placeholder="Masukkan email Anda"
                       class="w-full mt-1 p-2 rounded bg-white/10 border border-white/20 text-white placeholder:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" class="text-sm text-blue-200 hover:text-white transition-colors duration-200">
                    ← Kembali ke Login
                </a>
                
                <button type="submit"
                        class="bg-blue-600 px-6 py-2 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg">
                    Kirim Tautan
                </button>
            </div>
        </form>

    </div>
</div>
</div>

<script>
    // Particle effect script similar to login page
    const canvas = document.getElementById('particles');
    const ctx = canvas.getContext('2d');
    
    function resize() {
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
    }
    window.addEventListener('resize', resize);
    resize();
    
    const particles = [];
    for(let i=0; i<50; i++) {
        particles.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            r: Math.random() * 2 + 1,
            vx: (Math.random() - 0.5) * 0.5,
            vy: (Math.random() - 0.5) * 0.5
        });
    }
    
    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
        particles.forEach(p => {
            p.x += p.vx;
            p.y += p.vy;
            if(p.x < 0 || p.x > canvas.width) p.vx *= -1;
            if(p.y < 0 || p.y > canvas.height) p.vy *= -1;
            
            ctx.beginPath();
            ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
            ctx.fill();
        });
        requestAnimationFrame(draw);
    }
    draw();
</script>
</x-guest-layout>
