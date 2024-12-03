<script setup>
import {nextTick, onMounted, ref,watch} from 'vue';
import axios from 'axios';
import 'emoji-picker-element';
import FriendsList from './parts/FriendsList.vue';
import { scrollToBottom } from "./../utils/scrollToBottom.js";
import { useEmojiPicker } from './../utils/emojiPicker.js'
import { useFileHandler } from './../utils/fileHandler.js';
import { useModalHandler } from './../utils/modalHandler.js';
import { useStatusHandler } from './../utils/statusHandler.js';
import { connectToPresenceChannel, connectToPrivateChannel, sendTypingEvent } from './../utils/conectChannel.js';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
// import ApplicationLogo from "/ApplicationLogo.vue";

const { showEmojiPicker, selectedEmoji, emojiPickerContainer, toggleEmojiPicker, addEmoji } = useEmojiPicker();
const { selectedFile, selectedFileUrl, attachedFile, clearAttachedFile } = useFileHandler();
const { isModalOpen, selectedImageUrl, openModal, closeModal } = useModalHandler();

const props = defineProps([ 'current_user','rooms','profileUrl']);
const newMessage = ref("");
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);


const rooms = ref([...props.rooms]);
const { loadOnlineStatus, saveOnlineStatus } = useStatusHandler(rooms);

const showFriendsList = ref(false);

const selectedRoom = ref({
    room_id: '',
    other_user: {  },
    messages: []
});
const selectRoom = (room) => {
    selectedRoom.value = room;
    connectToPrivateChannel(room.room_id,props, selectedRoom, isFriendTyping, isFriendTypingTimer);
};

// Scroll down chat window
const messagesContainer = ref(null);
watch(
    () => selectedRoom.value.messages,
    async () => {
        await nextTick(); // Wait for DOM updates
        setTimeout(() => {
                scrollToBottom(messagesContainer.value);
        }, 400);
    },
    { deep: true }
);
watch(selectedEmoji, (emoji) => {
    if (emoji) {
        newMessage.value += emoji;
    }
});

const sendMessage = () => {
    if (newMessage.value.trim() !== "" || selectedFile.value ) {
        const formData = new FormData();
        formData.append('message', newMessage.value);
        if (selectedFile.value) {
            formData.append('image', selectedFile.value);
        }
        axios.post(`/chat/room/${selectedRoom.value.room_id}/send`, formData,
            {
                headers: { 'Content-Type': 'multipart/form-data' }
            })
            .then((response) => {
                // console.log('sent_msg',response.data)
                selectedRoom.value.messages.push(response.data);
                // console.log('Updated selectedRoom:', selectedRoom.value);
                newMessage.value = "";
                selectedFile.value = "";
                selectedFileUrl.value = "";
            });
    }
};
    // Send event typing
const sendTyping = () => {
    sendTypingEvent(selectedRoom.value.room_id, props.current_user.id);
};
onMounted(() => {
    selectRoom(rooms.value[0]) // Open 1 chat with friend
    connectToPresenceChannel(rooms, saveOnlineStatus);
    connectToPrivateChannel(selectedRoom.value.id, props, selectedRoom, isFriendTyping, isFriendTypingTimer);

    loadOnlineStatus()
});

</script>

