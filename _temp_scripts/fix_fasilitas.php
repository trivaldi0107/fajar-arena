<?php
$lapanganFile = 'resources/views/admin/lapangan/edit.blade.php';
$lapanganHtml = file_get_contents($lapanganFile);

$fasilitasBlock = '
                <div class="mb-6 mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Fasilitas</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="fasilitas[]" value="Parkiran" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" checked>
                            <span class="text-sm font-medium text-gray-700">Parkiran</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="fasilitas[]" value="Toilet" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" checked>
                            <span class="text-sm font-medium text-gray-700">Toilet</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="fasilitas[]" value="Musholla" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500" checked>
                            <span class="text-sm font-medium text-gray-700">Musholla</span>
                        </label>
                        <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition-colors">
                            <input type="checkbox" name="fasilitas[]" value="Kantin" class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                            <span class="text-sm font-medium text-gray-700">Kantin</span>
                        </label>
                    </div>
                </div>';

// Let's just put it inside Step 2 (Lokasi & Kontak) right at the top
$target = '<div class="step-section" id="step-2">
            <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-gray-100 p-5 md:p-8 mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Lokasi & Informasi Kontak</h3>';

$pos = strpos($lapanganHtml, $target);
if ($pos !== false) {
    $insertAt = $pos + strlen($target);
    $newHtml = substr($lapanganHtml, 0, $insertAt) . $fasilitasBlock . substr($lapanganHtml, $insertAt);
    file_put_contents($lapanganFile, $newHtml);
    echo "Fasilitas inserted successfully!";
} else {
    echo "Target not found.";
}
