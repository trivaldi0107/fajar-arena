@extends('admin.layouts.app')

@section('title', 'Kelola Lapangan - Fajar Arena')

@section('content')

<!-- Header -->
<div class="mb-6 flex justify-between items-end">
    <div>
        <h2 class="text-3xl font-bold text-gray-800">Kelola Lapangan</h2>
    </div>
</div>

<div class="max-w-6xl mx-auto">

    <!-- Grid Container -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Loop through Arenas -->
        @foreach($pengaturans as $pengaturan)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 overflow-hidden group relative flex flex-col h-full">
            <a href="{{ route('admin.lapangan.edit', $pengaturan->id) }}" class="flex-1 flex flex-col">
                <div class="h-40 shrink-0 flex items-center justify-center relative overflow-hidden bg-gray-100">
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
                    <p class="text-sm text-gray-500 mb-4 line-clamp-2">{{ $pengaturan->deskripsi ?? 'Pusat olahraga terbaik dengan fasilitas premium.' }}</p>
                    <div class="flex items-center gap-4 text-sm text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span class="font-medium text-gray-700">{{ $pengaturan->lapangan_count ?? 0 }}</span> Lapangan
                        </div>
                    </div>
                </div>
            </a>
            
            <!-- Actions -->
            <div class="absolute top-4 right-4 flex items-center gap-2 z-10">
                <span class="px-2.5 py-1 bg-white/90 text-green-600 text-xs font-bold rounded-lg shadow-sm border border-green-100">Aktif</span>
                @if(\App\Models\Pengaturan::count() > 1)
                <form id="delete-form-{{ $pengaturan->id }}" action="{{ route('admin.lapangan.destroy', $pengaturan->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="button" onclick="confirmDelete('{{ $pengaturan->id }}')" class="p-1.5 bg-white/90 text-red-500 hover:text-white hover:bg-red-500 shadow-sm rounded-lg transition-all" title="Hapus Lapangan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endforeach

        <!-- Box 2: Tambah Lapangan (Add New) -->
        <a href="{{ route('admin.lapangan.create') }}" class="group h-full min-h-[300px]">
            <div class="h-full bg-slate-50 rounded-2xl border-2 border-dashed border-gray-300 hover:border-blue-400 hover:bg-blue-50/50 transition-all duration-300 flex flex-col items-center justify-center p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-white shadow-sm border border-gray-200 flex items-center justify-center mb-4 group-hover:scale-110 group-hover:bg-blue-600 group-hover:border-blue-600 group-hover:text-white transition-all duration-300 text-gray-400">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-700 group-hover:text-blue-700 transition-colors">Tambah Lapangan</h3>
                <p class="text-sm text-gray-500 mt-2">Buat profil arena atau lapangan baru</p>
            </div>
        </a>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Cabang Olahraga?',
            text: "Semua jadwal, transaksi, dan data lapangan pada cabang ini akan ikut terhapus secara permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Permanen!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>

@endsection
