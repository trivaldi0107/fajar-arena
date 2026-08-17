<x-app-layout>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Pembayaran Reservasi</h1>
        <p class="text-gray-500 mt-1">Silakan lakukan transfer & unggah bukti pembayaran Anda untuk verifikasi.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-medium text-sm sm:text-base">{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl">
        <ul class="list-disc list-inside text-sm font-medium space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

        <!-- KIRI: Ringkasan Pemesanan -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Ringkasan Pemesanan
                </h3>

                <div class="space-y-4 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Kode Reservasi</span>
                        <span class="font-bold text-gray-900 bg-gray-100 px-3 py-1 rounded-lg">{{ $pemesanan->kode_reservasi }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Kategori Olahraga</span>
                        <span class="font-bold text-gray-900">{{ ucfirst(active_arena()->jenis_olahraga ?? 'Badminton') }}</span>
                    </div>

                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Tipe Pelanggan</span>
                        <span class="font-semibold text-indigo-600 bg-indigo-50 border border-indigo-100 px-3 py-1 rounded-full text-xs">
                            {{ ucfirst(str_replace('_', ' ', $pemesanan->jenis_user)) }}
                        </span>
                    </div>

                    @if($pemesanan->jenis_user == 'member')
                    <div class="py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium block mb-1">Periode Paket</span>
                        <span class="font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($pemesanan->detail->min('tanggal'))->translatedFormat('d M Y') }} 
                            — 
                            {{ \Carbon\Carbon::parse($pemesanan->detail->max('tanggal'))->translatedFormat('d M Y') }}
                        </span>
                    </div>
                    @else
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Tanggal Tanggal</span>
                        <span class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->translatedFormat('d M Y') }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500 font-medium">Total Durasi</span>
                        <span class="font-bold text-gray-900">{{ $pemesanan->durasi }} Jam</span>
                    </div>
                    @endif

                    <!-- Detail Lapangan & Jam (Grouped Per Tanggal) -->
                    <div class="py-3 border-b border-gray-100 space-y-3">
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

                    <!-- Total Pembayaran -->
                    <div class="pt-4 flex justify-between items-center">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Tagihan</span>
                            <span class="text-2xl font-black text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timer Card -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-3xl p-5 text-center">
                <p class="text-xs font-bold text-blue-500 uppercase tracking-widest mb-1">Batas Waktu Pembayaran</p>
                <div id="timer" class="text-2xl font-black text-blue-700 tracking-tight">
                    Calculating...
                </div>
            </div>
        </div>

        <!-- KANAN: Metode Pembayaran QRIS & Form Upload -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 md:p-8">

                <!-- Status Badge -->
                @if($pemesanan->status === 'proses')
                <div class="mb-6">
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-2xl flex items-start gap-3">
                        <svg class="w-6 h-6 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div>
                            <h5 class="font-bold text-sm">Menunggu Verifikasi Admin</h5>
                            <p class="text-xs mt-0.5 text-amber-700">Bukti pembayaran Anda sudah diterima dan sedang diperiksa oleh pengelola Fajar Arena.</p>
                        </div>
                    </div>
                </div>
                @elseif($pemesanan->status === 'batal' || $pemesanan->status === 'dibatalkan')
                <div class="mb-6">
                    <div class="bg-rose-50 border-2 border-rose-200 text-rose-900 p-4.5 rounded-2xl flex items-start gap-3.5 shadow-sm">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div class="space-y-0.5">
                            <h5 class="font-extrabold text-sm text-rose-900">Pemesanan Dibatalkan oleh Pengelola</h5>
                            <p class="text-xs text-rose-700 leading-relaxed font-semibold">
                                {{ $pemesanan->alasan_penolakan ?: 'Maaf, pesanan Anda telah ditolak. Slot jadwal yang Anda pilih telah dikembalikan.' }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Card QRIS Statis (Exact same width & box style as Upload Box) -->
                <div class="w-full bg-gray-50/50 border-2 border-gray-200 rounded-2xl p-6 text-center mb-6">
                    @if($qrisImage)
                        <div class="inline-block rounded-[28px] overflow-hidden shadow-sm border border-gray-300 bg-white">
                            <img src="{{ asset($qrisImage) }}" alt="QRIS Statis" class="w-full max-w-[280px] sm:max-w-[320px] h-auto mx-auto rounded-[28px]">
                        </div>
                    @else
                        <div class="inline-block rounded-[28px] overflow-hidden shadow-sm border border-gray-300 bg-white p-2">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=Fajar+Arena+Badminton+{{ $pemesanan->kode_reservasi }}" alt="QRIS Fajar Arena" class="w-full max-w-[260px] sm:max-w-[280px] h-auto mx-auto rounded-[24px]">
                        </div>
                    @endif
                </div>

                <!-- Form Upload Bukti Transfer -->
                <form action="{{ route('pembayaran.upload', $pemesanan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Unggah Bukti Pembayaran / Struk Transfer
                        </label>
                        
                        @if($pemesanan->bukti_transfer)
                        <div class="mb-3 p-4 bg-gray-50 rounded-2xl border border-gray-200 flex items-center gap-4">
                            <img src="{{ asset($pemesanan->bukti_transfer) }}" alt="Bukti Transfer" class="w-20 h-20 object-cover rounded-xl border border-gray-300">
                            <div>
                                <p class="text-xs font-bold text-emerald-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Bukti Berhasil Diunggah
                                </p>
                                <p class="text-xs text-gray-500 mt-1">Anda dapat mengganti foto jika ada kesalahan sebelum dikonfirmasi oleh Admin.</p>
                            </div>
                        </div>
                        @endif

                        <div class="relative border-2 border-dashed border-gray-300 hover:border-blue-500 transition-colors rounded-2xl p-6 text-center cursor-pointer bg-gray-50/50 hover:bg-blue-50/30" id="dropzone">
                            <input type="file" name="bukti_transfer" id="buktiInput" accept="image/png, image/jpeg, image/jpg, image/webp" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewImage(event)">
                            
                            <div id="uploadPlaceholder" class="space-y-2">
                                <svg class="w-10 h-10 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-semibold text-gray-700">Klik atau seret file foto resi transfer ke sini</p>
                                <p class="text-xs text-gray-400">Format: JPG, PNG, WEBP (Maksimal 5MB)</p>
                            </div>

                            <div id="imagePreviewContainer" class="hidden text-center">
                                <img id="imagePreview" class="max-h-48 mx-auto rounded-xl shadow-md border border-gray-200 mb-2">
                                <p id="fileName" class="text-xs font-semibold text-gray-600"></p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-3.5 px-6 rounded-2xl shadow-lg shadow-blue-500/25 transition-all duration-300 flex items-center justify-center gap-2 text-base cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>{{ $pemesanan->bukti_transfer ? 'Unggah Ulang Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}</span>
                    </button>
                </form>

                <!-- Tombol Batal -->
                <div class="pt-4 border-t border-gray-100">
                    <form id="formBatal" action="{{ route('pembayaran.batal', $pemesanan->id) }}" method="POST">
                        @csrf
                        <button type="button" onclick="bukaModal()" class="w-full text-rose-600 hover:text-rose-700 font-semibold py-2 text-sm text-center border border-rose-100 hover:border-rose-200 bg-rose-50/50 hover:bg-rose-50 rounded-xl transition-colors cursor-pointer">
                            Batalkan Pemesanan Ini
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- Modal Konfirmasi Batal -->
<div id="modalBatal" class="fixed inset-0 hidden items-center justify-center bg-black/50 backdrop-blur-sm z-50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 sm:p-8 text-center animate-modal">
        <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-500 mx-auto flex items-center justify-center mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Batalkan Pemesanan?</h3>
        <p class="text-gray-500 text-sm mb-6">Slot jadwal yang sudah Anda pilih akan kembali dibuka untuk pelanggan lain.</p>
        
        <div class="flex gap-3">
            <button type="button" onclick="tutupModal()" class="w-1/2 py-3 px-4 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">
                Kembali
            </button>
            <button type="button" onclick="lanjutBatal()" class="w-1/2 py-3 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold transition-colors">
                Ya, Batalkan
            </button>
        </div>
    </div>
</div>

<script>
function copyText(id) {
    const text = document.getElementById(id).innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Nomor rekening berhasil disalin!');
    });
}

function compressImage(file, maxDimension, quality, callback) {
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            let width = img.width;
            let height = img.height;

            if (width > maxDimension || height > maxDimension) {
                if (width > height) {
                    height = Math.round((height * maxDimension) / width);
                    width = maxDimension;
                } else {
                    width = Math.round((width * maxDimension) / height);
                    height = maxDimension;
                }
            }

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;

            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            canvas.toBlob(function(blob) {
                callback(blob || file);
            }, 'image/jpeg', quality);
        };
        img.onerror = function() {
            callback(file);
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

function previewImage(event) {
    const file = event.target.files[0];
    if (!file) return;

    // VALIDASI MAKSIMAL 5 MB
    const MAX_SIZE_MB = 5;
    const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

    if (file.size > MAX_SIZE_BYTES) {
        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
        event.target.value = ''; // Reset input file
        
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('imagePreviewContainer').classList.add('hidden');

        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: `Ukuran file Anda ${fileSizeMB} MB. Maksimal ukuran file yang diperbolehkan adalah ${MAX_SIZE_MB} MB. Silakan pilih foto lain atau screenshot resi tersebut.`,
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert(`Ukuran file Anda ${fileSizeMB} MB. Maksimal ukuran file yang diperbolehkan adalah ${MAX_SIZE_MB} MB.`);
        }
        return;
    }

    document.getElementById('uploadPlaceholder').classList.add('hidden');
    document.getElementById('imagePreviewContainer').classList.remove('hidden');
    document.getElementById('fileName').innerText = 'Mengoptimasi gambar: ' + file.name + '...';

    // Kompresi ringan agar upload cepat & hemat kuota
    compressImage(file, 1600, 0.85, function(compressedBlob) {
        try {
            const compressedFile = new File([compressedBlob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                type: "image/jpeg",
                lastModified: Date.now()
            });

            if (window.DataTransfer) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(compressedFile);
                document.getElementById('buktiInput').files = dataTransfer.files;
            }
        } catch(e) {}

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
            const sizeKB = Math.round(compressedBlob.size / 1024);
            const sizeLabel = sizeKB > 1024 ? (sizeKB/1024).toFixed(1) + ' MB' : sizeKB + ' KB';
            document.getElementById('fileName').innerText = file.name + ' (' + sizeLabel + ')';
        };
        reader.readAsDataURL(compressedBlob);
    });
}

