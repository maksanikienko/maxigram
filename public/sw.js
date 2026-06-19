// Service Worker — PWA lifecycle + Web Push notifications

const CACHE_NAME = 'maxigram-v1';

// ─── Lifecycle ───────────────────────────────────────────────────────────────

self.addEventListener('install', (event) => {
    // Take control immediately without waiting for old SW to expire
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    // Clean up old caches and claim all clients right away
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(keys.filter((k) => k !== CACHE_NAME).map((k) => caches.delete(k))))
            .then(() => self.clients.claim())
    );
});

// Network-first fetch — keeps the app working but doesn't break dynamic routes
self.addEventListener('fetch', (event) => {
    // Only intercept same-origin GET navigation requests
    if (
        event.request.method !== 'GET' ||
        !event.request.url.startsWith(self.location.origin) ||
        event.request.mode !== 'navigate'
    ) {
        return;
    }

    event.respondWith(
        fetch(event.request).catch(() =>
            // Offline fallback: serve whatever the browser already has cached
            caches.match(event.request)
        )
    );
});

// ─── Push Notifications ──────────────────────────────────────────────────────

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
        icon:    payload.icon    ?? '/icons/icon-192.png',
        badge:   payload.badge   ?? '/icons/icon-192.png',
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
            for (const client of windowClients) {
                if (new URL(client.url).pathname.startsWith('/chat') && 'focus' in client) {
                    return client.navigate(targetUrl).then((c) => c.focus());
                }
            }
            if (clients.openWindow) return clients.openWindow(targetUrl);
        })
    );
});
