<script setup>
import { cn } from '@/lib/utils';
import { ref } from 'vue';

const props = defineProps({
    src: { type: String, default: '' },
    alt: { type: String, default: '' },
    fallback: { type: String, default: '' },
    size: { type: String, default: 'default' },
    class: { type: String, default: '' },
});

const sizes = {
    sm: 'h-8 w-8 text-xs',
    default: 'h-10 w-10 text-sm',
    lg: 'h-12 w-12 text-base',
    xl: 'h-16 w-16 text-xl',
};

const imgError = ref(false);

const initials = (name) => {
    if (!name) return '?';
    return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};
</script>

<template>
    <div :class="cn('relative flex shrink-0 overflow-hidden rounded-full', sizes[size] || sizes.default, props.class)">
        <img
            v-if="src && !imgError"
            :src="src"
            :alt="alt"
            class="h-full w-full object-cover"
            @error="imgError = true"
        />
        <div
            v-else
            class="flex h-full w-full items-center justify-center bg-gradient-to-br from-primary/80 to-primary font-semibold text-primary-foreground"
        >
            {{ fallback ? fallback : initials(alt) }}
        </div>
    </div>
</template>
