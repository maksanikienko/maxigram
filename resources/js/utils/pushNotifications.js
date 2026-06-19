import axios from 'axios';

const urlBase64ToUint8Array = (base64String) => {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64  = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const raw     = atob(base64);
    return Uint8Array.from([...raw].map((c) => c.charCodeAt(0)));
};

// Call this on page load — does NOT need a user gesture
export const registerServiceWorker = async () => {
    if (!('serviceWorker' in navigator)) return null;
    try {
        return await navigator.serviceWorker.register('/sw.js', { scope: '/' });
    } catch (e) {
        console.error('[SW] Registration failed:', e);
        return null;
    }
};

export const isIOS = () => /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

// Returns true when the app runs in standalone (home-screen) mode
export const isStandalone = () =>
    window.matchMedia('(display-mode: standalone)').matches ||
    window.navigator.standalone === true;

// 'unsupported' | 'ios-browser' | 'denied' | 'subscribed' | 'unsubscribed'
export const getPushState = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        // On iOS in a regular browser push is not available — suggest installing PWA
        return isIOS() && !isStandalone() ? 'ios-browser' : 'unsupported';
    }
    if (Notification.permission === 'denied') return 'denied';
    try {
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.getSubscription();
        return sub ? 'subscribed' : 'unsubscribed';
    } catch {
        return 'unsupported';
    }
};

// MUST be called from a user-gesture handler (button click) — required on iOS
export const subscribeToPush = async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) return false;

    try {
        const reg = await navigator.serviceWorker.ready;

        const permission = await Notification.requestPermission();
        if (permission !== 'granted') return false;

        const vapidKey = window.Laravel?.vapidPublicKey;
        if (!vapidKey) {
            console.error('[Push] vapidPublicKey missing from window.Laravel');
            return false;
        }

        const subscription = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(vapidKey),
        });

        await axios.post('/push/subscribe', subscription.toJSON());
        return true;
    } catch (e) {
        console.error('[Push] Subscribe failed:', e);
        return false;
    }
};

export const unsubscribeFromPush = async () => {
    if (!('serviceWorker' in navigator)) return;
    try {
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.getSubscription();
        if (!sub) return;
        await axios.delete('/push/subscribe', { data: { endpoint: sub.endpoint } });
        await sub.unsubscribe();
    } catch (e) {
        console.warn('[Push] Unsubscribe failed:', e);
    }
};
