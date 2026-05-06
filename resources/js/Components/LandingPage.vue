<script setup>
import { ref } from 'vue';
import {
    MessageSquare, Zap, Shield, Users, Globe, Star,
    ArrowRight, Github, Twitter, Check, ChevronRight,
    MessageCircle, Video, Lock, Bell
} from 'lucide-vue-next';

const props = defineProps(['loginUrl', 'registerUrl']);

const features = [
    { icon: Zap, title: 'Real-time messaging', desc: 'Instant message delivery powered by WebSocket technology. No delays, no refresh needed.', color: 'text-yellow-500', bg: 'bg-yellow-50' },
    { icon: Shield, title: 'Private & Secure', desc: 'End-to-end channel encryption. Your conversations stay between you and your contacts.', color: 'text-emerald-500', bg: 'bg-emerald-50' },
    { icon: Users, title: 'Group Chats', desc: 'Create groups for teams, friends, or communities. Share files and images together.', color: 'text-blue-500', bg: 'bg-blue-50' },
    { icon: Globe, title: 'Online Presence', desc: 'See who is online in real-time with live presence tracking across all devices.', color: 'text-violet-500', bg: 'bg-violet-50' },
    { icon: Bell, title: 'Typing Indicators', desc: 'Know when someone is composing a reply with smooth animated typing indicators.', color: 'text-rose-500', bg: 'bg-rose-50' },
    { icon: Lock, title: 'Auth Protected', desc: 'Secure login with email verification and encrypted session management.', color: 'text-orange-500', bg: 'bg-orange-50' },
];

const stats = [
    { value: '∞', label: 'Messages' },
    { value: '<1ms', label: 'Latency' },
    { value: '100%', label: 'Uptime' },
    { value: 'Free', label: 'Forever' },
];

const menuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <!-- ─── HEADER ─── -->
        <header class="sticky top-0 z-50 border-b border-border bg-white/80 backdrop-blur-md">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-xl bg-primary flex items-center justify-center shadow-sm">
                        <MessageSquare class="w-4 h-4 text-white" />
                    </div>
                    <span class="font-bold text-lg tracking-tight text-foreground">Maxigram</span>
                </div>

                <!-- Desktop nav -->
                <nav class="hidden md:flex items-center gap-6">
                    <a href="#features" class="text-sm text-muted-foreground hover:text-foreground transition-colors">Features</a>
                    <a href="#stats" class="text-sm text-muted-foreground hover:text-foreground transition-colors">About</a>
                </nav>

                <!-- CTA buttons -->
                <div class="hidden md:flex items-center gap-3">
                    <a :href="loginUrl" class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors px-3 py-1.5">
                        Sign In
                    </a>
                    <a :href="registerUrl" class="inline-flex items-center gap-1.5 bg-primary text-primary-foreground hover:bg-primary/90 text-sm font-medium px-4 py-2 rounded-lg transition-colors shadow-sm">
                        Get started
                        <ArrowRight class="w-3.5 h-3.5" />
                    </a>
                </div>

                <!-- Mobile menu button -->
                <button @click="menuOpen = !menuOpen" class="md:hidden p-2 rounded-lg hover:bg-accent transition-colors">
                    <div class="w-5 h-0.5 bg-foreground mb-1 transition-all" :class="menuOpen ? 'rotate-45 translate-y-1.5' : ''" />
                    <div class="w-5 h-0.5 bg-foreground mb-1 transition-all" :class="menuOpen ? 'opacity-0' : ''" />
                    <div class="w-5 h-0.5 bg-foreground transition-all" :class="menuOpen ? '-rotate-45 -translate-y-1.5' : ''" />
                </button>
            </div>

            <!-- Mobile menu -->
            <Transition name="slide-down">
                <div v-if="menuOpen" class="md:hidden border-t border-border bg-white px-4 py-4 space-y-3">
                    <a href="#features" class="block text-sm text-muted-foreground hover:text-foreground" @click="menuOpen=false">Features</a>
                    <a href="#stats" class="block text-sm text-muted-foreground hover:text-foreground" @click="menuOpen=false">About</a>
                    <div class="pt-2 flex flex-col gap-2 border-t border-border">
                        <a :href="loginUrl" class="text-center text-sm font-medium py-2 rounded-lg border border-input hover:bg-accent transition-colors">Sign In</a>
                        <a :href="registerUrl" class="text-center text-sm font-medium py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors">Get started</a>
                    </div>
                </div>
            </Transition>
        </header>

        <!-- ─── HERO ─── -->
        <section class="flex-1 flex flex-col items-center justify-center text-center px-4 py-20 sm:py-28 relative overflow-hidden">
            <!-- Background glow -->
            <div class="absolute inset-0 -z-10">
                <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full bg-primary/5 blur-3xl" />
                <div class="absolute bottom-0 right-0 w-[400px] h-[400px] rounded-full bg-violet-500/5 blur-3xl" />
            </div>

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-semibold mb-6 border border-primary/20">
                <Zap class="w-3.5 h-3.5" />
                Real-time · Secure · Beautiful
            </div>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-foreground max-w-3xl leading-tight mb-6">
                Messaging that feels
                <span class="text-primary"> instant.</span>
            </h1>

            <p class="text-muted-foreground text-lg max-w-xl mb-10 leading-relaxed">
                Maxigram is a full-featured real-time messenger. Send messages, share images, create groups, and stay connected — all in one beautiful app.
            </p>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <a
                    :href="registerUrl"
                    class="inline-flex items-center gap-2 bg-primary text-primary-foreground hover:bg-primary/90 font-semibold px-6 py-3 rounded-xl transition-all shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5"
                >
                    Start messaging for free
                    <ArrowRight class="w-4 h-4" />
                </a>
                <a
                    :href="loginUrl"
                    class="inline-flex items-center gap-2 text-foreground hover:text-primary font-medium px-6 py-3 rounded-xl border border-input hover:border-primary/40 transition-all bg-white"
                >
                    Sign in to your account
                    <ChevronRight class="w-4 h-4" />
                </a>
            </div>

            <!-- Mock UI preview -->
            <div class="mt-16 w-full max-w-2xl rounded-2xl border border-border shadow-2xl shadow-black/10 overflow-hidden bg-white">
                <!-- App header mock -->
                <div class="flex items-center bg-sidebar px-4 py-3 gap-3">
                    <div class="flex gap-1.5">
                        <div class="h-2.5 w-2.5 rounded-full bg-red-400/70" />
                        <div class="h-2.5 w-2.5 rounded-full bg-yellow-400/70" />
                        <div class="h-2.5 w-2.5 rounded-full bg-green-400/70" />
                    </div>
                    <div class="flex-1 flex items-center gap-2">
                        <div class="h-6 w-6 rounded-lg bg-primary/80 flex items-center justify-center">
                            <MessageSquare class="w-3 h-3 text-white" />
                        </div>
                        <span class="text-sidebar-foreground/80 text-xs font-medium">Maxigram</span>
                    </div>
                </div>
                <!-- Mock chat -->
                <div class="flex" style="height:220px">
                    <!-- Sidebar -->
                    <div class="w-48 bg-sidebar/95 px-2 py-2 flex flex-col gap-1 border-r border-white/10">
                        <div v-for="i in 4" :key="i" :class="['flex items-center gap-2 px-2 py-1.5 rounded-lg', i===1 ? 'bg-primary' : 'hover:bg-white/10']">
                            <div :class="['h-6 w-6 rounded-full flex-shrink-0', i===1 ? 'bg-white/30' : 'bg-white/20']" />
                            <div class="flex-1 space-y-1">
                                <div :class="['h-1.5 rounded-full', i===1 ? 'bg-white/60 w-16' : 'bg-white/30 w-14']" />
                                <div :class="['h-1 rounded-full', i===1 ? 'bg-white/40 w-10' : 'bg-white/20 w-12']" />
                            </div>
                        </div>
                    </div>
                    <!-- Chat -->
                    <div class="flex-1 flex flex-col">
                        <div class="border-b border-border px-3 py-2 flex items-center gap-2">
                            <div class="h-6 w-6 rounded-full bg-primary/20" />
                            <div class="h-2 w-20 rounded-full bg-muted" />
                        </div>
                        <div class="flex-1 px-3 py-2 space-y-2 overflow-hidden">
                            <div class="flex gap-2 justify-start">
                                <div class="h-6 w-6 rounded-full bg-muted shrink-0" />
                                <div class="bg-muted rounded-xl rounded-bl-none px-3 py-1.5 max-w-[60%]">
                                    <div class="h-2 w-28 rounded-full bg-muted-foreground/30" />
                                </div>
                            </div>
                            <div class="flex gap-2 justify-end">
                                <div class="bg-primary rounded-xl rounded-br-none px-3 py-1.5 max-w-[60%]">
                                    <div class="h-2 w-24 rounded-full bg-primary-foreground/50" />
                                </div>
                                <div class="h-6 w-6 rounded-full bg-primary/20 shrink-0" />
                            </div>
                            <div class="flex gap-2 justify-start">
                                <div class="h-6 w-6 rounded-full bg-muted shrink-0" />
                                <div class="bg-muted rounded-xl rounded-bl-none px-3 py-1.5 max-w-[70%]">
                                    <div class="h-2 w-36 rounded-full bg-muted-foreground/30" />
                                </div>
                            </div>
                        </div>
                        <div class="border-t border-border px-3 py-2 flex items-center gap-2">
                            <div class="flex-1 h-6 rounded-full bg-muted" />
                            <div class="h-6 w-6 rounded-full bg-primary/80" />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── STATS ─── -->
        <section id="stats" class="py-16 bg-sidebar">
            <div class="max-w-4xl mx-auto px-4 grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div v-for="stat in stats" :key="stat.label">
                    <div class="text-3xl font-extrabold text-sidebar-foreground mb-1">{{ stat.value }}</div>
                    <div class="text-sm text-sidebar-foreground/60">{{ stat.label }}</div>
                </div>
            </div>
        </section>

        <!-- ─── FEATURES ─── -->
        <section id="features" class="py-20 px-4">
            <div class="max-w-5xl mx-auto">
                <div class="text-center mb-14">
                    <h2 class="text-3xl sm:text-4xl font-bold text-foreground mb-4">Everything you need</h2>
                    <p class="text-muted-foreground text-lg max-w-xl mx-auto">Built with modern technology to give you the best messaging experience.</p>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="f in features"
                        :key="f.title"
                        class="p-6 rounded-2xl border border-border bg-white hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200"
                    >
                        <div :class="['inline-flex p-2.5 rounded-xl mb-4', f.bg]">
                            <component :is="f.icon" :class="['w-5 h-5', f.color]" />
                        </div>
                        <h3 class="font-semibold text-foreground mb-2">{{ f.title }}</h3>
                        <p class="text-sm text-muted-foreground leading-relaxed">{{ f.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ─── CTA BANNER ─── -->
        <section class="py-20 px-4 bg-gradient-to-br from-primary/5 via-background to-violet-500/5">
            <div class="max-w-2xl mx-auto text-center">
                <h2 class="text-3xl sm:text-4xl font-bold text-foreground mb-4">Ready to get started?</h2>
                <p class="text-muted-foreground mb-8">Create your free account and start messaging in seconds. No credit card required.</p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a :href="registerUrl" class="inline-flex items-center gap-2 bg-primary text-primary-foreground hover:bg-primary/90 font-semibold px-8 py-3.5 rounded-xl transition-all shadow-lg shadow-primary/25">
                        Create free account
                        <ArrowRight class="w-4 h-4" />
                    </a>
                    <a :href="loginUrl" class="inline-flex items-center gap-2 font-medium px-8 py-3.5 rounded-xl border border-input hover:bg-accent transition-colors bg-white">
                        Sign in
                    </a>
                </div>
                <div class="mt-6 flex items-center justify-center gap-6 text-sm text-muted-foreground">
                    <span class="flex items-center gap-1.5"><Check class="w-4 h-4 text-emerald-500" /> Free forever</span>
                    <span class="flex items-center gap-1.5"><Check class="w-4 h-4 text-emerald-500" /> No ads</span>
                    <span class="flex items-center gap-1.5"><Check class="w-4 h-4 text-emerald-500" /> Unlimited messages</span>
                </div>
            </div>
        </section>

        <!-- ─── FOOTER ─── -->
        <footer class="border-t border-border bg-white py-10 px-4">
            <div class="max-w-6xl mx-auto">
                <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                    <!-- Brand -->
                    <div class="flex items-center gap-2.5">
                        <div class="h-7 w-7 rounded-xl bg-primary flex items-center justify-center">
                            <MessageSquare class="w-3.5 h-3.5 text-white" />
                        </div>
                        <span class="font-bold text-foreground">Maxigram</span>
                    </div>

                    <!-- Links -->
                    <div class="flex items-center gap-6 text-sm text-muted-foreground">
                        <a href="#features" class="hover:text-foreground transition-colors">Features</a>
                        <a :href="loginUrl" class="hover:text-foreground transition-colors">Sign In</a>
                        <a :href="registerUrl" class="hover:text-foreground transition-colors">Register</a>
                    </div>

                    <!-- Legal -->
                    <p class="text-xs text-muted-foreground">
                        © {{ new Date().getFullYear() }} Maxigram. Built with Laravel & Vue.
                    </p>
                </div>
            </div>
        </footer>

    </div>
</template>

<style scoped>
.slide-down-enter-active, .slide-down-leave-active { transition: all 0.2s ease; }
.slide-down-enter-from, .slide-down-leave-to { opacity: 0; transform: translateY(-8px); }
</style>
