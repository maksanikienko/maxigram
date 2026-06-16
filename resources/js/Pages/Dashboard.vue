<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    window.location.replace(route('get-rooms'));
});
</script>

<template>
    <Head title="Maxigram" />
    <div class="min-h-screen bg-gray-50 flex items-center justify-center">
        <div class="flex items-center gap-3 text-gray-400">
            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
            </svg>
            <span class="text-sm font-medium">Redirecting...</span>
        </div>
    </div>
</template>
