<x-app-layout>

<div class="max-w-md mx-auto py-10 px-4 md:px-0">

    <div class="bg-white rounded-2xl shadow-lg p-8">

        <h1 class="text-2xl font-bold text-center mb-8">

            Simulasi QRIS

        </h1>

        <div class="space-y-4">

            <div class="flex justify-between">

                <span>Merchant</span>

                <strong>Fajar Arena</strong>

            </div>

            <div class="flex justify-between">

                <span>Kode Reservasi</span>

                <strong>

                    {{ $pemesanan->kode_reservasi }}

                </strong>

            </div>

            <div class="flex justify-between">

                <span>Total</span>

                <strong>

                    Rp {{ number_format($total) }}

                </strong>

            </div>

            <div class="flex justify-between">

                <span>Status</span>

                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm">

                    Menunggu Pembayaran

                </span>

            </div>

            <hr>

            <div>

                <p class="font-semibold mb-3">

                    Metode Pembayaran

                </p>

                <label class="flex items-center gap-2 mb-2">

                    <input type="radio" checked>

                    DANA

                </label>

                <label class="flex items-center gap-2 mb-2">

                    <input type="radio">

                    GoPay

                </label>

                <label class="flex items-center gap-2 mb-2">

                    <input type="radio">

                    OVO

                </label>

                <label class="flex items-center gap-2">

                    <input type="radio">

                    ShopeePay

                </label>

            </div>

            <hr>

            <div class="flex justify-between">

                <span>Saldo Simulasi</span>

                <strong>

                    Rp 5.000.000

                </strong>

            </div>

            <div class="text-center">

                <p class="text-red-500 font-semibold">

                    Sisa Waktu Pembayaran

                </p>

                <h3 id="countdown" class="text-2xl font-bold text-red-500">

                    10:00

                </h3>

            </div>

            <div class="flex gap-3 mt-4">

                <form action="{{ route('pembayaran.batal', $pemesanan->id) }}"
                    method="POST"
                    class="w-1/2">

                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-gray-500 hover:bg-gray-600 text-white py-3 rounded-xl">

                        ← Kembali

                    </button>

                </form>

                <form action="{{ route('pembayaran.proses', $pemesanan->id) }}"
                    method="POST"
                    class="w-1/2">

                    @csrf

                    <button
                        type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl">

                        Bayar Sekarang

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<script>

let time = 600;

setInterval(function () {

    let menit = Math.floor(time / 60);

    let detik = time % 60;

    document.getElementById('countdown').innerHTML =
        menit + ":" + (detik < 10 ? "0" : "") + detik;

    time--;

}, 1000);

</script>

</x-app-layout>