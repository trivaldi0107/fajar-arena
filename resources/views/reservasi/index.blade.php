<!-- // HALAMAN UTAMA RESERVASI & MATRIKS JADWAL -->
<x-app-layout>

<!-- // KODE PHP: Menyiapkan dictionary lookup jadwal untuk rendering cepat -->
@php
    $jadwalDict = [];
    $keysLogged = false;
    if (isset($jadwal)) {
        foreach($jadwal as $j) {
            $key = $j->tanggal . '_' . $j->jam_mulai . '_' . $j->lapangan_id;
            if (!isset($jadwalDict[$key]) || $j->status != 'tersedia') {
                $jadwalDict[$key] = $j;
            }
        }
    }
@endphp


<!-- // STYLE CSS: Layout responsif matriks lapangan & slot jadwal -->
<style>
    /* // STYLE CSS: Tampilan default untuk 1 atau 2 lapangan */
    .court-name-full { display: inline; }
    .court-name-short { display: none; }
    .court-cols {
        grid-template-columns: 70px repeat({{ count($lapangan) }}, minmax(90px, 1fr));
    }
    @media (min-width: 768px) {
        .court-cols {
            grid-template-columns: 70px repeat({{ count($lapangan) }}, minmax(100px, 1fr));
        }
    }

    /* // STYLE CSS: Tampilan khusus untuk 3 lapangan atau lebih di perangkat mobile */
    @if(count($lapangan) >= 3)
    @media (max-width: 767px) {
        .court-cols {
            grid-template-columns: 70px repeat({{ count($lapangan) }}, minmax(56px, 1fr));
        }
        .court-name-full { display: none !important; }
        .court-name-short { display: inline !important; }
        .slot-ui {
            width: 2.25rem !important;
            height: 2.25rem !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin-left: auto;
            margin-right: auto;
            border-radius: 0.5rem !important;
        }
        .slot-text {
            display: none !important;
        }
        .legend-container {
            display: block !important;
        }
    }
    @endif
</style>


@auth
<div
    id="pendingWrapper"
    @if(!$pembayaranPending)
        class="hidden"
    @endif
>

    <div class="max-w-7xl mx-auto px-6 mt-6 mb-6">

        <div
            id="pendingBanner"
            class="bg-gradient-to-r from-amber-500 to-orange-500 text-white rounded-2xl p-4 sm:p-5 flex flex-col md:flex-row gap-4 justify-between items-center text-center md:text-left transition-all duration-300 shadow-lg shadow-amber-500/20">

            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm sm:text-base text-white">
                        {{ $pembayaranPending && $pembayaranPending->status === 'proses' ? 'Transaksi Anda Sedang Menunggu Persetujuan Admin' : 'Anda Memiliki Transaksi Pembayaran Yang Belum Diselesaikan' }}
                    </h3>
                    <p class="text-xs text-amber-100 mt-0.5">
                        {{ $pembayaranPending && $pembayaranPending->status === 'proses' ? 'Klik tombol di samping untuk melihat status persetujuan.' : 'Klik tombol di samping untuk melanjutkan pembayaran.' }}
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto shrink-0">

                <a 
                    id="btnLanjutkan"
                    href="{{ $pembayaranPending ? ($pembayaranPending->status === 'proses' ? route('pembayaran.menunggu', $pembayaranPending->id) : url('/pembayaran/'.$pembayaranPending->id)) : '#' }}"
                    class="bg-white hover:bg-amber-50 text-amber-600 font-bold px-5 py-2.5 rounded-xl w-full sm:w-auto text-center text-xs sm:text-sm shadow-md transition-all">

                    Lanjutkan Pembayaran →

                </a>

                @if(!$pembayaranPending || $pembayaranPending->status === 'pending')
                <form
                    id="formBatalkan"
                    action="{{ $pembayaranPending ? route('pembayaran.batal',$pembayaranPending->id) : '#' }}"
                    method="POST">

                    @csrf

                    <button
                        class="bg-black/20 hover:bg-black/30 text-white font-semibold px-4 py-2.5 rounded-xl w-full sm:w-auto text-center text-xs sm:text-sm transition-all border border-white/20">

                        Batalkan

                    </button>

                </form>
                @endif

            </div>

        </div>

    </div>
    
</div>

<!-- WRAPPER FULL -->
<div class="max-w-7xl mx-auto px-6 mt-6">

@guest
    <div class="text-center mt-20">
        <h1 class="text-2xl font-bold">Anda belum login</h1>

        <p class="text-gray-500 mt-2">
            Silakan login terlebih dahulu untuk melakukan pemesanan.
        </p>

        <a href="/login"
           class="mt-6 inline-block bg-blue-600 text-white px-6 py-2 rounded-full">
            Login Sekarang
        </a>
    </div>

