@extends('admin.layouts.app')

@section('title', 'Detail Pemesanan #' . $pemesanan->kode_reservasi)

@section('content')

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Header Page & Back Button -->
    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.pemesanan') }}" class="p-2.5 bg-white border border-gray-200 rounded-2xl text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Detail Pemesanan #{{ $pemesanan->kode_reservasi }}</h2>
                <p class="text-gray-500 text-xs sm:text-sm mt-0.5">Dibuat pada {{ \Carbon\Carbon::parse($pemesanan->created_at)->translatedFormat('d F Y, H:i') }} WIB</p>
            </div>
        </div>

        <div>
            @if($pemesanan->status === 'berhasil')
                <span class="bg-emerald-100 text-emerald-800 border border-emerald-200 px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider">
                    ✓ Lunas
                </span>
            @elseif($pemesanan->status === 'proses')
                <span class="bg-amber-100 text-amber-800 border border-amber-200 px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider">
                    ⏳ Menunggu Verifikasi
                </span>
            @elseif($pemesanan->status === 'batal' || $pemesanan->status === 'dibatalkan')
                <span class="bg-rose-100 text-rose-800 border border-rose-200 px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider">
                    ✕ Dibatalkan
                </span>
            @else
                <span class="bg-gray-100 text-gray-700 border border-gray-200 px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider">
                    Pending
                </span>
            @endif
        </div>
    </div>

    @php
        if ($pemesanan->jenis_user == 'member') {
            $total = active_arena()->member_harga ?? 1000000;
        } else {
            $hargaPerJam = active_arena()->harga_per_jam ?? 80000;
            $total = $pemesanan->durasi * $hargaPerJam;
        }
    @endphp

    <!-- Action Bar hanya tampil jika status 'proses' (bukti transfer telah diunggah) -->
    @if($pemesanan->status === 'proses')
    <div class="mb-8 p-6 bg-amber-50/80 rounded-3xl border border-amber-200 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-sm">
        <div>
            <h4 class="font-extrabold text-amber-900 text-base sm:text-lg">Verifikasi Pembayaran Pelanggan</h4>
            <p class="text-xs text-amber-700 mt-0.5">Periksa foto bukti transfer di bawah sebelum melakukan konfirmasi persetujuan.</p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <button type="button" onclick="openConfirmModal('setujui', '{{ route('admin.pemesanan.konfirmasi', $pemesanan->id) }}', '{{ $pemesanan->kode_reservasi }}')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Setujui
            </button>

            <button type="button" onclick="openConfirmModal('tolak', '{{ route('admin.pemesanan.tolak', $pemesanan->id) }}', '{{ $pemesanan->kode_reservasi }}')" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs sm:text-sm shadow-md shadow-rose-600/20 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                Tolak
            </button>
        </div>
    </div>
    @elseif($pemesanan->status === 'pending')
    <div class="mb-8 p-5 bg-blue-50/80 rounded-3xl border border-blue-200 flex items-center gap-3 text-blue-900 shadow-sm">
        <svg class="w-6 h-6 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-xs sm:text-sm font-semibold">Pelanggan masih berada di halaman pembayaran (belum mengunggah bukti transfer). Tombol konfirmasi persetujuan akan muncul otomatis begitu pelanggan mengunggah bukti transfer.</span>
    </div>
    @endif

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        
        <!-- Kartu 1: Informasi Pemesan & Tagihan -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Informasi Pemesan
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Nama Pemesan</span>
                    <span class="text-base font-bold text-gray-900">{{ $pemesanan->user->name ?? 'Non Member' }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Email / Kontak</span>
                    <span class="text-base font-bold text-gray-900">{{ $pemesanan->user->email ?? '-' }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Kategori Olahraga</span>
                    <span class="text-base font-bold text-gray-900">{{ ucfirst(active_arena()->jenis_olahraga ?? 'Badminton') }}</span>
                </div>

                <div>
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider block mb-1">Status Keanggotaan</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $pemesanan->jenis_user === 'member' ? 'bg-indigo-100 text-indigo-700 border border-indigo-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                        {{ str_replace('_',' ',$pemesanan->jenis_user) }}
                    </span>
                </div>

                <div class="sm:col-span-2 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-xs text-gray-400 font-bold uppercase tracking-wider">Total Tagihan</span>
                    <span class="text-2xl font-black text-blue-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Bukti Pembayaran / Transfer -->
        <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100 space-y-6">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2 border-b border-gray-100 pb-4">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Bukti Pembayaran / Transfer
            </h3>

            <div class="text-center">
                @if($pemesanan->bukti_transfer)
                    <a href="{{ asset($pemesanan->bukti_transfer) }}" target="_blank" title="Klik untuk membuka gambar penuh">
                        <img src="{{ asset($pemesanan->bukti_transfer) }}" alt="Bukti Transfer" class="max-h-56 mx-auto rounded-2xl shadow-sm border border-gray-200 hover:opacity-90 transition-opacity">
                    </a>
                    <p class="text-xs text-gray-400 font-medium mt-3">Klik gambar untuk memperbesar di tab baru.</p>
                @else
                    <div class="py-8 text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-semibold text-gray-600">Belum Ada Bukti Transfer</p>
                        <p class="text-xs text-gray-400 mt-0.5">Pelanggan belum mengunggah foto resi pembayaran.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Rincian Lapangan & Jam -->
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-sm border border-gray-100">
        <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Rincian Lapangan & Jadwal Jam
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @foreach($detailTanggal as $tanggal => $detail)
            <div class="bg-gray-50/70 rounded-2xl p-5 border border-gray-100">
                <div class="mb-4 flex items-center gap-3">
                    <div class="w-2 h-6 bg-blue-600 rounded-full"></div>
                    <span class="text-sm font-bold text-gray-800">
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y') }}
                    </span>
                </div>
                
                <div class="space-y-2.5">
                    @foreach($detail as $item)
                    <div class="flex items-center justify-between bg-white rounded-xl px-4 py-2.5 border border-gray-200/80 shadow-sm">
                        <span class="bg-blue-50 text-blue-600 border border-blue-100 px-3 py-1 rounded-lg text-xs font-bold">
                            {{ substr($item->jam_mulai,0,5) }} - {{ substr($item->jam_selesai,0,5) }}
                        </span>
                        <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            {{ $item->lapangan->nama_lapangan ?? 'Lapangan' }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Form untuk submit aksi konfirmasi/tolak -->
    <form id="formConfirmAction" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="alasan_penolakan" id="hiddenAlasanPenolakan" value="">
    </form>

    <!-- Modal Confirm Action -->
    <div id="modalConfirm" style="z-index: 99999 !important;" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto transition-all duration-300">
        <div id="confirmDialogBox" class="relative my-auto mx-auto bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all max-h-[88vh] overflow-y-auto">
            <div id="confirmIconBox" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4">
                <!-- Icon diisi via JS -->
            </div>
            <h4 id="confirmTitle" class="font-extrabold text-gray-900 text-lg mb-1">Konfirmasi</h4>
            <p id="confirmMessage" class="text-xs text-gray-500 mb-4 leading-relaxed"></p>
            
            <!-- Box Alasan Penolakan (Hanya tampil jika tolak) -->
            <div id="boxAlasanPenolakan" class="hidden text-left mb-5">
                <label class="block text-xs font-bold text-gray-700 mb-2">Pilih Alasan Cepat:</label>
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <button type="button" onclick="setPresetAlasan('Bukti transfer buram / tidak terbaca')" class="btn-preset-alasan text-[11px] font-semibold bg-gray-100 hover:bg-rose-50 hover:text-rose-700 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200 transition cursor-pointer">
                        Bukti buram / tidak jelas
                    </button>
                    <button type="button" onclick="setPresetAlasan('Nominal transfer tidak sesuai dengan total tagihan')" class="btn-preset-alasan text-[11px] font-semibold bg-gray-100 hover:bg-rose-50 hover:text-rose-700 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200 transition cursor-pointer">
                        Nominal tidak sesuai
                    </button>
                    <button type="button" onclick="setPresetAlasan('Bukti transfer tidak valid / dana belum masuk')" class="btn-preset-alasan text-[11px] font-semibold bg-gray-100 hover:bg-rose-50 hover:text-rose-700 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200 transition cursor-pointer">
                        Bukti tidak valid
                    </button>
                    <button type="button" onclick="setPresetAlasan('Melewati batas waktu konfirmasi pembayaran')" class="btn-preset-alasan text-[11px] font-semibold bg-gray-100 hover:bg-rose-50 hover:text-rose-700 text-gray-700 px-2.5 py-1 rounded-lg border border-gray-200 transition cursor-pointer">
                        Lewat batas waktu
                    </button>
                </div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Atau Tulis Alasan Manual:</label>
                <textarea id="inputAlasanPenolakan" rows="2" placeholder="Tuliskan alasan penolakan yang jelas untuk pelanggan..." class="w-full text-xs p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-rose-500 focus:border-rose-500 text-gray-800 transition"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal()" class="w-1/2 py-2.5 border border-gray-200 rounded-xl font-bold text-xs text-gray-600 hover:bg-gray-50 transition cursor-pointer">
                    Batal
                </button>
                <button type="button" id="btnSubmitConfirm" onclick="submitConfirmModal()" class="w-1/2 py-2.5 text-white rounded-xl font-bold text-xs transition shadow-md cursor-pointer">
                    Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
    let currentConfirmType = 'setujui';

    function setPresetAlasan(text) {
        const input = document.getElementById('inputAlasanPenolakan');
        if (input) {
            input.value = text;
            input.focus();
        }
    }

    function openConfirmModal(type, actionUrl, kode) {
        currentConfirmType = type;
        const form = document.getElementById('formConfirmAction');
        form.action = actionUrl;

        const dialogBox = document.getElementById('confirmDialogBox');
        const iconBox = document.getElementById('confirmIconBox');
        const title = document.getElementById('confirmTitle');
        const message = document.getElementById('confirmMessage');
        const boxAlasan = document.getElementById('boxAlasanPenolakan');
        const inputAlasan = document.getElementById('inputAlasanPenolakan');
        const btnSubmit = document.getElementById('btnSubmitConfirm');

        if (inputAlasan) inputAlasan.value = '';

        if (type === 'setujui') {
            if (dialogBox) {
                dialogBox.classList.remove('max-w-md');
                dialogBox.classList.add('max-w-sm');
            }
            if (boxAlasan) boxAlasan.classList.add('hidden');
            iconBox.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-emerald-100 text-emerald-600';
            iconBox.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
            title.innerText = 'Setujui Pembayaran';
            message.innerText = 'Apakah Anda yakin ingin menyetujui pembayaran untuk pesanan #' + kode + '?';
            btnSubmit.className = 'w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-emerald-600/20 cursor-pointer';
            btnSubmit.innerText = 'Ya, Setujui';
        } else {
            if (dialogBox) {
                dialogBox.classList.remove('max-w-sm');
                dialogBox.classList.add('max-w-md');
            }
            if (boxAlasan) boxAlasan.classList.remove('hidden');
            iconBox.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-rose-100 text-rose-600';
            iconBox.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
            title.innerText = 'Tolak Pemesanan #' + kode;
            message.innerText = 'Pilih atau tulis alasan penolakan agar pelanggan dapat melihat penjelasannya.';
            btnSubmit.className = 'w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-rose-600/20 cursor-pointer';
            btnSubmit.innerText = 'Ya, Tolak Pemesanan';
        }

        const modal = document.getElementById('modalConfirm');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeConfirmModal() {
        const modal = document.getElementById('modalConfirm');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }

    function submitConfirmModal() {
        if (currentConfirmType === 'tolak') {
            const inputAlasan = document.getElementById('inputAlasanPenolakan');
            const hiddenAlasan = document.getElementById('hiddenAlasanPenolakan');
            if (inputAlasan && hiddenAlasan) {
                hiddenAlasan.value = inputAlasan.value.trim() || 'Bukti transfer tidak valid atau pembayaran tidak sesuai.';
            }
        }
        document.getElementById('formConfirmAction').submit();
    }
    </script>

@endsection