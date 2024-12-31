<script setup>
import {nextTick, onMounted, ref,watch} from 'vue';
import axios from 'axios';
import 'emoji-picker-element';
import FriendsList from './parts/FriendsList.vue';
import GroupsList from "./parts/GroupsList.vue";
// import { scrollToBottom } from "./../utils/scrollToBottom.js";
import { useEmojiPicker } from './../utils/emojiPicker.js'
import { useFileHandler } from './../utils/fileHandler.js';
// import { useModalHandler } from './../utils/modalHandler.js';
import { useStatusHandler } from './../utils/statusHandler.js';
import { connectToPresenceChannel, connectToPrivateChannel, sendTypingEvent } from './../utils/conectChannel.js';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import MessengerHeader from "@/Components/parts/MessengerHeader.vue";
import Messages from "@/Components/parts/Messages.vue";
import MessageInput from "@/Components/parts/MessageInput.vue";
// import ApplicationLogo from "/ApplicationLogo.vue";

// const { showEmojiPicker, selectedEmoji, emojiPickerContainer, toggleEmojiPicker, addEmoji } = useEmojiPicker();
// const { selectedFile, selectedFileUrl, attachedFile, clearAttachedFile } = useFileHandler();
// const { isModalOpen, selectedImageUrl, openModal, closeModal } = useModalHandler();

const props = defineProps([ 'current_user','rooms','profileUrl']);
// const newMessage = ref("");
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
const showRoom = ref(true);
const toggleRoom = () => {
    showRoom.value = false
};
const selectRoom = (room) => {
    selectedRoom.value = room;
    connectToPrivateChannel(room.room_id,props, selectedRoom, isFriendTyping, isFriendTypingTimer);
};

// watch(selectedEmoji, (emoji) => {
//     if (emoji) {
//         newMessage.value += emoji;
//     }
// });

// const sendMessage = () => {
//     if (newMessage.value.trim() !== "" || selectedFile.value ) {
//         const formData = new FormData();
//         formData.append('message', newMessage.value);
//         if (selectedFile.value) {
//             formData.append('image', selectedFile.value);
//         }
//         axios.post(`/chat/room/${selectedRoom.value.room_id}/send`, formData,
//             {
//                 headers: { 'Content-Type': 'multipart/form-data' }
//             })
//             .then((response) => {
//                 // console.log('sent_msg',response.data)
//                 selectedRoom.value.messages.push(response.data);
//                 // console.log('Updated selectedRoom:', selectedRoom.value);
//                 newMessage.value = "";
//                 selectedFile.value = "";
//                 selectedFileUrl.value = "";
//             });
//     }
// };
    // Send event typing
const handleTyping = () => {
    console.log('1')
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

            <!--Burger Menu-->
            <button
                @click="$emit('toggleFriendsList')"
                class="absolute top-4 left-4 p-2 text-gray-500 bg-gray-200 rounded md:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            <div
                :class="{'hidden': !showFriendsList}"
                class="absolute md:relative top-0 left-0 md:top-auto md:left-auto md:block bg-gray-200 border-r border-gray-300 p-4 w-3/4 md:w-1/4 h-full z-10 transition-all duration-300">
                <!-- Left block with friends list -->
                <FriendsList
                    :rooms="rooms"
                    :selectedRoom="selectedRoom"
                    :showFriendsList="showFriendsList"
                    @toggleFriendsList="showFriendsList = !showFriendsList"
                    @selectRoom="selectRoom"
                />
                <!--Left block with Groups list-->
                <GroupsList
                    @selectGroup="toggleRoom"
                />
            </div>

            <!-- Vertical line -->
            <div class="w-px bg-gray-300 md:flex hidden"></div>

            <div v-if="showRoom" class="flex-1 p-4 bg-white flex flex-col h-full">

                <!-- MessengerHeader block-->
                <MessengerHeader
                    :room="selectedRoom"
                    :current_user="props.current_user"
                    :profileUrl="props.profileUrl"
                />
                <!-- Messages block-->
                <Messages :room="selectedRoom"
                          :current_user="props.current_user"
                          @typing="handleTyping"
                />

                <div v-if="isFriendTyping" class="mx-5 text-gray-600 text-sm">
                    {{ selectedRoom.other_user.name }} typing...
                </div>

<!--                &lt;!&ndash;Emoji container &ndash;&gt;-->
<!--                <div v-if="showEmojiPicker"-->
<!--                     class="absolute bottom-0 md:right-0  my-20 bg-white border rounded-lg shadow-lg p-2">-->
<!--                    <emoji-picker  @emoji-click="addEmoji"></emoji-picker>-->
<!--                </div>-->

                <!-- Message input form -->
                <MessageInput
                    :room="selectedRoom"
                />
<!--                <form v-if="selectedRoom.room_id" @submit.prevent="sendMessage" class="border-t-2 border-gray-200 px-4 pt-4">-->

<!--                    &lt;!&ndash;Attached file&ndash;&gt;-->
<!--                    <div v-if="selectedFileUrl" class=" relative  flex justify-end py-2 px-6">-->
<!--                        &lt;!&ndash; Remove attached file &ndash;&gt;-->
<!--                        <button-->
<!--                            @click="clearAttachedFile"-->
<!--                            class="absolute top-1 right-1 bg-gray-200 rounded-full p-1 text-gray-600 hover:bg-gray-300"-->
<!--                            aria-label="Remove image"-->
<!--                        >-->
<!--                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">-->
<!--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />-->
<!--                            </svg>-->
<!--                        </button>-->
<!--                        <img class="max-w-24 max-h-24" :src="selectedFileUrl" alt="Selected Image" />-->
<!--                    </div>-->

<!--                    <div class="relative flex ">-->

<!--                        &lt;!&ndash;  Emoji button&ndash;&gt;-->
<!--                      <span class="flex items-center">-->
<!--                        <button @click="toggleEmojiPicker" type="button" class="p-2 text-gray-500 rounded-full cursor-pointer hover:text-gray-900 hover:bg-gray-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">-->
<!--                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">-->
<!--                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>-->
<!--                            </svg>-->
<!--                        </button>-->
<!--                      </span>-->

<!--                        <input-->
<!--                            v-model="newMessage"-->
<!--                            @input="sendTyping"-->
<!--                            type="text"-->
<!--                            placeholder="Write your message!"-->
<!--                            class="w-full bg-gray-200 rounded-md"-->
<!--                        >-->
<!--                        &lt;!&ndash;  Attach button&ndash;&gt;-->
<!--                        <button @click="$refs.fileInput.click()" type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">-->
<!--                            <input type="file" @input="attachedFile" accept="image/*" class="hidden" ref="fileInput" />-->
<!--                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 20">-->
<!--                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M1 6v8a5 5 0 1 0 10 0V4.5a3.5 3.5 0 1 0-7 0V13a2 2 0 0 0 4 0V6"/>-->
<!--                            </svg>-->
<!--                        </button>-->

<!--                        &lt;!&ndash;  Send button&ndash;&gt;-->
<!--                        <button type="submit" class="inline-flex items-center justify-center rounded-lg md:px-4 md:py-3 p-2 text-white bg-blue-500 hover:bg-blue-400 focus:outline-none ml-2">-->
<!--                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 ml-2 transform rotate-45">-->
<!--                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>-->
<!--                            </svg>-->
<!--                        </button>-->
<!--                    </div>-->
<!--                </form>-->

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
