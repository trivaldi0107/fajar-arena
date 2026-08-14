<!-- // TAMPILAN UTAMA E-TIKET RESERVASI -->
<x-app-layout>
    <div class="py-12 bg-gray-50 min-h-screen">
        <!-- // AREA TANGKAPAN FOTO E-TIKET -->
        <div id="capture-area" class="w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 bg-gray-50 pb-8 pt-4">
            
            <!-- // KARTU TIKET RESERVASI -->
            <div id="ticket-card" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-200 relative mx-auto">
                
                <!-- // HEADER TIKET -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 md:p-10 text-center text-white relative rounded-t-3xl">
                    <h2 class="text-3xl font-extrabold uppercase tracking-widest drop-shadow-md">Fajar Arena</h2>
                    <p class="text-blue-100 mt-2 font-medium tracking-wide">E-Tiket Reservasi Lapangan</p>
                </div>

                <!-- // DEKORASI GARIS POTONG TIKET -->
                <div class="relative flex items-center justify-between py-2 bg-white px-0">
                    <div class="w-5 h-5 bg-gray-100 rounded-r-full border-t border-b border-r border-gray-300 -ml-0.5"></div>
                    <div class="h-0 border-b-2 border-dashed border-gray-300 w-full mx-2"></div>
                    <div class="w-5 h-5 bg-gray-100 rounded-l-full border-t border-b border-l border-gray-300 -mr-0.5"></div>
                </div>

                <!-- // DETAIL INFORMASI TIKET -->
                <div class="p-8 md:p-12 bg-white rounded-b-3xl">
                    
                    <!-- // INFORMASI KODE RESERVASI & STATUS PEMBAYARAN -->
                    <div class="flex flex-col sm:flex-row justify-between items-center bg-blue-50/50 rounded-2xl p-6 border border-blue-100 mb-8 gap-4 shadow-sm text-center sm:text-left">
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Kode Reservasi</p>
                            <p class="text-2xl md:text-3xl font-black text-blue-900 tracking-wider">{{ $pemesanan->kode_reservasi }}</p>
                        </div>
                        <div class="sm:text-right">
                            <p class="text-xs md:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Status</p>
                            <span class="inline-flex items-center justify-center px-5 py-2 rounded-full text-xs md:text-sm font-bold capitalize border shadow-sm
                                {{ $pemesanan->status == 'batal' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-green-100 text-green-700 border-green-200' }}">
                                {{ $pemesanan->status }}
                            </span>
                        </div>
                    </div>

                    <!-- // INFORMASI PEMESAN & TIPE AKUN -->
                    <div class="mb-8 flex justify-between items-center bg-gray-50 p-5 rounded-2xl border border-gray-100 shadow-inner">
                        <div>
                            <p class="text-xs md:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Nama Pemesan</p>
                            <p class="font-bold text-gray-800 text-base md:text-lg flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ $pemesanan->user ? $pemesanan->user->name : 'Tamu (Guest)' }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs md:text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Tipe Akun</p>
                            <span class="inline-flex items-center justify-center px-4 py-1.5 rounded-lg text-xs md:text-sm font-bold shadow-sm border whitespace-nowrap {{ $pemesanan->jenis_user == 'member' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : 'bg-gray-200 text-gray-700 border-gray-300' }}">
                                {{ $pemesanan->jenis_user == 'member' ? 'Member' : 'Non-Member' }}
                            </span>
                        </div>
                    </div>

                    <!-- // DAFTAR JADWAL BERMAIN -->
                    <h3 class="text-base md:text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-2">Detail Jadwal Bermain</h3>
                    <div class="space-y-4 mb-10">
                        <!-- // KODE PHP: Pengelompokan detail jadwal berdasarkan tanggal & lapangan -->
                        @php
                            $groupedDetails = collect($pemesanan->detail)->groupBy(function($d) {
                                return $d->tanggal . '|' . $d->lapangan_id;
                            })->map(function($group) {
                                $sorted = $group->sortBy('jam_mulai');
                                return (object)[
                                    'tanggal' => $sorted->first()->tanggal,
                                    'lapangan_id' => $sorted->first()->lapangan_id,
                                    'lapangan' => $sorted->first()->lapangan,
                                    'jam_mulai' => $sorted->first()->jam_mulai,
                                    'jam_selesai' => $sorted->last()->jam_selesai,
                                ];
                            })->sortBy('tanggal')->values();
                        @endphp
                        @foreach($groupedDetails as $d)
                        <div class="flex items-center justify-between p-3 md:p-4 bg-white rounded-xl border border-gray-200 hover:border-blue-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-center gap-3 md:gap-4">
                                <div class="bg-gradient-to-br from-blue-100 to-indigo-100 p-2 md:p-3 rounded-lg text-blue-600 shadow-sm shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 text-sm md:text-lg">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y') }}</p>
                                    <p class="text-xs md:text-sm text-gray-500 font-medium mt-0.5 flex items-center gap-1 whitespace-nowrap">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <polyline points="12 6 12 12 16 14"></polyline>
                                        </svg>
                                        <span>{{ substr($d->jam_mulai,0,5) }} - {{ substr($d->jam_selesai,0,5) }} WITA</span>
                                    </p>
                                </div>
                            </div>
                            <div class="px-3 py-1.5 md:px-5 md:py-2 bg-gradient-to-r from-indigo-50 to-blue-50 text-indigo-700 rounded-lg font-bold text-xs md:text-sm border border-indigo-100 shadow-sm whitespace-nowrap ml-2">
                                {{ $d->lapangan->nama_lapangan }}
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- // BLOK TAMPILAN QR CODE TIKET -->
                    <div class="flex flex-col items-center justify-center p-6 md:p-8 bg-gray-50 rounded-2xl border border-gray-200 shadow-inner text-center">
                        <p class="text-xs md:text-sm font-semibold text-gray-500 mb-4 tracking-wide uppercase">Tunjukkan QR Code ini kepada petugas</p>
                        <div class="bg-white p-3 md:p-4 rounded-2xl shadow-sm border border-gray-200 transition-transform duration-300 hover:scale-105">
                            <img id="qr-img" alt="QR Code" class="w-40 h-40 md:w-48 md:h-48 mx-auto" />
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <!-- // TOMBOL AKSI SIMPAN GAMBAR TIKET -->
        <div class="w-full max-w-3xl px-4 sm:px-6 lg:px-8 mx-auto mt-4 mb-8 flex justify-center">
            <button id="downloadBtn" class="bg-white text-gray-700 hover:text-blue-600 font-bold py-3 px-10 border border-gray-300 rounded-full shadow-md hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Unduh E-Tiket
            </button>
        </div>
    </div>
    
    <!-- // SCRIPT JAVASCRIPT: Library html-to-image & Proses Unduh Gambar -->
    <script src="https://cdn.jsdelivr.net/npm/html-to-image@1.11.11/dist/html-to-image.min.js"></script>
    <script>
        // // JS: Fetch QR Code & konversi ke Base64
        document.addEventListener("DOMContentLoaded", function() {
            const qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $pemesanan->kode_reservasi }}";
            fetch(qrUrl)
                .then(response => response.blob())
                .then(blob => {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        document.getElementById('qr-img').src = reader.result;
                    }
                    reader.readAsDataURL(blob);
                });
        });

        // // JS: Event handler unduh tiket sebagai gambar PNG
        document.getElementById('downloadBtn').addEventListener('click', function() {
            const ticketCard = document.getElementById('ticket-card');
            const btn = this;
            const originalText = btn.innerHTML;
            
            btn.innerHTML = 'Menyiapkan Tiket...';
            btn.disabled = true;

            htmlToImage.toPng(ticketCard, {
                pixelRatio: 3,
                backgroundColor: null,
                style: {
                    margin: '0',
                    padding: '0',
                    boxShadow: 'none'
                }
            })
            .then(function (dataUrl) {
                const link = document.createElement('a');
                link.download = 'Tiket-Fajar-Arena-{{ $pemesanan->kode_reservasi }}.png';
                link.href = dataUrl;
                link.click();
                
                btn.innerHTML = originalText;
                btn.disabled = false;
            })
            .catch(function (error) {
                console.error('Terjadi kesalahan saat memfoto:', error);
                alert('Oops, gagal menyimpan tiket. Pastikan koneksi lancar.');
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    </script>
</x-app-layout>