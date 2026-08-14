<?php
$file = 'resources/views/admin/beranda/edit.blade.php';
$c = file_get_contents($file);

$alamatBlock = '
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Alamat Kantor / Utama (Beranda)</label>
                    <textarea name="alamat" rows="3" class="form-textarea w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white resize-none" placeholder="Masukkan alamat lengkap...">{{ old("alamat", $pengaturan->alamat) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kota</label>
                        <input type="text" name="kota" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: Jakarta" value="{{ old("kota", $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Link Google Maps</label>
                        <input type="text" name="link_maps" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: https://maps.app.goo.gl/..." value="{{ old("link_maps", $pengaturan->link_maps) }}">
                    </div>
                </div>
';

$search = '<div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-4">Slider Beranda</label>';

if (strpos($c, 'name="alamat"') === false) {
    $c = str_replace($search, $alamatBlock . "\n                " . $search, $c);
    file_put_contents($file, $c);
    echo "Alamat inserted in beranda";
} else {
    echo "Alamat already exists";
}
