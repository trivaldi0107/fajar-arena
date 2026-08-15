@extends('admin.layouts.app')

@section('title', 'Data Pemesanan')

@section('content')

    @if(session('success'))
    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="font-semibold text-sm">{{ session('success') }}</span>
    </div>
    @endif

    <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Data Pemesanan</h2>
            <p class="text-gray-500 mt-1 text-sm">Daftar seluruh reservasi dan verifikasi pembayaran</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
            <!-- Search Bar khusus Data Pemesanan Admin (Server-Side Search Meliputi Seluruh Halaman Database) -->
            <form id="searchAdminForm" method="GET" action="{{ route('admin.pemesanan') }}" class="relative w-full sm:w-80">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="text" name="search" id="adminSearchInput" value="{{ $search ?? '' }}" oninput="debounceAdminSearch()" placeholder="Cari Kode / Customer..." class="w-full h-11 pl-10 pr-10 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-all">
                <button type="submit" class="absolute left-3 top-3 text-gray-400 hover:text-blue-600 transition-colors cursor-pointer" title="Cari Data">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
                @if(!empty($search))
                    <a href="{{ route('admin.pemesanan', array_filter(['status' => request('status')])) }}" class="absolute right-3 top-3 text-gray-400 hover:text-rose-500 p-0.5 rounded-full hover:bg-rose-50 transition-colors" title="Hapus Pencarian">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>

            <button type="button" onclick="openModalQris()" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2 cursor-pointer shrink-0 w-full sm:w-auto">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <span class="whitespace-nowrap">Upload QRIS Statis</span>
            </button>

            <!-- Tombol Setel Suara Notifikasi (di sebelah kanan tombol Upload QRIS) -->
            <button type="button" onclick="openModalAudioSettings()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-sm shadow-md shadow-slate-900/20 transition flex items-center justify-center gap-2 cursor-pointer shrink-0 w-full sm:w-auto border border-slate-700/50" title="Atur Nada Dering / Suara Notifikasi Pesanan">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                <span class="whitespace-nowrap">Setel Notifikasi</span>
                <span id="badgeActiveMode" class="text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full font-bold">Default</span>
            </button>
        </div>
    </div>

    <!-- Filter Tabs Status -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-6 no-scrollbar -mx-2 px-2 sm:mx-0 sm:px-0 sm:flex-wrap">
        <a href="{{ route('admin.pemesanan', array_filter(['status' => 'semua', 'search' => $search ?? null])) }}" 
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm whitespace-nowrap shrink-0 {{ ($status ?? 'semua') === 'semua' ? 'bg-slate-900 text-white shadow-slate-900/20' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            Semua
        </a>
        <a href="{{ route('admin.pemesanan', array_filter(['status' => 'proses', 'search' => $search ?? null])) }}" 
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm whitespace-nowrap shrink-0 flex items-center gap-2 {{ ($status ?? '') === 'proses' ? 'bg-amber-500 text-white shadow-amber-500/20' : 'bg-white text-amber-600 hover:bg-amber-50 border border-amber-200' }}">
            <span>Menunggu Verifikasi</span>
            <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs font-black">!</span>
        </a>
        <a href="{{ route('admin.pemesanan', array_filter(['status' => 'berhasil', 'search' => $search ?? null])) }}" 
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm whitespace-nowrap shrink-0 {{ ($status ?? '') === 'berhasil' ? 'bg-emerald-600 text-white shadow-emerald-600/20' : 'bg-white text-emerald-600 hover:bg-emerald-50 border border-emerald-200' }}">
            Lunas
        </a>
        <a href="{{ route('admin.pemesanan', array_filter(['status' => 'batal', 'search' => $search ?? null])) }}" 
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm whitespace-nowrap shrink-0 {{ ($status ?? '') === 'batal' ? 'bg-rose-600 text-white shadow-rose-600/20' : 'bg-white text-rose-600 hover:bg-rose-50 border border-rose-200' }}">
            Dibatalkan
        </a>
    </div>

<div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-4 md:p-8">

    <div class="overflow-x-auto -mx-4 sm:mx-0 px-4 sm:px-0">

        <table class="w-full min-w-[950px] text-left border-collapse">

            <thead>
                <tr class="border-b border-gray-100 text-xs uppercase tracking-wider text-gray-400 font-bold">
                    <th class="py-4 px-4 sm:px-5">Kode</th>
                    <th class="py-4 px-4 sm:px-5">Customer</th>
                    <th class="py-4 px-4 sm:px-5">Tanggal & Durasi</th>
                    <th class="py-4 px-4 sm:px-5">Total</th>
                    <th class="py-4 px-4 sm:px-5">Bukti Transfer</th>
                    <th class="py-4 px-4 sm:px-5">Status</th>
                    <th class="py-4 px-4 sm:px-5 text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm">

            @forelse($pemesanan as $item)

            @php
                if ($item->jenis_user == 'member') {
                    $total = active_arena()->member_harga ?? 1000000;
                } else {
                    $hargaPerJam = active_arena()->harga_per_jam ?? 80000;
                    $total = $item->durasi * $hargaPerJam;
                }
            @endphp

            <tr class="hover:bg-gray-50/70 transition-colors">

                <td class="py-4 px-4 sm:px-5 font-bold text-gray-900 whitespace-nowrap">
                    <a href="{{ route('admin.pemesanan.detail', $item->id) }}" class="text-blue-600 hover:underline">
                        {{ $item->kode_reservasi }}
                    </a>
                </td>

                <td class="py-4 px-4 sm:px-5 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        @if($item->user && $item->user->profile_photo_path)
                            <img src="{{ asset('storage/' . $item->user->profile_photo_path) }}" alt="{{ $item->user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-sm border border-gray-200 shadow-sm shrink-0">
                                {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="font-bold text-gray-900">{{ $item->user->name ?? 'Pelanggan' }}</div>
                            <div class="text-xs text-gray-400 font-medium">{{ $item->user->email ?? '-' }}</div>
                        </div>
                    </div>
                </td>

                <td class="py-4 px-4 sm:px-5 whitespace-nowrap">
                    <div class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}</div>
                    <div class="text-xs text-gray-500">{{ $item->durasi }} Jam</div>
                </td>

                <td class="py-4 px-4 sm:px-5 font-black text-gray-900 whitespace-nowrap">
                    Rp {{ number_format($total, 0, ',', '.') }}
                </td>

                <td class="py-4 px-4 sm:px-5 whitespace-nowrap">
                    @if($item->bukti_transfer)
                        <button type="button" onclick="previewProof('{{ asset($item->bukti_transfer) }}')" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-xl text-xs font-bold border border-blue-100 hover:bg-blue-100 transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            Lihat Bukti
                        </button>
                    @else
                        <span class="text-xs text-gray-400 italic">Belum Upload</span>
                    @endif
                </td>

                <td class="py-4 px-4 sm:px-5 whitespace-nowrap">
                    @if($item->status === 'berhasil')
                        <span class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold border border-emerald-200 inline-block">
                            ✓ Lunas
                        </span>
                    @elseif($item->status === 'proses')
                        <span class="bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-bold border border-amber-200 animate-pulse inline-block">
                            ⏳ Verifikasi Admin
                        </span>
                    @elseif($item->status === 'batal' || $item->status === 'dibatalkan')
                        <span class="bg-rose-100 text-rose-700 px-3 py-1 rounded-full text-xs font-bold border border-rose-200 inline-block">
                            ✕ Dibatalkan
                        </span>
                    @else
                        <span class="bg-slate-100 text-slate-500 px-3 py-1 rounded-full text-xs font-semibold border border-slate-200 inline-block">
                            Belum Bayar
                        </span>
                    @endif
                </td>

                <td class="py-4 px-4 sm:px-5 text-right whitespace-nowrap">
                    <div class="flex items-center justify-end gap-2">
                        @if($item->status === 'proses')
                            <button type="button" onclick="openConfirmModal('setujui', '{{ route('admin.pemesanan.konfirmasi', $item->id) }}', '{{ $item->kode_reservasi }}')" class="px-3 py-1.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-700 transition shadow-sm cursor-pointer">
                                Setujui
                            </button>
                            <button type="button" onclick="openConfirmModal('tolak', '{{ route('admin.pemesanan.tolak', $item->id) }}', '{{ $item->kode_reservasi }}')" class="px-3 py-1.5 bg-rose-600 text-white rounded-xl text-xs font-bold hover:bg-rose-700 transition shadow-sm cursor-pointer">
                                Tolak
                            </button>
                        @endif

                        <a href="{{ route('admin.pemesanan.detail', $item->id) }}"
                            class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 text-xs font-bold transition">
                            Detail
                        </a>
                    </div>
                </td>

            </tr>

            @empty
            <tr>
                <td colspan="7" class="text-center py-12 px-4 text-gray-400">
                    Belum ada data pemesanan pada kategori ini.
                </td>
            </tr>
            @endforelse

            </tbody>

        </table>

    </div>

    <div class="mt-8">
        {{ $pemesanan->links() }}
    </div>

</div>

<!-- Modal Preview Bukti Transfer -->
<div id="modalProof" class="fixed inset-0 hidden items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 text-center">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900 text-lg">Foto Bukti Transfer</h4>
            <button type="button" onclick="closeProof()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <img id="proofImage" src="" alt="Bukti Transfer" class="max-h-[70vh] mx-auto rounded-2xl border border-gray-200 shadow-md">
    </div>
</div>

<!-- Modal Upload QRIS Statis -->
<div id="modalQris" class="fixed inset-0 hidden items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900 text-lg">Upload Gambar QRIS Statis</h4>
            <button type="button" onclick="closeModalQris()" class="text-gray-400 hover:text-gray-600 p-1">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <form action="{{ route('admin.qris.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if(active_arena()->qris_image)
                <div class="mb-3">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">QRIS Statis Saat Ini</p>
                    <img src="{{ asset(active_arena()->qris_image) }}" alt="QRIS Statis" class="w-44 h-auto mx-auto rounded-2xl border border-gray-200 shadow-sm">
                </div>
            @endif
            <div class="text-left">
                <label class="block text-xs font-bold text-gray-700 mb-1">Pilih Gambar Barcode QRIS Baru</label>
                <input type="file" name="qris_image" accept="image/*" required class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-gray-200 rounded-xl cursor-pointer">
            </div>
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeModalQris()" class="w-1/2 py-2.5 border border-gray-200 rounded-xl font-bold text-sm text-gray-600 hover:bg-gray-50 cursor-pointer">Batal</button>
                <button type="submit" class="w-1/2 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 cursor-pointer shadow-md shadow-blue-500/20">Simpan QRIS</button>
            </div>
        </form>
    </div>
</div>

<!-- Form untuk submit aksi konfirmasi/tolak -->
<form id="formConfirmAction" method="POST" class="hidden">
    @csrf
</form>

<!-- Modal Confirm Action -->
<div id="modalConfirm" class="fixed inset-0 hidden items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 text-center transform transition-all">
        <div id="confirmIconBox" class="w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4">
            <!-- Icon diisi via JS -->
        </div>
        <h4 id="confirmTitle" class="font-extrabold text-gray-900 text-lg mb-1">Konfirmasi</h4>
        <p id="confirmMessage" class="text-xs text-gray-500 mb-6 leading-relaxed"></p>
        
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

<!-- Modal Setel Suara Notifikasi -->
<div id="modalAudioSettings" class="fixed inset-0 hidden items-center justify-center bg-black/60 backdrop-blur-sm z-50 p-4 transition-all duration-300">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 text-left transform transition-all max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center pb-4 border-b border-gray-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-gray-900 text-base sm:text-lg">Setel Suara Notifikasi</h4>
                    <p class="text-xs text-gray-500">Pilih nada dering dari perangkat Anda atau asisten pintar</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAudioSettings()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-3.5">
            <!-- OPSI 1: KUSTOM DARI PERANGKAT -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-slate-50/50 hover:bg-slate-50 border-gray-200" id="cardModeCustom" onclick="selectAudioMode('custom')">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="notif_sound_mode" id="modeCustom" value="custom" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <label for="modeCustom" class="font-bold text-sm text-gray-900 cursor-pointer flex items-center gap-2">
                                <span>📁 File Audio dari Perangkat</span>
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Kustom HP / Laptop</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Gunakan lagu atau ringtone favorit yang tersimpan di penyimpanan perangkat Anda.</p>
                            <div class="mt-2.5 flex items-center gap-2">
                                <input type="file" id="customAudioInput" accept="audio/*" onchange="handleAudioUpload(event)" class="hidden">
                                <button type="button" onclick="event.stopPropagation(); document.getElementById('customAudioInput').click()" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-bold rounded-xl transition shadow-sm flex items-center gap-1.5 cursor-pointer">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Pilih File Audio
                                </button>
                                <span id="customFileName" class="text-xs text-gray-600 truncate max-w-[180px] italic">Belum ada file dipilih</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); testPlayMode('custom')" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl transition flex items-center gap-1 shrink-0 cursor-pointer" title="Putar Pratinjau Suara">
                        <span>▶️ Tes</span>
                    </button>
                </div>
            </div>

            <!-- OPSI 2: ASISTEN SUARA BICARA (VOICE) -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-slate-50/50 hover:bg-slate-50 border-gray-200" id="cardModeVoice" onclick="selectAudioMode('voice')">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="notif_sound_mode" id="modeVoice" value="voice" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <label for="modeVoice" class="font-bold text-sm text-gray-900 cursor-pointer flex items-center gap-2">
                                <span>🗣️ Asisten Suara Pintar (Voice)</span>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Sebut Nama & Cabor</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                Suara otomatis menyebutkan: <em>"Pesanan Masuk! [Nama Customer], [Cabor]. Silakan periksa bukti pembayaran."</em>
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); testPlayMode('voice')" class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center gap-1 shrink-0 cursor-pointer" title="Putar Pratinjau Suara">
                        <span>▶️ Tes</span>
                    </button>
                </div>
            </div>

            <!-- OPSI 3: NADA BEL TING KLASIK -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-slate-50/50 hover:bg-slate-50 border-gray-200" id="cardModeChime" onclick="selectAudioMode('chime')">
                <div class="flex items-start justify-between">
                    <div class="flex items-start gap-3">
                        <input type="radio" name="notif_sound_mode" id="modeChime" value="chime" class="mt-1 text-blue-600 focus:ring-blue-500">
                        <div>
                            <label for="modeChime" class="font-bold text-sm text-gray-900 cursor-pointer flex items-center gap-2">
                                <span>🔔 Nada Bel 'Ting' Kasir (Chime Bawaan)</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Denting bel kasir klasik yang lembut dan jernih.</p>
                        </div>
                    </div>
                    <button type="button" onclick="event.stopPropagation(); testPlayMode('chime')" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-xl transition flex items-center gap-1 shrink-0 cursor-pointer" title="Putar Pratinjau Suara">
                        <span>▶️ Tes</span>
                    </button>
                </div>
            </div>

            <!-- OPSI 4: SENYAP -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-slate-50/50 hover:bg-slate-50 border-gray-200" id="cardModeMute" onclick="selectAudioMode('mute')">
                <div class="flex items-start gap-3">
                    <input type="radio" name="notif_sound_mode" id="modeMute" value="mute" class="mt-1 text-blue-600 focus:ring-blue-500">
                    <div>
                        <label for="modeMute" class="font-bold text-sm text-gray-900 cursor-pointer">
                            🔇 Senyap (Mute)
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Matikan suara notifikasi (hanya menampilkan tanda visual lonceng).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Volume Slider -->
        <div class="mt-5 pt-4 border-t border-gray-100">
            <div class="flex items-center justify-between mb-1.5">
                <label class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                    Volume Suara
                </label>
                <span id="volumeLabel" class="text-xs font-bold text-blue-600">80%</span>
            </div>
            <input type="range" id="volumeSlider" min="10" max="100" value="80" oninput="updateVolumeLabel(this.value)" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600">
        </div>

        <div class="flex gap-2.5 pt-6">
            <button type="button" onclick="resetDefaultAudioSettings()" class="w-1/3 py-2.5 border border-gray-200 rounded-xl font-bold text-xs text-gray-600 hover:bg-gray-50 cursor-pointer transition">
                Reset Bawaan
            </button>
            <button type="button" onclick="saveAudioSettings()" class="w-2/3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-500/20 cursor-pointer transition">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>

