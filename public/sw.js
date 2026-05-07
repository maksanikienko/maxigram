// Service Worker — handles Web Push notifications

self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch {
        payload = { title: 'Maxigram', body: event.data.text() };
    }

    const title   = payload.title  ?? 'Maxigram';
    const options = {
        body:    payload.body    ?? '',
        icon:    payload.icon    ?? '/favicon.ico',
        badge:   payload.badge   ?? '/favicon.ico',
        data:    payload.data    ?? { url: '/chat/rooms' },
        vibrate: [200, 100, 200],
        requireInteraction: false,
        tag: payload.tag ?? 'maxigram-message',
        renotify: true,
    };

    event.waitUntil(
        // Suppress notification if the user currently has the app open and focused
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            const appFocused = windowClients.some((c) => c.focused);
            if (appFocused) return;
            return self.registration.showNotification(title, options);
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const targetUrl = event.notification.data?.url ?? '/chat/rooms';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            // Focus an already-open tab
            for (const client of windowClients) {
                if (new URL(client.url).pathname.startsWith('/chat') && 'focus' in client) {
                    return client.focus();
                }
            }
            // Otherwise open a new tab
            if (clients.openWindow) return clients.openWindow(targetUrl);
        })
    );
});
