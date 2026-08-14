<aside class="w-full h-full bg-white shadow-lg border-r border-gray-100 flex flex-col justify-between overflow-y-auto">

    <!-- Logo -->
    <div>
        <div class="h-20 flex flex-col justify-center px-8 border-b">
            <a href="/admin/dashboard" class="flex items-center gap-2 group">
                <div class="flex flex-col">
                    <span class="text-gray-800 font-extrabold text-lg tracking-tight group-hover:text-blue-600 transition-colors duration-300 leading-none">Fajar Arena</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Admin</span>
                </div>
            </a>
        </div>

        <!-- Menu -->

        <nav class="mt-8 px-4">

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center gap-4 px-5 py-4 rounded-xl transition
                {{ request()->routeIs('admin.dashboard')
                        ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                        : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>

                Dashboard

            </a>

            <a href="{{ route('admin.pemesanan') }}"
            class="flex items-center justify-between px-5 py-4 rounded-xl mt-1 transition
            {{ request()->routeIs('admin.pemesanan')
                    ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                    : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <div class="flex items-center gap-4">
                    <svg class="w-5 h-5 {{ request()->routeIs('admin.pemesanan') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>Data Pemesanan</span>
                </div>

                @php
                    $sidebarPendingCount = \App\Models\Pemesanan::whereIn('status', ['proses', 'pending'])
                        ->whereHas('detail.lapangan', function($q) {
                            $q->where('pengaturan_id', active_arena()->id);
                        })->count();
                @endphp
                @if($sidebarPendingCount > 0)
                    <span class="bg-amber-500 text-white text-[11px] font-black px-2 py-0.5 rounded-full shadow-sm">
                        {{ $sidebarPendingCount }}
                    </span>
                @endif

            </a>

            <a href="{{ route('admin.scan') }}"
            class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
            {{ request()->routeIs('admin.scan')
                    ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                    : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.scan') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Zm10.5 0c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 14.25 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75Zm10.5 0h.75v.75h-.75v-.75Zm-10.5 10.5h.75v.75h-.75v-.75Zm10.5 0h.75v.75h-.75v-.75Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.125c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v4.5Zm10.5-4.5c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5c-.621 0-1.125-.504-1.125-1.125v-4.5Z" />
                </svg>

                Scan Tiket

            </a>

            <a href="{{ route('admin.jadwal') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ request()->routeIs('admin.jadwal')
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.jadwal') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>

                Kelola Jadwal

            </a>

            <a href="{{ route('admin.beranda.index') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ request()->routeIs('admin.beranda.*')
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.beranda.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>

                Pengaturan Beranda

            </a>

                        <a href="{{ route('admin.lapangan.index') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ (request()->routeIs('admin.lapangan.index') || request()->routeIs('admin.lapangan.edit'))
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ (request()->routeIs('admin.lapangan.index') || request()->routeIs('admin.lapangan.edit')) ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>

                Kelola Lapangan
            </a>

            <a href="{{ route('admin.lapangan.create') }}"
               class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
               {{ request()->routeIs('admin.lapangan.create')
                       ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                       : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.lapangan.create') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>

                Tambah Lapangan
            </a>

        </nav>

    </div>

    <!-- Footer -->

    <div class="border-t p-5 space-y-3">

        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 group cursor-pointer p-2.5 rounded-2xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">

            <div class="w-11 h-11 rounded-full bg-blue-600 flex items-center justify-center text-white text-lg font-bold group-hover:scale-105 transition-transform shrink-0 shadow-sm">

                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}

            </div>

            <div class="min-w-0">

                <p class="font-bold text-sm text-slate-800 group-hover:text-blue-600 transition-colors truncate">

                    {{ Auth::user()->name }}

                </p>

                <p class="text-xs font-medium text-gray-500">

                    Administrator

                </p>

            </div>

        </a>

        <form method="POST" action="{{ route('logout') }}">

            @csrf

            <button
                class="w-full py-2.5 rounded-xl bg-red-500 hover:bg-red-600 transition text-white font-bold text-sm shadow-md shadow-red-500/20 cursor-pointer">

                Logout

            </button>

        </form>

    </div>

</aside>