<script>
// ==================== ENGINE SUARA NOTIFIKASI ====================
let currentAudioMode = localStorage.getItem('fajar_notif_mode') || 'chime';
let currentVolume = parseFloat(localStorage.getItem('fajar_notif_volume') || '0.8');
let customAudioData = localStorage.getItem('fajar_custom_audio_data') || null;
let customAudioName = localStorage.getItem('fajar_custom_audio_name') || null;
let lastKnownPendingCount = 0;
let lastProcessedOrderId = null;

function initAudioSettingsUI() {
    currentAudioMode = localStorage.getItem('fajar_notif_mode') || 'chime';
    currentVolume = parseFloat(localStorage.getItem('fajar_notif_volume') || '0.8');
    customAudioData = localStorage.getItem('fajar_custom_audio_data') || null;
    customAudioName = localStorage.getItem('fajar_custom_audio_name') || null;

    selectAudioMode(currentAudioMode, false);
    
    const slider = document.getElementById('volumeSlider');
    if (slider) {
        slider.value = Math.round(currentVolume * 100);
        updateVolumeLabel(slider.value);
    }

    const fileLabel = document.getElementById('customFileName');
    if (fileLabel) {
        fileLabel.innerText = customAudioName ? ('🎵 ' + customAudioName) : 'Belum ada file dipilih';
    }

    updateBadgeMode(currentAudioMode);
}

