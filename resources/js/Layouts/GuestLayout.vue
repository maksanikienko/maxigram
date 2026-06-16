<script setup>
import { MessageSquare, Zap, Users, Image } from 'lucide-vue-next';

const features = [
    { icon: Zap,          text: 'Real-time WebSocket delivery' },
    { icon: Users,        text: 'Group chats & direct messages' },
    { icon: Image,        text: 'Image sharing & emoji support' },
    { icon: MessageSquare,text: 'Online presence indicators' },
];
</script>

<template>
    <div class="min-h-screen flex">

        <!-- Left branding panel -->
        <div class="hidden lg:flex lg:w-5/12 xl:w-1/2 flex-col justify-between p-12 bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 relative overflow-hidden">
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-600 rounded-full opacity-20 blur-3xl" />
                <div class="absolute bottom-0 right-0 w-80 h-80 bg-purple-600 rounded-full opacity-20 blur-3xl" />
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-blue-500 rounded-full opacity-10 blur-3xl" />
            </div>

            <div class="relative flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/40">
                    <MessageSquare class="w-5 h-5 text-white" />
                </div>
                <span class="text-white font-bold text-xl tracking-tight">Maxigram</span>
            </div>

            <div class="relative space-y-8">
                <div>
                    <h1 class="text-4xl xl:text-5xl font-bold text-white leading-tight">
                        Connect with anyone,<br/>anywhere
                    </h1>
                    <p class="mt-4 text-indigo-200 text-lg leading-relaxed max-w-sm">
                        Real-time messaging built for speed and simplicity. Stay close to the people that matter.
                    </p>
                </div>

                <div class="space-y-3">
                    <div v-for="f in features" :key="f.text" class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                            <component :is="f.icon" class="w-4 h-4 text-indigo-300" />
                        </div>
                        <span class="text-sm text-indigo-100">{{ f.text }}</span>
                    </div>
                </div>
            </div>

            <div class="relative text-indigo-400/60 text-sm">
                © 2025 Maxigram — Realtime Messaging
            </div>
        </div>

        <!-- Right form panel -->
        <div class="flex-1 flex items-center justify-center p-6 bg-gray-50">
            <div class="w-full max-w-md">

                <!-- Mobile logo -->
                <div class="flex items-center justify-center gap-3 mb-8 lg:hidden">
                    <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg">
                        <MessageSquare class="w-5 h-5 text-white" />
                    </div>
                    <span class="text-gray-900 font-bold text-xl tracking-tight">Maxigram</span>
                </div>

                <div class="bg-white rounded-2xl shadow-xl shadow-gray-200/60 border border-gray-100 p-8">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
