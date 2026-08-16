<x-app-layout>

<style>
    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.85; transform: scale(1.03); }
    }
    .animate-pulse-soft {
        animation: pulse-soft 2s ease-in-out infinite;
    }
    @keyframes rotate-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .spin-slow {
        animation: rotate-slow 12s linear infinite;
    }
</style>

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $namaArenaClean = trim(str_ireplace('Badminton', '', active_arena()->nama_arena ?? 'Fajar Arena'));
            if (empty($namaArenaClean)) { $namaArenaClean = 'Fajar Arena'; }
        @endphp

        <!-- Status Banner Menunggu Verifikasi -->
        <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-orange-600 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-amber-500/20 mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 relative z-10 text-center sm:text-left">
                <!-- Icon Animasi Loader -->
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shrink-0 border border-white/30 shadow-inner animate-pulse-soft">
                    <svg class="w-10 h-10 text-white spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <div class="space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-1">
                        <span class="w-2 h-2 rounded-full bg-amber-200 animate-ping"></span>
                        Status: Menunggu Persetujuan Admin
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Bukti Transfer Berhasil Dikirim</h2>
                    <p class="text-amber-100 text-xs sm:text-sm max-w-xl leading-relaxed">
                        Foto struk pembayaran Anda sudah masuk ke sistem. Pengelola <strong>{{ $namaArenaClean }}</strong> sedang memverifikasi transaksi Anda. 
                        Halaman ini akan <strong>otomatis berpindah ke Tiket Anda</strong> begitu disetujui.
                    </p>
                </div>
            </div>
        </div>

        <!-- Grid Rincian Pesanan & Bukti Transfer -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start mb-8">

            <!-- Card 1: Rincian Pemesanan -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-5">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Ringkasan Pemesanan
                </h3>

                <div class="space-y-3.5 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Kode Reservasi</span>
                        <span class="font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-lg font-mono">{{ $pemesanan->kode_reservasi }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Kategori Olahraga</span>
                        <span class="font-bold text-gray-900">{{ ucfirst(active_arena()->jenis_olahraga ?? 'Arena Olahraga') }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Status Keanggotaan</span>
                        <span class="font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full text-xs">
                            {{ ucfirst(str_replace('_', ' ', $pemesanan->jenis_user)) }}
                        </span>
                    </div>

                    @if($pemesanan->jenis_user == 'member')
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Periode Bermain</span>
                        <span class="font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($pemesanan->detail->min('tanggal'))->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($pemesanan->detail->max('tanggal'))->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    @else
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Tanggal Main</span>
                        <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->translatedFormat('d F Y') }}</span>
                    </div>
                    @endif

                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Total Durasi</span>
                        <span class="font-bold text-gray-900">{{ $pemesanan->durasi }} Jam</span>
                    </div>

                    <!-- Detail Lapangan & Jam (Grouped Per Tanggal) -->
                    <div class="pt-3 space-y-3">
                        <span class="text-gray-700 font-bold block text-sm">Rincian Jadwal Bermain (Per Tanggal):</span>
                        
                        @php
                            $groupedByDate = $pemesanan->detail->groupBy(function($item) {
                                return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
                            });
                        @endphp

                        @foreach($groupedByDate as $tanggalStr => $items)
                            @php
                                $dateCarbon = \Carbon\Carbon::parse($tanggalStr);
                                $firstItem = $items->first();
                                $mingguKe = $firstItem->minggu_ke ?? $loop->iteration;
                                $itemsByLapangan = $items->groupBy('lapangan_id');
                            @endphp
                            <div class="bg-gray-50/90 rounded-2xl p-4 border border-gray-200/80 space-y-2.5">
                                <div class="flex items-center justify-between border-b border-gray-200/60 pb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0 font-bold">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <span class="font-bold text-gray-900 text-xs sm:text-sm">
                                            {{ $dateCarbon->translatedFormat('l, d F Y') }}
                                        </span>
                                    </div>
                                    @if($pemesanan->jenis_user == 'member')
                                    <span class="px-2.5 py-0.5 rounded-full bg-blue-600 text-white text-[10px] font-extrabold uppercase tracking-wide shadow-xs">
                                        Pekan {{ $mingguKe }}
                                    </span>
                                    @endif
                                </div>

                                <div class="space-y-1.5 pt-0.5">
                                    @foreach($itemsByLapangan as $lapId => $lapItems)
                                        @php
                                            $lapNama = $lapItems->first()->lapangan->nama_lapangan ?? 'Lapangan';
                                            $minTime = \Carbon\Carbon::parse($lapItems->min('jam_mulai'))->format('H:i');
                                            $maxTime = \Carbon\Carbon::parse($lapItems->max('jam_selesai'))->format('H:i');
                                            $durasiJam = $lapItems->count();
                                        @endphp
                                        <div class="flex justify-between items-center bg-white px-3.5 py-2 rounded-xl border border-gray-200/70 shadow-2xs">
                                            <span class="font-bold text-gray-800 text-xs flex items-center gap-1.5">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                                                {{ $lapNama }}
                                            </span>
                                            <span class="font-bold text-blue-600 text-xs font-mono">
                                                {{ $minTime }} - {{ $maxTime }} <span class="text-[10px] font-medium text-gray-400 font-sans">({{ $durasiJam }} Jam)</span>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Tagihan</span>
                        <span class="text-xl font-black text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Foto Bukti Transfer -->
            <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-5 h-fit self-start">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Bukti Pembayaran Terunggah
                </h3>

                <div class="text-center">
                    @if($pemesanan->bukti_transfer)
                        <a href="{{ asset($pemesanan->bukti_transfer) }}" target="_blank" title="Klik untuk memperbesar">
                            <img src="{{ asset($pemesanan->bukti_transfer) }}" alt="Bukti Transfer" class="max-h-64 mx-auto rounded-2xl shadow-sm border border-gray-200 hover:opacity-90 transition-opacity">
                        </a>
                        <p class="text-xs text-gray-400 font-medium mt-3">Foto resi yang Anda kirimkan ke Admin.</p>
                    @else
                        <div class="py-12 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="text-sm font-semibold text-gray-600">Belum ada foto bukti transfer</p>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-center">
                    <a href="{{ route('reservasi') }}" class="text-xs text-gray-500 hover:text-blue-600 font-semibold transition flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Kembali ke Jadwal Reservasi
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Modern Rejection Modal -->
<div id="modalRejected" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all border border-gray-100">
        <div class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-rose-100 text-rose-600">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h4 class="font-extrabold text-gray-900 text-lg mb-2">Pemesanan Dibatalkan</h4>
        <div class="text-xs text-gray-500 mb-6 leading-relaxed space-y-2">
            <p>Maaf, pesanan Anda telah ditolak atau dibatalkan oleh pengelola. Slot jadwal yang Anda pilih telah dikembalikan.</p>
            <p id="textAlasanPenolakan" class="text-rose-600 font-bold text-xs">{{ $pemesanan->alasan_penolakan ?: '' }}</p>
        </div>
        
        <button type="button" onclick="redirectToReservasi()" class="w-full py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-rose-600/20 cursor-pointer">
            Kembali ke Jadwal Reservasi
        </button>
    </div>
