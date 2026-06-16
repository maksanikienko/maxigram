<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { MessageSquare, LogOut, ChevronLeft } from 'lucide-vue-next';
import Avatar from '@/Components/ui/Avatar.vue';

// /chat/rooms is a plain Blade view (not Inertia) — use native <a> for those links
const chatUrl = route('get-rooms');

const page = usePage();
const user = page.props.auth.user;

const menuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-gray-50">

        <!-- Top nav -->
        <nav class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 h-14 flex items-center justify-between gap-4">

                <!-- Back to chat (plain <a> — /chat/rooms is Blade, not Inertia) -->
                <a
                    :href="chatUrl"
                    class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-900 transition-colors font-medium"
                >
                    <ChevronLeft class="w-4 h-4" />
                    Back to chat
                </a>

                <!-- Logo -->
                <a :href="chatUrl" class="flex items-center gap-2">
                    <div class="h-7 w-7 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <MessageSquare class="w-3.5 h-3.5 text-white" />
                    </div>
                    <span class="font-bold text-gray-900 text-sm tracking-tight">Maxigram</span>
                </a>

                <!-- User menu -->
                <div class="relative">
                    <button
                        @click="menuOpen = !menuOpen"
                        class="flex items-center gap-2 rounded-xl px-2 py-1.5 hover:bg-gray-100 transition-colors"
                    >
                        <Avatar
                            :src="user.image"
                            :alt="user.name"
                            size="sm"
                        />
                        <span class="hidden sm:block text-sm font-medium text-gray-700 max-w-[120px] truncate">
                            {{ user.name }}
                        </span>
                    </button>

                    <!-- Click-away overlay -->
                    <div v-if="menuOpen" class="fixed inset-0 z-40" @click="menuOpen = false" />

                    <Transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="opacity-0 scale-95"
                        enter-to-class="opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="opacity-100 scale-100"
                        leave-to-class="opacity-0 scale-95"
                    >
                        <div
                            v-if="menuOpen"
                            class="absolute right-0 mt-1 w-48 rounded-xl bg-white shadow-lg border border-gray-100 py-1 z-50"
                        >
                            <Link
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                            >
                                <LogOut class="w-4 h-4" />
                                Sign out
                            </Link>
                        </div>
                    </Transition>
                </div>

            </div>
        </nav>

        <!-- Page header slot -->
        <header v-if="$slots.header" class="bg-white border-b border-gray-100">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 py-4">
                <slot name="header" />
            </div>
        </header>

        <!-- Content -->
        <main>
            <slot />
        </main>

    </div>
</template>
