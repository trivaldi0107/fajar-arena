<?php
$file = 'resources/views/reservasi/index.blade.php';
$c = file_get_contents($file);

$fasilitasBlock = '
    @php
        $fasilitasArr = json_decode(active_arena()->fasilitas ?? \'[]\', true) ?: [];
        $fasilitasTambahan = active_arena()->fasilitas_tambahan ?? \'\';
    @endphp
    
    @if(count($fasilitasArr) > 0 || $fasilitasTambahan)
    <!-- ================== FASILITAS ARENA ================== -->
    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-4 sm:p-6 md:p-8 mb-8">
        <h3 class="text-xl md:text-2xl font-bold text-gray-800 tracking-tight mb-4">Fasilitas Arena</h3>
        
        @if(count($fasilitasArr) > 0)
        <div class="flex flex-wrap gap-2 mb-4">
            @foreach($fasilitasArr as $fas)
                <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full text-sm font-semibold border border-blue-100 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $fas }}
                </div>
            @endforeach
        </div>
        @endif

        @if($fasilitasTambahan)
        <div class="text-gray-600 text-sm bg-gray-50 p-4 rounded-xl border border-gray-100">
            <span class="font-semibold text-gray-700 block mb-1">Fasilitas Tambahan:</span>
            {{ $fasilitasTambahan }}
        </div>
        @endif
    </div>
    @endif
';

$search = '<!-- ================== TANGGAL ================== -->';
$c = str_replace($search, $fasilitasBlock . "\n    " . $search, $c);

file_put_contents($file, $c);
echo "Added fasilitas to reservasi page";