function updateBadgeMode(mode) {
    const badge = document.getElementById('badgeActiveMode');
    if (!badge) return;
    if (mode === 'custom') {
        badge.innerText = '📁 File Perangkat';
        badge.className = 'text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded-full font-bold';
    } else if (mode === 'voice') {
        badge.innerText = '🗣️ Asisten Suara';
        badge.className = 'text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold';
    } else if (mode === 'mute') {
        badge.innerText = '🔇 Senyap';
        badge.className = 'text-[10px] bg-rose-500/20 text-rose-300 border border-rose-500/30 px-2 py-0.5 rounded-full font-bold';
    } else {
        badge.innerText = '🔔 Bel Ting';
        badge.className = 'text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full font-bold';
    }
}

function selectAudioMode(mode, saveImmediately = false) {
    currentAudioMode = mode;
    
    ['custom', 'voice', 'chime', 'mute'].forEach(m => {
        const card = document.getElementById('cardMode' + capitalize(m));
        const radio = document.getElementById('mode' + capitalize(m));
        if (card && radio) {
            if (m === mode) {
                card.classList.add('border-blue-500', 'bg-blue-50/40', 'ring-2', 'ring-blue-500/20');
                card.classList.remove('border-gray-200', 'bg-slate-50/50');
                radio.checked = true;
            } else {
                card.classList.remove('border-blue-500', 'bg-blue-50/40', 'ring-2', 'ring-blue-500/20');
                card.classList.add('border-gray-200', 'bg-slate-50/50');
                radio.checked = false;
            }
        }
    });

    if (saveImmediately) {
        localStorage.setItem('fajar_notif_mode', mode);
        updateBadgeMode(mode);
    }
}

