<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$c = file_get_contents($file);

// Replace Fasilitas Utama block
$searchFas = "@php 
                            \$fasilitas = ['Kipas', 'Parkiran', 'Kantin', 'Wifi', 'Musholla', 'Toilet', 'Locker Room']; 
                            \$checked = ['Parkiran', 'Kantin', 'Toilet', 'Musholla'];
                        @endphp
                        @foreach(\$fasilitas as \$f)
                        <label class=\"cursor-pointer\">
                            <input type=\"checkbox\" class=\"peer sr-only\" {{ in_array(\$f, \$checked) ? 'checked' : '' }}>";

$replaceFas = "@php 
                            \$fasilitas = ['Kipas', 'Parkiran', 'Kantin', 'Wifi', 'Musholla', 'Toilet', 'Locker Room']; 
                            \$checked = json_decode(\$pengaturan->fasilitas ?? '[]', true) ?: [];
                        @endphp
                        @foreach(\$fasilitas as \$f)
                        <label class=\"cursor-pointer\">
                            <input type=\"checkbox\" name=\"fasilitas[]\" value=\"{{ \$f }}\" class=\"peer sr-only\" {{ in_array(\$f, \$checked) ? 'checked' : '' }}>";

$c = str_replace($searchFas, $replaceFas, $c);

// Replace Fasilitas Tambahan block
$searchTambahan = "<textarea rows=\"3\" class=\"form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 placeholder-gray-400 transition-all bg-gray-50 focus:bg-white resize-none\" placeholder=\"Tuliskan fasilitas lain yang belum ada di daftar atas...\"></textarea>";
$replaceTambahan = "<textarea name=\"fasilitas_tambahan\" rows=\"3\" class=\"form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 placeholder-gray-400 transition-all bg-gray-50 focus:bg-white resize-none\" placeholder=\"Tuliskan fasilitas lain yang belum ada di daftar atas...\">{{ old('fasilitas_tambahan', \$pengaturan->fasilitas_tambahan) }}</textarea>";

$c = str_replace($searchTambahan, $replaceTambahan, $c);

file_put_contents($file, $c);
echo "Updated lapangan edit blade";
