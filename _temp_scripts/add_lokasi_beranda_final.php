<?php
$file = 'resources/views/admin/beranda/edit.blade.php';
$c = file_get_contents($file);

$lokasiBlock = '
                <!-- Lokasi & Kontak -->
                <h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Pengaturan Lokasi & Kontak Beranda</h3>
                
                <div class="mb-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Alamat Kantor / Utama</label>
                    <textarea name="alamat" rows="2" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: Jl. Fajar Kemenangan No. 123">{{ old("alamat", $pengaturan->alamat) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Kota</label>
                        <input type="text" name="kota" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: Jakarta" value="{{ old("kota", $pengaturan->kota) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Link Google Maps</label>
                        <input type="text" name="link_maps" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: https://maps.app.goo.gl/..." value="{{ old("link_maps", $pengaturan->link_maps) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Nomor Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: 081234567890" value="{{ old("no_telp", $pengaturan->no_telp) }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-2">Alamat Email</label>
                        <input type="email" name="email" class="form-input w-full px-4 py-3 rounded-lg border border-gray-200 text-gray-800 transition-all bg-white" placeholder="Contoh: cs@fajararena.com" value="{{ old("email", $pengaturan->email) }}">
                    </div>
                </div>
';

$search = '<h3 class="text-lg font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">Media</h3>';
$c = str_replace($search, $lokasiBlock . "\n                  " . $search, $c);
file_put_contents($file, $c);
echo "Lokasi dan kontak beranda added";