</div>

<!-- Realtime Polling Script -->
<script>
    const pemesananId = "{{ $pemesanan->id }}";
    const checkStatusUrl = "{{ route('pembayaran.status', $pemesanan->id) }}";
    const ticketUrl = "{{ route('tiket', $pemesanan->id) }}";
    let isModalShown = false;

    function checkPaymentStatus() {
        if (isModalShown) return;

        fetch(checkStatusUrl + '?_t=' + new Date().getTime())
            .then(res => res.json())
            .then(data => {
                if (data.status === 'berhasil') {
                    // Redirect langsung ke halaman tiket!
                    window.location.href = ticketUrl;
                } else if (data.status === 'batal' || data.status === 'dibatalkan') {
                    showRejectionModal(data.alasan_penolakan);
                }
            })
            .catch(err => console.log('Polling status...', err));
    }

    function showRejectionModal(alasan) {
        isModalShown = true;
        if (alasan) {
            const txt = document.getElementById('textAlasanPenolakan');
            if (txt) txt.innerText = alasan;
        }
        const modal = document.getElementById('modalRejected');
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function redirectToReservasi() {
        window.location.href = "{{ route('reservasi') }}";
    }

    @if($pemesanan->status === 'batal' || $pemesanan->status === 'dibatalkan')
    document.addEventListener('DOMContentLoaded', () => {
        showRejectionModal("{{ addslashes($pemesanan->alasan_penolakan ?? 'Bukti transfer tidak valid atau pembayaran tidak sesuai.') }}");
    });
    @endif

    // Polling setiap 3 detik
    setInterval(checkPaymentStatus, 3000);
</script>

</x-app-layout>
