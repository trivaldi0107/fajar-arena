<x-app-layout>

<style>
html{
    scroll-behavior:smooth;
}

body{
    overflow-x:hidden;
}

/* ================= HERO ================= */

.hero-slide{
    position:absolute;
    inset:0;
    width:100%;
    height:100%;
    object-fit:cover;
    object-position: center;
    opacity:1;
    transform:translateX(100%);
    transition:transform 1.1s cubic-bezier(.22,.61,.36,1);
    z-index:1;
}

@media (max-width: 768px) {
    .hero-slide {
        object-position: 25% center;
    }
}

.hero-slide.active{
    transform:translateX(0);
    z-index:3;
}

.hero-slide.out-left{
    transform:translateX(-100%);
    z-index:2;
}

.hero-slide.out-right{
    transform:translateX(100%);
    z-index:2;
}

.hero-slide.ready-right{
    transform:translateX(100%);
    z-index:1;
}

.hero-slide.ready-left{
    transform:translateX(-100%);
    z-index:1;
}

.hero-slide.hidden-reset{
    opacity:0 !important;
    z-index:0 !important;
}

/* ================= TEXT ================= */

.fade-up{
    opacity:0;
    transform:translateY(30px);
    transition:all .4s ease; /* Transisi cepat saat menghilang */
}

.fade-up.show{
    opacity:1;
    transform:translateY(0);
    transition:all 1s cubic-bezier(0.16, 1, 0.3, 1); /* Transisi sangat smooth saat muncul */
}

/* Staggered Delays */
#heroTitle.show { transition-delay: 50ms; }
#heroTagline.show { transition-delay: 150ms; }
#heroDesc.show { transition-delay: 250ms; }
#heroBtn.show { transition-delay: 350ms; }

/* ================= BUTTON ================= */

.nav-btn{
    width:58px;
    height:58px;
    border-radius:9999px;
    background:rgba(255,255,255,.2);
    backdrop-filter:blur(12px);
    -webkit-backdrop-filter:blur(12px);
    border:1px solid rgba(255,255,255,.3);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:all .2s ease;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
    -webkit-tap-highlight-color: transparent;
    outline: none;
}

@media (hover: hover) and (pointer: fine) {
    .nav-btn:hover{
        background:white;
        color:#2563eb;
        transform:scale(1.08);
    }
}

.nav-btn:active{
    background:rgba(255,255,255,.45);
    color:white;
    transform:scale(0.92);
}

/* ================= CARD ================= */

.feature-card{
    transition:.25s ease;
}

.feature-card:hover{
    transform:translateY(-6px);
    box-shadow:0 20px 35px rgba(0,0,0,.08);
}
</style>