function capitalize(s) {
    return s.charAt(0).toUpperCase() + s.slice(1);
}

function updateVolumeLabel(val) {
    document.getElementById('volumeLabel').innerText = val + '%';
    currentVolume = val / 100;
}

function handleAudioUpload(event) {
    const file = event.target.files[0];
    if (!file) return;

    if (file.size > 8 * 1024 * 1024) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Ukuran File Terlalu Besar',
                text: 'Maksimal ukuran file audio adalah 8MB agar sistem tetap cepat.',
                confirmButtonColor: '#2563eb'
            });
        } else {
            alert('Maksimal ukuran file audio adalah 8MB.');
        }
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        customAudioData = e.target.result;
        customAudioName = file.name;
        localStorage.setItem('fajar_custom_audio_data', customAudioData);
        localStorage.setItem('fajar_custom_audio_name', customAudioName);
        
        document.getElementById('customFileName').innerText = '🎵 ' + customAudioName;
        selectAudioMode('custom', true);

        testPlayMode('custom');
    };
    reader.readAsDataURL(file);
}

function playSynthesizedChime(vol = currentVolume) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const now = ctx.currentTime;
        
        // Tone 1
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(587.33, now);
        osc1.frequency.exponentialRampToValueAtTime(880, now + 0.1);
        gain1.gain.setValueAtTime(0.35 * vol, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.8);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.8);
        
        // Tone 2
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(1046.50, now + 0.12);
        gain2.gain.setValueAtTime(0.45 * vol, now + 0.12);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 1.2);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.12);
        osc2.stop(now + 1.2);
    } catch(e) {
        console.warn('Web Audio error', e);
    }
}

