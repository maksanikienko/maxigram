<script setup >
import {nextTick, ref, watch} from "vue";

import {useModalHandler} from '@/utils/modalHandler.js';
import {scrollToBottom} from "@/utils/scrollToBottom.js";

const props = defineProps(['room','current_user']);
const { isModalOpen, selectedImageUrl, openModal, closeModal } = useModalHandler();

// Scroll down chat window
const messagesContainer = ref(null);
watch(
    () => props.room.messages,
    async () => {
        await nextTick(); // Wait for DOM updates
        setTimeout(() => {
            scrollToBottom(messagesContainer.value);
        }, 400);
    },
    { deep: true }
);
</script>

<template>
    <!-- Messages block-->
    <div id="messages" ref="messagesContainer" v-if="room " class=" flex flex-col space-y-6 p-3 overflow-y-auto h-full scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">

        <div
            v-for="message in room.messages"
            :key="message.id"
        >
            <!--Left messages-->
            <div
                v-if="message.sender_id === room.other_user.id"
                class="chat-message">
                <div class="flex items-end">
                    <div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-2 items-start ">
                        <div class="bg-gray-300 text-gray-600 md:px-4 md:py-2 p-1 rounded-lg inline-block rounded-bl-none relative">
                            <span v-if="message.message" class="text-lg">{{message.message ?? null }}</span>
                            <img
                                v-if="message.picture_url"
                                :src="message.picture_url"
                                alt="Attached Image" class="max-w-48 max-h-48 p-1 rounded-lg cursor-pointer"
                                @click="openModal(message.picture_url)"
                            />
                            <span class="text-gray-500 text-[10px] ml-2 align-bottom flex justify-end">{{ message.formatted_time }}</span>
                        </div>
                    </div>
                    <img v-if="room.other_user.image" :src="room.other_user.image" alt="My profile" class="w-6 h-6 rounded-full order-1 md:block hidden">
                    <img v-else src="https://robohash.org/yyy" alt="avatar" class="w-10 h-10 rounded-full md:block hidden">
                </div>
            </div>
            <!--Right messages-->
            <div v-else >
                <div class="flex items-end justify-end">
                    <div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end ">
                        <div class="bg-blue-500 text-white md:px-4 md:py-2 p-1 rounded-lg inline-block rounded-br-none relative ">
                            <span v-if="message.message" class="text-lg">{{message.message ?? null }}</span>
                            <img
                                v-if="message.picture_url"
                                :src="message.picture_url"
                                alt="Attached Image" class="max-w-48 max-h-48 p-1 rounded-lg cursor-pointer"
                                @click="openModal(message.picture_url)"
                            />
                            <span class="text-gray-200 text-[10px] ml-2 align-bottom flex justify-end ">{{ message.formatted_time }}</span>
                        </div>
                    </div>
                    <img v-if="props.current_user.image" :src="props.current_user.image" alt="My profile" class="w-6 h-6 rounded-full order-2 md:block hidden">
                    <img v-else src="https://robohash.org/yyy" alt="avatar" class="w-10 h-10 rounded-full md:block hidden">
                </div>
                <!-- Main modal -->
                <div
                    v-if="isModalOpen"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-5 transition-opacity duration-300"
                    :class="{ 'opacity-100': isModalOpen, 'opacity-0 pointer-events-none': !isModalOpen }"

                >
                    <div class="relative p-4 md:p-5 space-y-4  bg-white rounded-lg overflow-hidden shadow-lg transition-transform transform duration-300 scale-100"
                         :class="{ 'scale-100': isModalOpen, 'scale-90': !isModalOpen }"
                    >
                        <img :src="selectedImageUrl" alt="Full Image" class="max-w-xs md:max-w-2xl h-auto rounded-lg"/>
                        <button
                            class="absolute top-2 right-2 bg-gray-800 text-white rounded-full p-1"
                            @click="closeModal"
                        >
                            ✖
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>


</template>

