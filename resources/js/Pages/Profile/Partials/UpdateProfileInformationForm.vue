<script setup>
import { ref } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import Button from '@/Components/ui/Button.vue';
import Input from '@/Components/ui/Input.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import Separator from '@/Components/ui/Separator.vue';
import { Camera, Check, Loader2 } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    name:  user.name,
    email: user.email,
    image: null,
});

const fileInput = ref(null);
const previewUrl = ref(user.image || null);

const onFileChange = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.image = file;
    previewUrl.value = URL.createObjectURL(file);
};

const submit = () => {
    form.post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <div class="space-y-6">

        <!-- Avatar upload -->
        <div class="flex flex-col items-center gap-4">
            <div class="relative group">
                <Avatar
                    :src="previewUrl"
                    :alt="user.name"
                    size="xl"
                    class="ring-4 ring-white shadow-lg"
                />
                <button
                    type="button"
                    @click="fileInput.click()"
                    class="absolute inset-0 flex items-center justify-center rounded-full bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity"
                    title="Change photo"
                >
                    <Camera class="w-5 h-5 text-white" />
                </button>
            </div>
            <div class="text-center">
                <p class="font-semibold text-gray-900">{{ user.name }}</p>
                <p class="text-sm text-gray-500">{{ user.email }}</p>
            </div>
            <button
                type="button"
                @click="fileInput.click()"
                class="text-xs text-indigo-600 hover:text-indigo-700 font-medium transition-colors"
            >
                Change photo
            </button>
            <input
                ref="fileInput"
                type="file"
                accept="image/*"
                class="hidden"
                @change="onFileChange"
            />
            <p v-if="form.errors.image" class="text-xs text-red-500 -mt-2">{{ form.errors.image }}</p>
        </div>

        <Separator />

        <!-- Fields -->
        <form @submit.prevent="submit" class="space-y-4">

            <div class="space-y-1.5">
                <label for="name" class="block text-sm font-medium text-gray-700">Display name</label>
                <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Your name"
                    autocomplete="name"
                    :class="form.errors.name ? 'border-red-400 focus-visible:ring-red-400' : ''"
                />
                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <div class="space-y-1.5">
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="you@example.com"
                    autocomplete="username"
                    :class="form.errors.email ? 'border-red-400 focus-visible:ring-red-400' : ''"
                />
                <p v-if="form.errors.email" class="text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <!-- Email verification notice -->
            <div v-if="mustVerifyEmail && !user.email_verified_at" class="px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-sm text-amber-800">
                Your email is unverified.
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="font-semibold underline hover:text-amber-900 ml-1"
                >
                    Resend verification
                </Link>
                <span v-show="status === 'verification-link-sent'" class="block mt-1 text-green-700 font-medium">
                    Verification link sent!
                </span>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                    <Loader2 v-if="form.processing" class="w-4 h-4 animate-spin" />
                    <Check v-else class="w-4 h-4" />
                    Save changes
                </Button>

                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-1"
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100"
                    leave-to-class="opacity-0"
                >
                    <span v-if="form.recentlySuccessful" class="flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                        <Check class="w-4 h-4" />
                        Saved!
                    </span>
                </Transition>
            </div>
        </form>

    </div>
</template>
