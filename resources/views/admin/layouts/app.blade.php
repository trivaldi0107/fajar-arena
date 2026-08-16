<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Admin Fajar Arena</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}?v=2">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('images/logo.png') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}?v=2">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        /* Animasi Transisi Halaman (Page Transition) */
        @keyframes pageFadeUp {
            0% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }
        
        .animate-page-fade-up {
            animation: pageFadeUp 0.5s ease-out forwards;
        }
    </style>

</head>

<body class="font-sans antialiased bg-slate-100" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen relative">

    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden" @click="sidebarOpen = false" x-transition.opacity></div>

    {{-- Sidebar --}}
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-72 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:block">
        @include('admin.partials.sidebar')
    </div>

    <div class="flex-1 flex flex-col min-w-0 w-full lg:ml-72">

        {{-- Topbar --}}
        @include('admin.partials.topbar')

        {{-- Content --}}
        <main class="p-4 md:p-8 pb-12 animate-page-fade-up">

            @yield('content')

        </main>

    </div>

</div>

@stack('modals')
<!-- Alpine Plugins -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Bulletproof Native Web Push & Sound Notification Manager -->
<script>
// Audio Chime Generator using Web Audio API (Loud 2-tone chime: E5 -> A5)
let globalAudioCtx = null;

function getAudioContext() {
    if (!globalAudioCtx) {
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        if (AudioCtx) {
            globalAudioCtx = new AudioCtx();
        }
    }
    if (globalAudioCtx && globalAudioCtx.state === 'suspended') {
        globalAudioCtx.resume();
    }
    return globalAudioCtx;
}

// Unlock audio context on any user click anywhere on page
document.addEventListener('click', function unlockAudioOnPage() {
    try {
        const ctx = getAudioContext();
        if (ctx && ctx.state === 'suspended') {
            ctx.resume();
        }
    } catch(e) {}
}, { passive: true });

function playNotificationSound(customerName = 'Pelanggan', caborName = 'Badminton') {
    const mode = localStorage.getItem('fajar_notif_mode') || 'chime';
    const vol = parseFloat(localStorage.getItem('fajar_notif_volume') || '0.8');

    if (mode === 'mute') {
        return;
    }

    if (mode === 'custom') {
        const audioData = localStorage.getItem('fajar_custom_audio_data');
        if (audioData) {
            try {
                const audio = new Audio(audioData);
                audio.volume = vol;
                audio.play().catch(function(e) {
                    console.warn('Audio play error, fallback to chime:', e);
                    playGlobalChime(vol);
                });
                return;
            } catch(e) {
                console.warn('Custom audio error:', e);
            }
        }
        playGlobalChime(vol);
        return;
    }

    if (mode === 'voice') {
        if ('speechSynthesis' in window) {
            try {
                window.speechSynthesis.cancel();
                setTimeout(function() {
                    const text = "Pesanan Masuk! Atas nama " + (customerName || 'Pelanggan') + ", " + (caborName || 'Badminton') + ". Silakan periksa bukti pembayaran.";
                    const utterance = new SpeechSynthesisUtterance(text);
                    utterance.lang = 'id-ID';
                    utterance.rate = 0.92;
                    utterance.pitch = 1.0;
                    utterance.volume = Math.max(0.1, Math.min(1.0, vol));

                    const voices = window.speechSynthesis.getVoices();
                    if (voices && voices.length > 0) {
                        const idVoice = voices.find(function(v) {
                            return (v.lang && v.lang.toLowerCase().includes('id')) || (v.name && v.name.toLowerCase().includes('indonesia'));
                        });
                        if (idVoice) utterance.voice = idVoice;
                    }

                    utterance.onerror = function() { playGlobalChime(vol); };
                    window.speechSynthesis.resume();
                    window.speechSynthesis.speak(utterance);
                }, 60);
                return;
            } catch(e) {
                console.warn('Speech error:', e);
            }
        }
        playGlobalChime(vol);
        return;
    }

    // Default: Chime
    playGlobalChime(vol);
}

function playGlobalChime(vol = 0.8) {
    try {
        const ctx = getAudioContext();
        if (!ctx) return;
        if (ctx.state === 'suspended') {
            ctx.resume().then(function() { makeTwoToneChime(ctx, vol); });
        } else {
            makeTwoToneChime(ctx, vol);
        }
    } catch(e) {
        console.log('Audio error:', e);
    }
}

function makeTwoToneChime(ctx, vol = 0.8) {
    try {
        // Tone 1: E5 (659.25 Hz)
        const osc1 = ctx.createOscillator();
        const gain1 = ctx.createGain();
        osc1.type = 'sine';
        osc1.frequency.setValueAtTime(659.25, ctx.currentTime);
        gain1.gain.setValueAtTime(0.6 * vol, ctx.currentTime);
        gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc1.connect(gain1);
        gain1.connect(ctx.destination);
        osc1.start(ctx.currentTime);
        osc1.stop(ctx.currentTime + 0.3);

        // Tone 2: A5 (880 Hz)
        const osc2 = ctx.createOscillator();
        const gain2 = ctx.createGain();
        osc2.type = 'sine';
        osc2.frequency.setValueAtTime(880, ctx.currentTime + 0.15);
        gain2.gain.setValueAtTime(0.7 * vol, ctx.currentTime + 0.15);
        gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        osc2.connect(gain2);
        gain2.connect(ctx.destination);
        osc2.start(ctx.currentTime + 0.15);
        osc2.stop(ctx.currentTime + 0.6);
    } catch(e) {}
}

