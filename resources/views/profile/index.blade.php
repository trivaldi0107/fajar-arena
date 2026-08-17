<x-app-layout>
<div class="max-w-7xl mx-auto mt-10 px-4 pb-20">

    @auth
        <h1 class="text-xl md:text-2xl font-extrabold mb-6 text-gray-900 tracking-tight">Profil Saya</h1>

        <!-- Informasi Akun -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-8 overflow-hidden hover:shadow-md transition-shadow duration-300">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-4 md:px-6 py-3 border-b border-blue-700">
                <h2 class="text-base md:text-lg font-bold text-white">Informasi Akun</h2>
            </div>
            <div class="p-5 md:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-5">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        @if(auth()->user()->profile_photo_path)
                            <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="{{ auth()->user()->name }}" class="w-16 h-16 md:w-20 md:h-20 rounded-full object-cover shrink-0 border-4 border-gray-50 shadow-sm">
                        @else
                            <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-2xl md:text-3xl shrink-0 border-4 border-gray-50 shadow-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <div class="absolute bottom-0 right-0 w-4 h-4 md:w-5 md:h-5 bg-green-500 border-2 border-white rounded-full"></div>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="text-lg md:text-xl font-bold text-gray-900">{{ auth()->user()->name }}</p>
                            @if(auth()->user()->role === 'admin')
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-200">Administrator</span>
                            @endif
                        </div>
                        <p class="text-sm md:text-base text-gray-500 font-medium mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                <div class="w-full sm:w-auto pt-2 sm:pt-0 flex flex-col sm:flex-row gap-2">
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block w-full sm:w-auto text-center text-sm bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl font-bold shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Ke Panel Admin</span>
                        </a>
                    @endif
                    <a href="{{ route('profile.edit') }}" class="block w-full sm:w-auto text-center text-sm border border-gray-200 px-5 py-2.5 rounded-xl text-gray-700 hover:bg-gray-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm font-semibold">
                        Pengaturan Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Informasi Pesanan -->
        @if(isset($pemesanans) && $pemesanans->count() > 0)
            @foreach($pemesanans as $pesanan)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden hover:shadow-md transition-shadow duration-300">
                <div class="bg-gradient-to-r from-slate-700 to-slate-800 px-4 md:px-6 py-3 md:py-4 border-b border-slate-900 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                    <h2 class="text-base md:text-lg font-bold text-white flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-400">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
                        </svg>
                        Tiket Pesanan
                    </h2>
                    <span class="text-xs md:text-sm text-slate-300 font-mono bg-black/30 px-3 py-1 rounded-full w-fit border border-slate-600/50">#{{ $pesanan->kode_reservasi }}</span>
                </div>
                
                <div class="p-4 md:p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-8">
                        <!-- Kolom Kiri -->
                        <div class="space-y-4">
                            <div class="flex justify-between md:block md:space-y-1 border-b border-gray-100 md:border-0 pb-3 md:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Kategori Olahraga</span>
                                <p class="font-bold text-gray-800 text-sm md:text-base">{{ ucfirst($pesanan->detail->first()?->jadwal->lapangan->pengaturan->jenis_olahraga ?? active_arena()->jenis_olahraga ?? 'Badminton') }}</p>
                            </div>
                            
                            @if($pesanan->detail->count() > 0)
                            <div class="flex justify-between md:block md:space-y-1 border-b border-gray-100 md:border-0 pb-3 md:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Tanggal Bermain</span>
                                <p class="font-bold text-gray-800 text-sm md:text-base">{{ \Carbon\Carbon::parse($pesanan->tanggal_mulai)->translatedFormat('d F Y') }}</p>
                            </div>
                            @endif
                            
                            <div class="flex justify-between md:block md:space-y-1 border-b border-gray-100 md:border-0 pb-3 md:pb-0">
                                <span class="text-sm text-gray-500 font-medium">Durasi Sewa</span>
                                <p class="font-bold text-gray-800 text-sm md:text-base">{{ $pesanan->durasi }} Jam</p>
                            </div>
                            
                            <div class="flex justify-between items-center md:block md:space-y-1">
                                <span class="text-sm text-gray-500 font-medium">Jalur Pemesanan</span>
                                <div class="md:mt-1">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $pesanan->jenis_user == 'member' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-gray-100 text-gray-700 border-gray-300' }}">
                                        {{ $pesanan->jenis_user == 'member' ? 'Member' : 'Non-Member' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="space-y-4 mt-2 md:mt-0">
                            <!-- Box Jadwal -->
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 relative">
                                <div class="absolute -left-2 top-1/2 -translate-y-1/2 w-4 h-8 bg-blue-500 rounded-r-lg"></div>
                                <span class="text-sm text-gray-500 font-bold block mb-3 pl-3 uppercase tracking-wider">Rincian Lapangan</span>
                                <div class="space-y-2.5 pl-3">
                                    @if($pesanan->jenis_user == 'member')
                                        @php
                                            $groupedDetails = $pesanan->detail->groupBy('tanggal');
                                            $mingguKe = 1;
                                        @endphp
                                        @foreach($groupedDetails as $tgl => $details)
                                            <div class="mt-4 first:mt-0">
                                                <span class="inline-block text-xs text-indigo-700 font-bold bg-indigo-50 border border-indigo-100 px-2.5 py-1 rounded-md mb-2">Pekan ke-{{ $mingguKe++ }} — {{ \Carbon\Carbon::parse($tgl)->translatedFormat('d M Y') }}</span>
                                                <div class="space-y-2">
                                                    @foreach($details as $d)
                                                    <div class="flex items-center justify-between text-sm font-bold text-gray-700 bg-white px-4 py-2.5 rounded-lg shadow-sm border border-gray-100">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                                            <span>{{ $d->lapangan->nama_lapangan }}</span>
                                                        </div>
                                                        <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">{{ substr($d->jam_mulai,0,5) }} - {{ substr($d->jam_selesai,0,5) }}</span>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        @foreach($pesanan->detail as $d)
                                        <div class="flex items-center justify-between text-sm font-bold text-gray-700 bg-white px-4 py-2.5 rounded-lg shadow-sm border border-gray-100">
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></div>
                                                <span>{{ $d->lapangan->nama_lapangan }}</span>
                                            </div>
                                            <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md border border-blue-100">{{ substr($d->jam_mulai,0,5) }} - {{ substr($d->jam_selesai,0,5) }}</span>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Box Status -->
                            <div class="flex items-center justify-between bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                                <span class="text-sm text-gray-500 font-bold uppercase tracking-wider">Status Pembayaran</span>
                                <span class="px-3 py-1.5 rounded-md text-xs font-bold uppercase tracking-wider shadow-sm {{ $pesanan->status == 'berhasil' ? 'bg-green-100 text-green-700 border border-green-200' : ($pesanan->status == 'pending' || $pesanan->status == 'menunggu_pembayaran' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-red-100 text-red-700 border border-red-200') }}">
                                    {{ str_replace('_', ' ', $pesanan->status) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($pesanan->status == 'berhasil')
                        <div class="mt-6 pt-5 border-t-2 border-dashed border-gray-200 flex justify-center md:justify-end">
                            <a href="{{ route('tiket', $pesanan->id) }}" class="w-full md:w-auto justify-center bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm inline-flex items-center gap-2 transition-all shadow-md shadow-blue-500/30 hover:-translate-y-0.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat E-Tiket Saya
                            </a>
                        </div>
                    @elseif($pesanan->status == 'batal' || $pesanan->status == 'dibatalkan')
                        @if($pesanan->alasan_penolakan)
                        <div class="mt-4 p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-rose-900 flex items-start gap-3">
                            <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <p class="text-xs text-rose-700 font-semibold leading-relaxed">{{ $pesanan->alasan_penolakan }}</p>
                        </div>
                        @endif
                        <div class="mt-4 pt-4 border-t-2 border-dashed border-gray-200 flex justify-center md:justify-end">
                            <form id="delete-form-{{ $pesanan->id }}" action="{{ route('pemesanan.destroy', $pesanan->id) }}" method="POST" class="w-full md:w-auto">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDeleteTicket('delete-form-{{ $pesanan->id }}')" class="w-full md:w-auto justify-center bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 px-6 py-2.5 rounded-xl font-bold text-sm inline-flex items-center gap-2 transition-all cursor-pointer">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Riwayat
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
            @endforeach
        @else
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-10 text-center mt-8">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-10 h-10 text-gray-300">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Belum ada riwayat pesanan tiket Anda.</p>
                <a href="{{ route('reservasi') }}" class="mt-6 inline-block bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition font-bold shadow-md shadow-blue-500/30">Mulai Pesan Lapangan</a>
            </div>
        @endif

    @else
        <style>
            @keyframes fadeUpBounce {
                0% { opacity: 0; transform: translateY(40px) scale(0.95); }
                60% { opacity: 1; transform: translateY(-10px) scale(1.02); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }
            @keyframes floatingLock {
                0% { transform: translateY(0px) rotate(0deg); }
                25% { transform: translateY(-5px) rotate(-3deg); }
                50% { transform: translateY(-10px) rotate(0deg); }
                75% { transform: translateY(-5px) rotate(3deg); }
                100% { transform: translateY(0px) rotate(0deg); }
            }
            .animate-fade-up-bounce {
                animation: fadeUpBounce 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
            }
            .animate-floating-lock {
                animation: floatingLock 4s ease-in-out infinite;
            }
        </style>
        <div class="text-center mt-20 bg-white p-10 rounded-3xl border border-gray-100 shadow-xl shadow-blue-900/5 max-w-lg mx-auto animate-fade-up-bounce hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-500 hover:-translate-y-2">
            <div class="w-24 h-24 bg-gradient-to-tr from-blue-50 to-indigo-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner relative group">
                <div class="absolute inset-0 bg-blue-100 rounded-full blur opacity-0 group-hover:opacity-50 transition-opacity duration-500"></div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 text-blue-500 animate-floating-lock relative z-10">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
            <h1 class="text-2xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-gray-900 to-gray-700 tracking-tight">Belum Login</h1>
            <p class="mt-3 text-gray-500 font-medium leading-relaxed">Silakan login atau daftar terlebih dahulu untuk dapat mengakses pengaturan profil dan riwayat pesanan Anda.</p>
            <a href="/login" class="mt-8 block w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 bg-[length:200%_auto] text-white px-6 py-3.5 rounded-xl font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:-translate-y-1 hover:bg-[center_right_1rem] transition-all duration-300 text-lg">
                Masuk Akun
            </a>
        </div>
    @endauth

</div>

<script>
    function confirmDeleteTicket(formId) {
        Swal.fire({
            title: 'Hapus Riwayat Tiket?',
            text: 'Apakah Anda yakin ingin menghapus tiket ini secara permanen dari riwayat Anda?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-3xl shadow-2xl border border-gray-100 p-6',
                title: 'text-gray-900 font-extrabold text-xl',
                confirmButton: 'px-6 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-red-500/20 cursor-pointer',
                cancelButton: 'px-6 py-2.5 rounded-xl text-sm font-bold cursor-pointer'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: {
                popup: 'rounded-3xl shadow-xl border border-gray-100 p-5'
            }
        });
    @endif
</script>

</x-app-layout>