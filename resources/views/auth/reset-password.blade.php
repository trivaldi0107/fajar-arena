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

<div class="relative z-10 w-full max-w-xl px-6">

    <!-- CARD -->
    <div class="backdrop-blur-2xl bg-white/10 border border-white/20 shadow-[0_8px_40px_rgba(0,0,0,0.35)] rounded-3xl p-8 text-white">

        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold mb-2">Buat Sandi Baru</h2>
            <p class="text-blue-100 text-sm">
                Silakan masukkan kata sandi baru Anda di bawah ini.
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <label class="text-sm text-blue-100">Email</label>
                <input id="email"
                       type="email"
                       name="email"
                       value="{{ old('email', $request->email) }}"
                       readonly
                       class="w-full mt-1 p-2 rounded bg-white/5 border border-white/10 text-gray-300 cursor-not-allowed">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label class="text-sm text-blue-100 mb-1 block">Kata Sandi Baru</label>
                <div style="position: relative;">
                    <input id="password"
                           type="password"
                           name="password"
                           required
                           autofocus
                           placeholder="Masukkan kata sandi baru"
                           style="width: 100%; padding-right: 2.75rem !important;"
                           class="w-full p-2.5 rounded bg-white/10 border border-white/20 text-white placeholder:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button type="button" onclick="togglePassword('password', this)"
                        style="position: absolute; right: 14px; top: 0; bottom: 0; height: 100%; display: flex; align-items: center; justify-content: center; background: transparent; border: none; padding: 0; margin: 0; z-index: 20; cursor: pointer;"
                        class="text-blue-200 hover:text-white transition-colors"
                        title="Tampilkan / Sembunyikan Kata Sandi">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label class="text-sm text-blue-100 mb-1 block">Konfirmasi Kata Sandi Baru</label>
                <div style="position: relative;">
                    <input id="password_confirmation"
                           type="password"
                           name="password_confirmation"
                           required
                           placeholder="Ketik ulang kata sandi baru"
                           style="width: 100%; padding-right: 2.75rem !important;"
                           class="w-full p-2.5 rounded bg-white/10 border border-white/20 text-white placeholder:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500">

                    <button type="button" onclick="togglePassword('password_confirmation', this)"
                        style="position: absolute; right: 14px; top: 0; bottom: 0; height: 100%; display: flex; align-items: center; justify-content: center; background: transparent; border: none; padding: 0; margin: 0; z-index: 20; cursor: pointer;"
                        class="text-blue-200 hover:text-white transition-colors"
                        title="Tampilkan / Sembunyikan Kata Sandi">
                        <svg xmlns="http://www.w3.org/2000/svg" style="width: 20px; height: 20px; pointer-events: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
            </div>

            <div class="mt-8">
                <button type="submit"
                        class="w-full bg-blue-600 px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg cursor-pointer">
                    Simpan Kata Sandi
                </button>
            </div>
        </form>

    </div>
</div>
</div>

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
</script>

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