// Service Worker Registration & Auto-Init
if ('serviceWorker' in navigator && 'PushManager' in window) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/sw.js').then(function (registration) {
            console.log('ServiceWorker registered:', registration.scope);
            autoInitPush();
        }).catch(function (err) {
            console.log('ServiceWorker registration error:', err);
        });
    });
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/\-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}

function autoInitPush() {
    if ('Notification' in window) {
        if (Notification.permission === 'granted') {
            subscribeUserToPush(false);
        } else if (Notification.permission !== 'denied') {
            setTimeout(function() {
                Swal.fire({
                    title: 'Aktifkan Notifikasi Pesanan?',
                    text: 'Dapatkan pemberitahuan suara & notifikasi push instan di HP/Laptop saat ada pelanggan mengunggah bukti transfer.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Aktifkan Sekarang',
                    cancelButtonText: 'Nanti Saja',
                    confirmButtonColor: '#2563eb'
                }).then(function(result) {
                    if (result.isConfirmed) {
                        enablePushNotificationManually();
                    }
                });
            }, 1200);
        }
    }
}

function triggerNativeOSNotification(title, body, targetUrl) {
    if (!('Notification' in window) || Notification.permission !== 'granted') return;

    try {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.ready.then(function(reg) {
                reg.showNotification(title, {
                    body: body,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    vibrate: [200, 100, 200],
                    data: { url: targetUrl || "{{ route('admin.pemesanan') }}" }
                });
            }).catch(function() {
                new Notification(title, { body: body, icon: '/favicon.ico' });
            });
        } else {
            new Notification(title, { body: body, icon: '/favicon.ico' });
        }
    } catch(e) {
        console.log('Native notification error:', e);
    }
}

function enablePushNotificationManually() {
    playNotificationSound();

    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'Test Notifikasi & Suara Berhasil!',
        text: 'Suara chime dan notifikasi aktif dengan sempurna.',
        showConfirmButton: false,
        timer: 3500,
        timerProgressBar: true
    });

    if (!('Notification' in window)) return;

    if (Notification.permission === 'granted') {
        triggerNativeOSNotification('Fajar Arena', 'Notifikasi push & suara aktif di device ini!');
        subscribeUserToPush(false);
    } else {
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                triggerNativeOSNotification('Fajar Arena', 'Notifikasi push & suara berhasil diaktifkan!');
                subscribeUserToPush(false);
            } else {
                Swal.fire('Notifikasi Ditolak', 'Notifikasi push belum diizinkan di browser ini.', 'info');
            }
        });
    }
}

function subscribeUserToPush(showAlert = false) {
    if (!('serviceWorker' in navigator)) return;

    navigator.serviceWorker.register('/sw.js').then(function(registration) {
        return fetch('/admin/vapid-public-key')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                const publicKey = data.publicKey;
                if (!publicKey) return;

                const applicationServerKey = urlBase64ToUint8Array(publicKey);
                return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            })
            .then(function(subscription) {
                if (!subscription) return;
                const subJson = subscription.toJSON();
                return fetch('/admin/push-subscribe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(subJson)
                });
            })
            .then(function(res) {
                if (res) return res.json();
            })
            .then(function(result) {
                console.log('Push subscription saved to DB:', result);
                if (showAlert) {
                    Swal.fire({
                        title: 'Notifikasi Push Aktif!',
                        text: 'HP / Browser Anda sekarang terdaftar untuk menerima notifikasi push.',
                        icon: 'success',
                        confirmButtonColor: '#2563eb'
                    });
                }
            })
            .catch(function(err) {
                console.log('Push subscription error:', err);
            });
    });
}

// Real-time Order Monitoring Polling
function checkNewOrders() {
    fetch('/admin/check-new-orders')
        .then(function(res) { return res.json(); })
        .then(function(data) {
            const currentLatestId = data.latest_id;
            const ackId = sessionStorage.getItem('ack_order_id');

            if (data.count > 0 && currentLatestId && ackId != currentLatestId) {
                // 1. Play Configured Notification Sound (Custom File / Voice / Chime)
                playNotificationSound(data.customer_name || 'Pelanggan', data.cabor_name || 'Badminton');

                const targetUrl = "/admin/pemesanan/" + currentLatestId;
                const notifTitle = (data.arena_name || 'Fajar Arena') + ' 💳';
                const notifBody = 'Bukti pembayaran (' + (data.kode_reservasi || '') + ') cabor ' + (data.cabor_name || '') + ' dari ' + (data.customer_name || 'Pelanggan') + ' telah diunggah.';

                // 2. Show In-App Toast Notification
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: notifTitle,
                    text: notifBody,
                    showConfirmButton: true,
                    confirmButtonText: 'Lihat Detail',
                    timer: 15000,
                    timerProgressBar: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        window.location.href = targetUrl;
                    }
                });

                // 3. Trigger Native OS Windows / Phone Notification
                triggerNativeOSNotification(notifTitle, notifBody, targetUrl);

                // Mark order ID as notified in session storage
                sessionStorage.setItem('ack_order_id', currentLatestId);

                // Auto reload if admin is on /admin/pemesanan page after audio finishes
                if (window.location.pathname.includes('/admin/pemesanan')) {
                    setTimeout(function() { window.location.reload(); }, 6000);
                }
            }
        })
        .catch(function(err) { console.log('Polling check order error:', err); });
}

setInterval(checkNewOrders, 3000);
checkNewOrders();
</script>
</body>
</html>