function testPlayMode(mode) {
    const vol = parseFloat(document.getElementById('volumeSlider').value) / 100;
    
    if (mode === 'custom') {
        if (customAudioData) {
            const audio = new Audio(customAudioData);
            audio.volume = vol;
            audio.play().catch(e => {
                console.warn('Audio play error', e);
                playSynthesizedChime(vol);
            });
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Pilih File Terlebih Dahulu',
                    text: 'Silakan klik tombol "Pilih File Audio" untuk mengambil lagu/ringtone dari perangkat Anda.',
                    confirmButtonColor: '#2563eb'
                });
            } else {
                alert('Silakan pilih file audio dari perangkat terlebih dahulu.');
            }
        }
function speakIndonesianText(text, vol = currentVolume) {
    if (!('speechSynthesis' in window)) {
        playSynthesizedChime(vol);
        return;
    }

    try {
        window.speechSynthesis.cancel();
        
        setTimeout(() => {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.92;
            utterance.pitch = 1.0;
            utterance.volume = Math.max(0.1, Math.min(1.0, vol));

            // Cari suara bahasa Indonesia jika tersedia di browser
            const voices = window.speechSynthesis.getVoices();
            if (voices && voices.length > 0) {
                const idVoice = voices.find(v => (v.lang && v.lang.toLowerCase().includes('id')) || (v.name && v.name.toLowerCase().includes('indonesia')));
                if (idVoice) {
                    utterance.voice = idVoice;
                }
            }

            utterance.onerror = function(err) {
                console.warn('SpeechSynthesis error, fallback to chime', err);
                playSynthesizedChime(vol);
            };

            window.speechSynthesis.resume();
            window.speechSynthesis.speak(utterance);
        }, 60);
    } catch(e) {
        console.warn('Speech synthesis catch error', e);
        playSynthesizedChime(vol);
    }
}

