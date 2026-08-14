<x-app-layout>

<style>
    .success-animation {
        animation: scale-in 0.5s ease-out forwards, float 3s ease-in-out infinite alternate;
    }
    
    @keyframes scale-in {
        0% { transform: scale(0); opacity: 0; }
        60% { transform: scale(1.1); }
        100% { transform: scale(1); opacity: 1; }
    }

    @keyframes float {
        0% { transform: translateY(0); }
        100% { transform: translateY(-10px); }
    }

    .pulse-ring {
        animation: pulse-ring 2s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
    }

    @keyframes pulse-ring {
        0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 20px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }
</style>

<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-green-50">
    
    <!-- Background Decor -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-40 pointer-events-none">
        <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-gradient-to-br from-green-200 to-emerald-400 blur-3xl opacity-50"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-gradient-to-tr from-blue-200 to-indigo-400 blur-3xl opacity-50"></div>
    </div>

    <div class="relative z-10 w-full max-w-md">
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/50">
            
            <div class="px-8 pt-12 pb-8 text-center relative">
                
                <!-- Icon with Pulse Ring -->
                <div class="mx-auto w-24 h-24 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center mb-8 shadow-lg shadow-green-500/40 success-animation pulse-ring relative z-20">
                    <svg class="w-12 h-12 text-white drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600 mb-2">
                    Pembayaran Berhasil!
                </h1>
                
                <p class="text-gray-500 text-sm font-medium">
                    Hore! Transaksi Anda telah kami terima.
                </p>

            </div>

            <!-- Receipt Detail Box -->
            <div class="px-8 py-6 bg-gradient-to-b from-gray-50/50 to-gray-100/80 border-t border-gray-100">
                
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-100 border-dashed">
                        <span class="text-gray-400 text-sm font-medium">Kode Booking</span>
                        <span class="text-gray-800 font-bold font-mono tracking-wider">{{ $pemesanan->kode_reservasi }}</span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-sm font-medium">Tanggal Main</span>
                        <span class="text-gray-800 font-semibold">{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="mt-8">
                    <a href="{{ route('tiket', $pemesanan->id) }}"
                       class="group relative flex w-full justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-2xl text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-xl shadow-blue-500/30 hover:shadow-2xl hover:shadow-blue-500/40 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                        
                        <span class="relative z-10 flex items-center">
                            Lihat Tiket Saya
                            <svg class="ml-2 w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </span>
                        
                        <!-- Shine effect -->
                        <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite]"></div>
                    </a>
                </div>

                <!-- Back to Home -->
                <div class="mt-4 text-center">
                    <a href="{{ route('beranda') }}" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition-colors">
                        Kembali ke Beranda
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

<style>
    @keyframes shimmer {
        100% { transform: translateX(100%); }
    }
</style>

</x-app-layout>