<!-- ================= TOMBOL INFO PRICELIST & KEBIJAKAN (HANYA ICON BERSIH DI SUDUT KANAN ATAS) ================= -->
<div x-data="{ showKebijakanModal: false }">
    <!-- Floating Icon Button di Sudut Kanan Atas (Diberi Jarak Lega dari Topbar Putih) -->
    <div class="fixed top-28 sm:top-32 md:top-36 right-4 sm:right-6 md:right-8 z-40">
        <button @click="showKebijakanModal = true" type="button" 
            class="flex items-center justify-center w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white shadow-[0_10px_25px_rgba(37,99,235,0.4)] hover:shadow-[0_14px_30px_rgba(37,99,235,0.55)] border border-white/30 hover:scale-105 active:scale-95 transition-all duration-200 cursor-pointer">
            
            <!-- Icon Saja Tanpa Teks & Tanpa Bulatan -->
            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
        </button>
    </div>

    <!-- MODAL POPUP PRICELIST & KEBIJAKAN -->
    <div x-show="showKebijakanModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 sm:p-6" 
         style="display: none;"
         @keydown.escape.window="showKebijakanModal = false">
        
        <div x-show="showKebijakanModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             @click.away="showKebijakanModal = false"
             class="bg-white rounded-3xl shadow-2xl border border-slate-100 w-full max-w-2xl overflow-hidden my-8 flex flex-col max-h-[90vh]">
            
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 text-white flex items-center justify-between relative shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/30 shadow-inner">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold leading-tight">Pricelist & Kebijakan Reservasi</h3>
                        <p class="text-xs text-blue-100 mt-0.5">Informasi tarif dan ketentuan pemesanan di {{ $pengaturan->nama_arena ?? 'Fajar Arena' }}</p>
                    </div>
                </div>
                <button @click="showKebijakanModal = false" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition-colors cursor-pointer" title="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 overflow-y-auto space-y-6 text-sm text-slate-600">
                
                <!-- Quick Pricelist Cards -->
                <div>
                    <h4 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">Daftar Tarif Sewa Lapangan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <!-- Card Non-Member -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 relative overflow-hidden">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Sewa Reguler</span>
                                <span class="text-[10px] bg-slate-200/70 text-slate-700 px-2 py-0.5 rounded-full font-semibold">Harian</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-slate-800">
                                Rp {{ number_format($pengaturan->harga_per_jam ?? 80000, 0, ',', '.') }}
                                <span class="text-xs font-medium text-slate-400">/ jam</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1.5 leading-snug">Pemesanan fleksibel per jam dengan rekomendasi filter cerdas otomatis.</p>
                        </div>

                        <!-- Card Member -->
                        <div class="p-4 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50/60 border border-blue-200/90 relative overflow-hidden">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-700">Paket Member</span>
                                <span class="text-[10px] bg-blue-600 text-white px-2 py-0.5 rounded-full font-bold">Hemat Rutin</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-black text-blue-700">
                                Rp {{ number_format($pengaturan->member_harga ?? 1000000, 0, ',', '.') }}
                                <span class="text-xs font-medium text-blue-500">/ paket</span>
                            </div>
                            <p class="text-xs text-blue-900/80 mt-1.5 leading-snug">
                                {{ $pengaturan->member_jumlah_pekan ?? 4 }} pekan rutin ({{ $pengaturan->member_jam_per_pertemuan ?? 2 }} jam/pertemuan) pada jam & hari tetap.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Catatan / Kebijakan dari Admin -->
                <div class="border-t border-slate-100 pt-5">
                    <h4 class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">Kebijakan & Ketentuan Reservasi</h4>
                    @if(!empty($pengaturan->catatan_member))
                        <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4.5 text-slate-700 leading-relaxed space-y-2 whitespace-pre-line text-sm">
                            {!! nl2br(e($pengaturan->catatan_member)) !!}
                        </div>
                    @else
                        <div class="bg-slate-50 border border-slate-200/70 rounded-2xl p-4 text-slate-600 leading-relaxed text-sm space-y-2">
                            <p>1. <strong>Waktu Pembayaran:</strong> Batas waktu pembayaran adalah <strong>10 menit</strong> setelah checkout. Jika melewati batas waktu, pesanan akan otomatis dibatalkan.</p>
                            <p>2. <strong>Verifikasi QRIS:</strong> Pastikan mengunggah bukti transfer yang jelas agar operator dapat segera mengonfirmasi pesanan Anda.</p>
                            <p>3. <strong>E-Tiket & Check-in:</strong> Tunjukkan E-Tiket ber-QR Code kepada petugas di lokasi lapangan saat kedatangan untuk proses check-in kehadiran.</p>
                            <p>4. <strong>Paket Member:</strong> Berlaku untuk 4 pekan berturut-turut pada hari dan jam yang telah dipilih secara otomatis oleh sistem.</p>
                        </div>
                    @endif
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                <p class="text-xs text-slate-500 text-center sm:text-left">Ada pertanyaan lain? Hubungi WhatsApp: <strong>{{ $pengaturan->beranda_no_telp ?? $pengaturan->no_telp ?? '0853-9993-9799' }}</strong></p>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a href="/pilih-cabang" class="w-full sm:w-auto text-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 hover:shadow-lg transition-all">
                        Pesan Lapangan 🏸
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ================= HERO ================= -->
@if(isset($sliders) && $sliders->count() > 0)
<section class="relative md:h-screen bg-gradient-to-b from-gray-900 to-slate-950 overflow-hidden flex flex-col justify-start md:flex-row md:items-center pt-3 sm:pt-6 md:pt-0 pb-12 md:pb-0">

    <!-- slides container -->
    <div class="relative w-full aspect-video md:absolute md:inset-0 md:h-full md:w-full z-0 group rounded-2xl md:rounded-none overflow-hidden mx-auto max-w-[94%] sm:max-w-[92%] md:max-w-none shadow-2xl md:shadow-none">
        
        <div class="absolute inset-0 bg-gradient-to-t from-gray-950 via-gray-900/40 to-transparent z-10 pointer-events-none md:block hidden"></div>

        <!-- Slides -->
        @if(isset($sliders) && $sliders->count() > 0)
            @foreach($sliders as $index => $slider)
                <img src="{{ asset('storage/' . $slider->gambar) }}" class="hero-slide {{ $index === 0 ? 'active' : 'ready-right' }}">
            @endforeach
        @endif

        <!-- tombol kiri (dipindah ke dalam container gambar) -->
        <div class="absolute left-2 md:left-8 z-20 top-1/2 -translate-y-1/2">
            <button id="prevBtn" class="nav-btn !w-10 !h-10 md:!w-14 md:!h-14">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 md:w-6 md:h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2.5">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
        </div>

        <!-- tombol kanan (dipindah ke dalam container gambar) -->
        <div class="absolute right-2 md:right-8 z-20 top-1/2 -translate-y-1/2">
            <button id="nextBtn" class="nav-btn !w-10 !h-10 md:!w-14 md:!h-14">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-5 h-5 md:w-6 md:h-6"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke="currentColor"
                     stroke-width="2.5">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>

    </div>




    <!-- content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-10 w-full mt-4 sm:mt-6 md:mt-0 text-center md:text-left">

        <div class="max-w-2xl mx-auto md:mx-0 flex flex-col justify-center md:block">

            <h1 id="heroTitle"
                class="fade-up text-2xl sm:text-4xl md:text-6xl lg:text-7xl font-extrabold leading-tight text-white drop-shadow-md">
                {{ (isset($sliders) && $sliders->count() > 0 && $sliders[0]->judul) ? $sliders[0]->judul : 'Fajar Arena' }}
            </h1>

            <h2 id="heroTagline"
                class="fade-up mt-2 sm:mt-3 text-sm sm:text-xl md:text-3xl font-semibold text-blue-100 max-w-2xl leading-snug drop-shadow-sm">
                {{ (isset($sliders) && $sliders->count() > 0 && !empty($sliders[0]->tagline)) ? $sliders[0]->tagline : ($pengaturan->tagline ?? '') }}
            </h2>

            <p id="heroDesc"
               class="fade-up mt-2 sm:mt-4 text-xs sm:text-base md:text-lg text-gray-200 max-w-xl leading-relaxed drop-shadow-sm">
               {{ (isset($sliders) && $sliders->count() > 0 && !empty($sliders[0]->deskripsi)) ? $sliders[0]->deskripsi : ($pengaturan->deskripsi ?? '') }}
            </p>

        </div>
    </div>

</section>
@else
<section class="relative bg-gradient-to-br from-gray-900 via-blue-950 to-slate-950 pt-24 pb-16 text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 text-center md:text-left">
        <div class="max-w-3xl">
            <h1 class="text-3xl md:text-6xl font-extrabold leading-tight tracking-tight drop-shadow-lg">
                {{ $pengaturan->nama_arena ?? 'Fajar Arena' }}
            </h1>
            @if(!empty($pengaturan->tagline))
            <h2 class="mt-3 text-lg md:text-3xl font-semibold text-blue-200 leading-snug drop-shadow-sm">
                {{ $pengaturan->tagline }}
            </h2>
            @endif
            @if(!empty($pengaturan->deskripsi))
            <p class="mt-3 text-sm md:text-lg text-gray-300 leading-relaxed">
                {{ $pengaturan->deskripsi }}
            </p>
            @endif
        </div>
    </div>