@else

    @if($isMember)

        <h2 class="text-base md:text-lg lg:text-xl font-semibold mb-2">
            Pemesanan Member {{ active_arena()->nama_arena }}
        </h2>

        <p class="text-sm mt-2 opacity-90 max-w-xl mb-6">
            Nikmati harga spesial dengan berlangganan paket Member.
        </p>

    @else

        <h2 class="text-base md:text-lg lg:text-xl font-semibold mb-6">
            Pemesanan Lapangan {{ active_arena()->nama_arena }} Non Member
        </h2>

    @endif

    {{-- ERROR --}}
    @if(session('error'))
        <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- ================== TANGGAL ================== -->
    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-4 sm:p-6 md:p-8 mb-8 overflow-x-auto">

        <div class="flex justify-between items-center mb-8">
            <h3 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight">Pilih Tanggal</h3>

            <div class="flex items-center gap-3 md:gap-4 relative" x-data="{ filterOpen: false }">

                @if($isMember)
                    <a href="{{ route('reservasi') }}"
                        class="bg-white border-2 border-blue-600 text-blue-600 px-5 py-2 md:px-7 md:py-2.5 text-sm md:text-base font-bold rounded-full hover:bg-blue-50 hover:-translate-y-0.5 transition-all duration-300 whitespace-nowrap">
                        Pemesanan Reguler
                    </a>
                @else
                    @if(active_arena()->is_member_active)
                    <a href="{{ route('reservasi', ['member' => 1]) }}"
                        class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2 md:px-7 md:py-2.5 text-sm md:text-base font-semibold rounded-full shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-0.5 transition-all duration-300 whitespace-nowrap">
                        Join Member
                    </a>
                    @endif
                @endif

                <button type="button" @click="filterOpen = !filterOpen" 
                    class="w-10 h-10 md:w-12 md:h-12 bg-white border border-gray-200 rounded-full flex items-center justify-center shadow-sm hover:shadow-md hover:border-blue-300 transition-all duration-300">
                    <img src="{{ asset('images/filter.png') }}" class="w-4 h-4 md:w-5 md:h-5">
                </button>

                <!-- // STYLE CSS: Animasi PopUp untuk Dropdown Filter -->
                <style>
                /* // STYLE CSS: Keyframes animasi popUp */
                @keyframes popUp {
                    0% { opacity: 0; transform: translateY(15px) scale(0.95); }
                    60% { opacity: 1; transform: translateY(-2px) scale(1.02); }
                    100% { opacity: 1; transform: translateY(0) scale(1); }
                }
                .stagger-item { opacity: 0; animation: popUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
                .stagger-1 { animation-delay: 0.05s; }
                .stagger-2 { animation-delay: 0.1s; }
                .stagger-3 { animation-delay: 0.15s; }
                .stagger-4 { animation-delay: 0.2s; }
                </style>
                <div id="filterDropdown" 
                    x-show="filterOpen"
                    @click.away="filterOpen = false"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-4"
                    class="absolute right-0 top-14 md:top-16 w-[320px] sm:w-[350px] md:w-[430px] max-w-[calc(100vw-2rem)] bg-white/95 backdrop-blur-xl rounded-3xl shadow-[0_20px_50px_rgb(0,0,0,0.2)] border border-gray-100 p-4 sm:p-6 z-50 origin-top-right"
                    style="display: none;">

                    @if(!$isMember)

                    <!-- // ========================================================================= -->
                    <!-- // FORM FILTER PENCARIAN RESERVASI HARIAN (NON-MEMBER) -->
                    <!-- // Parameter yang dikirim ke ReservasiController@filter: -->
                    <!-- // 1. durasi          : Jumlah jam berurutan yang dicari -->
                    <!-- // 2. tanggal_mulai   : Batas awal tanggal query -->
                    <!-- // 3. tanggal_akhir   : Batas akhir tanggal query -->
                    <!-- // 4. jam_mulai       : Batas jam awal filter -->
                    <!-- // 5. jam_selesai     : Batas jam akhir filter -->
                    <!-- // ========================================================================= -->
                    <form method="POST" action="/reservasi/filter" @reset="$dispatch('form-reset')">
                    @csrf

                    <div class="mb-5 stagger-item stagger-1 relative z-50">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">Rentang Tanggal</p>
                        <div class="flex gap-2 items-center z-30 relative">
                            <!-- Dropdown Tanggal Mulai -->
                            <div x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tanggal_mulai') }}',
                                    options: [
                                        @foreach($tanggalList as $tgl)
                                            { value: '{{ $tgl->format('Y-m-d') }}', label: '{{ $tgl->translatedFormat('d M Y') }}' }{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    ]
                                }"
                                @form-reset.window="selected = ''"
                                @click.away="open = false" 
                                class="relative flex-1"
                            >
                                <input type="hidden" name="tanggal_mulai" :value="selected">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                    :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                >
                                    <span x-text="selected ? (options.find(o => o.value === selected)?.label || selected) : 'Mulai'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                    style="display: none;"
                                >
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" 
                                            @click="selected = option.value; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                            :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                        >
                                            <span x-text="option.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <span class="text-gray-300 font-light text-lg">—</span>
                            
                            <!-- Dropdown Tanggal Akhir -->
                            <div x-data="{ 
                                    open: false, 
                                    selected: '{{ request('tanggal_akhir') }}',
                                    options: [
                                        @foreach($tanggalList as $tgl)
                                            { value: '{{ $tgl->format('Y-m-d') }}', label: '{{ $tgl->translatedFormat('d M Y') }}' }{{ !$loop->last ? ',' : '' }}
                                        @endforeach
                                    ]
                                }"
                                @form-reset.window="selected = ''"
                                @click.away="open = false" 
                                class="relative flex-1"
                            >
                                <input type="hidden" name="tanggal_akhir" :value="selected">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                    :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                >
                                    <span x-text="selected ? (options.find(o => o.value === selected)?.label || selected) : 'Akhir'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                    style="display: none;"
                                >
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" 
                                            @click="selected = option.value; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                            :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                        >
                                            <span x-text="option.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 stagger-item stagger-2 relative z-40">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">Rentang Jam</p>
                        <div class="flex gap-2 items-center z-20 relative">
                            <!-- Dropdown Mulai -->
                            <div x-data="{ 
                                    open: false, 
                                    selected: '{{ request('jam_mulai') }}',
                                    options: [
                                        @for ($i = $jamBuka; $i < $jamTutup; $i++)
                                            { value: '{{ sprintf('%02d:00:00', $i) }}', label: '{{ sprintf('%02d:00', $i) }}' }{{ $i < $jamTutup - 1 ? ',' : '' }}
                                        @endfor
                                    ]
                                }"
                                @form-reset.window="selected = ''"
                                @click.away="open = false" 
                                class="relative flex-1"
                            >
                                <input type="hidden" name="jam_mulai" :value="selected">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                    :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                >
                                    <span x-text="selected ? selected.substring(0,5) : 'Mulai'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                    style="display: none;"
                                >
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" 
                                            @click="selected = option.value; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                            :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                        >
                                            <span x-text="option.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                            
                            <span class="text-gray-300 font-light text-lg">—</span>
                            
                            <!-- Dropdown Selesai -->
                            <div x-data="{ 
                                    open: false, 
                                    selected: '{{ request('jam_selesai') }}',
                                    options: [
                                        @for ($i = $jamBuka + 1; $i <= $jamTutup; $i++)
                                            { value: '{{ sprintf('%02d:00:00', $i == 24 ? 0 : $i) }}', label: '{{ sprintf('%02d:00', $i == 24 ? 0 : $i) }}' }{{ $i < $jamTutup ? ',' : '' }}
                                        @endfor
                                    ]
                                }"
                                @form-reset.window="selected = ''"
                                @click.away="open = false" 
                                class="relative flex-1"
                            >
                                <input type="hidden" name="jam_selesai" :value="selected">
                                <button type="button" @click="open = !open"
                                    class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                    :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                >
                                    <span x-text="selected ? selected.substring(0,5) : 'Akhir'"></span>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                                <div x-show="open" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-150"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 translate-y-2"
                                    class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                    style="display: none;"
                                >
                                    <template x-for="option in options" :key="option.value">
                                        <button type="button" 
                                            @click="selected = option.value; open = false"
                                            class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                            :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                        >
                                            <span x-text="option.label"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5 stagger-item stagger-3 relative z-30">
                        <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">Durasi</p>
                        @php
                            $jbInt = (int)substr(active_arena()->jam_buka, 0, 2);
                            $jtInt = (int)substr(active_arena()->jam_tutup, 0, 2);
                            $maxDurasi = ($jtInt <= $jbInt) ? ($jtInt + 24 - $jbInt) : ($jtInt - $jbInt);
                            if ($maxDurasi <= 0) $maxDurasi = 24;
                        @endphp
                        <div class="flex items-center gap-3" x-data="{ 
                            durasi: {{ request('durasi', 1) }}, 
                            max: {{ $maxDurasi }} 
                        }" @form-reset.window="durasi = 1">
                            <input type="hidden" name="durasi" :value="durasi">
                            <div class="flex items-center bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm h-10 w-24">
                                <div class="flex-1 text-center font-semibold text-gray-800 text-sm select-none pl-2" x-text="durasi"></div>
                                <div class="flex flex-col border-l border-gray-200 h-full w-8 bg-gray-50">
                                    <button type="button" @click="if(durasi < max) durasi++" class="flex-1 flex items-center justify-center text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-gray-200 outline-none group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 group-active:scale-75 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="if(durasi > 1) durasi--" class="flex-1 flex items-center justify-center text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition-colors outline-none group">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 group-active:scale-75 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-500">Jam</span>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-6 stagger-item stagger-4 relative z-20">
                        <button type="reset" onclick="window.dispatchEvent(new Event('form-reset'))" class="w-1/3 border border-gray-200 bg-white hover:bg-gray-50 py-3 rounded-xl font-semibold text-gray-500 transition-all duration-300 text-sm">Reset</button>
                        <button type="submit"
                            class="w-2/3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-semibold shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 hover:shadow-blue-500/50 transition-all duration-300 text-sm">
                            Cari Jadwal
                        </button>
                    </div>

                    </form>
                    @endif
                    @if($isMember)

                    <!-- // ========================================================================= -->
                    <!-- // FORM FILTER PENCARIAN RESERVASI MEMBER (RUTINAN 4 PEKAN) -->
                    <!-- // Parameter yang dikirim: member=1, tanggal_mulai, tanggal_akhir, jam_mulai, jam_akhir -->
                    <!-- // ========================================================================= -->
                    <form method="GET" action="/reservasi" @reset="$dispatch('form-reset')">
                        <input type="hidden" name="member" value="1">
                        <div class="space-y-5">

                            <div class="stagger-item stagger-1 relative z-50">
                                <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">Rentang Tanggal</p>
                                <div class="flex gap-2 items-center z-30 relative">
                                    <!-- Dropdown Tanggal Mulai Member -->
                                    <div x-data="{ 
                                            open: false, 
                                            selected: '{{ request('tanggal_mulai') }}',
                                            options: [
                                                @foreach($tanggalList as $tgl)
                                                    { value: '{{ $tgl->format('Y-m-d') }}', label: '{{ $tgl->translatedFormat('d M Y') }}' }{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            ]
                                        }"
                                        @form-reset.window="selected = ''"
                                        @click.away="open = false" 
                                        class="relative flex-1"
                                    >
                                        <input type="hidden" name="tanggal_mulai" :value="selected">
                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                            :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                        >
                                            <span x-text="selected ? (options.find(o => o.value === selected)?.label || selected) : 'Mulai'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-2"
                                            class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                            style="display: none;"
                                        >
                                            <template x-for="option in options" :key="option.value">
                                                <button type="button" 
                                                    @click="selected = option.value; open = false"
                                                    class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                                    :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <span class="text-gray-300 font-light text-lg">—</span>
                                    
                                    <!-- Dropdown Tanggal Akhir Member -->
                                    <div x-data="{ 
                                            open: false, 
                                            selected: '{{ request('tanggal_akhir') }}',
                                            options: [
                                                @foreach($tanggalList as $tgl)
                                                    { value: '{{ $tgl->format('Y-m-d') }}', label: '{{ $tgl->translatedFormat('d M Y') }}' }{{ !$loop->last ? ',' : '' }}
                                                @endforeach
                                            ]
                                        }"
                                        @form-reset.window="selected = ''"
                                        @click.away="open = false" 
                                        class="relative flex-1"
                                    >
                                        <input type="hidden" name="tanggal_akhir" :value="selected">
                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                            :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                        >
                                            <span x-text="selected ? (options.find(o => o.value === selected)?.label || selected) : 'Akhir'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-2"
                                            class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                            style="display: none;"
                                        >
                                            <template x-for="option in options" :key="option.value">
                                                <button type="button" 
                                                    @click="selected = option.value; open = false"
                                                    class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                                    :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- RENTANG JAM -->
                            <div class="stagger-item stagger-2 relative z-40">
                                <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">Rentang Jam</p>
                                <div class="flex gap-2 items-center z-20 relative">
                                    <!-- Dropdown Mulai Member -->
                                    <div x-data="{ 
                                            open: false, 
                                            selected: '{{ request('jam_mulai') }}',
                                            options: [
                                                @php $jamPerPertemuan = active_arena()->member_jam_per_pertemuan ?? 2; @endphp
                                                @for ($i = $jamBuka; $i <= $jamTutup - $jamPerPertemuan; $i++)
                                                    { value: '{{ sprintf('%02d:00:00', $i) }}', label: '{{ sprintf('%02d:00', $i) }}' }{{ $i < ($jamTutup - $jamPerPertemuan) ? ',' : '' }}
                                                @endfor
                                            ]
                                        }"
                                        @form-reset.window="selected = ''"
                                        @click.away="open = false" 
                                        class="relative flex-1"
                                    >
                                        <input type="hidden" name="jam_mulai" :value="selected">
                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                            :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                        >
                                            <span x-text="selected ? selected.substring(0,5) : 'Mulai'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-2"
                                            class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                            style="display: none;"
                                        >
                                            <template x-for="option in options" :key="option.value">
                                                <button type="button" 
                                                    @click="selected = option.value; open = false"
                                                    class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                                    :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <span class="text-gray-300 font-light text-lg">—</span>
                                    
                                    <!-- Dropdown Selesai Member -->
                                    <div x-data="{ 
                                            open: false, 
                                            selected: '{{ request('jam_akhir') }}',
                                            options: [
                                                @for ($i = $jamBuka + $jamPerPertemuan; $i <= $jamTutup; $i++)
                                                    { value: '{{ sprintf('%02d:00:00', $i) }}', label: '{{ sprintf('%02d:00', $i) }}' }{{ $i < $jamTutup ? ',' : '' }}
                                                @endfor
                                            ]
                                        }"
                                        @form-reset.window="selected = ''"
                                        @click.away="open = false" 
                                        class="relative flex-1"
                                    >
                                        <input type="hidden" name="jam_akhir" :value="selected">
                                        <button type="button" @click="open = !open"
                                            class="w-full flex items-center justify-between border border-gray-200 bg-gray-50/80 hover:bg-white p-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all duration-300 text-sm cursor-pointer font-medium"
                                            :class="selected ? 'text-gray-800' : 'text-gray-500'"
                                        >
                                            <span x-text="selected ? selected.substring(0,5) : 'Akhir'"></span>
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        <div x-show="open" 
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-2"
                                            class="absolute left-0 mt-2 w-full bg-white/95 backdrop-blur-xl border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-56 overflow-y-auto z-50"
                                            style="display: none;"
                                        >
                                            <template x-for="option in options" :key="option.value">
                                                <button type="button" 
                                                    @click="selected = option.value; open = false"
                                                    class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                                                    :class="selected === option.value ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
                                                >
                                                    <span x-text="option.label"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- BUTTON -->
                            <div class="flex gap-3 mt-6 stagger-item stagger-3">
                                <button type="reset" onclick="window.dispatchEvent(new Event('form-reset'))"
                                    class="w-1/3 border border-gray-200 bg-white hover:bg-gray-50 py-3 rounded-xl font-semibold text-gray-500 transition-all duration-300 text-sm">
                                    Reset
                                </button>
                                <button type="submit"
                                    class="w-2/3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-semibold shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 hover:shadow-blue-500/50 transition-all duration-300 text-sm">
                                    Cari Jadwal
                                </button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- GRID TANGGAL -->
        @php
            $tglMulaiFilter = request('tanggal_mulai') ?: (isset($tanggalMulai) ? $tanggalMulai : null);
            $tglAkhirFilter = request('tanggal_akhir') ?: (isset($tanggalAkhir) ? $tanggalAkhir : null);
            $isRangeFilter = !empty($tglMulaiFilter) && !empty($tglAkhirFilter);
        @endphp
        <div class="grid grid-cols-3 sm:grid-cols-5 md:grid-cols-7 gap-4">
            @foreach($tanggalList as $tgl)
            @php
                $tglStr = $tgl->format('Y-m-d');
                if ($isRangeFilter) {
                    $isSelected = ($tglStr >= $tglMulaiFilter && $tglStr <= $tglAkhirFilter);
                } elseif (!empty($tglMulaiFilter)) {
                    $isSelected = ($tglStr == $tglMulaiFilter);
                } else {
                    $isSelected = ($tanggal == $tglStr);
                }
            @endphp
            <a href="{{ route('reservasi', [
                'tanggal' => $tgl->format('Y-m-d'),
                'member' => $isMember ? 1 : null
            ]) }}" class="group block stagger-item" style="animation-delay: {{ $loop->index * 0.03 }}s;">
                <div class="p-3 md:p-4 rounded-2xl text-center text-sm md:text-base transition-all duration-300 border
                {{ $isSelected 
                    ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white border-transparent shadow-lg shadow-blue-500/30 scale-[1.02]' 
                    : 'bg-white border-gray-100 text-gray-700 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 group-hover:border-blue-200' }}">

                    <div class="font-bold text-lg md:text-xl {{ $isSelected ? 'text-white' : 'text-gray-800' }}">{{ $tgl->format('j') }}</div>
                    <div class="text-xs font-medium uppercase tracking-wider mt-0.5 {{ $isSelected ? 'text-blue-100' : 'text-gray-400' }}">{{ $tgl->format('M') }}</div>

                </div>
            </a>
            @endforeach
        </div>
    </div>

    <!-- // ========================================================================= -->
    <!-- // HASIL TAMPILAN PAKET MEMBER DARI ALGORITMA GREEDY -->
    <!-- // Opsi 1 = Paket dengan total perpindahan lapangan (switches) terkecil -->
    <!-- // ========================================================================= -->
    @if($isMember)

        @php
            $memberByDate = collect($memberSlots)->groupBy('tanggal_member');
            $isFilterMember = request()->filled('tanggal_mulai') || request()->filled('tanggal_akhir') || request()->filled('jam_mulai') || request()->filled('jam_akhir');
        @endphp

        @if($memberByDate->isEmpty())
            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-8 text-center my-6">
                <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h4 class="font-bold text-gray-800 text-lg mb-1">Tidak Ada Jadwal Member Tersedia</h4>
                <p class="text-sm text-gray-500 max-w-md mx-auto mb-5">Tidak ditemukan paket member yang tersedia pada rentang waktu filter yang dipilih. Silakan coba atur jam atau rentang tanggal lainnya.</p>
                <a href="{{ route('reservasi', ['member' => 1]) }}" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-xl font-semibold text-sm shadow-md hover:bg-blue-700 transition">
                    Reset Filter
                </a>
            </div>
        @endif

        @foreach($memberByDate as $tglMember => $slotsPerTgl)

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-6 md:p-8 mb-6">

            <h3 class="font-bold text-2xl text-gray-800 mb-6 tracking-tight">
                {{ \Carbon\Carbon::parse($tglMember)->translatedFormat('d F Y') }}
            </h3>

            @foreach($slotsPerTgl as $memberIndex => $member)

            {{-- Garis pemisah antar slot (kecuali yang pertama) --}}
            @if($memberIndex > 0)
            <div class="border-t-2 border-dashed border-gray-200 my-6"></div>
            @endif

            @if($member['status'] == 'tersedia')

            {{-- SLOT TERSEDIA --}}
            <div class="border border-gray-100 rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-shadow duration-300 scroll-reveal" x-data="{ open: false }">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

                    <!-- Title & Toggle -->
                    <div>
                        <button @click="open = !open" class="flex items-center gap-2 group outline-none">
                            <h4 class="font-extrabold text-blue-600 text-xl tracking-tight group-hover:text-blue-700 transition-colors">
                                {{ $member['jam_awal'] }} - {{ $member['jam_akhir'] }}
                                @if(isset($member['opsi']))
                                    <span class="text-sm font-medium text-blue-500 ml-1 bg-blue-50 px-2 py-0.5 rounded">Opsi {{ $member['opsi'] }}</span>
                                @endif
                            </h4>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-blue-600 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        
                        <div class="flex items-center gap-2 mt-1">
                            <p class="text-sm text-gray-500 font-medium">
                                Paket Member {{ active_arena()->member_jumlah_pekan ?? 4 }} Pekan
                            </p>
                            <span class="text-green-600 font-semibold text-sm">
                                ✓ Tersedia
                            </span>
                        </div>
                    </div>

                    <!-- Button Form -->
                    <form method="POST" action="{{ route('reservasi.pesan') }}" class="m-0">
                        @csrf
                        <input type="hidden" name="is_member" value="1">
                        @foreach($member['paket'] as $minggu)
                            @foreach($minggu['slots'] as $slot)
                                <input type="hidden" name="jadwal[]" value="{{ $slot->id }}">
                            @endforeach
                        @endforeach
                        <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-2.5 rounded-full font-semibold shadow-lg shadow-blue-500/30 hover:-translate-y-0.5 hover:shadow-blue-500/50 transition-all duration-300">
                            Pesan
                        </button>
                    </form>

                </div>

                <div x-show="open" x-transition class="mt-5 space-y-3">

                    @foreach($member['paket'] as $minggu)

                        <div class="border rounded-xl p-4">

                            <div class="font-semibold mb-3">
                                Pekan ke-{{ $minggu['minggu'] }}
                                —
                                {{ \Carbon\Carbon::parse($minggu['tanggal'])->translatedFormat('d F Y') }}
                            </div>

                            @foreach($minggu['slots'] as $slot)

                                <div class="flex justify-between items-center text-sm py-2 border-b last:border-b-0 border-gray-100">

                                    <div class="text-gray-700 font-medium">
                                        {{ substr($slot->jam_mulai,0,5) }} - {{ substr($slot->jam_selesai,0,5) }}
                                    </div>

                                    <div class="text-blue-600 font-semibold">
                                        {{ $slot->lapangan->nama_lapangan }}
                                    </div>

                                </div>

                            @endforeach

                        </div>

                        @endforeach

                    </div>

            </div>

            @else

            {{-- SLOT TIDAK TERSEDIA --}}
            <div class="border border-gray-100 rounded-2xl p-4 md:p-5 opacity-50 scroll-reveal">

                <div class="flex justify-between items-center">

                    <div>
                        <h4 class="font-extrabold text-gray-400 text-xl tracking-tight">
                            {{ $member['jam_awal'] }}
                            -
                            {{ $member['jam_akhir'] }}
                        </h4>

                        <p class="text-sm text-gray-400 font-medium mt-1">
                            Paket Member {{ active_arena()->member_jumlah_pekan ?? 4 }} Pekan
                        </p>
                    </div>

                    <div class="bg-gray-100 text-gray-400 px-6 py-2.5 rounded-full font-semibold">
                        Tidak Tersedia
                    </div>

                </div>

            </div>

            @endif

            @endforeach

            {{-- Catatan penjelasan: hanya muncul jika tanpa filter --}}
            @php
                $isFilterMember = request()->filled('tanggal_mulai') || request()->filled('tanggal_akhir') || request()->filled('jam_mulai') || request()->filled('jam_akhir');
            @endphp
            @if(!$isFilterMember)
            <div class="mt-8 bg-slate-50 border border-slate-200 rounded-xl px-5 py-4">
                <div class="flex items-start gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-slate-500 mt-0.5 flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                    <div class="text-sm text-slate-600">
                        <p class="font-semibold text-slate-700 mb-1">Mengapa beberapa jadwal tidak tersedia?</p>
                        <p>Paket Member membutuhkan <span class="font-semibold">{{ active_arena()->member_jam_per_pertemuan ?? 2 }} jam berurutan</span> di jam dan hari yang sama selama <span class="font-semibold">{{ active_arena()->member_jumlah_pekan ?? 4 }} pekan berturut-turut</span>. Jadwal bertanda "Tidak Tersedia" berarti salah satu atau beberapa jam dalam rentang tersebut sudah dipesan oleh orang lain di minggu tertentu, sehingga tidak bisa dijadikan paket member.</p>
                    </div>
                </div>
            </div>
            @endif

        </div>

        @endforeach

    @endif
    
    <!-- // ========================================================================= -->
    <!-- // HASIL TAMPILAN MATRIKS JADWAL RESERVASI HARIAN (NON-MEMBER) -->
    <!-- // Jika $hasil dari Algoritma Greedy ada, slot yang recommended disorot warna biru -->
    <!-- // ========================================================================= -->
    @if(!$isMember)

        @php
        if(isset($hasil) && count($hasil) > 0){

            $jadwalFilter = collect();

            foreach($hasil as $tanggalHasil => $blocks){

                foreach($blocks as $block){

                    foreach($block as $slot){
                        $jadwalFilter->push($slot);
                    }

                }

            }

            $group = $jadwalFilter->groupBy('jam_mulai');

        }else{

            $group = $jadwal->groupBy('jam_mulai');

        }
        @endphp

        @if(isset($hasil) && count($hasil) > 0)




            @foreach($hasil as $tanggalHasil => $blocks)

            <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-6 md:p-8 mb-8">

                <h3 class="font-bold text-2xl text-gray-800 mb-6 tracking-tight">
                    {{ \Carbon\Carbon::parse($tanggalHasil)->translatedFormat('d F Y') }}
                </h3>

                @foreach($blocks as $blockIndex => $block)

                @php
                    // Cek apakah block ini menggabungkan lapangan berbeda
                    $lapanganIds = collect($block)->pluck('lapangan_id')->unique();
                    $isMixedCourt = $lapanganIds->count() > 1;
                @endphp

                {{-- Garis pemisah antar block (kecuali yang pertama) --}}
                @if($blockIndex > 0)
                <div class="border-t-2 border-dashed border-gray-200 my-6"></div>
                @endif

                <div class="border border-gray-100 rounded-2xl p-4 md:p-5 shadow-sm hover:shadow-md transition-shadow duration-300 scroll-reveal">

                    @if($isMixedCourt)
                    @php
                        /* // KODE PHP: Bangun deskripsi gabungan lapangan secara dinamis */
                        $jamAwalBlock = substr($block[0]->jam_mulai, 0, 5);
                        $jamAkhirBlock = substr(end($block)->jam_selesai, 0, 5);

                        /* // KODE PHP: Kelompokkan slot berurutan per lapangan */
                        $segments = [];
                        $currentSeg = ['lapName' => $block[0]->lapangan->nama_lapangan, 'lap' => $block[0]->lapangan_id, 'mulai' => $block[0]->jam_mulai, 'selesai' => $block[0]->jam_selesai];
                        
                        for ($s = 1; $s < count($block); $s++) {
                            if ($block[$s]->lapangan_id == $currentSeg['lap']) {
                                $currentSeg['selesai'] = $block[$s]->jam_selesai;
                            } else {
                                $segments[] = $currentSeg;
                                $currentSeg = ['lapName' => $block[$s]->lapangan->nama_lapangan, 'lap' => $block[$s]->lapangan_id, 'mulai' => $block[$s]->jam_mulai, 'selesai' => $block[$s]->jam_selesai];
                            }
                        }
                        $segments[] = $currentSeg;

                        $gabunganParts = [];
                        foreach ($segments as $seg) {
                            $gabunganParts[] = $seg['lapName'] . ' (' . substr($seg['mulai'], 0, 5) . ' - ' . substr($seg['selesai'], 0, 5) . ')';
                        }
                        $gabunganText = implode(' dan ', $gabunganParts);
                    @endphp
                    <div class="flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                        </svg>
                        <p class="text-sm text-blue-700">
                            <span class="font-semibold">Penggabungan Lapangan:</span> Anda tetap bisa bermain di jam <span class="font-bold">{{ $jamAwalBlock }} - {{ $jamAkhirBlock }}</span> dengan gabungan {{ $gabunganText }}.
                        </p>
                    </div>
                    @endif

                    <div class="overflow-x-auto pb-4">
                    <div class="grid min-w-[max-content] gap-3 text-sm font-semibold border-b border-gray-100 pb-4 text-gray-600 court-cols">
                        <div class="sticky left-0 bg-white z-10 border-r border-gray-100 px-2 flex items-center justify-center self-stretch after:absolute after:-bottom-[1px] after:left-0 after:right-0 after:h-[1px] after:bg-white -mb-4 pb-4">Waktu</div>
                        @foreach($lapangan as $lap)
                        <div class="text-center">
                    @php
                        $words = explode(' ', $lap->nama_lapangan);
                        $initial = strtoupper(substr($words[0], 0, 1));
                        $num = isset($words[1]) ? $words[1] : '';
                    @endphp
                    <span class="court-name-full">{{ $lap->nama_lapangan }}</span>
                    <span class="court-name-short">{{ $initial }}{{ $num }}</span>
                </div>
                        @endforeach
                    </div>

                    @foreach($block as $slot)

                    <div class="grid min-w-[max-content] gap-3 items-center border-b text-sm court-cols">

                        <div class="sticky left-0 bg-white z-10 border-r border-gray-100 px-2 font-bold self-stretch py-2 flex items-center justify-center after:absolute after:-bottom-[1px] after:left-0 after:right-0 after:h-[1px] after:bg-white"><div class="flex flex-col items-center justify-center text-[11px] sm:text-xs md:text-sm leading-tight text-gray-700"><span>{{ substr($slot->jam_mulai,0,5) }}</span><span class="text-[10px] text-gray-400 leading-none my-0.5">-</span><span>{{ substr($slot->jam_selesai,0,5) }}</span></div></div>

                        @foreach($lapangan as $lap)
                        <div class="text-center">
                            @php
                                /* // KODE PHP: Cek apakah slot jam & lapangan ini adalah HASIL REKOMENDASI ALGORITMA GREEDY */
                                /* // $lapRecommended = true jika slot ini terpilih oleh Greedy Choice (switches minimal) */
                                $lookupKey = $tanggalHasil . '_' . $slot->jam_mulai . '_' . $lap->id;
                                $slotLap = $jadwalDict[$lookupKey] ?? null;
                                $lapRecommended = ($slot->lapangan_id == $lap->id);
                                if (!$slotLap && !isset($GLOBALS['logged_mismatch'])) {
                                    file_put_contents('storage/logs/dict_debug.log', "MISMATCH!\nLookup Key: " . $lookupKey . "\nAvailable Keys: " . implode(', ', array_slice(array_keys($jadwalDict), 0, 10)) . "\n", FILE_APPEND);
                                    $GLOBALS['logged_mismatch'] = true;
                                }
                            @endphp

                            @if($slotLap)
                                @if($slotLap->status == 'proses')
                                    @if($lapRecommended)
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui min-h-[36px] flex items-center justify-center bg-orange-50 text-orange-600 font-medium border border-orange-100 py-2 rounded-full shadow-sm">
                                            <span class="slot-text">Menunggu</span>
                                        </div>
                                    @else
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity min-h-[36px] flex items-center justify-center bg-yellow-50 text-yellow-600 font-medium py-2 rounded-full border border-yellow-100">
                                            <span class="slot-text">Menunggu</span>
                                        </div>
                                    @endif
                                @elseif($slotLap->status == 'berhasil' || $slotLap->status == 'telah dipesan')
                                    <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity min-h-[36px] flex items-center justify-center bg-gray-200 text-gray-500 font-medium py-2 rounded-full border border-gray-300">
                                        <span class="slot-text">Sudah Dipesan</span>
                                    </div>
                                @elseif($slotLap->status == 'diperbaiki')
                                    <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity min-h-[36px] flex items-center justify-center bg-red-50 text-red-500 font-medium py-2 rounded-full border border-red-100">
                                        <span class="slot-text">Diperbaiki</span>
                                    </div>
                                @elseif($slotLap->status == 'event')
                                    <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity min-h-[36px] flex items-center justify-center bg-purple-50 text-purple-600 font-medium py-2 rounded-full border border-purple-100">
                                        <span class="slot-text">Event</span>
                                    </div>
                                @elseif($slotLap->status == 'tutup')
                                    <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity min-h-[36px] flex items-center justify-center bg-gray-200 text-gray-600 font-medium py-2 rounded-full border border-gray-300">
                                        <span class="slot-text">Tutup</span>
                                    </div>
                                <!-- // HASIL ALGORITMA GREEDY: Tampilan slot yang direkomendasikan (Sorot Biru) -->
                                @elseif($lapRecommended)
                                    @if(\Carbon\Carbon::parse($tanggalHasil . ' ' . $slotLap->jam_mulai)->isPast())
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui min-h-[36px] flex items-center justify-center bg-white text-gray-400 font-medium py-2 rounded-full border border-gray-200">
                                            <span class="slot-text">Waktu Habis</span>
                                        </div>
                                    @else
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui bg-blue-500 text-white font-bold py-2 min-h-[36px] flex items-center justify-center rounded-xl shadow-md border border-blue-600/20">
                                            <span class="slot-text">Tersedia</span>
                                        </div>
                                    @endif
                                @else
                                    @if(\Carbon\Carbon::parse($tanggalHasil . ' ' . $slotLap->jam_mulai)->isPast())
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui min-h-[36px] flex items-center justify-center bg-white text-gray-400 font-medium py-2 rounded-full border border-gray-200">
                                            <span class="slot-text">Waktu Habis</span>
                                        </div>
                                    @else
                                        <div id="slot-{{ $slotLap->id }}" class="slot-ui opacity-40 hover:opacity-100 transition-opacity bg-blue-100 text-blue-600 font-medium py-2 min-h-[36px] flex items-center justify-center rounded-xl shadow-sm border border-blue-200">
                                            <span class="slot-text">Tersedia</span>
                                        </div>
                                    @endif
                                @endif
                            @endif

                        </div>
                        @endforeach

                    </div>

                    @endforeach
                    </div>

                    <form method="POST" action="{{ route('reservasi.pesan') }}">
                        @csrf

                        @foreach($block as $slot)
                            <input type="hidden" name="jadwal[]" value="{{ $slot->id }}">
                        @endforeach

                        @php
                            $blockDurasi = count($block);
                            $hargaPerJam = active_arena()->harga_per_jam ?? 80000;
                            $blockTotal = $blockDurasi * $hargaPerJam;
                        @endphp

                        <div class="mt-4 pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gray-50/80 p-4 rounded-2xl border border-gray-200/60">
                            <div class="text-xs sm:text-sm text-gray-600 font-medium">
                                Total Harga: <span class="font-bold text-blue-600 text-base sm:text-lg">Rp {{ number_format($blockTotal, 0, ',', '.') }}</span>
                            </div>

                            <button type="submit"
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-full font-bold text-sm shadow-md shadow-blue-500/20 hover:shadow-blue-500/40 transition-all flex items-center justify-center cursor-pointer">
                                Pesan
                            </button>
                        </div>

                    </form>

                </div>

                @endforeach

            </div>

            @endforeach

        @else

        <form method="POST" action="/reservasi/pesan">
        @csrf

        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-6 md:p-8 mb-6 scroll-reveal">

            <h3 class="font-bold text-2xl text-gray-800 mb-6 tracking-tight">
                {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
            </h3>

            <div class="overflow-x-auto pb-4">
            <!-- HEADER -->
            <div class="grid min-w-[max-content] gap-3 text-sm font-semibold border-b border-gray-100 pb-4 text-gray-600 court-cols">
                <div class="sticky left-0 bg-white z-10 border-r border-gray-100 px-2 flex items-center justify-center self-stretch after:absolute after:-bottom-[1px] after:left-0 after:right-0 after:h-[1px] after:bg-white -mb-4 pb-4">Waktu</div>
                @foreach($lapangan as $lap)
                <div class="text-center">
                    @php
                        $words = explode(' ', $lap->nama_lapangan);
                        $initial = strtoupper(substr($words[0], 0, 1));
                        $num = isset($words[1]) ? $words[1] : '';
                    @endphp
                    <span class="court-name-full">{{ $lap->nama_lapangan }}</span>
                    <span class="court-name-short">{{ $initial }}{{ $num }}</span>
                </div>
                @endforeach
            </div>

            @foreach($group as $jam => $items)

            <div class="grid min-w-[max-content] gap-3 items-center border-b border-gray-50 text-sm hover:bg-gray-50/50 transition-colors duration-200 rounded-xl court-cols">

                <!-- JAM -->
                <div class="sticky left-0 bg-white z-10 border-r border-gray-100 px-2 font-bold self-stretch py-2 flex items-center justify-center after:absolute after:-bottom-[1px] after:left-0 after:right-0 after:h-[1px] after:bg-white"><div class="flex flex-col items-center justify-center text-[11px] sm:text-xs md:text-sm leading-tight text-gray-700"><span>{{ substr($jam,0,5) }}</span><span class="text-[10px] text-gray-400 leading-none my-0.5">-</span><span>{{ substr($items->first()->jam_selesai,0,5) }}</span></div></div>

                @foreach($lapangan as $lapObj)
                @php $lap = $lapObj->id; @endphp

                <div class="text-center">

                    @php
                        $slot = $items->where('lapangan_id',$lap)->first();
                    @endphp

                    @if($slot)

                        @php
                            $isPast = \Carbon\Carbon::parse($tanggal . ' ' . $slot->jam_mulai)->isPast();
                        @endphp

                        @if($slot->status == 'tersedia')

                            @if($isPast)
                                <div class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-gradient-to-br from-white to-gray-100 text-gray-400 font-medium border border-gray-200 opacity-80">
<span class="slot-text">Waktu Habis</span>
</div>
                            @else
                                <label class="group block w-full cursor-pointer slot-label">
                                    <input type="checkbox"
                                        class="slot hidden"
                                        name="jadwal[]"
                                        value="{{ $slot->id }}">

                                    <div
                                        id="slot-{{ $slot->id }}"
                                        class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-blue-100 text-blue-600 border border-transparent font-medium transition-all shadow-sm duration-300 hover:bg-blue-200"
                                        style="transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;">
                                        <span class="slot-text">Tersedia</span>
                                    </div>
                                </label>
                            @endif

                        @elseif($slot->status == 'proses')

                        <div
                            id="slot-{{ $slot->id }}"
                            class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-medium shadow-sm border border-orange-100">
<span class="slot-text">Menunggu</span>
</div>

                        @elseif($slot->status == 'berhasil' || $slot->status == 'telah dipesan')

                        <div
                            id="slot-{{ $slot->id }}"
                            class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-gray-200 text-gray-500 font-medium border border-gray-300">
<span class="slot-text">Sudah Dipesan</span>
</div>
                        
                        @elseif($slot->status == 'diperbaiki')

                        <div
                            id="slot-{{ $slot->id }}"
                            class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-red-50 text-red-500 font-medium border border-red-100">
<span class="slot-text">Diperbaiki</span>
</div>
                        
                        @elseif($slot->status == 'event')

                        <div
                            id="slot-{{ $slot->id }}"
                            class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-purple-50 text-purple-600 font-medium border border-purple-100">
<span class="slot-text">Event</span>
</div>

                        @elseif($slot->status == 'tutup')

                        <div
                            id="slot-{{ $slot->id }}"
                            class="slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-gray-200 text-gray-800 font-medium border border-gray-300">
<span class="slot-text">Tutup</span>
</div>

                        @endif

                    @endif

                </div>

                @endforeach

            </div>

            @endforeach

            </div>
        </div>

        
        <!-- LEGENDA WARNA (KHUSUS MOBILE) -->
        <div class="legend-container hidden mt-8 mb-4 border border-gray-100 bg-white p-4 rounded-2xl shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 text-center">Keterangan Warna</p>
            <div class="flex flex-wrap gap-x-4 gap-y-3 justify-center text-[11px] font-medium text-gray-600">
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-blue-500 shadow-md border border-blue-600/20"></div>
                    <span>Tersedia</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-white border border-gray-200"></div>
                    <span>Waktu Habis</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-gray-500 shadow-md border border-gray-600/20"></div>
                    <span>Dipesan</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-orange-500 shadow-md border border-orange-600/20"></div>
                    <span>Menunggu</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-purple-500 shadow-md border border-purple-600/20"></div>
                    <span>Event</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-red-500 shadow-md border border-red-600/20"></div>
                    <span>Diperbaiki</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <div class="w-4 h-4 rounded-md bg-black shadow-md border border-black"></div>
                    <span>Tutup</span>
                </div>
            </div>
            
            <!-- Keterangan Singkatan -->
            <div class="mt-4 pt-3 border-t border-gray-50">
                <div class="flex flex-wrap gap-4 justify-center text-[11px] font-medium text-gray-600">
                    @php
                        $jenisFasilitas = [];
                        foreach($lapangan as $lap) {
                            $firstWord = explode(' ', $lap->nama_lapangan)[0];
                            $initial = strtoupper(substr($firstWord, 0, 1));
                            $jenisFasilitas[$initial] = $firstWord;
                        }
                    @endphp
                    @foreach($jenisFasilitas as $init => $nama)
                        <span><b>{{ $init }}</b> = {{ $nama }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- TOTAL -->

        <div class="mt-8 mb-24 bg-gradient-to-br from-gray-50 to-gray-100/50 p-6 md:p-8 rounded-3xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-blue-100/50 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="w-full md:w-auto flex flex-col gap-2 relative z-10">
                <div class="flex items-center justify-between md:justify-start gap-4">
                    <span class="text-gray-500 font-medium w-28">Jumlah Jam</span>
                    <span class="font-bold text-blue-600">: <span id="jumlahJam">0</span> Jam</span>
                </div>
                <div class="flex items-center justify-between md:justify-start gap-4">
                    <span class="text-gray-500 font-medium w-28">Total Harga</span>
                    <span class="font-bold text-blue-600">: Rp <span id="totalHarga">0</span></span>
                </div>
            </div>

            <button type="submit"
                class="relative z-10 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-md shadow-blue-500/30 hover:-translate-y-1 hover:shadow-blue-500/50 transition-all duration-300 w-full md:w-auto text-base flex items-center justify-center gap-2">
                <span>Pesan Sekarang</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>

        </form>

    @endif
    @endif

    </div>


    @php
        $fasilitasArr = json_decode(active_arena()->fasilitas ?? '[]', true) ?: [];
        $fasilitasTambahan = active_arena()->fasilitas_tambahan ?? '';
        $arena = active_arena();
    @endphp

    <!-- ================== PREMIUM INFO ARENA ================== -->
    <div class="bg-white border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] px-6 py-5 mt-12 mb-8 rounded-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        
        <!-- Lokasi & Kontak (Kiri) -->
        <div class="flex flex-col md:flex-row gap-6 md:gap-10">
            <!-- Alamat -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lokasi Arena</p>
                    <p class="text-sm font-bold text-gray-800 leading-relaxed">
                        {{ $arena->alamat ?: '-' }}<br>
                        {{ implode(', ', array_filter([$arena->kota, $arena->provinsi, $arena->kodepos])) }}
                    </p>
                </div>
            </div>

            <!-- Kontak -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center shrink-0 border border-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kontak Resmi</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $arena->no_telp ?: '-' }}</p>
                    <p class="text-sm text-gray-500">{{ $arena->email ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Fasilitas (Kanan) -->
        <div class="flex items-start gap-4 lg:border-l lg:border-gray-100 lg:pl-8">
             <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center shrink-0 border border-purple-100">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Fasilitas Tersedia</p>
                <div class="flex flex-wrap gap-2 lg:max-w-xs">
                    @if(count($fasilitasArr) > 0)
                        @foreach($fasilitasArr as $fas)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                {{ $fas }}
                            </span>
                        @endforeach
                    @endif
                    
                    @if($fasilitasTambahan)
                        @php
                            $extraList = array_map('trim', preg_split('/[,\r\n]+/', $fasilitasTambahan));
                            $extraList = array_filter($extraList);
                        @endphp
                        @foreach($extraList as $ext)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                {{ $ext }}
                            </span>
                        @endforeach
                    @endif

                    @if(count($fasilitasArr) == 0 && !$fasilitasTambahan)
                        <span class="text-sm text-gray-500 italic">Belum ada informasi</span>
                    @endif
                </div>
            </div>
        </div>
        
    </div>

@endguest
</div>

<script>
function toggleFilter(){

    const filter = document.getElementById("filterDropdown");

    filter.classList.toggle("hidden");

    // AKTIFKAN TOMSELECT MEMBER
    if(document.querySelector("#member_tanggal_mulai")){

        if(!document.querySelector("#member_tanggal_mulai").tomselect){

            new TomSelect("#member_tanggal_mulai", {
                create: false,
                searchField: false,
                placeholder: "Pilih Tanggal",
            });

            new TomSelect("#member_tanggal_akhir", {
                create: false,
                searchField: false,
                placeholder: "Pilih Tanggal",
            });

            new TomSelect("#member_jam_mulai", {
                create: false,
                searchField: false,
                placeholder: "Pilih Jam",
            });

            new TomSelect("#member_jam_akhir", {
                create: false,
                searchField: false,
                placeholder: "Pilih Jam",
            });
        }
    }
}

let hargaPerJam = {{ isset($hargaPerJamDefault) ? $hargaPerJamDefault : 80000 }};

document.querySelectorAll('.slot').forEach(function(cb){

    cb.addEventListener('change', function(e){

        const pendingWrapper = document.getElementById('pendingWrapper');
        
        if (pendingWrapper && !pendingWrapper.classList.contains('hidden')) {
            e.preventDefault();
            cb.checked = false;
            tampilkanBannerPending();
            return;
        }

        let parent = cb.nextElementSibling;

        if (cb.checked) {
            parent.classList.add('selected-slot', 'bg-gradient-to-br', 'from-blue-600', 'to-indigo-600', 'border-transparent', 'shadow-lg', 'shadow-blue-500/30', 'scale-110', 'z-20', 'text-white');
            parent.classList.remove('bg-blue-100', 'text-blue-600', 'border-transparent', 'hover:bg-blue-200', 'scale-[1.02]');
        } else {
            parent.classList.remove('selected-slot', 'bg-gradient-to-br', 'from-blue-600', 'to-indigo-600', 'border-transparent', 'shadow-lg', 'shadow-blue-500/30', 'scale-110', 'z-20', 'scale-[1.02]', 'text-white');
            parent.classList.add('bg-blue-100', 'text-blue-600', 'border-transparent', 'hover:bg-blue-200');
            parent.style.transform = 'none';
            parent.style.boxShadow = 'none';
        }

        let total = document.querySelectorAll('.slot:checked').length;

        document.getElementById('jumlahJam').innerText = total;
        document.getElementById('totalHarga').innerText =
            (total * hargaPerJam).toLocaleString();

    });

});

document.addEventListener("DOMContentLoaded", function () {

    new TomSelect("#tanggal_mulai", {
        create: false,
        searchField: false,
        placeholder: "Pilih Tanggal",
    });

    new TomSelect("#tanggal_akhir", {
        create: false,
        searchField: false,
        placeholder: "Pilih Tanggal",
    });

    new TomSelect("#jam_mulai", {
        create: false,
        searchField: false,
        placeholder: "Pilih Jam",
    });

    new TomSelect("#jam_selesai", {
        create: false,
        searchField: false,
        placeholder: "Pilih Jam",
    });

    new TomSelect("#durasi", {
        create: false,
        searchField: false,
        placeholder: "Pilih Durasi",
    });


    document.querySelectorAll('.ts-control input').forEach(input => {
    input.setAttribute('readonly', true);
});

});

</script>

<script>

let datesToFetch = [];
@if(isset($hasil) && count($hasil) > 0)
    datesToFetch = {!! json_encode(array_keys($hasil)) !!};
@else
    datesToFetch = ['{{ $tanggal }}'];
@endif

function refreshStatus() {

    datesToFetch.forEach(date => {
        fetch('/reservasi/status/' + date)
            .then(response => response.json())
            .then(data => {

                console.log(data);

            data.forEach(slot => {

                const el = document.getElementById('slot-' + slot.id);

                if (!el) return;

                if (el.classList.contains('selected-slot')) {
                    return;
                }
                
                const textEl = el.querySelector('.slot-text');
                const currentText = textEl ? textEl.innerText.trim() : '';

                if (slot.status === 'tersedia' && currentText === 'Tersedia') return;
                if ((slot.status === 'berhasil' || slot.status === 'telah dipesan') && currentText === 'Sudah Dipesan') return;
                if (slot.status === 'diperbaiki' && currentText === 'Diperbaiki') return;
                if (slot.status === 'event' && currentText === 'Event') return;
                if (slot.status === 'tutup' && currentText === 'Tutup') return;
                if (slot.status === 'proses' && currentText === 'Menunggu') return;

                if (slot.status === 'tersedia') {
                    el.innerHTML = '<span class="slot-text">Tersedia</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-blue-100 text-blue-600 border border-transparent font-medium transition-all shadow-sm duration-300 cursor-pointer hover:bg-blue-200';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) checkbox.disabled = false;
                }
                else if (slot.status === 'proses') {
                    el.innerHTML = '<span class="slot-text">Menunggu</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-medium shadow-sm border border-orange-100';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                    }
                }
                else if (slot.status === 'berhasil' || slot.status === 'telah dipesan') {
                    el.innerHTML = '<span class="slot-text">Sudah Dipesan</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-gray-200 text-gray-500 font-medium border border-gray-300';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) {
                        checkbox.checked = false;
                        checkbox.disabled = true;
                    }
                }
                else if (slot.status === 'diperbaiki') {
                    el.innerHTML = '<span class="slot-text">Diperbaiki</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-red-50 text-red-500 font-medium border border-red-100';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) { checkbox.checked = false; checkbox.disabled = true; }
                }
                else if (slot.status === 'event') {
                    el.innerHTML = '<span class="slot-text">Event</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-purple-50 text-purple-600 font-medium border border-purple-100';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) { checkbox.checked = false; checkbox.disabled = true; }
                }
                else if (slot.status === 'tutup') {
                    el.innerHTML = '<span class="slot-text">Tutup</span>';
                    el.className = 'slot-ui w-full py-2 min-h-[36px] flex items-center justify-center rounded-full bg-gray-200 text-gray-800 font-medium border border-gray-300';
                    const checkbox = el.previousElementSibling;
                    if (checkbox) { checkbox.checked = false; checkbox.disabled = true; }
                }

            });

        });
    });

}

