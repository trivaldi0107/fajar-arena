<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$content = file_get_contents($file);

// Let's remove the broken block first
// The block started at: <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8">
//                 <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Fasilitas</h3>
// And it ends before <!-- STEP 2: LOKASI & KONTAK -->

$startFasilitas = strpos($content, '<div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Fasilitas</h3>');

$endFasilitas = strpos($content, '<!-- STEP 2: LOKASI & KONTAK -->');

if ($startFasilitas !== false && $endFasilitas !== false) {
    // Cut it out completely
    $content = substr($content, 0, $startFasilitas) . substr($content, $endFasilitas);
    
    // Now insert the correct Fasilitas block right before <!-- STEP 2: LOKASI & KONTAK -->
    $correctFasilitas = <<<'HTML'
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mt-6">
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

    // Notice that step-1 was closed BEFORE Fasilitas? No, step-1 should contain Fasilitas. 
    // Wait, the original step-1 had:
    /*
        <div class="step-section active" id="step-1">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                ...
            </div>
        </div>
    */
    // If I put it before STEP 2, I need to make sure it's inside step-1, so I need to remove the closing </div> of step-1, insert Fasilitas, then close step-1.
    // Let's just put it as part of step-1
    $target = "</div>\n\n        <!-- STEP 2: LOKASI & KONTAK -->";
    $pos = strpos($content, $target);
    if ($pos !== false) {
        $content = substr($content, 0, $pos) . "\n" . $correctFasilitas . "        <!-- STEP 2: LOKASI & KONTAK -->" . substr($content, $pos + strlen($target));
    }
    
    file_put_contents($file, $content);
    echo "Fixed";
} else {
    echo "Not found";
}
