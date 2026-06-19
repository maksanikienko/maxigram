<script setup>
import { ref, onMounted } from 'vue';
import { Bell, BellOff, BellRing, Smartphone } from 'lucide-vue-next';
import { getPushState, subscribeToPush, unsubscribeFromPush, isIOS, isStandalone } from '@/utils/pushNotifications.js';

// 'loading' | 'unsupported' | 'ios-browser' | 'denied' | 'subscribed' | 'unsubscribed'
const state  = ref('loading');
const busy   = ref(false);
const showIosHint = ref(false);

onMounted(async () => {
    state.value = await getPushState();
});

const labels = {
    loading:     'Загрузка...',
    unsupported: 'Уведомления не поддерживаются',
    'ios-browser': 'Установите как приложение',
    denied:      'Уведомления заблокированы',
    subscribed:  'Уведомления включены',
    unsubscribed:'Включить уведомления',
};

const toggle = async () => {
    if (busy.value) return;

    if (state.value === 'ios-browser') {
        showIosHint.value = !showIosHint.value;
        return;
    }

    if (state.value === 'denied') {
        alert('Уведомления заблокированы браузером.\nОткройте Настройки → Safari/Chrome → Уведомления и разрешите для этого сайта.');
        return;
    }

    busy.value = true;
    try {
        if (state.value === 'subscribed') {
            await unsubscribeFromPush();
            state.value = 'unsubscribed';
        } else if (state.value === 'unsubscribed') {
            const ok = await subscribeToPush();
            state.value = ok ? 'subscribed' : (Notification.permission === 'denied' ? 'denied' : 'unsubscribed');
        }
    } finally {
        busy.value = false;
    }
};
</script>

<template>
    <div class="relative">
        <button
            @click="toggle"
            :disabled="state === 'loading' || state === 'unsupported' || busy"
            :title="labels[state]"
            :class="[
                'p-1.5 rounded-lg transition-colors',
                state === 'subscribed'
                    ? 'text-emerald-400 hover:bg-white/10'
                    : state === 'denied' || state === 'unsupported'
                        ? 'text-sidebar-foreground/30 cursor-not-allowed'
                        : 'text-sidebar-foreground/60 hover:text-sidebar-foreground hover:bg-white/10'
            ]"
        >
            <BellRing v-if="state === 'subscribed'" class="w-4 h-4" />
            <Smartphone v-else-if="state === 'ios-browser'" class="w-4 h-4 text-amber-400" />
            <BellOff v-else-if="state === 'denied' || state === 'unsupported'" class="w-4 h-4" />
            <Bell v-else class="w-4 h-4" />
        </button>

        <!-- iOS "Add to Home Screen" hint -->
        <Transition name="hint">
            <div
                v-if="showIosHint"
                class="absolute bottom-full right-0 mb-2 w-64 bg-gray-900 text-white text-xs rounded-xl p-3 shadow-xl z-50 leading-relaxed"
            >
                <p class="font-semibold mb-1 text-amber-400">Установите как приложение</p>
                <p>Откройте в <strong>Safari</strong>, нажмите кнопку
                    <strong>«Поделиться» ↑</strong> внизу экрана,
                    затем <strong>«На экран "Домой"»</strong>.</p>
                <p class="mt-1 text-gray-400">После установки уведомления заработают автоматически.</p>
                <div class="absolute -bottom-1.5 right-4 w-3 h-3 bg-gray-900 rotate-45" />
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.hint-enter-active, .hint-leave-active { transition: opacity 0.15s, transform 0.15s; }
.hint-enter-from, .hint-leave-to { opacity: 0; transform: translateY(4px); }
</style>
