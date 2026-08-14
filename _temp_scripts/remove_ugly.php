<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$c = file_get_contents($file);

$uglyBlock = '<div class="mb-6 mt-6">
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

$c = str_replace($uglyBlock, '', $c);
file_put_contents($file, $c);
echo "Removed ugly block";
