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

@push('modals')
<!-- Modal Preview Bukti Transfer -->
<div id="modalProof" style="z-index: 99999 !important;" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative my-auto mx-auto bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 text-center max-h-[88vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900 text-lg">Foto Bukti Transfer</h4>
            <button type="button" onclick="closeProof()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        <img id="proofImage" src="" alt="Bukti Transfer" class="max-h-[70vh] mx-auto rounded-2xl border border-gray-200 shadow-md">
    </div>
</div>

<!-- Modal Upload QRIS Statis -->
<div id="modalQris" style="z-index: 99999 !important;" class="fixed inset-0 hidden items-center justify-center bg-black/70 backdrop-blur-sm p-4 overflow-y-auto">
    <div class="relative my-auto mx-auto bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 text-center max-h-[88vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h4 class="font-bold text-gray-900 text-lg">Upload Gambar QRIS Statis</h4>
            <button type="button" onclick="closeModalQris()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg hover:bg-gray-100 transition cursor-pointer">
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

<!-- Modal Setel Suara Notifikasi -->
<div id="modalAudioSettings" style="z-index: 99999 !important;" class="fixed inset-0 hidden items-center justify-center bg-slate-900/70 backdrop-blur-md p-4 sm:p-6 overflow-y-auto transition-all duration-300">
    <div class="relative my-auto mx-auto bg-white rounded-3xl shadow-2xl max-w-lg w-full p-5 sm:p-7 text-left border border-gray-100 max-h-[85vh] overflow-y-auto">
        
        <!-- Header Modal -->
        <div class="flex justify-between items-start pb-4 border-b border-gray-100 mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"></path></svg>
                </div>
                <div>
                    <h4 class="font-extrabold text-gray-900 text-base sm:text-lg">Setel Suara Notifikasi</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Pilih nada dering dari perangkat Anda atau gunakan asisten suara pintar</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAudioSettings()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-xl hover:bg-gray-100 transition cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <div class="space-y-3.5">
            <!-- OPSI 1: KUSTOM DARI PERANGKAT -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-white hover:bg-slate-50 border-gray-200" id="cardModeCustom" onclick="selectAudioMode('custom')">
                <div class="flex items-start gap-3">
                    <!-- Custom Radio Circle -->
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center mt-0.5 shrink-0 transition-colors" id="radioCircleCustom">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden" id="radioDotCustom"></div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm text-gray-900">File Audio Perangkat</span>
                                <span class="bg-blue-100 text-blue-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Kustom</span>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); testPlayMode('custom')" class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm" title="Putar Pratinjau Suara">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span>Tes</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Gunakan nada dering atau lagu yang tersimpan di penyimpanan perangkat Anda.</p>
                        
                        <div class="mt-3 flex flex-col sm:flex-row sm:items-center gap-2">
                            <input type="file" id="customAudioInput" accept="audio/*" onchange="handleAudioUpload(event)" class="hidden">
                            <button type="button" onclick="event.stopPropagation(); document.getElementById('customAudioInput').click()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl transition shadow-sm flex items-center justify-center gap-1.5 cursor-pointer w-full sm:w-auto shrink-0">
                                <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <span>Pilih File Audio</span>
                            </button>
                            <div class="bg-gray-100 px-3 py-1.5 rounded-xl text-xs text-gray-600 truncate flex items-center gap-2 max-w-full">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path></svg>
                                <span id="customFileName" class="truncate font-medium">Belum ada file dipilih</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OPSI 2: ASISTEN SUARA BICARA (VOICE) -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-white hover:bg-slate-50 border-gray-200" id="cardModeVoice" onclick="selectAudioMode('voice')">
                <div class="flex items-start gap-3">
                    <!-- Custom Radio Circle -->
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center mt-0.5 shrink-0 transition-colors" id="radioCircleVoice">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden" id="radioDotVoice"></div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm text-gray-900">Asisten Suara Pintar</span>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Sebut Nama & Cabor</span>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); testPlayMode('voice')" class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm" title="Putar Pratinjau Suara">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span>Tes</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                            Suara otomatis menyebutkan: <em>"Pesanan Masuk! Atas nama [Nama Customer], [Cabor]. Silakan periksa bukti pembayaran."</em>
                        </p>
                    </div>
                </div>
            </div>

            <!-- OPSI 3: NADA BEL TING KLASIK -->
            <div class="p-4 rounded-2xl border-2 transition-all cursor-pointer bg-white hover:bg-slate-50 border-gray-200" id="cardModeChime" onclick="selectAudioMode('chime')">
                <div class="flex items-start gap-3">
                    <!-- Custom Radio Circle -->
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center mt-0.5 shrink-0 transition-colors" id="radioCircleChime">
                        <div class="w-2.5 h-2.5 rounded-full bg-blue-600 hidden" id="radioDotChime"></div>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-sm text-gray-900">Nada Bel Kasir</span>
                                <span class="bg-amber-100 text-amber-800 text-[10px] font-extrabold px-2 py-0.5 rounded-full">Bawaan</span>
                            </div>
                            <button type="button" onclick="event.stopPropagation(); testPlayMode('chime')" class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5 shrink-0 cursor-pointer shadow-sm" title="Putar Pratinjau Suara">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                <span>Tes</span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Denting bel kasir klasik yang lembut dan jernih.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-6">
            <button type="button" onclick="resetDefaultAudioSettings()" class="w-1/3 py-2.5 border border-gray-200 rounded-xl font-bold text-xs text-gray-600 hover:bg-gray-50 cursor-pointer transition">
                Reset
            </button>
            <button type="button" onclick="saveAudioSettings()" class="w-2/3 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs shadow-md shadow-blue-500/20 cursor-pointer transition">
                Simpan Pengaturan
            </button>
        </div>
    </div>
</div>
@endpush

<script>
// ==================== ENGINE SUARA NOTIFIKASI ====================
let currentAudioMode = localStorage.getItem('fajar_notif_mode') || 'chime';
let customAudioData = localStorage.getItem('fajar_custom_audio_data') || null;
let customAudioName = localStorage.getItem('fajar_custom_audio_name') || null;
let lastKnownPendingCount = 0;
let lastProcessedOrderId = null;

function initAudioSettingsUI() {
    currentAudioMode = localStorage.getItem('fajar_notif_mode') || 'chime';
    customAudioData = localStorage.getItem('fajar_custom_audio_data') || null;
    customAudioName = localStorage.getItem('fajar_custom_audio_name') || null;

    selectAudioMode(currentAudioMode, false);

    const fileLabel = document.getElementById('customFileName');
    if (fileLabel) {
        fileLabel.innerText = customAudioName ? customAudioName : 'Belum ada file dipilih';
    }

    updateBadgeMode(currentAudioMode);
}

function updateBadgeMode(mode) {
    const badge = document.getElementById('badgeActiveMode');
    if (!badge) return;
    if (mode === 'custom') {
        badge.innerText = 'Kustom';
        badge.className = 'text-[10px] bg-blue-500/20 text-blue-300 border border-blue-500/30 px-2 py-0.5 rounded-full font-bold';
    } else if (mode === 'voice') {
        badge.innerText = 'Asisten Suara';
        badge.className = 'text-[10px] bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 px-2 py-0.5 rounded-full font-bold';
    } else {
        badge.innerText = 'Bel Kasir';
        badge.className = 'text-[10px] bg-amber-500/20 text-amber-300 border border-amber-500/30 px-2 py-0.5 rounded-full font-bold';
    }
}

function selectAudioMode(mode, saveImmediately = false) {
    currentAudioMode = mode;
    
    ['custom', 'voice', 'chime'].forEach(m => {
        const card = document.getElementById('cardMode' + capitalize(m));
        const circle = document.getElementById('radioCircle' + capitalize(m));
        const dot = document.getElementById('radioDot' + capitalize(m));
        if (card && circle && dot) {
            if (m === mode) {
                card.classList.add('border-blue-600', 'bg-blue-50/40', 'shadow-md', 'shadow-blue-500/5');
                card.classList.remove('border-gray-200', 'bg-white');
                circle.classList.add('border-blue-600');
                circle.classList.remove('border-gray-300');
                dot.classList.remove('hidden');
            } else {
                card.classList.remove('border-blue-600', 'bg-blue-50/40', 'shadow-md', 'shadow-blue-500/5');
                card.classList.add('border-gray-200', 'bg-white');
                circle.classList.remove('border-blue-600');
                circle.classList.add('border-gray-300');
                dot.classList.add('hidden');
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

function playSynthesizedChime() {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        if (ctx.state === 'suspended') {
            ctx.resume().then(() => makeCleanChime(ctx));
        } else {
            makeCleanChime(ctx);
        }
    } catch(e) {
        console.warn('Web Audio error', e);
    }
}

function makeCleanChime(ctx) {
    try {
        const now = ctx.currentTime;
        // Tone 1: E5 (659.25 Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, now);
        gain1.gain.setValueAtTime(0.5, now);
        gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(now);
        osc1.stop(now + 0.35);

        // Tone 2: A5 (880 Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880, now + 0.15);
        gain2.gain.setValueAtTime(0.6, now + 0.15);
        gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.7);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(now + 0.15);
        osc2.stop(now + 0.7);
    } catch(e) {}
}

function speakIndonesianText(text) {
    if (!('speechSynthesis' in window)) {
        playSynthesizedChime();
        return;
    }

    try {
        window.speechSynthesis.cancel();
        
        setTimeout(() => {
            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'id-ID';
            utterance.rate = 0.92;
            utterance.pitch = 1.0;
            utterance.volume = 1.0;

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
                playSynthesizedChime();
            };

            window.speechSynthesis.resume();
            window.speechSynthesis.speak(utterance);
        }, 60);
    } catch(e) {
        console.warn('Speech synthesis catch error', e);
        playSynthesizedChime();
    }
}

// Muat daftar suara browser secara asinkron
if ('speechSynthesis' in window) {
    window.speechSynthesis.onvoiceschanged = () => {
        window.speechSynthesis.getVoices();
    };
}

function testPlayMode(mode) {
    if (mode === 'custom') {
        if (customAudioData) {
            const audio = new Audio(customAudioData);
            audio.play().catch(e => {
                console.warn('Audio play error', e);
                playSynthesizedChime();
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
        speakIndonesianText("Pesanan Masuk! Atas nama Budi Santoso, Badminton. Silakan periksa bukti pembayaran.");
    } else if (mode === 'chime') {
        playSynthesizedChime();
    }
}

function playActiveNotification(customerName = 'Pelanggan', cabor = 'Badminton') {
    const mode = localStorage.getItem('fajar_notif_mode') || 'chime';

    if (mode === 'custom') {
        const audioData = localStorage.getItem('fajar_custom_audio_data');
        if (audioData) {
            const audio = new Audio(audioData);
            audio.play().catch(e => {
                console.warn('Autoplay error', e);
                playSynthesizedChime();
            });
        } else {
            playSynthesizedChime();
        }
    } else if (mode === 'voice') {
        const text = `Pesanan Masuk! Atas nama ${customerName}, ${cabor}. Silakan periksa bukti pembayaran.`;
        speakIndonesianText(text);
    } else {
        playSynthesizedChime();
    }
}

function saveAudioSettings() {
    localStorage.setItem('fajar_notif_mode', currentAudioMode);
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