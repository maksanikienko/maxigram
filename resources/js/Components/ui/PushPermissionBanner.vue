<script setup>
import { ref, onMounted } from 'vue';
import { Bell, X, Smartphone } from 'lucide-vue-next';
import { getPushState, subscribeToPush, isIOS, isStandalone } from '@/utils/pushNotifications.js';

const DISMISSED_KEY = 'push_banner_dismissed_until';

const visible = ref(false);
const iosMode = ref(false);  // true = show "add to home screen" hint instead
const busy    = ref(false);

onMounted(async () => {
    // Don't show if user dismissed recently (7 days)
    const until = localStorage.getItem(DISMISSED_KEY);
    if (until && Date.now() < Number(until)) return;

    const state = await getPushState();

    if (state === 'subscribed' || state === 'denied' || state === 'unsupported') return;

    if (state === 'ios-browser') {
        iosMode.value = true;
    }

    // Small delay so it doesn't flash immediately on load
    setTimeout(() => { visible.value = true; }, 1500);
});

const allow = async () => {
    busy.value = true;
    const ok = await subscribeToPush();
    busy.value = false;
    if (ok || Notification.permission === 'denied') {
        visible.value = false;
    }
};

const dismiss = () => {
    visible.value = false;
    // Snooze for 7 days
    localStorage.setItem(DISMISSED_KEY, Date.now() + 7 * 24 * 60 * 60 * 1000);
};
</script>

<template>
    <Transition name="banner">
        <div
            v-if="visible"
            class="fixed bottom-4 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-2rem)] max-w-sm"
        >
            <div class="flex items-center gap-3 bg-gray-900 text-white rounded-2xl shadow-2xl px-4 py-3">
                <div class="shrink-0 w-9 h-9 rounded-xl bg-indigo-500 flex items-center justify-center">
                    <Smartphone v-if="iosMode" class="w-5 h-5" />
                    <Bell v-else class="w-5 h-5" />
                </div>

                <div class="flex-1 min-w-0">
                    <template v-if="iosMode">
                        <p class="text-sm font-semibold leading-tight">Install the app</p>
                        <p class="text-xs text-gray-400 mt-0.5 leading-snug">
                            Click <strong class="text-white">Share ↑</strong> → <strong class="text-white">On the home screen</strong>
                        </p>
                    </template>
                    <template v-else>
                        <p class="text-sm font-semibold leading-tight">Enable notifications?</p>
                        <p class="text-xs text-gray-400 mt-0.5">Receive messages even when the app is closed</p>
                    </template>
                </div>

                <div class="flex items-center gap-1 shrink-0">
                    <button
                        v-if="!iosMode"
                        @click="allow"
                        :disabled="busy"
                        class="px-3 py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white text-xs font-semibold transition-colors disabled:opacity-50"
                    >
                        {{ busy ? '...' : 'Разрешить' }}
                    </button>
                    <button
                        @click="dismiss"
                        class="p-1.5 rounded-lg hover:bg-white/10 text-gray-400 hover:text-white transition-colors"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.banner-enter-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.banner-leave-active { transition: opacity 0.2s ease, transform 0.2s ease; }
.banner-enter-from, .banner-leave-to { opacity: 0; transform: translateX(-50%) translateY(12px); }
</style>
