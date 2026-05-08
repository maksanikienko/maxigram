import axios from 'axios';

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw     = atob(base64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
};

export const subscribeToPush = async () => {
    if (!('serviceWorker' in navigator)) {
        console.warn('[Push] ServiceWorker not supported');
        return;
    }
    if (!('PushManager' in window)) {
        console.warn('[Push] PushManager not supported (HTTPS required)');
        return;
    }

    try {
        const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        console.info('[Push] SW registered:', registration.scope);

        await navigator.serviceWorker.ready;
        console.info('[Push] SW ready');

        const permission = await Notification.requestPermission();
        console.info('[Push] Notification permission:', permission);
        if (permission !== 'granted') return;

        const vapidKey = window.Laravel?.vapidPublicKey;
        if (!vapidKey) {
            console.error('[Push] vapidPublicKey missing from window.Laravel');
            return;
        }

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey),
        });
        console.info('[Push] Subscribed:', subscription.endpoint);

        const { data } = await axios.post('/push/subscribe', subscription.toJSON());
        console.info('[Push] Subscription saved to server:', data);
    } catch (e) {
        console.error('[Push] Failed at step:', e.message, e);
    }
};

export const unsubscribeFromPush = async () => {
    if (!('serviceWorker' in navigator)) return;
    try {
        const registration  = await navigator.serviceWorker.ready;
        const subscription  = await registration.pushManager.getSubscription();
        if (!subscription) return;
        await axios.delete('/push/subscribe', { data: { endpoint: subscription.endpoint } });
        await subscription.unsubscribe();
    } catch (e) {
        console.warn('Push unsubscribe failed:', e);
    }
};
