<script setup>
import { ref } from 'vue';
import { cn } from '@/lib/utils';

const props = defineProps({
    content: { type: String, default: '' },
    side: { type: String, default: 'top' },
});

const show = ref(false);
const positions = {
    top: 'bottom-full left-1/2 -translate-x-1/2 mb-2',
    bottom: 'top-full left-1/2 -translate-x-1/2 mt-2',
    left: 'right-full top-1/2 -translate-y-1/2 mr-2',
    right: 'left-full top-1/2 -translate-y-1/2 ml-2',
};
</script>

<template>
    <div class="relative inline-flex" @mouseenter="show = true" @mouseleave="show = false">
        <slot />
        <Transition name="fade">
            <div
                v-if="show && content"
                :class="cn(
                    'absolute z-50 px-2 py-1 text-xs rounded-md bg-foreground text-background whitespace-nowrap pointer-events-none',
                    positions[side]
                )"
            >
                {{ content }}
            </div>
        </Transition>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.1s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
