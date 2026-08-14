<?php
$file = 'resources/views/reservasi/index.blade.php';
$c = file_get_contents($file);

// Remove the old fasilitas block
$pattern = '/\s*@php\s*\$fasilitasArr = json_decode\(active_arena\(\)->fasilitas \?\? \'\[\]\', true\) \?\: \[\];\s*\$fasilitasTambahan = active_arena\(\)->fasilitas_tambahan \?\? \'\';\s*@endphp\s*@if\(count\(\$fasilitasArr\) > 0 \|\| \$fasilitasTambahan\).*?<!-- ================== FASILITAS ARENA ================== -->.*?<\/div>\s*@endif/s';
$c = preg_replace($pattern, '', $c);

// Build the new combined block
$newBlock = '

    @php
        $fasilitasArr = json_decode(active_arena()->fasilitas ?? \'[]\', true) ?: [];
        $fasilitasTambahan = active_arena()->fasilitas_tambahan ?? \'\';
        $arena = active_arena();
    @endphp

    <!-- ================== INFO ARENA ================== -->
    <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-4 sm:p-6 mb-8 flex flex-col md:flex-row gap-8">
        
        <!-- Lokasi & Kontak -->
        <div class="flex-1">
            <h3 class="font-bold text-gray-800 mb-4 text-base md:text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Lokasi & Kontak
            </h3>
            <div class="space-y-3 text-sm text-gray-600">
                <p><strong>Alamat:</strong> {{ $arena->alamat ?: \'-\' }} {{ $arena->kota ? \', \' . $arena->kota : \'\' }}</p>
                <p><strong>Telepon:</strong> {{ $arena->no_telp ?: \'-\' }}</p>
                <p><strong>Email:</strong> {{ $arena->email ?: \'-\' }}</p>
                @if($arena->link_maps)
                <a href="{{ $arena->link_maps }}" target="_blank" class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-700 font-medium mt-1">
                    Lihat di Google Maps
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
                @endif
            </div>
        </div>

        <!-- Fasilitas -->
        <div class="flex-1">
            <h3 class="font-bold text-gray-800 mb-4 text-base md:text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Fasilitas
            </h3>
            @if(count($fasilitasArr) > 0)
            <div class="flex flex-wrap gap-2 mb-3">
                @foreach($fasilitasArr as $fas)
                    <div class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold border border-blue-100 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ $fas }}
                    </div>
                @endforeach
            </div>
            @else
            <p class="text-sm text-gray-500 italic mb-3">Belum ada informasi fasilitas</p>
            @endif

            @if($fasilitasTambahan)
            <div class="text-xs text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100">
                <span class="font-semibold text-gray-700 block mb-1">Tambahan:</span>
                {{ $fasilitasTambahan }}
            </div>
            @endif
        </div>
        
    </div>';

// Find the position to insert (before @endif and @endguest block at the end)
// We will look for:
//       @endif
//   
//       </div>
//   @endguest
//   </div>

$searchEnd = "    @endif\n\n    </div>\n@endguest\n</div>";
$replaceEnd = $newBlock . "\n\n" . $searchEnd;

$c = str_replace($searchEnd, $replaceEnd, $c);

file_put_contents($file, $c);
echo "Moved and combined block!";
