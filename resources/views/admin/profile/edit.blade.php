@extends('admin.layouts.app')

@section('title', 'Pengaturan Profil - Fajar Arena')

@section('content')

<!-- Header -->
<div class="mb-8">
    <h2 class="text-3xl font-bold text-gray-800">Pengaturan Profil</h2>
    <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data informasi akun dan kata sandi Anda dalam satu tempat.</p>
</div>

@if(session('success'))
<div class="mb-6 max-w-4xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
    <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <span class="font-semibold text-sm">{{ session('success') }}</span>
</div>
@endif

<div class="max-w-4xl pb-12">
    <!-- Single Combined Card Box & Form -->
    <form method="post" action="{{ route('profile.update') }}" class="p-8 sm:p-10 bg-white shadow-sm rounded-3xl border border-slate-100 space-y-8">
        @csrf
        @method('patch')

        <!-- SECTION 1: INFORMASI PROFIL -->
        <div class="space-y-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-3">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Profil & Kontak
            </h3>

            <!-- Nama -->
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Nama Lengkap</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm text-gray-900" placeholder="Masukkan nama lengkap">
                <x-input-error class="mt-1 text-xs text-rose-500 font-semibold" :messages="$errors->get('name')" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Alamat Email</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm text-gray-900" placeholder="Masukkan alamat email">
                <x-input-error class="mt-1 text-xs text-rose-500 font-semibold" :messages="$errors->get('email')" />
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- SECTION 2: GANTI KATA SANDI (OPSIONAL) -->
        <div class="space-y-6">
            <div class="border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0110 0v4"></path>
                    </svg>
                    <span>Ubah Kata Sandi</span>
                </h3>
                <p class="text-xs text-gray-400 font-normal mt-1 ml-7">
                    (Opsional - Kosongkan jika tidak ingin mengubah)
                </p>
            </div>

            <!-- Password Saat Ini -->
            <div x-data="{ show: false }">
                <label for="current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Kata Sandi Saat Ini</label>
                <div class="relative">
                    <input id="current_password" name="current_password" :type="show ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm text-gray-900" placeholder="Masukkan kata sandi saat ini">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer transition-colors p-1" title="Lihat / Sembunyikan Kata Sandi">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error class="mt-1 text-xs text-rose-500 font-semibold" :messages="$errors->get('current_password')" />
            </div>

            <!-- Password Baru -->
            <div x-data="{ show: false }">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Kata Sandi Baru</label>
                <div class="relative">
                    <input id="password" name="password" :type="show ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm text-gray-900" placeholder="Minimal 8 karakter">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer transition-colors p-1" title="Lihat / Sembunyikan Kata Sandi">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error class="mt-1 text-xs text-rose-500 font-semibold" :messages="$errors->get('password')" />
            </div>

            <!-- Konfirmasi Password Baru -->
            <div x-data="{ show: false }">
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <input id="password_confirmation" name="password_confirmation" :type="show ? 'text' : 'password'" class="w-full pl-4 pr-12 py-3 rounded-2xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500 font-semibold text-sm text-gray-900" placeholder="Ulangi kata sandi baru">
                    <button type="button" @click="show = !show" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-600 focus:outline-none cursor-pointer transition-colors p-1" title="Lihat / Sembunyikan Kata Sandi">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                        </svg>
                    </button>
                </div>
                <x-input-error class="mt-1 text-xs text-rose-500 font-semibold" :messages="$errors->get('password_confirmation')" />
            </div>
        </div>

        <hr class="border-gray-100">

        <!-- SINGLE SAVE BUTTON -->
        <div class="pt-2 flex items-center justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-8 py-3.5 rounded-2xl text-sm shadow-lg shadow-blue-600/30 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

@endsection
