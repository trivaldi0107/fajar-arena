@extends('admin.layouts.app')

@section('title', 'Pengaturan Beranda - Fajar Arena')

@section('content')

<!-- Header -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Pengaturan Beranda</h2>
        <p class="text-gray-500 mt-2">Pilih cabang olahraga yang ingin diatur berandanya (Landing Page).</p>
    </div>
</div>

<div class="max-w-6xl mx-auto">

    <!-- Grid Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Loop through Arenas -->
        @foreach($pengaturans as $pengaturan)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 overflow-hidden group relative">
            <a href="{{ route('admin.beranda.edit', $pengaturan->id) }}" class="block">
                <div class="h-40 flex items-center justify-center relative overflow-hidden bg-gray-100">
                    @if($pengaturan->gambar_utama)
                        <img src="{{ asset('storage/' . $pengaturan->gambar_utama) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $pengaturan->nama_arena }}">
                    @else
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600 opacity-90 group-hover:scale-105 transition-transform duration-500"></div>
                        <svg class="w-16 h-16 text-white/50 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    @endif
                </div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-2">
                        <h3 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ $pengaturan->nama_arena }}</h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">Klik untuk mengatur banner, slider, dan pengumuman untuk beranda {{ $pengaturan->nama_arena }}.</p>
                    <div class="flex items-center gap-4 text-sm text-blue-600 font-semibold group-hover:text-blue-700">
                        Atur Landing Page &rarr;
                    </div>
                </div>
            </a>
            
        </div>
        @endforeach

    </div>

</div>

@endsection
