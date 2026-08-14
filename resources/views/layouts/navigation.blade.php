<nav class="bg-white/90 backdrop-blur-xl border-b border-gray-100 sticky top-0 z-50 transition-all duration-300 shadow-sm" x-data="{ open: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 md:py-4 flex justify-between items-center relative">

        <!-- LOGO -->
        <a href="/" class="flex items-center gap-2 group">
            <span class="text-gray-800 font-extrabold text-lg md:text-xl tracking-tight group-hover:text-blue-600 transition-colors duration-300">{{ active_arena()->navbar_name ?? 'Fajar Arena' }}</span>
        </a>

        <!-- DESKTOP MENU -->
        <div class="hidden md:flex items-center gap-2">
            <a href="/"
               class="px-4 py-2 rounded-xl transition-all duration-300 text-sm font-medium {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Beranda
            </a>

            <a href="/pilih-cabang"
               class="px-4 py-2 rounded-xl transition-all duration-300 text-sm font-medium {{ request()->is('reservasi*') || request()->is('pilih-cabang*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Pemesanan
            </a>

            <!--  PROFIL SELALU MUNCUL -->
            <a href="/profile"
               class="px-4 py-2 rounded-xl transition-all duration-300 text-sm font-medium {{ request()->is('profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                Profil
            </a>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}"
               class="px-4 py-2 rounded-xl transition-all duration-300 text-sm font-bold bg-blue-600 text-white hover:bg-blue-700 shadow-md shadow-blue-500/20 flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Panel Admin</span>
            </a>
            @endif

            <div class="w-px h-6 bg-gray-200 mx-2"></div>

            @auth
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button class="px-5 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-300 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Logout
                </button>
            </form>
            @else
            <a href="/login"
               class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-2.5 rounded-xl font-semibold text-sm shadow-md shadow-blue-500/30 hover:shadow-lg hover:shadow-blue-500/40 hover:-translate-y-0.5 transition-all duration-300 ml-2">
                Login
            </a>
            @endauth
        </div>

        <!-- HAMBURGER ICON (MOBILE) -->
        <div class="md:hidden flex items-center">
            <button @click="open = !open" type="button"
                class="w-10 h-10 rounded-xl flex flex-col justify-center items-center gap-1.5 transition-all duration-300 focus:outline-none"
                :class="open ? 'bg-blue-50' : 'bg-gray-50'">
                <span class="block w-5 h-0.5 bg-gray-600 transition-transform duration-300 origin-center" :class="open ? 'rotate-45 translate-y-[8px]' : ''"></span>
                <span class="block w-5 h-0.5 bg-gray-600 transition-opacity duration-300" :class="open ? 'opacity-0' : ''"></span>
                <span class="block w-5 h-0.5 bg-gray-600 transition-transform duration-300 origin-center" :class="open ? '-rotate-45 -translate-y-[8px]' : ''"></span>
            </button>
        </div>
    </div>

    <!-- MOBILE MENU DROPDOWN -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        @click.away="open = false"
        class="md:hidden absolute w-full left-0 top-full bg-white/95 backdrop-blur-xl border-b border-gray-100 shadow-[0_20px_40px_rgb(0,0,0,0.1)]"
        style="display: none;">
        
        <div class="px-4 py-4 space-y-1.5 max-h-[80vh] overflow-y-auto">
            <a href="/" class="block px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->is('/') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Beranda</a>
            <a href="/pilih-cabang" class="block px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->is('reservasi*') || request()->is('pilih-cabang*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Pemesanan</a>
            <a href="/profile" class="block px-4 py-3 rounded-xl font-medium transition-all duration-200 {{ request()->is('profile*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Profil</a>

            @if(auth()->check() && auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 rounded-xl font-bold bg-blue-600 text-white shadow-md shadow-blue-500/20 text-center">Panel Admin 🛠️</a>
            @endif

            <div class="border-t border-gray-100 my-3 pt-3">
                @auth
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl font-semibold text-red-600 bg-red-50 hover:bg-red-100 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        Logout
                    </button>
                </form>
                @else
                <a href="/login" class="flex justify-center items-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold shadow-md shadow-blue-500/30 hover:shadow-lg transition-all duration-200">
                    Login
                </a>
                @endauth
            </div>
        </div>
    </div>
</nav>