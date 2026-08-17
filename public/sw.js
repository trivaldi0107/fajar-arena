// Service Worker for Fajar Arena Web Push Notifications
self.addEventListener('push', function (event) {
    if (!event.data) return;

    let data = {};
    try {
        data = event.data.json();
    } catch (e) {
        data = {
            title: 'Fajar Arena',
            body: event.data.text(),
            icon: '/favicon.ico',
            url: '/admin/pemesanan'
        };
    }

    const title = data.title || 'Fajar Arena';
    const origin = self.location.origin;
    const options = {
        body: data.body || 'Ada notifikasi baru dari Fajar Arena.',
        icon: data.icon || (origin + '/images/logo.png'),
        badge: data.badge || (origin + '/favicon.png'),
        tag: 'fajar-order-' + Date.now(),
        renotify: true,
        requireInteraction: true,
        vibrate: [300, 150, 300],
        data: {
            url: data.url || '/admin/pemesanan'
        },
        actions: [
            {
                action: 'open_url',
                title: 'Lihat Detail'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = event.notification.data && event.notification.data.url 
        ? event.notification.data.url 
        : '/admin/pemesanan';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                let client = clientList[i];
                if (client.url.includes('/admin') && 'focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
