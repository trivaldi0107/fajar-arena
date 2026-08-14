<?php
$adminFile = 'app/Http/Controllers/AdminController.php';
$adminContent = file_get_contents($adminFile);

$replacement = <<<EOF
            // Update harga semua lapangan (dan mungkin prefix jika diperlukan di masa depan)
            \App\Models\Lapangan::where('pengaturan_id', \$pengaturan->id)->update(['harga_per_jam' => \$pengaturan->harga_per_jam]);

            // Update nama lapangan sesuai prefix baru jika ada
            if (\$pengaturan->prefix_lapangan) {
                \$semuaLapangan = \App\Models\Lapangan::where('pengaturan_id', \$pengaturan->id)->orderBy('id')->get();
                foreach (\$semuaLapangan as \$index => \$lap) {
                    \$lap->nama_lapangan = \$pengaturan->prefix_lapangan . ' ' . (\$index + 1);
                    \$lap->save();
                }
            }
EOF;

if (strpos($adminContent, "Update nama lapangan sesuai prefix baru") === false) {
    $adminContent = str_replace(
        "// Update harga semua lapangan (dan mungkin prefix jika diperlukan di masa depan)\n            \App\Models\Lapangan::where('pengaturan_id', \$pengaturan->id)->update(['harga_per_jam' => \n\$pengaturan->harga_per_jam]);",
        $replacement,
        $adminContent
    );
    // Also try without line break
    $adminContent = str_replace(
        "// Update harga semua lapangan (dan mungkin prefix jika diperlukan di masa depan)\n            \App\Models\Lapangan::where('pengaturan_id', \$pengaturan->id)->update(['harga_per_jam' => \$pengaturan->harga_per_jam]);",
        $replacement,
        $adminContent
    );
    file_put_contents($adminFile, $adminContent);
}

echo "AdminController patched for renaming courts.\n";