// Jalankan sekali saat halaman dibuka
refreshStatus();

// Jalankan setiap 2 detik
setInterval(refreshStatus, 2000);

// Jalankan lagi ketika user kembali dengan tombol Back browser
window.addEventListener('pageshow', function () {

    refreshStatus();

});

function tampilkanBannerPending() {

    const banner = document.getElementById('pendingBanner');

    if (!banner) return;

    banner.scrollIntoView({

        behavior: 'smooth',
        block: 'center'

    });

    banner.classList.remove(
        'bg-yellow-100',
        'border-yellow-300'
    );

    banner.classList.add(
        'bg-yellow-300',
        'border-yellow-500',
        'scale-105',
        'shadow-2xl'
    );

    setTimeout(() => {

        banner.classList.remove(
            'bg-yellow-300',
            'border-yellow-500',
            'scale-105',
            'shadow-2xl'
        );

        banner.classList.add(
            'bg-yellow-100',
            'border-yellow-300'
        );

    },1000);

}

setInterval(() => {

    fetch('/reservasi/pending')
        .then(response => response.json())
        .then(data => {

            const wrapper = document.getElementById('pendingWrapper');

            if (!wrapper) return;

            if (data.pending) {

                wrapper.classList.remove('hidden');

                document.getElementById('btnLanjutkan').href =
                    '/pembayaran/' + data.id;

                document.getElementById('formBatalkan').action =
                    '/pembayaran/batal/' + data.id;

                refreshStatus();

            } else {

                wrapper.classList.add('hidden');

                refreshStatus();

            }

        });

}, 1000);

