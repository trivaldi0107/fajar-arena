<?php
$file = 'resources/views/reservasi/index.blade.php';
$c = file_get_contents($file);

$startMarker = '<!-- ================== INFO ARENA ================== -->';
$endMarker = '@endguest';

$startPos = strpos($c, $startMarker);
$endPos = strpos($c, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $oldBlock = substr($c, $startPos, $endPos - $startPos);
    
    $premiumBlock = '<!-- ================== PREMIUM INFO ARENA ================== -->
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-[2rem] shadow-2xl shadow-indigo-900/20 p-6 sm:p-8 md:p-10 mb-10 mt-12 group border border-white/10">
        <!-- Background Decorations -->
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-72 h-72 rounded-full bg-blue-500 opacity-10 blur-3xl group-hover:opacity-20 transition-opacity duration-700 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 rounded-full bg-indigo-500 opacity-10 blur-3xl group-hover:opacity-20 transition-opacity duration-700 pointer-events-none"></div>
        
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-10">
            <!-- Lokasi & Kontak -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-inner">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="font-extrabold text-white text-xl tracking-wide">Info & Kontak</h3>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-4 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all duration-300 backdrop-blur-sm">
                        <svg class="w-5 h-5 text-indigo-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        <div>
                            <p class="text-indigo-200/70 text-xs font-semibold uppercase tracking-wider mb-1">Alamat Arena</p>
                            <p class="text-white text-sm leading-relaxed font-medium">{{ $arena->alamat ?: \'-\' }} {{ $arena->kota ? \', \' . $arena->kota : \'\' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all duration-300 backdrop-blur-sm">
                            <svg class="w-5 h-5 text-emerald-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <div>
                                <p class="text-emerald-200/70 text-xs font-semibold uppercase tracking-wider mb-1">Telepon/WA</p>
                                <p class="text-white text-sm font-medium">{{ $arena->no_telp ?: \'-\' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 hover:border-white/10 transition-all duration-300 backdrop-blur-sm overflow-hidden">
                            <svg class="w-5 h-5 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <div class="min-w-0">
                                <p class="text-amber-200/70 text-xs font-semibold uppercase tracking-wider mb-1">Email</p>
                                <p class="text-white text-sm font-medium truncate" title="{{ $arena->email }}">{{ $arena->email ?: \'-\' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fasilitas -->
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl bg-white/5 backdrop-blur-md flex items-center justify-center border border-white/10 shadow-inner">
                        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h3 class="font-extrabold text-white text-xl tracking-wide">Fasilitas Arena</h3>
                </div>

                <div class="bg-white/5 border border-white/5 hover:border-white/10 transition-colors duration-300 rounded-3xl p-5 sm:p-6 backdrop-blur-sm h-[calc(100%-4.5rem)] flex flex-col">
                    @if(count($fasilitasArr) > 0)
                        <div class="flex flex-wrap gap-3 mb-5">
                            @foreach($fasilitasArr as $fas)
                                <div class="px-4 py-2 bg-gradient-to-r from-blue-600/20 to-indigo-600/20 text-blue-50 rounded-xl text-sm font-medium border border-blue-400/20 flex items-center gap-2 shadow-lg shadow-black/10 hover:-translate-y-0.5 hover:border-blue-400/40 hover:from-blue-600/30 hover:to-indigo-600/30 transition-all duration-300">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-400 shadow-[0_0_8px_rgba(96,165,250,0.8)]"></div>
                                    {{ $fas }}
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-white/40 text-sm italic mb-5 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Belum ada informasi fasilitas utama.
                        </p>
                    @endif

                    @if($fasilitasTambahan)
                        <div class="relative overflow-hidden rounded-2xl bg-white/5 border border-white/10 p-5 mt-auto">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-purple-500 to-indigo-500"></div>
                            <span class="font-semibold text-purple-300/80 text-xs uppercase tracking-widest block mb-2">Fasilitas Ekstra</span>
                            <p class="text-white/80 text-sm leading-relaxed">{{ $fasilitasTambahan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

';
    
    $c = str_replace($oldBlock, $premiumBlock, $c);
    file_put_contents($file, $c);
    echo "Replaced properly using substr and str_replace";
} else {
    echo "Could not find markers";
}