<template>

    <div class="container mx-auto flex justify-center h-screen bg-gray-100 ">
        <!-- Main block -->
        <div class="flex flex-row md:w-3/4 w-full border rounded-lg shadow-lg h-full relative">

            <!-- Left block with friends list -->
                <FriendsList
                    :rooms="rooms"
                    :selectedRoom="selectedRoom"
                    :showFriendsList="showFriendsList"
                    @toggleFriendsList="showFriendsList = !showFriendsList"
                    @selectRoom="selectRoom"
                />

            <!-- Vertical line -->
            <div class="w-px bg-gray-300 md:flex hidden"></div>

            <div class="flex-1 p-4 bg-white flex flex-col h-full">
                <!-- Messenger block Header -->
                <div class="flex items-center justify-between py-3 border-b-2 border-gray-200">
                    <div class="relative md:mx-auto md:ml-0 flex items-center space-x-4">
                        <div class="relative">
                            <span v-if="selectedRoom.other_user.is_online" class="absolute text-green-500 right-0 bottom-0">
                              <svg width="20" height="20">
                                <circle cx="8" cy="8" r="8" fill="currentColor"></circle>
                              </svg>
                            </span>
                            <span v-else class="absolute text-green-500 right-0 bottom-0">
                              <svg width="16" height="16">
                                <circle cx="8" cy="8" r="8" fill="#7d7c7c"></circle>
                              </svg>
                            </span>
                        <img v-if="selectedRoom.other_user.image" :src="selectedRoom.other_user.image" alt="" class="w-10 sm:w-16 h-10 sm:h-16 rounded-full">
                        <img v-else src="https://robohash.org/vvvv" alt="avatar" class="w-10 h-10 rounded-full">
        </div>
                        <div v-if="selectedRoom" class="flex flex-col leading-tight">
                            <div class="text-2xl mt-1 flex items-center">
                                <span class="text-gray-700 mr-3">{{selectedRoom.other_user.name}}</span>
                            </div>
                            <span v-if="selectedRoom.other_user.is_online" class="text-lg text-gray-600">Online</span>
                            <span v-if="!selectedRoom.other_user.is_online" class="text-lg text-gray-600">Offline</span>
                        </div>
                    </div>

                    <div class="relative">
                        <button id="userDropdown" data-dropdown-toggle="dropdownMenu" class="text-gray-700 font-semibold flex gap-3 ">
                            <img v-if="props.current_user.image" class="md:max-w-16 max-w-8 md:max-h-16 max-h-8 rounded-full" :src="props.current_user.image" alt="">
                            <ApplicationLogo
                                v-else
                                class="block md:max-w-16 max-w-8 w-auto fill-current text-gray-800"
                            />
                        </button>
                        <div id="dropdownMenu" class="z-10 hidden divide-y divide-gray-100 rounded-lg shadow bg-gray-50">
                            <ul class="py-1 text-gray-700" aria-labelledby="userDropdown">
                                <li>
                                    <a v-if="profileUrl" :href="profileUrl" class="block px-4 py-2 text-sm">Profile</a>
                                </li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Messages block-->
                <div id="messages" ref="messagesContainer" v-if="selectedRoom" class=" flex flex-col space-y-6 p-3 overflow-y-auto h-full scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">

                    <div
                        v-for="message in selectedRoom.messages"
                        :key="message.id"
                    >
                    <!--Left messages-->
                    <div
                        v-if="message.sender_id === selectedRoom.other_user.id"
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
                            <img v-if="selectedRoom.other_user.image" :src="selectedRoom.other_user.image" alt="My profile" class="w-6 h-6 rounded-full order-1 md:block hidden">
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
<!--                                <button @click="closeModal" class=" text-white bg-gray-600 hover:bg-gray-800 rounded-full p-2 focus:outline-none">-->
<!--                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-4 h-4">-->
<!--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />-->
<!--                                    </svg>-->
<!--                                </button>-->
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

                <div v-if="isFriendTyping" class="mx-5 text-gray-600 text-sm">
                    {{ selectedRoom.other_user.name }} typing...
                </div>

                <!--Emoji container -->
                <div v-if="showEmojiPicker"
                     class="absolute bottom-0 md:right-0  my-20 bg-white border rounded-lg shadow-lg p-2">
                    <emoji-picker  @emoji-click="addEmoji"></emoji-picker>
                </div>

                <!-- Message input form -->
                <form v-if="selectedRoom.room_id" @submit.prevent="sendMessage" class="border-t-2 border-gray-200 px-4 pt-4">

                    <!--Attached file-->
                    <div v-if="selectedFileUrl" class=" relative  flex justify-end py-2 px-6">
                        <!-- Remove attached file -->
                        <button
                            @click="clearAttachedFile"
                            class="absolute top-1 right-1 bg-gray-200 rounded-full p-1 text-gray-600 hover:bg-gray-300"
                            aria-label="Remove image"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                        <img class="max-w-24 max-h-24" :src="selectedFileUrl" alt="Selected Image" />
                    </div>

                    <div class="relative flex ">

                        <!--  Emoji button-->
                      <span class="flex items-center">
                        <button @click="toggleEmojiPicker" type="button" class="p-2 text-gray-500 rounded-full cursor-pointer hover:text-gray-900 hover:bg-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </button>
                      </span>

                        <input
                            v-model="newMessage"
                            @input="sendTyping"
                            type="text"
                            placeholder="Write your message!"
                            class="w-full bg-gray-200 rounded-md"
                        >
                        <!--  Attach button-->
                        <button @click="$refs.fileInput.click()" type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                            <input type="file" @input="attachedFile" accept="image/*" class="hidden" ref="fileInput" />
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 20">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M1 6v8a5 5 0 1 0 10 0V4.5a3.5 3.5 0 1 0-7 0V13a2 2 0 0 0 4 0V6"/>
                            </svg>
                        </button>

                        <!--  Send button-->
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg md:px-4 md:py-3 p-2 text-white bg-blue-500 hover:bg-blue-400 focus:outline-none ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 ml-2 transform rotate-45">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

</template>

<style scoped>
@import "flowbite";
emoji-picker {
    --num-columns: 5;
    --emoji-size: 1.5rem;
}

</style>
