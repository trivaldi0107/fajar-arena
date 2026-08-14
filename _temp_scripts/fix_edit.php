<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$content = file_get_contents($file);

$startMarker = '<!-- Error Display -->';
$endMarker = '<!-- STEP 4: LAPANGAN & HARGA -->';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker);

if ($startPos === false || $endPos === false) {
    die("Markers not found");
}

$replacement = <<<'HTML'
    <!-- Error Display -->
    @if ($errors->any())
        <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan saat menyimpan data:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- FORM SECTIONS -->
    <form id="lapangan-form" method="POST" action="{{ route('admin.lapangan.update', $pengaturan->id) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" id="current_step_input" value="1">
        
        <!-- STEP 1: INFO ARENA -->
        <div class="step-section active" id="step-1">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Arena</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lapangan / Cabang</label>
                    <input type="text" name="nama_arena" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('nama_arena', $pengaturan->nama_arena) }}">
                </div>
            </div>
        </div>

        <!-- STEP 2: LOKASI & KONTAK -->
        <div class="step-section" id="step-2">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Lokasi & Informasi Kontak</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Lokasi Peta (Bebas Pilih Format)</label>
                    <input type="text" name="link_maps" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ $pengaturan->link_maps ?? '' }}" placeholder="Masukkan Link, Kode Iframe, atau Nama Tempat">
                    <p class="text-xs text-gray-500 mt-2">
                        Anda bisa memasukkan: <strong>1. Link biasa</strong> (https://maps.app.goo.gl/...) atau <strong>2. Kode HTML Iframe</strong> untuk peta interaktif, atau <strong>3. Nama Gedung</strong>.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kota</label>
                        <input type="text" name="kota" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('kota', $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Provinsi</label>
                        <input type="text" name="provinsi" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('provinsi', $pengaturan->provinsi) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Pos</label>
                        <input type="text" name="kodepos" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('kodepos', $pengaturan->kodepos) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">No Telepon</label>
                        <input type="text" name="no_telp" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('no_telp', $pengaturan->no_telp) }}">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('email', $pengaturan->email) }}">
                    </div>
                </div>
            </div>
        </div>

        
HTML;

$newContent = substr($content, 0, $startPos) . $replacement . substr($content, $endPos);
file_put_contents($file, $newContent);
echo "Fixed!";
