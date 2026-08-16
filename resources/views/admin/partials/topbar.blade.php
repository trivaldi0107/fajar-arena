<header class="bg-white h-20 shadow-sm border-b flex items-center justify-between px-4 sm:px-6 md:px-8 sticky top-0 z-40">
    <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0 pr-2">
        <!-- Mobile Menu Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 text-gray-500 hover:bg-gray-100 rounded-xl flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Cabang Olahraga Selector -->
        <div class="relative cursor-pointer z-30 flex-shrink min-w-0" x-data="{ openCabang: false }" @click.outside="openCabang = false">
            <div @click="openCabang = !openCabang" class="flex items-center justify-between gap-2 px-3 sm:px-4 py-2 bg-slate-50 hover:bg-slate-100 rounded-2xl border border-slate-200 transition-colors">
                <div class="flex flex-col min-w-0">
                    <span class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-1">Pilih Cabang</span>
                    <div class="flex items-center gap-1.5 sm:gap-2 min-w-0">
                        <span class="text-xs sm:text-sm font-bold text-slate-800 leading-none truncate max-w-[125px] sm:max-w-none">{{ active_arena()->nama_arena ?? 'Fajar Arena' }}</span>
                        @php
                            $activePendingCount = \App\Models\Pemesanan::whereIn('status', ['proses', 'pending'])
                                ->whereHas('detail.lapangan', function($q) {
                                    $q->where('pengaturan_id', active_arena()->id);
                                })->count();
                        @endphp
                        @if($activePendingCount > 0)
                            <span class="bg-amber-500 text-white text-[10px] sm:text-[11px] font-black px-1.5 sm:px-2 py-0.5 rounded-full animate-pulse shadow-sm flex-shrink-0">
                                {{ $activePendingCount }}
                            </span>
                        @endif
                    </div>
                </div>
                <svg :class="{'rotate-180': openCabang}" class="w-4 h-4 text-slate-400 ml-1 sm:ml-3 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
            
            <!-- Dropdown Menu -->
            <div x-show="openCabang" x-transition.opacity x-transition.duration.200ms style="display: none;" class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 transition-all duration-200 transform origin-top-left">
                <div class="py-2">
                    @php
                        $arenas = \App\Models\Pengaturan::all();
                        $activeSlug = session('active_arena_slug') ?: (\App\Models\Pengaturan::first()->slug ?? '');
                    @endphp
                    @foreach($arenas as $arena)
                        @php
                            $arenaPendingCount = \App\Models\Pemesanan::whereIn('status', ['proses', 'pending'])
                                ->whereHas('detail.lapangan', function($q) use ($arena) {
                                    $q->where('pengaturan_id', $arena->id);
                                })->count();
                        @endphp
                        <div class="relative group/item flex items-center border-b border-gray-50 last:border-0 hover:bg-slate-50 transition-colors {{ $activeSlug === $arena->slug ? 'bg-blue-50/50' : '' }}">
                            <a href="{{ route('admin.set_arena', $arena->slug) }}" class="flex-1 block px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <p class="text-sm font-bold {{ $activeSlug === $arena->slug ? 'text-blue-700' : 'text-slate-800' }}">{{ $arena->nama_arena }}</p>
                                            @if($arenaPendingCount > 0)
                                                <span class="bg-amber-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm">
                                                    {{ $arenaPendingCount }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $arena->jenis_olahraga }}</p>
                                    </div>
                                    @if($activeSlug === $arena->slug)
                                        <svg class="w-5 h-5 text-blue-600 mr-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @endif
                                </div>
                            </a>
                            @if(count($arenas) > 1)
                            <button type="button" onclick="event.preventDefault(); event.stopPropagation(); confirmDeleteTopbar({{ $arena->id }})" class="absolute right-3 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors z-10" title="Hapus Cabang">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Right Menu -->
    <div class="flex items-center gap-3.5 sm:gap-4 md:gap-6 flex-shrink-0">
        <!-- Tanggal -->
        <div class="text-right hidden sm:block">
            <p class="text-sm text-gray-400">
                Hari Ini
            </p>
            <p class="font-semibold text-gray-700">
                {{ now()->translatedFormat('d F Y') }}
            </p>
        </div>

        <!-- Notification Bell Button -->
        <button type="button" onclick="enablePushNotificationManually()" title="Aktifkan Notifikasi HP / Push" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;" class="bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-full transition-all relative border border-blue-200 cursor-pointer group flex items-center justify-center flex-shrink-0">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1 right-1 flex h-2.5 w-2.5 pointer-events-none">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500 ring-2 ring-white"></span>
            </span>
        </button>

        <!-- Avatar -->
        <a href="{{ route('profile.edit') }}" style="width: 40px; height: 40px; min-width: 40px; min-height: 40px;" class="rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-base cursor-pointer hover:bg-blue-700 hover:scale-105 transition-all shadow-sm flex-shrink-0">
            {{ strtoupper(substr(Auth::user()->name,0,1)) }}
        </a>
    </div>
</header>

<form id="form-hapus-topbar" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
<script>
function confirmDeleteTopbar(id) {
    if(typeof Swal === 'undefined') {
        if(confirm('Hapus cabang ini? Semua data terkait akan terhapus permanen!')) {
            let form = document.getElementById('form-hapus-topbar');
            form.action = "/admin/lapangan/destroy/" + id;
            form.submit();
        }
        return;
    }
    Swal.fire({
        title: 'Hapus Cabang Ini?',
        text: "Semua pengaturan dan jadwal terkait cabang ini akan ikut terhapus dan tidak bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            let form = document.getElementById('form-hapus-topbar');
            form.action = "/admin/lapangan/destroy/" + id;
            form.submit();
        }
    })
}
</script>