</section>
@endif

<!-- ================= WHY US ================= -->
<div class="bg-gray-100 pb-8 sm:pb-12 md:pb-14">
    <section class="relative z-20 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 {{ (isset($sliders) && $sliders->count() > 0) ? '-mt-10 sm:-mt-16 md:-mt-28 pt-0' : 'pt-24' }} pb-4 sm:pb-6">
        
        <!-- ================= PROMO / EVENT INLINE ================= -->
        @php
            $promosList = [];
            if (isset($pengaturan) && !empty($pengaturan->pengumuman)) {
                $decoded = json_decode($pengaturan->pengumuman, true);
                if (is_array($decoded)) {
                    $promosList = $decoded;
                } else {
                    $promosList = [[
                        'gambar' => $pengaturan->gambar_pengumuman,
                        'judul' => $pengaturan->promo_judul ?? 'Jangan Lewatkan Kesempatan Ini!',
                        'label' => $pengaturan->promo_label ?? 'Promo Terbatas',
                        'deskripsi' => $pengaturan->pengumuman
                    ]];
                }
            }
        @endphp

        @foreach($promosList as $promoItem)
        <div class="rounded-[2.5rem] shadow-[0_20px_50px_-12px_rgba(30,58,138,0.5)] border border-blue-800/30 overflow-hidden mb-4 sm:mb-5 flex flex-col md:flex-row items-center bg-gradient-to-br from-indigo-950 via-blue-900 to-slate-900 relative group">
            
            <!-- Dekorasi Efek Cahaya -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-500/20 blur-[80px]"></div>
                <div class="absolute -bottom-[20%] -right-[10%] w-[50%] h-[50%] rounded-full bg-purple-500/20 blur-[80px]"></div>
            </div>

            @if(!empty($promoItem['gambar']))
            <!-- Area Gambar (Mengikuti Dimensi Asli) -->
            <div class="w-full md:w-1/2 p-6 md:p-10 flex items-center justify-center relative z-10">
                <div class="relative w-full max-w-md mx-auto group-hover:-translate-y-2 transition-transform duration-500 ease-out">
                    <div class="absolute inset-0 bg-blue-400 opacity-20 blur-2xl rounded-3xl group-hover:opacity-40 transition-opacity duration-500"></div>
                    <img src="{{ asset('storage/' . $promoItem['gambar']) }}" alt="Promo" class="relative z-10 w-full h-auto object-contain rounded-2xl shadow-2xl border border-white/10">
                </div>
            </div>
            @endif

            <!-- Area Teks -->
            <div class="w-full {{ !empty($promoItem['gambar']) ? 'md:w-1/2 p-6 sm:p-8 md:p-14 lg:pr-16 md:pl-0' : 'p-6 sm:p-10 md:p-16 text-center' }} relative z-10 flex flex-col justify-center">
                @if(!empty($promoItem['label']))
                <div class="{{ empty($promoItem['gambar']) ? 'mx-auto' : '' }} inline-flex items-center justify-center px-4 py-1.5 mb-4 md:mb-6 text-[10px] md:text-xs font-bold uppercase tracking-widest text-blue-100 bg-blue-900/50 backdrop-blur-md rounded-full self-start border border-blue-400/30 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse mr-2"></span> {{ $promoItem['label'] }}
                </div>
                @endif
                
                @if(!empty($promoItem['judul']))
                <h2 class="text-2xl sm:text-3xl md:text-5xl font-extrabold text-white mb-4 md:mb-6 leading-tight drop-shadow-lg">
                    {{ $promoItem['judul'] }}
                </h2>
                @endif

                <div class="prose prose-base sm:prose-lg prose-invert max-w-none mb-6 md:mb-8 text-blue-100/90 leading-relaxed font-medium text-base sm:text-lg md:text-2xl border-l-4 border-blue-500 pl-4 md:pl-6">
                    {{ $promoItem['deskripsi'] }}
                </div>

            </div>
        </div>
        @endforeach

        <!-- ================= BERITA & HIGHLIGHT OLAHRAGA ================= -->
        @if(isset($pengaturan->berita_list) && is_array($pengaturan->berita_list) && count($pengaturan->berita_list) > 0)
        @php
            $allBerita = array_values(array_filter($pengaturan->berita_list, function($item) {
                return !empty($item['judul']) || !empty($item['gambar']);
            }));
            // Find explicit headline or take first item as headline
            $headlineBerita = null;
            $otherBerita = [];
            foreach ($allBerita as $b) {
                if (empty($headlineBerita) && !empty($b['is_headline'])) {
                    $headlineBerita = $b;
                } else {
                    $otherBerita[] = $b;
                }
            }
            if (empty($headlineBerita) && count($allBerita) > 0) {
                $headlineBerita = array_shift($otherBerita);
            }
        @endphp

        <div class="w-full" style="margin-bottom: 0px !important; padding-bottom: 0px !important;">
            <!-- ================= HEADLINE BANNER (IMAGE 1 TOP) ================= -->
            @if($headlineBerita)
            <div onclick="openBeritaModal('headline')" class="relative rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl bg-gray-950 aspect-[16/10] sm:aspect-[21/9] md:aspect-[24/9] group cursor-pointer border border-gray-800 transition-all duration-300 hover:shadow-2xl mb-6 sm:mb-8">
                @if(!empty($headlineBerita['gambar']))
                <img src="{{ asset('storage/' . $headlineBerita['gambar']) }}" alt="{{ $headlineBerita['judul'] ?? 'Headline' }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 opacity-85">
                @else
                <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900"></div>
                @endif

                <!-- Dark Gradient Overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/60 to-transparent"></div>

                <!-- Headline Pill Badge (Top Left) -->
                <div class="absolute top-3 sm:top-6 left-3 sm:left-6 z-10">
                    <span class="inline-flex items-center px-3 py-1 sm:px-4 sm:py-1.5 rounded-full text-[10px] sm:text-xs font-extrabold bg-white text-gray-900 shadow-md">
                        <span class="w-1.5 h-1.5 sm:w-2 sm:h-2 rounded-full bg-red-500 animate-pulse mr-1.5 sm:mr-2"></span> Headline
                    </span>
                </div>

                <!-- Headline Content (Bottom Left) -->
                <div class="absolute bottom-0 left-0 right-0 p-3.5 sm:p-8 md:p-10 z-10 flex flex-col justify-end">
                    <!-- Meta Info: Kategori, Tanggal, Penulis -->
                    <div class="flex flex-wrap items-center gap-1.5 sm:gap-2.5 text-[10px] sm:text-sm font-semibold text-blue-200 mb-1 sm:mb-2.5">
                        <span class="bg-blue-600/90 text-white px-2 py-0.5 rounded text-[10px] sm:text-xs font-extrabold shadow-sm">{{ $headlineBerita['kategori'] ?? $headlineBerita['sumber'] ?? 'Olahraga' }}</span>
                        @if(!empty($headlineBerita['tanggal']))
                        <span>• {{ $headlineBerita['tanggal'] }}</span>
                        @endif
                        @if(!empty($headlineBerita['penulis']))
                        <span class="text-gray-300">• Penulis: <strong class="text-white">{{ $headlineBerita['penulis'] }}</strong></span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h3 class="text-base sm:text-2xl md:text-3xl lg:text-4xl font-extrabold text-white leading-tight sm:leading-snug drop-shadow-md group-hover:text-blue-200 transition-colors line-clamp-2 mb-1 sm:mb-2">
                        <span class="border-l-3 sm:border-l-4 border-red-500 pl-2 sm:pl-3 inline-block">{{ $headlineBerita['judul'] ?? '' }}</span>
                    </h3>

                    <!-- Ringkasan (Disembunyikan di HP agar tidak terlalu penuh, hanya tampil di Layar Tablet/Desktop) -->
                    @if(!empty($headlineBerita['ringkasan']))
                    <p class="hidden sm:block text-xs sm:text-sm text-gray-200 font-medium line-clamp-2 max-w-3xl drop-shadow mb-1 pl-4 opacity-95">
                        {{ $headlineBerita['ringkasan'] }}
                    </p>
                    @endif

                    <!-- Caption Foto (Disembunyikan di HP) -->
                    @if(!empty($headlineBerita['caption']))
                    <p class="hidden sm:block text-[11px] text-gray-400 italic pl-4">
                        {{ $headlineBerita['caption'] }}
                    </p>
                    @endif
                </div>
            </div>
            @endif

            <!-- ================= 2-COLUMN MOBILE / 3-COLUMN DESKTOP NEWS GRID ================= -->
            @if(count($otherBerita) > 0)
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6 lg:gap-8">
                @foreach($otherBerita as $index => $item)
                <div onclick="openBeritaModal({{ $index }})" class="group cursor-pointer flex flex-col">
                    <div class="relative aspect-[16/10] rounded-xl sm:rounded-2xl overflow-hidden shadow-sm bg-gray-900 mb-2 sm:mb-3 border border-gray-100 group-hover:shadow-xl transition-all duration-300">
                        @if(!empty($item['gambar']))
                        <img src="{{ asset('storage/' . $item['gambar']) }}" alt="{{ $item['judul'] ?? 'Berita' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                        <div class="w-full h-full bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center text-3xl sm:text-4xl">📰</div>
                        @endif
                    </div>

                    <h4 class="font-bold text-gray-900 text-xs sm:text-base lg:text-lg leading-snug line-clamp-2 group-hover:text-blue-600 transition-colors mb-1">
                        {{ $item['judul'] ?? '' }}
                    </h4>

                    @if(!empty($item['ringkasan']))
                    <p class="hidden sm:block text-xs text-gray-600 line-clamp-2 mb-2 leading-relaxed">
                        {{ $item['ringkasan'] }}
                    </p>
                    @endif

                    <div class="flex flex-wrap items-center gap-1 sm:gap-2 text-[10px] sm:text-xs font-semibold text-gray-500 mt-auto pt-0.5 sm:pt-1">
                        <span class="text-blue-600 font-bold">{{ $item['kategori'] ?? $item['sumber'] ?? 'Olahraga' }}</span>
                        @if(!empty($item['tanggal']))
                        <span>• {{ $item['tanggal'] }}</span>
                        @endif
                        @if(!empty($item['penulis']))
                        <span class="text-gray-400">• Oleh {{ $item['penulis'] }}</span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- ================= INTERACTIVE DETAIL MODAL (VANILLA JS) ================= -->
            <style>
                /* Remove ALL scrollbar arrows and clip cleanly */
                .custom-modal-scroll::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-modal-scroll::-webkit-scrollbar-button,
                .custom-modal-scroll::-webkit-scrollbar-button:single-button,
                .custom-modal-scroll::-webkit-scrollbar-button:vertical,
                .custom-modal-scroll::-webkit-scrollbar-button:vertical:decrement,
                .custom-modal-scroll::-webkit-scrollbar-button:vertical:increment,
                .custom-modal-scroll::-webkit-scrollbar-button:start,
                .custom-modal-scroll::-webkit-scrollbar-button:end {
                    display: none !important;
                    width: 0 !important;
                    height: 0 !important;
                    background: transparent !important;
                }
                .custom-modal-scroll::-webkit-scrollbar-track {
                    background: transparent;
                    margin: 16px 0;
                }
                .custom-modal-scroll::-webkit-scrollbar-thumb {
                    background-color: #cbd5e1;
                    border-radius: 9999px;
                }
                .custom-modal-scroll::-webkit-scrollbar-thumb:hover {
                    background-color: #94a3b8;
                }
                .custom-modal-scroll {
                    scrollbar-width: thin;
                    scrollbar-color: #cbd5e1 transparent;
                }
            </style>

            <div id="berita-modal-overlay" class="fixed inset-0 z-[99999] hidden overflow-y-auto items-center justify-center pt-24 pb-12 px-4 sm:px-6 md:px-10 bg-black/80 backdrop-blur-md transition-all duration-300">
                <div id="berita-modal-container" class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full max-h-[80vh] overflow-hidden border border-gray-100 relative text-gray-900 my-auto flex flex-col">
                    
                    <!-- Close Button (Absolute pinned inside outer clipped card) -->
                    <button onclick="closeBeritaModal()" class="absolute top-5 right-5 z-30 p-2.5 rounded-full bg-gray-100/90 backdrop-blur-md text-gray-500 hover:text-gray-900 hover:bg-gray-200 transition-all cursor-pointer shadow-sm" title="Tutup Modal">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Inner Scrollable Content Area -->
                    <div class="overflow-y-auto custom-modal-scroll p-6 sm:p-8 md:p-10 flex-1 pr-7 sm:pr-9 md:pr-11">

                        <!-- Article Title -->
                        <h2 id="modal-berita-judul" class="text-2xl sm:text-3xl md:text-4xl font-extrabold leading-tight tracking-tight mb-4 text-gray-900 pr-10"></h2>

                        <!-- Meta Info Bar (Kategori, Tanggal, Penulis) -->
                        <div class="flex flex-wrap items-center gap-3 text-xs sm:text-sm text-gray-500 border-b border-gray-100 pb-5 mb-6">
                            <span id="modal-berita-kategori" class="font-extrabold text-blue-600 text-sm"></span>
                            <span>•</span>
                            <span id="modal-berita-tanggal"></span>
                            <div id="modal-berita-penulis-box" class="flex items-center gap-2 ml-auto bg-gray-50 px-3 py-1 rounded-full border border-gray-200">
                                <div id="modal-berita-penulis-avatar" class="w-6 h-6 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[10px]">A</div>
                                <span id="modal-berita-penulis-nama" class="font-semibold text-xs text-gray-700"></span>
                            </div>
                        </div>

                        <!-- Featured Photo & Caption -->
                        <div id="modal-berita-gambar-box" class="mb-8">
                            <div class="rounded-2xl overflow-hidden shadow-lg bg-gray-100 max-h-[450px]">
                                <img id="modal-berita-gambar" src="" alt="Berita" class="w-full h-full object-cover">
                            </div>
                            <p id="modal-berita-caption" class="text-xs text-gray-500 mt-2.5 italic leading-relaxed"></p>
                        </div>

                        <!-- Ringkasan Singkat -->
                        <div id="modal-berita-ringkasan" class="p-4 sm:p-5 rounded-2xl bg-blue-50 border-l-4 border-blue-600 mb-6 text-blue-950 text-base font-semibold leading-relaxed"></div>

                        <!-- Article Body Content -->
                        <div id="modal-berita-isi" class="prose prose-lg max-w-none text-gray-800 leading-relaxed font-normal whitespace-pre-line text-base sm:text-lg mb-8"></div>

                        <!-- External Link Button -->
                        <div id="modal-berita-link-box" class="pt-6 border-t border-gray-100 flex flex-wrap gap-4 items-center justify-between">
                            <span class="text-xs text-gray-500">Ingin membaca versi artikel di website resmi / menonton video?</span>
                            <a id="modal-berita-link-btn" href="#" target="_blank" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-full shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                <span>Tonton Video / Buka Link Sumber</span>
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <script>
            window.headlineBeritaData = @js($headlineBerita);
            window.otherBeritaData = @js(array_values($otherBerita));

            function openBeritaModal(target) {
                const data = (target === 'headline') ? window.headlineBeritaData : window.otherBeritaData[target];
                if (!data) return;

                document.getElementById('modal-berita-judul').textContent = data.judul || '';
                document.getElementById('modal-berita-kategori').textContent = data.kategori || data.sumber || 'Olahraga';
                document.getElementById('modal-berita-tanggal').textContent = data.tanggal || '';

                // Penulis
                const penulisBox = document.getElementById('modal-berita-penulis-box');
                const penulisNama = document.getElementById('modal-berita-penulis-nama');
                const penulisAvatar = document.getElementById('modal-berita-penulis-avatar');
                if (data.penulis && data.penulis.trim() !== '') {
                    penulisNama.textContent = data.penulis;
                    penulisAvatar.textContent = data.penulis.trim().charAt(0).toUpperCase();
                    penulisBox.style.display = 'flex';
                } else {
                    penulisBox.style.display = 'none';
                }

                // Gambar & Caption
                const imgEl = document.getElementById('modal-berita-gambar');
                const imgBox = document.getElementById('modal-berita-gambar-box');
                const captionEl = document.getElementById('modal-berita-caption');
                if (data.gambar && data.gambar.trim() !== '') {
                    imgEl.src = '/storage/' + data.gambar;
                    imgBox.style.display = 'block';
                } else {
                    imgBox.style.display = 'none';
                }

                if (data.caption && data.caption.trim() !== '') {
                    captionEl.textContent = data.caption;
                    captionEl.style.display = 'block';
                } else {
                    captionEl.style.display = 'none';
                }

                // Ringkasan
                const ringkasanBox = document.getElementById('modal-berita-ringkasan');
                if (data.ringkasan && data.ringkasan.trim() !== '') {
                    ringkasanBox.textContent = data.ringkasan;
                    ringkasanBox.style.display = 'block';
                } else {
                    ringkasanBox.style.display = 'none';
                }

                // Isi Berita (Supports formatted HTML or text)
                const isiBox = document.getElementById('modal-berita-isi');
                const rawIsi = data.isi || data.ringkasan || 'Tidak ada isi berita.';
                if (rawIsi.trim().startsWith('<') && rawIsi.trim().endsWith('>')) {
                    isiBox.innerHTML = rawIsi;
                } else {
                    isiBox.textContent = rawIsi;
                }

                // External Link / Video Link
                const linkBox = document.getElementById('modal-berita-link-box');
                const linkBtn = document.getElementById('modal-berita-link-btn');
                if (data.link && data.link.trim() !== '') {
                    linkBtn.href = data.link;
                    linkBox.style.display = 'flex';
                } else {
                    linkBox.style.display = 'none';
                }

                // Show modal overlay
                const modal = document.getElementById('berita-modal-overlay');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeBeritaModal() {
                const modal = document.getElementById('berita-modal-overlay');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }

            document.addEventListener('DOMContentLoaded', function() {
                const overlay = document.getElementById('berita-modal-overlay');
                const container = document.getElementById('berita-modal-container');
                if (overlay && container) {
                    overlay.addEventListener('click', function(e) {
                        if (!container.contains(e.target)) {
                            closeBeritaModal();
                        }
                    });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeBeritaModal();
                }
            });
        </script>
        </div>
        @endif

        <!-- ================= YOUTUBE VIDEO ================= -->
        @if(isset($pengaturan) && !empty($pengaturan->youtube_link))
        @php
            $ytData = json_decode($pengaturan->youtube_link, true);
            if (!is_array($ytData)) {
                $oldLinks = explode("\n", str_replace("\r", "", $pengaturan->youtube_link));
                $oldLinks = array_filter(array_map('trim', $oldLinks));
                $ytData = [];
                foreach($oldLinks as $ol) {
                    $ytData[] = ['title' => '', 'url' => $ol];
                }
            }
        @endphp
        @if(!empty($ytData))
        <div style="margin-top: -1.5rem !important; margin-bottom: 2rem !important;" class="flex flex-col gap-6 w-full relative z-10">
            @foreach($ytData as $video)
                @php
                    $link = $video['url'] ?? '';
                    $title = $video['title'] ?? '';
                    if(empty($link)) continue;

                    $embedUrl = $link;
                    if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/|live\/))([a-zA-Z0-9_-]{11})/i', $link, $match)) {
                        $embedUrl = "https://www.youtube.com/embed/" . $match[1];
                    } elseif (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|shorts|live)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $link, $match)) {
                        $embedUrl = "https://www.youtube.com/embed/" . $match[1];
                    }
                @endphp
                <div class="max-w-4xl w-full mx-auto flex flex-col items-center group relative z-10 px-4 sm:px-6">
                    @if($title)
                    <h3 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 text-center tracking-tight" style="margin-top: 0 !important; margin-bottom: 1.25rem !important;">{{ $title }}</h3>
                    @endif
                    <div class="w-full rounded-2xl md:rounded-3xl overflow-hidden shadow-lg relative aspect-video bg-gray-900 border-4 border-white group-hover:scale-[1.005] transition-all duration-300">
                        <iframe src="{{ $embedUrl }}" class="absolute top-0 left-0 w-full h-full" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
        @endif

        <!-- BAGIAN: MENGAPA MEMILIH KAMI? (FLAT CENTERED FLEX ROW) -->
        <div class="max-w-6xl w-full mx-auto my-8 sm:my-12 px-4">
            
            <div class="text-center mb-8 sm:mb-10">
                <h2 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $pengaturan->fitur_judul ?? 'Mengapa memilih kami?' }}</h2>
                <p class="text-gray-500 mt-1.5 text-xs sm:text-sm max-w-lg mx-auto">{{ $pengaturan->fitur_deskripsi ?? 'Sistem reservasi cepat, aman, dan modern.' }}</p>
            </div>

            <div class="flex flex-wrap justify-center gap-6 sm:gap-8 text-center">

                @php
                    $defaultCards = [
                        ['ikon' => '⚡', 'judul' => 'Cepat', 'deskripsi' => 'Booking hanya beberapa langkah.'],
                        ['ikon' => '📅', 'judul' => 'Real-time', 'deskripsi' => 'Jadwal selalu update.'],
                        ['ikon' => '🔒', 'judul' => 'Aman', 'deskripsi' => 'Data terlindungi.'],
                        ['ikon' => '🏟️', 'judul' => 'Modern', 'deskripsi' => 'Multi olahraga.']
                    ];
                    $cards = is_array($pengaturan->fitur_cards) && !empty($pengaturan->fitur_cards) ? $pengaturan->fitur_cards : $defaultCards;
                @endphp

                @foreach($cards as $card)
                <div class="flex flex-col items-center p-2 flex-1 min-w-[140px] max-w-[220px]">
                    <div class="text-3xl sm:text-4xl mb-3 transform hover:scale-110 transition-transform duration-200">{{ $card['ikon'] ?? '' }}</div>
                    <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">{{ $card['judul'] ?? '' }}</h3>
                    <p class="text-xs text-gray-500 mt-1.5 leading-relaxed mx-auto">{{ $card['deskripsi'] ?? '' }}</p>
                </div>
                @endforeach

            </div>

        </div>

    </section>
