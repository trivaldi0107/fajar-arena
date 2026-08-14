<?php

// 1. Update Pengaturan.php
$pengaturanFile = 'app/Models/Pengaturan.php';
$pengaturanContent = file_get_contents($pengaturanFile);
if (strpos($pengaturanContent, "'prefix_lapangan',") === false) {
    $pengaturanContent = str_replace("'slug',", "'slug',\n        'prefix_lapangan',", $pengaturanContent);
    file_put_contents($pengaturanFile, $pengaturanContent);
}

// 2. Update edit.blade.php
$editFile = 'resources/views/admin/lapangan/edit.blade.php';
$editContent = file_get_contents($editFile);
$prefixFieldEdit = <<<EOF
                      <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-2">Prefix Lapangan</label>
                          <input type="text" name="prefix_lapangan" placeholder="Contoh: Lapangan, Court, Meja" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('prefix_lapangan', \$pengaturan->prefix_lapangan ?? 'Lapangan') }}">
                      </div>
EOF;

if (strpos($editContent, "name=\"prefix_lapangan\"") === false) {
    $editContent = str_replace(
        "<div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">",
        "<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6\">\n" . $prefixFieldEdit,
        $editContent
    );
    file_put_contents($editFile, $editContent);
}

// 3. Update create.blade.php
$createFile = 'resources/views/admin/lapangan/create.blade.php';
if (file_exists($createFile)) {
    $createContent = file_get_contents($createFile);
    $prefixFieldCreate = <<<EOF
                      <div>
                          <label class="block text-sm font-semibold text-gray-700 mb-2">Prefix Lapangan</label>
                          <input type="text" name="prefix_lapangan" placeholder="Contoh: Lapangan, Court, Meja" class="form-input w-full px-4 py-3 rounded-xl border border-gray-200 text-gray-800 transition-all bg-gray-50 focus:bg-white" value="{{ old('prefix_lapangan', 'Lapangan') }}">
                      </div>
EOF;
    if (strpos($createContent, "name=\"prefix_lapangan\"") === false) {
        $createContent = str_replace(
            "<div class=\"grid grid-cols-1 md:grid-cols-2 gap-6\">",
            "<div class=\"grid grid-cols-1 md:grid-cols-3 gap-6\">\n" . $prefixFieldCreate,
            $createContent
        );
        file_put_contents($createFile, $createContent);
    }
}

echo "Files patched successfully!\n";
