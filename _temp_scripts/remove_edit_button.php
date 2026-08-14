<?php
$file = 'resources/views/admin/lapangan/edit.blade.php';
$content = file_get_contents($file);

$oldHeader = <<<EOF
<!-- Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Pengaturan Arena</h2>
    </div>
    <div>
        @if(isset(\$pengaturan->id) && \App\Models\Pengaturan::count() > 1)
        <button type="button" onclick="confirmDeleteCabang()" class="px-5 py-2 rounded-xl font-bold text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition-all border border-red-100 shadow-sm flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            Hapus Cabang Ini
        </button>
        @endif
    </div>
</div>
EOF;

$newHeader = <<<EOF
<!-- Header -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Pengaturan Arena</h2>
    </div>
</div>
EOF;

$content = str_replace($oldHeader, $newHeader, $content);
file_put_contents($file, $content);
echo "Removed delete button from edit.blade.php\n";
