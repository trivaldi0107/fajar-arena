<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$content = file_get_contents($file);

$startMarker = '<!-- STEP 1: INFO ARENA -->';
$endMarker = '<!-- STEP 2: LOKASI & KONTAK -->';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $replacement = <<<'HTML'
<!-- STEP 1: INFO ARENA -->
        <div class="step-section active" id="step-1">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Informasi Dasar Arena</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lapangan / Cabang</label>
                    <input type="text" name="nama_arena" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('nama_arena', $pengaturan->nama_arena) }}">
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Fasilitas</h3>
                
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-3">Fasilitas Utama</label>
                    <div class="flex flex-wrap gap-3">
                        @php 
                            $fasilitas = ['Kipas', 'Parkiran', 'Kantin', 'Wifi', 'Musholla', 'Toilet', 'Locker Room']; 
                            $checked = ['Parkiran', 'Kantin', 'Toilet', 'Musholla'];
                        @endphp
                        @foreach($fasilitas as $f)
                        <label class="cursor-pointer">
                            <input type="checkbox" class="peer sr-only" {{ in_array($f, $checked) ? 'checked' : '' }}>
                            <div class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-600 font-medium text-sm transition-all peer-checked:bg-blue-50 peer-checked:border-blue-500 peer-checked:text-blue-700 hover:bg-gray-50 shadow-sm">
                                {{ $f }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Fasilitas Tambahan (Opsional)</label>
                    <textarea rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 placeholder-gray-400 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Tuliskan fasilitas lain yang belum ada di daftar atas..."></textarea>
                </div>
            </div>
        </div>

        
HTML;

    $newContent = substr($content, 0, $startPos) . $replacement . substr($content, $endPos);
    file_put_contents($file, $newContent);
    echo "Fixed layout completely!";
} else {
    echo "Markers not found";
}
