<script setup>
import {nextTick, onMounted, ref,watch} from 'vue';
import axios from 'axios';
import {scrollToBottom} from "./../utils/scrollToBottom.js";
const props = defineProps([ 'current_user','rooms']);

const newMessage = ref("");
const messagesContainer = ref(null);
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);
const selectedFile = ref("");
const selectedFileUrl = ref('');
const attachedFile = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        selectedFileUrl.value = URL.createObjectURL(file);
    }
    console.log('onImageSelect',selectedFile.value)
};
const clearAttachedFile = () => {
    selectedFile.value = null;
    selectedFileUrl.value = '';
};
const selectedRoom = ref({
    room_id: '',
    other_user: {  },
    messages: []
});
const rooms = ref([...props.rooms]);
watch(
    () => selectedRoom.value.messages,
    async () => {
        await nextTick(); // Wait for DOM updates
        scrollToBottom(messagesContainer.value); // Call the scroll function
    },
    { deep: true }
);
const loadOnlineStatus = () => {
    const storedStatus = JSON.parse(localStorage.getItem('friendsStatus'));
    if (storedStatus) {
        rooms.value.forEach(room => {
            const status = storedStatus.find(s => s.id === room.other_user.id);
            if (status) {
                room.other_user.is_online = status.is_online;
            } else {
                room.other_user.is_online = false;
            }
        });
    }
};
const saveOnlineStatus = () => {
    const statusToStore = rooms.value.map(room => ({
        id: room.other_user.id,
        is_online: room.other_user.is_online
    }));
    localStorage.setItem('friendsStatus', JSON.stringify(statusToStore));
};

// Get last messages foreach friend
const getLastMessage = (messages) => {
    return messages.length > 0 ? messages[messages.length - 1].message : 'No Messages';
};

const sendMessage = () => {
    if (newMessage.value.trim() !== "" && selectedRoom.value.room_id) {
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
                console.log('sent_msg',response.data)
                selectedRoom.value.messages.push(response.data);
                console.log('Updated selectedRoom:', selectedRoom.value);
                newMessage.value = "";
                selectedFile.value = "";
                selectedFileUrl.value = "";
            });
    }
};
const selectRoom = (room) => {
    selectedRoom.value = room;
    connectToChannel(room.room_id);
    console.log('When selected',selectedRoom.value)
};

