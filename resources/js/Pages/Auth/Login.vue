<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const demoUsers = [
    { name: 'John Doe',       email: 'john_doe@example.com' },
    { name: 'Klaus Jenkins',  email: 'klaus_jenkins@example.com' },
    { name: 'Casandra Stray', email: 'casandra_stray@example.com' },
    { name: 'Garry Border',   email: 'garry_border@example.com' },
    { name: 'Travis Scott',   email: 'travis_scott@example.com' },
];

const fillDemo = (user) => {
    form.email    = user.email;
    form.password = 'password';
};
</script>

<template>
    <GuestLayout>
        <Head title="Sign in" />

        <!-- Heading -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Welcome back</h2>
            <p class="mt-1 text-sm text-gray-500">Sign in to continue your conversations</p>
        </div>

        <!-- Status -->
        <div v-if="status" class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-sm font-medium text-green-700">
            {{ status }}
        </div>

        <!-- Google OAuth -->
        <a
            href="/auth/google"
            class="flex w-full items-center justify-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-all mb-5"
        >
            <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Continue with Google
        </a>

        <!-- Divider -->
        <div class="relative mb-5">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-200" />
            </div>
            <div class="relative flex justify-center">
                <span class="bg-white px-3 text-xs text-gray-400 uppercase tracking-widest">or</span>
            </div>
        </div>

        <!-- Form -->
        <form @submit.prevent="submit" class="space-y-4">
            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="you@example.com"
                    autocomplete="username"
                    autofocus
                    :class="form.errors.email ? 'border-red-400 focus-visible:ring-red-400' : ''"
                />
                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-indigo-600 hover:text-indigo-700 font-medium"
                    >
                        Forgot password?
                    </Link>
                </div>
                <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    :class="form.errors.password ? 'border-red-400 focus-visible:ring-red-400' : ''"
                />
                <p v-if="form.errors.password" class="text-xs text-red-500">{{ form.errors.password }}</p>
            </div>

            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input
                    type="checkbox"
                    v-model="form.remember"
                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer"
                />
                <span class="text-sm text-gray-600">Remember me</span>
            </label>

            <Button
                type="submit"
                class="w-full"
                :disabled="form.processing"
            >
                <span v-if="form.processing" class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                    </svg>
                    Signing in...
                </span>
                <span v-else>Sign in</span>
            </Button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-500">
            Don't have an account?
            <Link :href="route('register')" class="text-indigo-600 font-semibold hover:text-indigo-700 ml-1">
                Sign up
            </Link>
        </p>

        <!-- Demo accounts -->
        <div class="mt-7 pt-6 border-t border-gray-100">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Demo accounts</p>
                <span class="text-xs text-gray-400">password: <code class="bg-gray-100 px-1.5 py-0.5 rounded font-mono">password</code></span>
            </div>
            <div class="space-y-1.5">
                <button
                    v-for="user in demoUsers"
                    :key="user.email"
                    type="button"
                    @click="fillDemo(user)"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition-all text-left group"
                >
                    <span class="h-7 w-7 rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold flex items-center justify-center shrink-0 group-hover:bg-indigo-200 transition-colors">
                        {{ user.name.charAt(0) }}
                    </span>
                    <span class="flex-1 min-w-0">
                        <span class="block text-sm font-medium text-gray-800 truncate">{{ user.name }}</span>
                        <span class="block text-xs text-gray-400 truncate">{{ user.email }}</span>
                    </span>
                    <span class="text-xs text-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 font-medium">
                        Use →
                    </span>
                </button>
            </div>
            <p class="mt-2.5 text-xs text-gray-400 text-center">Click to prefill · then press Sign in</p>
        </div>

    </GuestLayout>
</template>
