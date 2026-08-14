@extends('admin.layouts.app')

@section('title', 'Scan Tiket')

@section('content')
<style>
    #reader {
        border-radius: 1rem !important;
        overflow: hidden !important;
        border: none !important;
    }
    #reader video {
        border-radius: 1rem !important;
        object-fit: cover !important;
    }
</style>
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Scan E-Tiket</h2>
            <p class="text-sm text-gray-500 mt-1">Gunakan kamera untuk memindai barcode/QR Code pada e-tiket pengunjung.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-6 md:p-8">
        
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Camera Section -->
            <div class="w-full md:w-1/2 flex flex-col items-center">
                <div id="reader" class="w-full rounded-2xl overflow-hidden shadow-inner border border-gray-100 bg-gray-50 min-h-[300px]"></div>
                
                <div class="mt-6 flex gap-3">
                    <button id="start-scan" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all cursor-pointer">
                        Mulai Kamera
                    </button>
                    <button id="stop-scan" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl font-bold text-sm transition-all hidden cursor-pointer">
                        Berhenti
                    </button>
                </div>

                <!-- Input Kode Tiket Manual -->
                <div class="mt-6 w-full pt-5 border-t border-gray-100">
                    <label for="manual-kode" class="block text-xs text-gray-500 font-bold uppercase tracking-wider mb-2">Atau Cari Kode Tiket Manual</label>
                    <div class="flex gap-2">
                        <input type="text" id="manual-kode" placeholder="Masukkan Kode (Contoh: RES-XXXXXX)" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm font-bold uppercase focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button id="btn-manual-search" type="button" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all shrink-0 cursor-pointer">
                            Cari Tiket
                        </button>
                    </div>
                </div>
            </div>

            <!-- Result Section -->
            <div class="w-full md:w-1/2 flex flex-col justify-center border-t md:border-t-0 md:border-l border-gray-100 pt-8 md:pt-0 md:pl-8">
                
                <div id="scan-waiting" class="text-center py-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-50 text-blue-300 mb-4">
                        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-400">Menunggu Scan...</h3>
                    <p class="text-sm text-gray-400 mt-1">Arahkan QR Code ke kamera.</p>
                </div>

                <div id="scan-result" class="hidden flex-col gap-4">
                    <div id="status-badge" class="inline-flex self-start items-center px-4 py-1.5 rounded-full text-sm font-bold capitalize shadow-sm border mb-2">
                        Status
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Kode Reservasi</p>
                        <p id="res-kode" class="text-xl font-black text-gray-800">-</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Nama Pemesan</p>
                        <p id="res-nama" class="text-lg font-bold text-blue-700">-</p>
                    </div>

                    <div id="res-jadwal" class="bg-gray-50 rounded-2xl p-4 border border-gray-100 max-h-48 overflow-y-auto">
                        <!-- Grouped schedules will be injected here -->
                    </div>


                    <button id="btn-scan-lagi" class="mt-3 bg-white border-2 border-gray-200 hover:bg-gray-50 text-gray-700 w-full py-2.5 rounded-xl font-bold text-sm transition-all shadow-sm">
                        Scan Tiket Lain
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- HTML5-QRCode Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Fokus hanya pada format QR_CODE agar jauh lebih cepat dan ringan memproses gambar
    let html5QrcodeScanner = new Html5Qrcode("reader", { formatsToSupport: [ Html5QrcodeSupportedFormats.QR_CODE ] });
    let isScanning = false;
    let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const btnStart = document.getElementById('start-scan');
    const btnStop = document.getElementById('stop-scan');
    const scanWaiting = document.getElementById('scan-waiting');
    const scanResult = document.getElementById('scan-result');
    const btnScanLagi = document.getElementById('btn-scan-lagi');

    let currentScannedCode = null;

    function refreshScanResult(kode, showSwal = false) {
        if (showSwal) {
            Swal.fire({
                title: 'Mencari Tiket...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        fetch("{{ route('admin.scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({ kode_reservasi: kode })
        })
        .then(response => response.json())
        .then(data => {
            if (showSwal) Swal.close();
            if(data.status === 'success') {
                currentScannedCode = data.data.kode_reservasi;
                showResult(data.data);
                
                if (showSwal) {
                    if (data.data.status_pembayaran === 'berhasil') {
                        if (data.data.is_used) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Sudah Digunakan',
                                text: 'Tiket ini sudah di-scan dan digunakan sebelumnya.',
                                confirmButtonColor: '#2563eb'
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tiket Valid!',
                                text: 'Pemesanan atas nama ' + data.data.nama_pemesan,
                                confirmButtonColor: '#2563eb'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Perhatian!',
                            text: 'Status pembayaran tiket ini adalah: ' + data.data.status_pembayaran.toUpperCase(),
                            confirmButtonColor: '#2563eb'
                        });
                    }
                }
            } else {
                if (showSwal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Tidak Ditemukan',
                        text: data.message || 'Kode tiket tidak valid.',
                        confirmButtonColor: '#2563eb'
                    });
                }
                resetUI();
            }
        })
        .catch(error => {
            if (showSwal) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal terhubung ke server.',
                    confirmButtonColor: '#2563eb'
                });
            }
            resetUI();
        });
    }

    function onScanSuccess(decodedText, decodedResult) {
        if (isScanning) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                btnStart.classList.remove('hidden');
                btnStop.classList.add('hidden');
                refreshScanResult(decodedText, true);
            }).catch(() => {
                refreshScanResult(decodedText, true);
            });
        } else {
            refreshScanResult(decodedText, true);
        }
    }

    function onScanFailure(error) {
        // handle scan failure
    }

    function showResult(data) {
        scanWaiting.classList.add('hidden');
        scanResult.classList.remove('hidden');
        scanResult.classList.add('flex');

        document.getElementById('res-kode').textContent = data.kode_reservasi;
        
        let jenisUserLabel = data.jenis_user === 'member' 
            ? '<span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md ml-2 uppercase">Member</span>'
            : '<span class="text-[10px] bg-gray-100 text-gray-600 px-2 py-0.5 rounded-md ml-2 uppercase">Non Member</span>';
        document.getElementById('res-nama').innerHTML = data.nama_pemesan + jenisUserLabel;
        document.getElementById('res-jadwal').innerHTML = data.jadwal_html;

        let badge = document.getElementById('status-badge');
        badge.textContent = data.status_pembayaran;

        if(data.status_pembayaran === 'berhasil') {
            if (data.is_used) {
                badge.textContent = 'TELAH DIGUNAKAN';
                badge.className = "inline-flex self-start items-center px-4 py-1.5 rounded-full text-sm font-bold shadow-sm border mb-2 bg-gray-100 text-gray-600 border-gray-200";
            } else {
                badge.className = "inline-flex self-start items-center px-4 py-1.5 rounded-full text-sm font-bold capitalize shadow-sm border mb-2 bg-green-100 text-green-700 border-green-200";
            }
        } else if(data.status_pembayaran === 'batal') {
            badge.className = "inline-flex self-start items-center px-4 py-1.5 rounded-full text-sm font-bold capitalize shadow-sm border mb-2 bg-red-100 text-red-700 border-red-200";
        } else {
            badge.className = "inline-flex self-start items-center px-4 py-1.5 rounded-full text-sm font-bold capitalize shadow-sm border mb-2 bg-yellow-100 text-yellow-700 border-yellow-200";
        }
    }

    function resetUI() {
        scanWaiting.classList.remove('hidden');
        scanResult.classList.add('hidden');
        scanResult.classList.remove('flex');
    }

    btnStart.addEventListener('click', () => {
        if (!isScanning) {
            html5QrcodeScanner.start(
                { facingMode: "environment" },
                { 
                    fps: 10, 
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                onScanSuccess,
                onScanFailure
            ).then(() => {
                isScanning = true;
                btnStart.classList.add('hidden');
                btnStop.classList.remove('hidden');
                resetUI();
            }).catch((err) => {
                Swal.fire('Akses Ditolak', 'Tidak dapat mengakses kamera. Pastikan Anda telah memberikan izin pada browser.', 'error');
            });
        }
    });

    btnStop.addEventListener('click', () => {
        if (isScanning) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                btnStart.classList.remove('hidden');
                btnStop.classList.add('hidden');
            });
        }
    });

    btnScanLagi.addEventListener('click', () => {
        btnStart.click();
    });

    const btnManual = document.getElementById('btn-manual-search');
    const inputManual = document.getElementById('manual-kode');

    function executeManualSearch() {
        let kode = inputManual.value.trim();
        if (!kode) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Silakan masukkan kode reservasi terlebih dahulu.',
                confirmButtonColor: '#2563eb'
            });
            return;
        }
        
        if (isScanning) {
            html5QrcodeScanner.stop().then(() => {
                isScanning = false;
                btnStart.classList.remove('hidden');
                btnStop.classList.add('hidden');
                refreshScanResult(kode, true);
            }).catch(() => {
                refreshScanResult(kode, true);
            });
        } else {
            refreshScanResult(kode, true);
        }
    }

    if (btnManual) {
        btnManual.addEventListener('click', executeManualSearch);
    }
    if (inputManual) {
        inputManual.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                executeManualSearch();
            }
        });
    }

    document.getElementById('res-jadwal').addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-checkin-item')) {
            let btn = e.target;
            let kode = btn.getAttribute('data-kode');
            let tanggal = btn.getAttribute('data-tanggal');
            let lapangan = btn.getAttribute('data-lapangan');

            Swal.fire({
                title: 'Proses Check-in?',
                text: "Jadwal ini akan ditandai sebagai 'Telah Digunakan'.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Check-in!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    fetch("{{ route('admin.scan.checkin') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ kode_reservasi: kode, tanggal: tanggal, lapangan_id: lapangan })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success') {
                            Swal.fire('Berhasil!', data.message, 'success');
                            // Replace button with badge
                            let badgeHtml = '<button type="button" class="btn-cancel-checkin text-xs font-bold bg-green-100 hover:bg-red-100 text-green-700 hover:text-red-700 px-3 py-1.5 rounded-lg border border-green-200 hover:border-red-200 shadow-sm transition-all" data-kode="'+kode+'" data-tanggal="'+tanggal+'" data-lapangan="'+lapangan+'" title="Klik untuk membatalkan check-in">Telah Digunakan (Batal)</button>';
                            btn.outerHTML = badgeHtml;
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memproses check-in.', 'error');
                    });
                }
            });
        } else if (e.target.classList.contains('btn-cancel-checkin')) {
            let btn = e.target;
            let kode = btn.getAttribute('data-kode');
            let tanggal = btn.getAttribute('data-tanggal');
            let lapangan = btn.getAttribute('data-lapangan');

            Swal.fire({
                title: 'Batalkan Check-in?',
                text: "Status jadwal ini akan dikembalikan menjadi belum digunakan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    fetch("{{ route('admin.scan.cancelCheckIn') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ kode_reservasi: kode, tanggal: tanggal, lapangan_id: lapangan })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success') {
                            Swal.fire('Berhasil!', data.message, 'success');
                            let btnHtml = '<button type="button" class="btn-checkin-item text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all" data-kode="'+kode+'" data-tanggal="'+tanggal+'" data-lapangan="'+lapangan+'">Check-in</button>';
                            btn.outerHTML = btnHtml;
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memproses pembatalan check-in.', 'error');
                    });
                }
            });
        } else if (e.target.classList.contains('btn-checkin-all')) {
            let btn = e.target;
            let kode = btn.getAttribute('data-kode');

            Swal.fire({
                title: 'Check-in Semua Tiket?',
                text: "Seluruh jadwal pada tiket ini akan ditandai sebagai 'Telah Digunakan'.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Check-in Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    fetch("{{ route('admin.scan.checkin') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ kode_reservasi: kode })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success') {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                // refresh UI
                                refreshScanResult(kode);
                            });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memproses check-in.', 'error');
                    });
                }
            });
        } else if (e.target.classList.contains('btn-cancel-all')) {
            let btn = e.target;
            let kode = btn.getAttribute('data-kode');

            Swal.fire({
                title: 'Batalkan Semua Check-in?',
                text: "Status seluruh jadwal pada tiket ini akan dikembalikan menjadi belum digunakan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan Semua!',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    fetch("{{ route('admin.scan.cancelCheckIn') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ kode_reservasi: kode })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.status === 'success') {
                            Swal.fire('Berhasil!', data.message, 'success').then(() => {
                                // refresh UI
                                refreshScanResult(kode);
                            });
                        } else {
                            Swal.fire('Gagal!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Gagal memproses pembatalan check-in.', 'error');
                    });
                }
            });
        }
    });
});
</script>

<style>
/* Custom style for html5-qrcode elements */
#reader {
    border: none !important;
}
#reader video {
    border-radius: 1rem;
    object-fit: cover;
}
#reader__dashboard_section_csr span {
    color: #ef4444 !important; /* Red for permissions text */
    font-size: 0.875rem;
}
#reader__dashboard_section_swaplink {
    text-decoration: none !important;
    color: #2563eb !important;
    font-weight: 600;
    margin-top: 10px;
    display: inline-block;
}
</style>
@endsection
