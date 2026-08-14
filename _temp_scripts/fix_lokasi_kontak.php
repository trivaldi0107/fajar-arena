<?php
$file = 'resources/views/admin/beranda/edit.blade.php';
$c = file_get_contents($file);

$search = '                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kota</label>
                        <input type="text" name="kota" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: Jakarta" value="{{ old("kota", $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Link Google Maps</label>
                        <input type="text" name="link_maps" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: https://maps.app.goo.gl/..." value="{{ old("link_maps", $pengaturan->link_maps) }}">
                    </div>
                </div>';

$replace = '                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kota</label>
                        <input type="text" name="kota" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: Jakarta" value="{{ old("kota", $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Link Google Maps</label>
                        <input type="text" name="link_maps" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: https://maps.app.goo.gl/..." value="{{ old("link_maps", $pengaturan->link_maps) }}">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: 081234567890" value="{{ old("no_telp", $pengaturan->no_telp) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Alamat Email</label>
                        <input type="email" name="email" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: cs@fajararena.com" value="{{ old("email", $pengaturan->email) }}">
                    </div>
                </div>';

$c = str_replace($search, $replace, $c);
file_put_contents($file, $c);
echo "Added email and no_telp to beranda edit blade. ";

// Update BerandaAdminController
$controller = 'app/Http/Controllers/BerandaAdminController.php';
$c2 = file_get_contents($controller);
$c2 = str_replace(
    "'fitur_judul', 'fitur_deskripsi', 'fitur_cards', 'alamat', 'kota', 'link_maps'",
    "'fitur_judul', 'fitur_deskripsi', 'fitur_cards', 'alamat', 'kota', 'link_maps', 'no_telp', 'email'",
    $c2
);
file_put_contents($controller, $c2);
echo "Updated BerandaAdminController. ";

// Remove Maps link from reservasi/index.blade.php
$resvFile = 'resources/views/reservasi/index.blade.php';
$c3 = file_get_contents($resvFile);
// We will regex replace the @if($arena->link_maps) block
$c3 = preg_replace('/@if\(\$arena->link_maps\).*?@endif/s', '', $c3, 1);
file_put_contents($resvFile, $c3);
echo "Removed maps link from reservasi page. ";

