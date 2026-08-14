{{-- ========================================================================= --}}
{{-- HALAMAN KONFIRMASI RESERVASI & RINCIAN JADWAL YANG DIPILIH --}}
{{-- ========================================================================= --}}
<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Reservasi</title>
</head>

<body>

<h1>Konfirmasi Reservasi</h1>

{{-- Informasi Kode Reservasi, Durasi, dan Total Harga --}}
<p>Kode Reservasi : {{ $pemesanan->kode_reservasi }}</p>
<p>Total Jam : {{ $pemesanan->durasi }} Jam</p>
<p>Total Harga : Rp{{ number_format($total) }}</p>

<h3>Jadwal yang dipilih</h3>

{{-- Tabel Rincian Slot Tanggal, Jam, dan Nama Lapangan --}}
<table border="1" cellpadding="10">

<tr>
    <th>Tanggal</th>
    <th>Jam</th>
    <th>Lapangan</th>
</tr>

@foreach($pemesanan->detail as $d)

<tr>
    <td>{{ $d->tanggal }}</td>
    <td>{{ $d->jam_mulai }} - {{ $d->jam_selesai }}</td>
    <td>{{ $d->lapangan->nama_lapangan }}</td>
</tr>

@endforeach

</table>

<br>

{{-- Form Tombol Lanjut ke Simulasi Pembayaran QRIS --}}
<form method="POST" action="/reservasi/bayar/{{ $pemesanan->id }}">

    @csrf

    <button type="submit">
        Bayar Sekarang (Simulasi QRIS)
    </button>

</form>

</body>
</html>