// Countdown timer (10 menit)
let createdAtMs = {{ strtotime($pemesanan->created_at) * 1000 }};
let expiredAtMs = createdAtMs + (600 * 1000); // 10 menit

function checkExpired() {
    let nowMs = Date.now();
    let time = Math.floor((expiredAtMs - nowMs) / 1000);

    if (time <= 0) {
        document.getElementById("timer").innerText = "Waktu Habis!";
        document.getElementById('formBatal').submit();
        return true;
    }
    return false;
}

function updateTimerText() {
    let nowMs = Date.now();
    let time = Math.floor((expiredAtMs - nowMs) / 1000);
    
    if (time > 0) {
        let m = Math.floor(time / 60);
        let s = time % 60;
        document.getElementById("timer").innerText = m + ":" + (s < 10 ? "0" : "") + s;
    }
}

if (!checkExpired()) {
    updateTimerText();
    let timerInterval = setInterval(() => {
        if (checkExpired()) {
            clearInterval(timerInterval);
            return;
        }
        updateTimerText();
    }, 1000);
}

function bukaModal() {
    const modal = document.getElementById('modalBatal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function tutupModal() {
    const modal = document.getElementById('modalBatal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

function lanjutBatal() {
    document.getElementById('formBatal').submit();
}
</script>

</x-app-layout>