// === ANIMASI MELAYANG (JAVASCRIPT MURNI) ===
// Kita menggunakan JS karena Tailwind JIT sedang tidak mensinkronisasi class hover yang baru di komputer Anda.
// Ini sangat aman dan tidak akan menimbulkan bug.
document.querySelectorAll('.slot-label').forEach(label => {
    label.addEventListener('mouseenter', () => {
        const cb = label.querySelector('.slot');
        if (cb && !cb.checked && !cb.disabled) {
            const ui = label.querySelector('.slot-ui');
            if (ui) {
                ui.style.transform = 'translateY(-5px)';
                ui.style.boxShadow = '0 4px 10px rgba(0, 0, 0, 0.15)';
                ui.style.backgroundColor = '#bfdbfe'; // setara bg-blue-200
            }
        }
    });

    label.addEventListener('mouseleave', () => {
        const cb = label.querySelector('.slot');
        if (cb && !cb.checked && !cb.disabled) {
            const ui = label.querySelector('.slot-ui');
            if (ui) {
                ui.style.transform = 'none';
                ui.style.boxShadow = 'none';
                ui.style.backgroundColor = ''; 
            }
        }
    });
});

</script>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dateRangePicker', (initialStart, initialEnd) => ({
        start: initialStart || '',
        end: initialEnd || '',
        hoverDate: null,
        selectDate(date) {
            if (!this.start || (this.start && this.end)) {
                this.start = date;
                this.end = '';
            } else {
                if (date < this.start) {
                    this.end = this.start;
                    this.start = date;
                } else {
                    this.end = date;
                }
            }
        },
        isSelected(date) {
            return this.start === date || this.end === date;
        },
        isInRange(date) {
            if (this.start && this.end) {
                return date > this.start && date < this.end;
            }
            if (this.start && this.hoverDate && !this.end) {
                if (this.hoverDate > this.start) {
                    return date > this.start && date < this.hoverDate;
                } else {
                    return date < this.start && date > this.hoverDate;
                }
            }
            return false;
        },
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            const formatter = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            return formatter.format(d);
        }
    }));
});
</script>

