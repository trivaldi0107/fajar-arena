@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-8">

    <!-- Welcome -->

    <div>

        <h2 class="text-3xl font-bold text-gray-800">
            Dashboard
        </h2>

        <p class="text-gray-500 mt-2">
            Selamat datang kembali, Admin Fajar Arena
        </p>

    </div>

    <!-- Statistik -->

    <div class="grid grid-cols-3 gap-2 sm:gap-4 md:gap-6">

        <!-- Lapangan -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-sm p-3 md:p-6 flex flex-col justify-between">
            <p class="text-gray-500 text-xs md:text-sm font-semibold leading-tight">
                Lapangan Aktif
            </p>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-blue-600 mt-2 md:mt-4">
                {{ $lapanganAktif }}
            </h1>
        </div>

        <!-- Reservasi -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-sm p-3 md:p-6 flex flex-col justify-between">
            <p class="text-gray-500 text-xs md:text-sm font-semibold leading-tight">
                Pesanan Hari Ini
            </p>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-green-600 mt-2 md:mt-4">
                {{ $reservasiHariIni }}
            </h1>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-xl md:rounded-2xl shadow-sm p-3 md:p-6 flex flex-col justify-between">
            <p class="text-gray-500 text-xs md:text-sm font-semibold leading-tight">
                Reservasi Pending
            </p>
            <h1 class="text-2xl sm:text-3xl md:text-5xl font-bold text-orange-500 mt-2 md:mt-4">
                {{ $pending }}
            </h1>
        </div>

    </div>

