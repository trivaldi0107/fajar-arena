<?php
$file = 'resources/views/reservasi/index.blade.php';
$c = file_get_contents($file);

$strayBlock = '
        @if($fasilitasTambahan)
        <div class="text-gray-600 text-sm bg-gray-50 p-4 rounded-xl border border-gray-100">
            <span class="font-semibold text-gray-700 block mb-1">Fasilitas Tambahan:</span>
            {{ $fasilitasTambahan }}
        </div>
        @endif
    </div>
    @endif
';

$c = str_replace($strayBlock, '', $c);
file_put_contents($file, $c);
echo "Removed stray block from reservasi index";
