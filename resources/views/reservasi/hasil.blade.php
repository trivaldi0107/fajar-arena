{{-- ========================================================================= --}}
{{-- RINGKASAN HASIL RESERVASI & PEMBAYARAN --}}
{{-- ========================================================================= --}}
<div class="max-w-7xl mx-auto mt-10 px-4">

    <h1 class="text-3xl font-bold mb-6">Pembayaran</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- BAGIAN KIRI: RINGKASAN PEMESANAN (Rincian Olahraga, Tanggal, Durasi, Jam, Total) --}}
        <div class="bg-white p-6 rounded-xl shadow-md">

            <h2 class="text-xl font-semibold mb-4">Ringkasan Pemesanan</h2>

            <div class="space-y-4 text-sm">

                <div class="flex justify-between">
                    <span>Olahraga:</span>
                    <span class="font-medium">{{ ucfirst(active_arena()->jenis_olahraga ?? 'Badminton') }}</span>
                </div>

                <div class="border-t pt-2 flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('d M Y') }}</span>
                </div>

                <div class="border-t pt-2 flex justify-between">
                    <span>Durasi:</span>
                    <span>{{ $durasi }} Jam</span>
                </div>

                <div class="border-t pt-2">
                    <p class="mb-2">Jam:</p>

                    @foreach($jadwal as $j)
                        <div class="flex justify-between">
                            <span>{{ $j->lapangan->nama_lapangan }}</span>
                            <span>{{ substr($j->jam_mulai,0,5) }} - {{ substr($j->jam_selesai,0,5) }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="border-t pt-2 flex justify-between">
                    <span>Tipe:</span>
                    <span>Non-Member</span>
                </div>

                <div class="border-t pt-2 flex justify-between">
                    <span>Harga per jam:</span>
                    <span>{{ number_format($harga,0,',','.') }}</span>
                </div>

                <div class="border-t pt-2 flex justify-between font-bold text-lg">
                    <span>Total harga:</span>
                    <span>{{ number_format($total,0,',','.') }}</span>
                </div>

            </div>
        </div>

        {{-- BAGIAN KANAN: PEMBAYARAN VIA QRIS & SIMULASI WAKTU --}}
        <div class="bg-white p-6 rounded-xl shadow text-center">

            <h2 class="text-xl font-semibold mb-4">Pembayaran</h2>

            {{-- QRIS Dinamis berdasarkan Kode Reservasi --}}
            <div class="bg-gray-100 p-4 rounded-lg inline-block shadow">
                <p class="font-bold text-lg mb-2">Rp {{ number_format($total,0,',','.') }}</p>

                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $pemesanan->kode_reservasi }}" 
                     class="mx-auto">
            </div>

            {{-- Batas Waktu Pembayaran --}}
            <p class="mt-4 text-sm text-gray-600">
                Batas Waktu Pembayaran: 10:00
            </p>

            {{-- Tombol Konfirmasi Bayar --}}
            <form action="{{ route('bayar', $pemesanan->id) }}" method="POST">
                @csrf
                <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
                    Bayar Sekarang
                </button>
            </form>

            {{-- Indikator Status Pembayaran --}}
            @if($status == 'berhasil')
                <div class="mt-4 text-green-600 font-bold">
                    Pembayaran Berhasil
                </div>
            @endif

        </div>

    </div>

</div>