<style>
    /* Menyembunyikan scrollbar bawaan browser agar grafik tampil bersih */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

    <!-- Grafik -->
    <div class="bg-white rounded-3xl shadow-sm p-8 mb-8 border border-slate-100">

        <h3 class="text-xl font-semibold mb-6 text-slate-800">
            Aktivitas Reservasi
        </h3>

        <!-- Outer wrapper for padding to prevent scroll leaking -->
        <div class="border border-slate-100 rounded-2xl bg-white p-4 shadow-inner">
            <!-- Wrapper Scroll Native (X dan Y) yang sangat mulus -->
            <div class="w-full overflow-auto no-scrollbar cursor-grab" id="mainScrollWrapper" style="height: 350px;">
                
                <!-- Container internal yang lebarnya memanjang -->
                <div class="flex relative" id="innerContainer">
                    
                    <!-- Sticky Y-Axis: Menempel di kiri, lebar 64px, warna putih bersih -->
                    <div class="w-16 flex-shrink-0 sticky left-0 z-20 bg-white border-r border-slate-200 shadow-[4px_0_10px_rgba(0,0,0,0.02)]" id="yAxisWrapper">
                        <canvas id="yAxisChart"></canvas>
                    </div>

                    <!-- Main Chart: Langsung menyambung di sebelah kanannya -->
                    <div class="flex-1" id="chartContainer">
                        <canvas id="bookingChart"></canvas>
                    </div>

                </div>
                
            </div>
        </div>

    </div>

    <!-- Tabel -->

    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-12">

        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-xl font-bold text-slate-800">
                Reservasi Terbaru
            </h3>
            <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                Hari ini
            </span>
        </div>

        <div class="overflow-x-auto overflow-y-auto no-scrollbar" style="max-height: 440px;">
            <table class="w-full min-w-[700px]">
                <thead class="sticky top-0 z-10 bg-white">
                    <tr class="border-b border-slate-100 text-left text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="p-5">Customer</th>
                        <th class="p-5">Jam</th>
                        <th class="p-5">Tanggal & Waktu</th>
                        <th class="p-5">Durasi</th>
                        <th class="p-5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-sm">
                @forelse ($pemesananTerbaru as $item)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="p-5">
                            <div class="flex items-center gap-3">
                                @if($item->user && $item->user->profile_photo_path)
                                    <img src="{{ asset('storage/' . $item->user->profile_photo_path) }}" alt="{{ $item->user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200 shadow-sm shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-extrabold text-sm shadow-sm border border-gray-100 shrink-0">
                                        {{ strtoupper(substr($item->user->name ?? 'G', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $item->user->name ?? 'Guest' }}</div>
                                    <div class="text-slate-400 text-xs mt-0.5">{{ $item->no_hp ?? 'No contact' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-5">
                            @if(isset($item->detail) && $item->detail->count() > 0)
                                @php
                                    $jamMulai = \Carbon\Carbon::parse($item->detail->first()->jam_mulai)->format('H:i');
                                    $jamSelesai = \Carbon\Carbon::parse($item->detail->last()->jam_selesai)->format('H:i');
                                @endphp
                                <div class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    {{ $jamMulai }} - {{ $jamSelesai }}
                                </div>
                            @else
                                <span class="text-slate-400 italic text-xs">Belum dipilih</span>
                            @endif
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-2 text-slate-600 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td class="p-5">
                            <div class="flex items-center gap-2 text-slate-600 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ $item->durasi }} Jam
                            </div>
                        </td>
                        <td class="p-5 text-center">
                            @php
                                $statusRaw = strtolower($item->status ?? '');
                                $statusStyle = match($statusRaw) {
                                    'berhasil', 'success', 'sukses', 'selesai' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'pending', 'menunggu' => 'bg-amber-50 text-amber-600 border-amber-200',
                                    'batal', 'failed', 'gagal' => 'bg-red-50 text-red-600 border-red-200',
                                    default => 'bg-slate-50 text-slate-600 border-slate-200'
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-bold border {{ $statusStyle }} inline-block w-24 text-center shadow-sm">
                                {{ ucfirst($statusRaw) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 italic">
                            Belum ada reservasi terbaru.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const chartData = {!! json_encode($chartData) !!};
    const maxData = chartData.length > 0 ? Math.max(...chartData) : 0;
    const yMax = maxData > 8 ? maxData + 1 : 8;

    const innerContainer = document.getElementById('innerContainer');
    const chartContainer = document.getElementById('chartContainer');
    const mainScrollWrapper = document.getElementById('mainScrollWrapper');
    
    // 1. Dinamika Tinggi (Vertikal)
    let newHeight = 350;
    if (maxData > 8) {
        newHeight = 350 + ((maxData - 8) * 40); // Perbesar kanvas ke bawah
    }
    innerContainer.style.height = newHeight + 'px';

    // 2. Dinamika Lebar (Horizontal)
    const visibleDays = 8;
    if (chartData.length > visibleDays) {
        innerContainer.style.width = ((chartData.length / visibleDays) * 100) + '%';
    } else {
        innerContainer.style.width = '100%';
    }

    // ==========================================
    // Inisialisasi Y-Axis Chart (Hanya Angka Y)
    // ==========================================
    new Chart(document.getElementById('yAxisChart'), {
        type: 'line',
        data: {
            // Gunakan label dan data yang SAMA agar tinggi/proporsi sama persis
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                data: chartData,
                borderColor: 'transparent',
                backgroundColor: 'transparent',
                pointBackgroundColor: 'transparent',
                pointBorderColor: 'transparent',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 0 },
            plugins: { legend: { display: false }, tooltip: { enabled: false } },
            scales: {
                x: {
                    offset: true, // Beri jarak pada titik awal agar tidak terpotong
                    ticks: { color: 'transparent', maxRotation: 0, minRotation: 0 }, // Teks transparan
                    grid: { display: false, drawBorder: false },
                    border: { display: false }
                },
                y: {
                    position: 'right', // Posisi di ujung kanan dari canvas kecil
                    beginAtZero: true,
                    min: 0,
                    max: yMax,
                    ticks: { 
                        stepSize: 1, 
                        precision: 0,
                        mirror: true // Teks dipantulkan ke kiri agar menempel persis di sisi garis
                    },
                    grid: { display: false }, 
                    border: { display: false } // Hilangkan garis vertikal
                }
            }
        }
    });

    // ==========================================
    // Inisialisasi Main Chart (Grafik Utama)
    // ==========================================
    new Chart(document.getElementById('bookingChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [{
                label: 'Reservasi',
                data: chartData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            // Beri padding 48px untuk memberikan ruang bagi Y-Axis yang overlap
            layout: { padding: { left: 48 } },
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    offset: true, // Beri jarak pada titik awal agar tidak terpotong
                    ticks: { maxRotation: 0, minRotation: 0 },
                    grid: { color: '#f1f5f9' },
                    border: { display: false }
                },
                y: {
                    display: true, 
                    beginAtZero: true,
                    min: 0,
                    max: yMax,
                    ticks: { display: false, stepSize: 1, precision: 0 }, 
                    grid: { drawTicks: false, color: '#f1f5f9' },
                    border: { display: false } // Hilangkan garis vertikal
                }
            }
        }
    });

    // Otomatis geser posisi grafik saat awal dimuat (Horizontal ke Hari Ini, Vertikal ke Bawah/Angka 0)
    // Scroll Vertikal: Geser ke paling bawah agar terlihat dari angka 0
    mainScrollWrapper.scrollTop = mainScrollWrapper.scrollHeight;

    // Scroll Horizontal: Indeks ke-7 adalah "Hari Ini"
    if (chartData.length > visibleDays) {
        const todayIndex = 7;
        const itemWidth = chartContainer.offsetWidth / chartData.length;
        mainScrollWrapper.scrollLeft = itemWidth * todayIndex;
    }

    // Fitur Click & Drag Native Browser yang Sangat Halus
    const slider = mainScrollWrapper;
    let isDown = false, startX, startY, scrollLeft, scrollTop;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.style.cursor = 'grabbing';
        startX = e.pageX - slider.offsetLeft;
        startY = e.pageY - slider.offsetTop;
        scrollLeft = slider.scrollLeft;
        scrollTop = slider.scrollTop;
    });
    slider.addEventListener('mouseleave', () => { isDown = false; slider.style.cursor = 'grab'; });
    slider.addEventListener('mouseup', () => { isDown = false; slider.style.cursor = 'grab'; });
    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const walkX = (e.pageX - slider.offsetLeft - startX) * 1.5;
        const walkY = (e.pageY - slider.offsetTop - startY) * 1.5;
        slider.scrollLeft = scrollLeft - walkX;
        slider.scrollTop = scrollTop - walkY;
    });

</script>
@endsection