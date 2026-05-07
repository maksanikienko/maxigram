import axios from 'axios';

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw     = atob(base64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
};

export const subscribeToPush = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return;

    try {
        // Register (or reuse) the service worker
        const registration = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
        await navigator.serviceWorker.ready;

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return;

        const vapidKey = window.Laravel?.vapidPublicKey;
        if (!vapidKey) return;

        const subscription = await registration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey),
        });

        await axios.post('/push/subscribe', subscription.toJSON());
    } catch (e) {
        console.warn('Push subscription failed:', e);
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
