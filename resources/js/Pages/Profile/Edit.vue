<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';
import { UserCircle } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="py-8 px-4 sm:px-6">
            <div class="mx-auto max-w-lg">

                <!-- Page title -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="h-9 w-9 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <UserCircle class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Profile settings</h1>
                        <p class="text-sm text-gray-500">Update your name, email and photo</p>
                    </div>
                </div>

                <!-- Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>
