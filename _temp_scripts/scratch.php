<?php
$content = file_get_contents('resources/views/admin/lapangan/edit.blade.php');

$replaceWith = <<<'HTML'
    <!-- FORM SECTIONS -->
    <form id="lapangan-form" method="POST" action="{{ route('admin.lapangan.update', $pengaturan->id) }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="step" id="current_step_input" value="1">
        
        <!-- STEP 1: INFO ARENA -->
        <div class="step-section active" id="step-1">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Arena</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lapangan / Cabang</label>
                    <input type="text" name="nama_arena" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('nama_arena', $pengaturan->nama_arena) }}">
                </div>
            </div>
        </div>

        <!-- STEP 2: LOKASI & KONTAK -->
        <div class="step-section" id="step-2">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Lokasi & Informasi Kontak</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none">{{ old('alamat', $pengaturan->alamat) }}</textarea>
                </div>

                <!-- STEP 3
HTML;

$content = preg_replace('/<!-- FORM SECTIONS -->.*?<!-- STEP 3/s', $replaceWith, $content);
file_put_contents('resources/views/admin/lapangan/edit.blade.php', $content);
echo "Fixed!";