</div>

<!-- ================= CONTACT & LOCATION (FULL WIDTH WHITE SECTION) ================= -->
<section class="w-full bg-white relative z-20 pt-10 sm:pt-14 md:pt-16 pb-12 sm:pb-16 md:pb-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div x-data="{ 
            cabangs: @js((isset($semuaCabang) && count($semuaCabang) > 0) ? $semuaCabang : [
                [
                    'nama_arena' => active_arena()->nama_arena ?? 'Fajar Arena',
                    'alamat' => active_arena()->alamat ?? '',
                    'kota' => active_arena()->kota ?? '',
                    'provinsi' => active_arena()->provinsi ?? '',
                    'kodepos' => active_arena()->kodepos ?? '',
                    'no_telp' => active_arena()->no_telp ?? '',
                    'email' => active_arena()->email ?? '',
                    'link_maps' => active_arena()->link_maps ?? '',
                ]
            ]),
            activeIndex: 0,
            open: false,
            
            get active() { 
                if (!this.cabangs || this.cabangs.length === 0) return {};
                return this.cabangs[this.activeIndex] || this.cabangs[0] || {}; 
            },
            setActive(idx) { this.activeIndex = idx; this.open = false; },

            get fullAddress() {
                if (!this.active) return 'Alamat belum diatur';
                let alamat = (this.active.alamat || '').trim();
                let kota = (this.active.kota || '').trim();
                let provinsi = (this.active.provinsi || '').trim();
                let kodepos = (this.active.kodepos || '').trim();

                let areaParts = [];
                if (kota) areaParts.push(kota);
                if (provinsi) areaParts.push(provinsi);
                if (kodepos) areaParts.push(kodepos);

                let areaString = areaParts.join(', ');
                if (alamat && areaString) {
                    return alamat + '<br>' + areaString;
                }
                return alamat || areaString || 'Alamat belum diatur';
            },
            
            get mapIframe() {
                let link = (this.active && this.active.link_maps) ? this.active.link_maps : '';
                if (link.trim().startsWith('<iframe')) return link;
                let q = (link.trim().startsWith('http') || !link) ? ((this.active.alamat || this.active.nama_arena || 'Fajar Arena') + ' ' + (this.active.kota || 'Makassar')) : link;
                return '<iframe src=\'https://maps.google.com/maps?q=' + encodeURIComponent(q) + '&t=&z=15&ie=UTF8&iwloc=&output=embed\' class=\'w-full h-full border-0\' allowfullscreen=\'\' loading=\'lazy\'></iframe>';
            },
            
            get mapUrl() {
                let link = (this.active && this.active.link_maps) ? this.active.link_maps : '';
                if (link.trim().startsWith('http')) return link;
                let loc = ((this.active.alamat || '') + ' ' + (this.active.kota || '') + ' ' + (this.active.nama_arena || 'Fajar Arena')).trim();
                return 'https://maps.google.com/?q=' + encodeURIComponent(link.trim() || loc);
            }
        }" class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-start">
            
            <!-- Kiri: Detail Kontak -->
            <div>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Hubungi Kami</h2>
                <p class="text-gray-500 mt-2 text-sm sm:text-base leading-relaxed">
                    Kami siap melayani Anda. Kunjungi arena kami atau hubungi kontak di bawah ini untuk informasi lebih lanjut mengenai ketersediaan jadwal.
                </p>

                <div class="mt-8 space-y-6 relative">
                    <!-- Judul & Dropdown -->
                    <div class="relative z-20">
                        <button @click="if(cabangs.length > 1) open = !open" @click.away="open = false" class="flex items-center gap-3 text-left focus:outline-none group">
                            <h4 class="font-bold text-gray-900 text-xl group-hover:text-blue-600 transition-colors" x-text="active.nama_arena || 'Fajar Arena'">{{ active_arena()->nama_arena ?? 'Fajar Arena' }}</h4>
                            <div x-show="cabangs.length > 1" x-cloak class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-100 transition-colors cursor-pointer">
                                <svg :class="{'rotate-180': open}" class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>

                        <div x-show="open" x-transition.opacity x-cloak class="absolute left-0 mt-3 w-80 bg-white rounded-2xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-gray-100 py-2 overflow-hidden">
                            <template x-for="(cabang, index) in cabangs" :key="index">
                                <button @click="setActive(index)" class="w-full text-left px-5 py-3 hover:bg-slate-50 transition-colors flex items-center gap-3" :class="{'bg-blue-50/50 text-blue-700 font-bold': activeIndex === index}">
                                    <div class="w-2 h-2 rounded-full bg-blue-500 opacity-0" :class="{'opacity-100': activeIndex === index}"></div>
                                    <span x-text="cabang.nama_arena"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <!-- Alamat -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0 text-blue-600 shadow-sm border border-blue-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-base">Alamat Arena</p>
                            <p class="text-gray-600 mt-1 leading-relaxed text-sm" x-html="fullAddress"></p>
                        </div>
                    </div>

                    <!-- Telepon -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-green-50 flex items-center justify-center shrink-0 text-green-600 shadow-sm border border-green-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-base">Nomor Telepon</p>
                            <p class="text-gray-600 mt-1 text-sm" x-text="active.no_telp || '{{ active_arena()->no_telp ?? '-' }}'">{{ active_arena()->no_telp ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center shrink-0 text-orange-600 shadow-sm border border-orange-100">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 text-base">Email</p>
                            <p class="text-gray-600 mt-1 text-sm" x-text="active.email || '{{ active_arena()->email ?? '-' }}'">{{ active_arena()->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kanan: Map Placeholder -->
            <div class="bg-gray-50 rounded-2xl h-[350px] lg:h-[420px] flex flex-col overflow-hidden relative shadow-inner border border-gray-200 group">
                <div class="w-full h-full [&>iframe]:w-full [&>iframe]:h-full [&>iframe]:border-0" x-html="mapIframe"></div>
                <div class="absolute bottom-5 left-1/2 -translate-x-1/2 z-10 opacity-90 hover:opacity-100 transition-opacity">
                    <a :href="mapUrl" href="https://maps.google.com/?q={{ urlencode((active_arena()->alamat ?? 'Fajar Arena') . ' ' . (active_arena()->kota ?? 'Makassar')) }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-full font-bold shadow-md hover:bg-blue-700 hover:-translate-y-0.5 transition-all text-xs">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Buka di Google Maps
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= FOOTER ================= -->
<footer style="background: linear-gradient(135deg, #0a1c38 0%, #153a6d 100%); border-top: 1px solid rgba(59, 130, 246, 0.3); padding-top: 1.75rem; padding-bottom: 1.75rem;" class="relative z-10">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-xl md:text-2xl font-black tracking-tight" style="color: #ffffff !important;">Fajar Arena</h3>
            <p class="mt-1 text-xs md:text-sm font-medium" style="color: #93c5fd !important;">Pusat olahraga terbaik dengan fasilitas premium</p>
        </div>
        <div class="text-xs md:text-sm font-medium" style="color: #93c5fd !important;">
            &copy; {{ date('Y') }} Fajar Arena. Hak cipta dilindungi.
        </div>
    </div>
</footer>

@if(isset($sliders) && $sliders->count() > 0)
<script>
document.addEventListener("DOMContentLoaded", function(){

const slides = document.querySelectorAll('.hero-slide');
const title  = document.getElementById('heroTitle');
const tagline = document.getElementById('heroTagline');
const desc   = document.getElementById('heroDesc');
const btnWrapper = document.getElementById('heroBtn');
const nextBtn = document.getElementById('nextBtn');
const prevBtn = document.getElementById('prevBtn');

const data = [
@if(isset($sliders) && $sliders->count() > 0)
    @foreach($sliders as $slider)
    {
    title: `{!! $slider->judul ? addslashes(e($slider->judul)) : 'Fajar Arena' !!}`,
    tagline: `{!! $slider->tagline ? addslashes($slider->tagline) : '' !!}`,
    desc: `{!! $slider->deskripsi ? addslashes(e($slider->deskripsi)) : '' !!}`
    },
    @endforeach
@else
    {
    title: `{!! 'Fajar Arena' !!}`,
    tagline: 'Booking Lapangan <br>Lebih Mudah',
    desc: 'Pesan lapangan favorit Anda kapan saja dengan cepat dan sistem real-time.'
    }
@endif
];

let index = 0;
let autoSlide;
let isAnimating = false;

/* reset class */
function clearClasses(slide){
slide.className = 'hero-slide';
slide.style.transform = '';
slide.style.transition = '';
}

function resetSlide(slide){
clearClasses(slide);
slide.classList.add('hero-slide','hidden-reset');
}

/* text animasi */
function textAnim(i){

    title.classList.remove('show');
    tagline.classList.remove('show');
    desc.classList.remove('show');
    if(btnWrapper) btnWrapper.classList.remove('show');

    // Tunggu 400ms (sesuai durasi CSS saat menghilang) sebelum mengganti teks
    setTimeout(()=>{
        title.innerHTML = data[i].title;
        tagline.innerHTML = data[i].tagline;
        desc.innerHTML = data[i].desc;

        title.classList.add('show');
        tagline.classList.add('show');
        desc.classList.add('show');
        if(btnWrapper) btnWrapper.classList.add('show');
    }, 450);

}

/* next */
function goNext(){

if(slides.length <= 1) return;
if(isAnimating) return;
isAnimating = true;

let current = slides[index];
let nextIndex = (index + 1) % slides.length;
let next = slides[nextIndex];

/* reset total */
clearClasses(next);

/* paksa posisi kanan */
next.style.transition = 'none';
next.style.transform = 'translateX(100%)';

next.offsetHeight;

next.style.transition = '';

requestAnimationFrame(()=>{

    clearClasses(current);
    current.classList.add('hero-slide','out-left');

    clearClasses(next);
    next.classList.add('hero-slide','active');

});

index = nextIndex;
textAnim(index);

setTimeout(()=>{
    clearClasses(current);
    current.classList.add('hero-slide','ready-right');
    isAnimating = false;
},1100);

}

/* prev */
function goPrev(){

if(slides.length <= 1) return;
if(isAnimating) return;
isAnimating = true;

let current = slides[index];
let prevIndex = (index - 1 + slides.length) % slides.length;
let prev = slides[prevIndex];

/* bersihkan total */
clearClasses(prev);

/* paksa posisi awal di kiri (WAJIB) */
prev.style.transition = 'none';
prev.style.transform = 'translateX(-100%)';

/* paksa browser render posisi kiri */
prev.offsetHeight;

/* hidupkan kembali animasi */
prev.style.transition = '';

requestAnimationFrame(()=>{

    /* current keluar ke kanan */
    clearClasses(current);
    current.classList.add('hero-slide','out-right');

    /* prev masuk dari kiri */
    clearClasses(prev);
    prev.classList.add('hero-slide','active');

});

index = prevIndex;
textAnim(index);

setTimeout(()=>{
    clearClasses(current);
    current.classList.add('hero-slide','ready-left');
    isAnimating = false;
},1100);

}

function startAuto(){
autoSlide = setInterval(goNext, 4000);
}

function resetAuto(){
clearInterval(autoSlide);
startAuto();
}

nextBtn.addEventListener('click', ()=>{
goNext();
resetAuto();
});

prevBtn.addEventListener('click', ()=>{
goPrev();
resetAuto();
});

title.classList.add('show');
tagline.classList.add('show');
desc.classList.add('show');
if(btnWrapper) btnWrapper.classList.add('show');

if(slides.length > 1) {
    startAuto();
} else {
    // Hide buttons if only 1 slide
    const prevBtnEl = document.getElementById('prevBtn');
    const nextBtnEl = document.getElementById('nextBtn');
    if(prevBtnEl) prevBtnEl.parentElement.style.display = 'none';
    if(nextBtnEl) nextBtnEl.parentElement.style.display = 'none';
}

});
</script>
@endif

</x-app-layout>