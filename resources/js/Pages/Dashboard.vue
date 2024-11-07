<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import {onMounted} from "vue";
// const props = defineProps(['users'])
onMounted(() => {
    window.location.href = '/'
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900">
                        You're logged in!
                    </div>
                </div>

<!--                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 mt-5">-->
<!--                    <div v-for="user in users" :key="user.id">-->
<!--                        <a-->
<!--                            :href="`/chat/${user.id}`"-->
<!--                            class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700"-->
<!--                        >-->
<!--                            <h3 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ user.name }}</h3>-->
<!--                            <p class="font-normal text-gray-700 dark:text-gray-400">{{ user.email }}</p>-->
<!--                        </a>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>

    </AuthenticatedLayout>
</template>
