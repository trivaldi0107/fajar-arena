<?php
$file = 'resources/views/reservasi/index.blade.php';
$c = file_get_contents($file);

$startMarker = '<!-- ================== PREMIUM INFO ARENA ================== -->';
$endMarker = '@endguest';

$startPos = strpos($c, $startMarker);
$endPos = strpos($c, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $oldBlock = substr($c, $startPos, $endPos - $startPos);
    
    $cleanBlock = '<!-- ================== PREMIUM INFO ARENA ================== -->
    <div class="bg-white border border-gray-100 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] px-6 py-5 mt-12 mb-8 rounded-2xl flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        
        <!-- Lokasi & Kontak (Kiri) -->
        <div class="flex flex-col md:flex-row gap-6 md:gap-10">
            <!-- Alamat -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Lokasi Arena</p>
                    <p class="text-sm font-bold text-gray-800 leading-relaxed">{{ $arena->alamat ?: \'-\' }}<br>{{ $arena->kota ? $arena->kota : \'\' }}</p>
                </div>
            </div>

            <!-- Kontak -->
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center shrink-0 border border-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Kontak Resmi</p>
                    <p class="text-sm font-bold text-gray-800 mb-0.5">{{ $arena->no_telp ?: \'-\' }}</p>
                    <p class="text-sm text-gray-500">{{ $arena->email ?: \'-\' }}</p>
                </div>
            </div>
        </div>

        <!-- Fasilitas (Kanan) -->
        <div class="flex items-start gap-4 lg:border-l lg:border-gray-100 lg:pl-8">
             <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center shrink-0 border border-purple-100">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Fasilitas Tersedia</p>
                <div class="flex flex-wrap gap-2 lg:max-w-xs">
                    @if(count($fasilitasArr) > 0)
                        @foreach($fasilitasArr as $fas)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200">
                                {{ $fas }}
                            </span>
                        @endforeach
                    @else
                        <span class="text-sm text-gray-500 italic">Belum ada informasi</span>
                    @endif
                    
                    @if($fasilitasTambahan)
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100" title="{{ $fasilitasTambahan }}">
                            + Ekstra
                        </span>
                    @endif
                </div>
            </div>
        </div>
        
    </div>

';
    
    $c = str_replace($oldBlock, $cleanBlock, $c);
    file_put_contents($file, $c);
    echo "Replaced with clean white navbar style";
} else {
    echo "Could not find markers";
}