// Muat daftar suara browser secara asinkron
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = () => {
        window.speechSynthesis.getVoices();
    };
}

function testPlayMode(mode) {
    const vol = parseFloat(document.getElementById('volumeSlider').value) / 100;
    
    if (mode === 'custom') {
        if (customAudioData) {
            const audio = new Audio(customAudioData);
            audio.volume = vol;
            audio.play().catch(e => {
                console.warn('Audio play error', e);
                playSynthesizedChime(vol);
            });
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Pilih File Terlebih Dahulu',
                    text: 'Silakan klik tombol "Pilih File Audio" untuk mengambil lagu/ringtone dari perangkat Anda.',
                    confirmButtonColor: '#2563eb'
                });
            } else {
                alert('Silakan pilih file audio dari perangkat terlebih dahulu.');
            }
        }
    } else if (mode === 'voice') {
        speakIndonesianText("Pesanan Masuk! Budi Santoso, Badminton. Silakan periksa bukti pembayaran.", vol);
    } else if (mode === 'chime') {
        playSynthesizedChime(vol);
    }
}

function playActiveNotification(customerName = 'Pelanggan', cabor = 'Badminton') {
    const mode = localStorage.getItem('fajar_notif_mode') || 'chime';
    const vol = parseFloat(localStorage.getItem('fajar_notif_volume') || '0.8');

    if (mode === 'mute') {
        return;
    } else if (mode === 'custom') {
        const audioData = localStorage.getItem('fajar_custom_audio_data');
        if (audioData) {
            const audio = new Audio(audioData);
            audio.volume = vol;
            audio.play().catch(e => {
                console.warn('Autoplay error', e);
                playSynthesizedChime(vol);
            });
        } else {
            playSynthesizedChime(vol);
        }
    } else if (mode === 'voice') {
        const text = `Pesanan Masuk! ${customerName}, ${cabor}. Silakan periksa bukti pembayaran.`;
        speakIndonesianText(text, vol);
    } else {
        playSynthesizedChime(vol);
    }
}

function saveAudioSettings() {
    localStorage.setItem('fajar_notif_mode', currentAudioMode);
    localStorage.setItem('fajar_notif_volume', currentVolume.toString());
    updateBadgeMode(currentAudioMode);
    closeModalAudioSettings();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Pengaturan Tersimpan!',
            text: 'Suara notifikasi pesanan berhasil diperbarui.',
            timer: 2000,
            showConfirmButton: false,
            customClass: { popup: 'rounded-3xl' }
        });
    }
}