const connectToChannel = (roomId) => {
    console.log('connectToChannel',roomId)
    Echo.private(`chat.room.${roomId}`)
        .listen("MessageSent", (event) => {
            console.log(`channel.room.${roomId}`,event)
            selectedRoom.value.messages.push(event);
            console.log('Updated selectedRoom',selectedRoom.value)
        })
        .listenForWhisper("typing", (event) => {
            console.log(event,selectedRoom.value.other_user.id )
            isFriendTyping.value = event.userID === selectedRoom.value.other_user.id;

            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }
            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
}
const connectToPresenceChannel = () => {
    Echo.join('presence-chat.main')
        .here((users) => {
            // Check all online users
            users.forEach((user) => {
                rooms.value.forEach(room => {
                    if (room.other_user.id === user.id) {
                        room.other_user.is_online = true;
                    }
                });
            });
            saveOnlineStatus();
        })
        .joining((user) => {
            rooms.value.forEach(room => {
                if (room.other_user.id === user.id) {
                    room.other_user.is_online = true;
                    saveOnlineStatus();
                }
            });
        })
        .leaving((user) => {
            rooms.value.forEach(room => {
                if (room.other_user.id === user.id) {
                    room.other_user.is_online = false;
                    saveOnlineStatus();
                }
            });
        });
}

    // Send event typing
const sendTypingEvent = () => {
    Echo.private(`chat.room.${selectedRoom.value.room_id}`).whisper("typing", {
        userID: props.current_user.id,
    });
};
onMounted(() => {
    selectRoom(rooms.value[0]) // Open 1 chat with friend
    connectToPresenceChannel()
    loadOnlineStatus()

    console.log( rooms.value)
});

</script>

<template>

    <div class="flex justify-center h-screen bg-gray-100">
        <!-- Main block -->
        <div class="flex flex-row md:w-3/4 w-full border rounded-lg shadow-lg h-full">

            <!-- Left block with friends list -->
            <div class="w-1/4 bg-gray-200 border-r border-gray-300 p-4 hidden md:block">
                <h2 class="text-lg font-semibold mb-4">Friends</h2>
                <!-- friends list -->
                <ul class="space-y-2 divide-y divide-gray-300 ">
                    <li
                        v-for="room in rooms"
                        :key="room.id"
                        @click="selectRoom(room)"
                        :class="{ 'selected': selectedRoom && selectedRoom.room_id === room.room_id, 'bg-blue-300': selectedRoom.room_id === room.room_id }"
                        class="p-2 flex items-center space-x-3 rounded-md cursor-pointer hover:bg-gray-300 ">
                        <div class="relative">
                            <img :src="room.other_user.image" alt="avatar" class="w-10 h-10 rounded-full">
                            <span v-if="room.other_user.is_online" class="absolute text-green-500 right-0 bottom-0">
                                <svg width="12" height="12">
                                <circle cx="6" cy="6" r="6" fill="currentColor"></circle>
                              </svg>
                            </span>
                            <span v-else class="absolute text-green-500 right-0 bottom-0">
                                <svg width="12" height="12">
                                <circle cx="6" cy="6" r="6" fill="#7d7c7c"></circle>
                              </svg>
                            </span>
                        </div>
                        <div >
                            <div class="font-semibold">{{room.other_user.name}}</div>
                            <div class="text-xs text-gray-500">{{ getLastMessage(room.messages) }}</div>
                        </div>
                    </li>

                </ul>
            </div>

            <!-- Vertical line -->
            <div class="w-px bg-gray-300"></div>

            <!-- Right block with messages -->
            <div class="flex-1 p-4 bg-white flex flex-col h-full">
                <div class="flex items-center justify-between py-3 border-b-2 border-gray-200">
                    <div class="relative flex items-center space-x-4">
                        <div class="relative">
                            <span v-if="selectedRoom.other_user.is_online" class="absolute text-green-500 right-0 bottom-0">
                              <svg width="20" height="20">
                                <circle cx="8" cy="8" r="8" fill="currentColor"></circle>
                              </svg>
                            </span>
                            <span v-else class="absolute text-green-500 right-0 bottom-0">
                              <svg width="20" height="20">
                                <circle cx="8" cy="8" r="8" fill="#7d7c7c"></circle>
                              </svg>
                            </span>
                        <img :src="selectedRoom.other_user.image" alt="" class="w-10 sm:w-16 h-10 sm:h-16 rounded-full">
        </div>
                        <div v-if="selectedRoom" class="flex flex-col leading-tight">
                            <div class="text-2xl mt-1 flex items-center">
                                <span class="text-gray-700 mr-3">{{selectedRoom.other_user.name}}</span>
                            </div>
                            <span v-if="selectedRoom.other_user.is_online" class="text-lg text-gray-600">Online</span>
                            <span v-if="!selectedRoom.other_user.is_online" class="text-lg text-gray-600">Offline</span>
                        </div>
                    </div>
                </div>

                <!-- Messages block-->
                <div id="messages" ref="messagesContainer" v-if="selectedRoom" class="flex flex-col space-y-6 p-3 overflow-y-auto h-full scrollbar-thumb-blue scrollbar-thumb-rounded scrollbar-track-blue-lighter scrollbar-w-2 scrolling-touch">

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
                                <div class="bg-gray-300 text-gray-600 px-4 py-2 rounded-lg inline-block rounded-bl-none relative">
                                    <span v-if="message.message">{{message.message ?? null }}</span>
                                    <img v-if="message.picture_url" :src="message.picture_url" alt="Attached Image" class="max-w-48 max-h-48"/>
                                    <span class="text-gray-500 text-[10px] ml-2 align-bottom flex justify-end">{{ message.formatted_time }}</span>
                                </div>
                            </div>
                            <img :src="selectedRoom.other_user.image" alt="My profile" class="w-6 h-6 rounded-full order-1">
                        </div>
                    </div>
                        <!--Right messages-->
                    <div
                        v-else
                        class="chat-message">
                        <div class="flex items-end justify-end">
                            <div class="flex flex-col space-y-2 text-xs max-w-xs mx-2 order-1 items-end ">
                                <div class="bg-blue-500 text-white px-4 py-2 rounded-lg inline-block rounded-br-none relative ">
                                    <span>{{message.message ?? null }}</span>
                                    <img v-if="message.picture_url" :src="message.picture_url" alt="Attached Image" class="max-w-48 max-h-48 p-1 rounded-lg"/>
                                    <span class="text-gray-200 text-[10px] ml-2 align-bottom flex justify-end ">{{ message.formatted_time }}</span>
                                </div>
                            </div>
                            <img :src="props.current_user.image" alt="My profile" class="w-6 h-6 rounded-full order-2">
                        </div>
                    </div>
                    </div>

                </div>

                <div v-if="isFriendTyping" class="mx-5 text-gray-600 text-sm">
                    {{ selectedRoom.other_user.name }} typing...
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

                    <div class="relative flex">
                      <span class="absolute inset-y-0 flex items-center">
                        <button type="button" class="inline-flex items-center justify-center rounded-full h-12 w-12 text-gray-500 hover:bg-gray-300 focus:outline-none">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6 text-gray-600">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path>
                          </svg>
                        </button>
                      </span>
                        <input
                            v-model="newMessage"
                            @input="sendTypingEvent"
                            type="text"
                            placeholder="Write your message!"
                            class="w-full pl-12 bg-gray-200 rounded-md py-3"
                        >
                        <!--  Attach button-->
                        <button @click="$refs.fileInput.click()" type="button" class="p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-600">
                            <input type="file" @change="attachedFile" accept="image/*" class="hidden" ref="fileInput" />
                            <svg class="w-6 h-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 20">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M1 6v8a5 5 0 1 0 10 0V4.5a3.5 3.5 0 1 0-7 0V13a2 2 0 0 0 4 0V6"/>
                            </svg>
                            <span class="sr-only">Attach file</span>
                        </button>
                        <!--  Send button-->
                        <button type="submit" class="inline-flex items-center justify-center rounded-lg px-4 py-3 text-white bg-blue-500 hover:bg-blue-400 focus:outline-none ml-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-6 w-6 ml-2 transform rotate-45">
                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                            </svg>
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>



    <!--    <script>-->
<!--        const el = document.getElementById('messages')-->
<!--        el.scrollTop = el.scrollHeight-->
<!--    </script>-->
</template>

<style scoped>
.scrollbar-w-2::-webkit-scrollbar {
    width: 0.25rem;
    height: 0.25rem;
}

.scrollbar-track-blue-lighter::-webkit-scrollbar-track {
    --bg-opacity: 1;
    background-color: #f7fafc;
    background-color: rgba(247, 250, 252, var(--bg-opacity));
}

.scrollbar-thumb-blue::-webkit-scrollbar-thumb {
    --bg-opacity: 1;
    background-color: #edf2f7;
    background-color: rgba(237, 242, 247, var(--bg-opacity));
}

.scrollbar-thumb-rounded::-webkit-scrollbar-thumb {
    border-radius: 0.25rem;
}

</style>