<!-- // STYLE CSS: Efek transisi elemen scroll-reveal -->
<style>
/* // STYLE CSS: Kelas animasi scroll-reveal */
.scroll-reveal {
    opacity: 0;
    transform: translateY(20px) scale(0.98);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.scroll-reveal.is-revealed {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>

<!-- // SCRIPT JAVASCRIPT: Observer interseksi scroll-reveal -->
<script>
// // JS: Observer Intersection untuk menganimasikan elemen saat di-scroll
document.addEventListener("DOMContentLoaded", function() {
    let delay = 0;
    let timer = null;
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.classList.add('is-revealed');
                }, delay);
                delay += 50;
                
                if(timer) clearTimeout(timer);
                timer = setTimeout(() => { delay = 0; }, 100);
                
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: "0px 0px -20px 0px" });

    document.querySelectorAll('.scroll-reveal').forEach(el => observer.observe(el));
});
</script>

<!-- // TAMPILAN JIKA PENGGUNA BELUM LOGIN (GUEST) -->
@else
<!-- // STYLE CSS: Animasi kartu akses terkunci -->
<style>
    /* // STYLE CSS: Keyframes animasi bounce & floating lock */
    @keyframes fadeUpBounce {
        0% { opacity: 0; transform: translateY(40px) scale(0.95); }
        60% { opacity: 1; transform: translateY(-10px) scale(1.02); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes floatingLock {
        0% { transform: translateY(0px) rotate(0deg); }
        25% { transform: translateY(-5px) rotate(-3deg); }
        50% { transform: translateY(-10px) rotate(0deg); }
        75% { transform: translateY(-5px) rotate(3deg); }
        100% { transform: translateY(0px) rotate(0deg); }
    }
    .animate-fade-up-bounce {
        animation: fadeUpBounce 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    .animate-floating-lock {
        animation: floatingLock 4s ease-in-out infinite;
    }
</style>
<!-- // BLOK BOX PEMBERITAHUAN BELUM LOGIN -->
<div class="max-w-7xl mx-auto mt-10 px-4 pb-20">
    <div class="text-center mt-20 bg-white p-10 rounded-3xl border border-gray-100 shadow-xl shadow-blue-900/5 max-w-lg mx-auto animate-fade-up-bounce hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-500 hover:-translate-y-2">
        <div class="w-24 h-24 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner relative group">
            <div class="absolute inset-0 bg-blue-100 rounded-full blur opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-500 animate-floating-lock relative z-10">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-700 tracking-tight">Belum Login</h1>
        <p class="mt-3 text-gray-500 font-medium leading-relaxed">Silakan login atau daftar terlebih dahulu untuk dapat melihat ketersediaan jadwal dan memesan lapangan.</p>
        <a href="/login" class="mt-8 block w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-[length:200%_auto] text-white px-6 py-3.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 hover:bg-[center_right_1rem] transition-all duration-300 text-lg">
            Masuk Akun
        </a>
    </div>
</div>
@endauth

</x-app-layout>