function resetDefaultAudioSettings() {
    localStorage.removeItem('fajar_custom_audio_data');
    localStorage.removeItem('fajar_custom_audio_name');
    localStorage.setItem('fajar_notif_mode', 'chime');
    localStorage.setItem('fajar_notif_volume', '0.8');
    customAudioData = null;
    customAudioName = null;
    initAudioSettingsUI();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Reset Selesai',
            text: 'Suara notifikasi dikembalikan ke nada bel bawaan.',
            timer: 1800,
            showConfirmButton: false,
            customClass: { popup: 'rounded-3xl' }
        });
    }
}

function openModalAudioSettings() {
    initAudioSettingsUI();
    const modal = document.getElementById('modalAudioSettings');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModalAudioSettings() {
    const modal = document.getElementById('modalAudioSettings');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// ==================== REAL-TIME BACKGROUND CHECK ====================
function startRealtimeNotificationWatcher() {
    setInterval(() => {
        fetch("{{ route('admin.pemesanan.latest_check') }}", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data && typeof data.count !== 'undefined') {
                if (lastKnownPendingCount === 0) {
                    lastKnownPendingCount = data.count;
                    lastProcessedOrderId = data.latest_id;
                    return;
                }

                if (data.count > lastKnownPendingCount || (data.latest_id && data.latest_id !== lastProcessedOrderId && data.count > 0)) {
                    lastKnownPendingCount = data.count;
                    lastProcessedOrderId = data.latest_id;

                    playActiveNotification(data.customer_name, data.cabor);

                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: true,
                            confirmButtonText: 'Segarkan Tabel',
                            confirmButtonColor: '#2563eb',
                            timer: 10000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            }
                        });
                        Toast.fire({
                            icon: 'info',
                            title: `🔔 Pesanan Masuk: ${data.customer_name}`,
                            text: `${data.cabor} (Menunggu Verifikasi)`
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    }
                }
            }
        })
        .catch(err => console.debug('Watcher quiet check', err));
    }, 8000);
}

document.addEventListener('DOMContentLoaded', () => {
    initAudioSettingsUI();
    startRealtimeNotificationWatcher();
});

// ==================== MODAL KONFIRMASI & QRIS ====================
function openConfirmModal(type, actionUrl, kode) {
    const form = document.getElementById('formConfirmAction');
    form.action = actionUrl;

    const iconBox = document.getElementById('confirmIconBox');
    const title = document.getElementById('confirmTitle');
    const message = document.getElementById('confirmMessage');
    const btnSubmit = document.getElementById('btnSubmitConfirm');

    if (type === 'setujui') {
        iconBox.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-emerald-100 text-emerald-600';
        iconBox.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>';
        title.innerText = 'Setujui Pembayaran';
        message.innerText = 'Apakah Anda yakin ingin menyetujui pembayaran untuk pesanan #' + kode + '?';
        btnSubmit.className = 'w-1/2 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-emerald-600/20 cursor-pointer';
        btnSubmit.innerText = 'Ya, Setujui';
    } else {
        iconBox.className = 'w-14 h-14 rounded-2xl mx-auto flex items-center justify-center mb-4 bg-rose-100 text-rose-600';
        iconBox.innerHTML = '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>';
        title.innerText = 'Tolak Pemesanan';
        message.innerText = 'Apakah Anda yakin ingin menolak pemesanan #' + kode + '? Status jadwal akan dikembalikan menjadi tersedia.';
        btnSubmit.className = 'w-1/2 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-bold text-xs transition shadow-md shadow-rose-600/20 cursor-pointer';
        btnSubmit.innerText = 'Ya, Tolak';
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
    document.getElementById('formConfirmAction').submit();
}

function previewProof(url) {
    document.getElementById('proofImage').src = url;
    document.getElementById('modalProof').classList.remove('hidden');
    document.getElementById('modalProof').classList.add('flex');
}

function closeProof() {
    document.getElementById('modalProof').classList.remove('flex');
    document.getElementById('modalProof').classList.add('hidden');
}

function openModalQris() {
    document.getElementById('modalQris').classList.remove('hidden');
    document.getElementById('modalQris').classList.add('flex');
}

function closeModalQris() {
    document.getElementById('modalQris').classList.remove('flex');
    document.getElementById('modalQris').classList.add('hidden');
}

let adminSearchTimer;
function debounceAdminSearch() {
    clearTimeout(adminSearchTimer);
    adminSearchTimer = setTimeout(function() {
        document.getElementById('searchAdminForm').submit();
    }, 600);
}
</script>

@endsection