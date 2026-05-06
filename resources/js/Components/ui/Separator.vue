<script setup>
import { cn } from '@/lib/utils';

const props = defineProps({
    orientation: { type: String, default: 'horizontal' },
    class: { type: String, default: '' },
});
</script>

<template>
    <div
        :class="cn(
            'shrink-0 bg-border',
            props.orientation === 'horizontal' ? 'h-px w-full' : 'h-full w-px',
            props.class
        )"
    />
</template>
