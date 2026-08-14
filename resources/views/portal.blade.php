<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pilih Cabang Olahraga - Fajar Arena</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<body class="font-sans antialiased overflow-y-scroll bg-gray-50 text-gray-900 selection:bg-blue-500 selection:text-white flex flex-col min-h-screen">
    
    @include('layouts.navigation')

    <div class="flex-grow flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        
        <!-- HEADER -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">
                Selamat Datang di <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Fajar Arena</span>
            </h1>
            <p class="text-lg text-gray-500 max-w-2xl mx-auto">
                Platform reservasi olahraga terbaik. Silakan pilih cabang olahraga yang ingin Anda tuju.
            </p>
        </div>

        <!-- ARENA GRID -->
        <div class="w-full max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                
                @forelse($arenas as $arena)
                <a href="{{ route('set-arena', $arena->slug) }}" class="group block relative rounded-3xl overflow-hidden bg-white shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
                    <!-- Image -->
                    <div class="aspect-w-16 aspect-h-10 relative bg-gray-100 overflow-hidden h-56">
                        @if($arena->gambar_utama)
                            <img src="{{ asset('storage/' . $arena->gambar_utama) }}" alt="{{ $arena->nama_arena }}" class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-indigo-600"></div>
                            <div class="absolute inset-0 flex items-center justify-center opacity-20 group-hover:scale-110 transition-transform duration-700">
                                <i class="fa-solid fa-medal text-8xl text-white"></i>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                        
                        <!-- Badge Sport Type -->
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 rounded-full text-white text-xs font-semibold uppercase tracking-wider">
                                {{ $arena->jenis_olahraga }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-3">
                            <h2 class="text-2xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $arena->nama_arena }}</h2>
                        </div>
                        <p class="text-gray-500 mb-6 line-clamp-2 text-sm leading-relaxed">
                            {{ $arena->deskripsi ?? 'Nikmati fasilitas ' . $arena->jenis_olahraga . ' terbaik dengan pelayanan premium.' }}
                        </p>
                        
                        <!-- Footer details -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center text-gray-500 text-sm font-medium">
                                <i class="fa-solid fa-map-location-dot mr-2 text-blue-500"></i>
                                {{ $arena->kota ?? 'Lokasi' }}
                            </div>
                            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-50 text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @empty
                <div class="col-span-full text-center py-12">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i class="fa-solid fa-circle-exclamation text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Belum ada cabang olahraga</h3>
                    <p class="text-gray-500">Silakan tambahkan cabang olahraga melalui panel admin.</p>
                </div>
                @endforelse

            </div>
        </div>

    </div>

</body>
</html>
