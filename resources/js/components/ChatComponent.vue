<script setup>
import {nextTick, onMounted, ref,watch} from 'vue';
import axios from 'axios';
const props = defineProps(['friend', 'current_user','friends','rooms']);

// const messages = ref([]);
const newMessage = ref("");
const messagesContainer = ref(null);
const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);
// const friends = ref([...props.friends])
const selectedRoom = ref({
    room_id: '',
    other_user: {  },
    messages: []
});
const rooms = ref([...props.rooms]);
watch(
    () => selectedRoom.value.messages,
    () => {
        nextTick(() => {
            messagesContainer.value.scrollTo({
                top: messagesContainer.value.scrollHeight,
                behavior: "smooth",
            });
        });
    },
    { deep: true }
);
const loadOnlineStatus = () => {
    const storedStatus = JSON.parse(localStorage.getItem('friendsStatus'));
    if (storedStatus) {
        friends.value.forEach(friend => {
            const status = storedStatus.find(s => s.id === friend.id);
            if (status) {
                friend.is_online = status.is_online;
            }
        });
    }
};
// localStorage
const saveOnlineStatus = () => {
    const statusToStore = friends.value.map(friend => ({
        id: friend.id,
        is_online: friend.is_online
    }));
    localStorage.setItem('friendsStatus', JSON.stringify(statusToStore));
};
const getLastMessage = (messages) => {
    return messages.length > 0 ? messages[messages.length - 1].message : 'No Messages';
};

const sendMessage = () => {
    if (newMessage.value.trim() !== "" && selectedRoom.value.room_id) {
        axios.post(`/chat/room/${selectedRoom.value.room_id}/send`, {
                message: newMessage.value,
            })
            .then((response) => {
                console.log('sent_msg',response.data)
                selectedRoom.value.messages.push(response.data);
                console.log('Updated selectedRoom:', selectedRoom.value);
                newMessage.value = "";
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
            console.log(event)
            isFriendTyping.value = event.userID === selectedRoom.value.other_user.id;

            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }
            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
}
// const connectToPresenceChannel = () => {
//     Echo.join('presence-chat.main')
//         .here((users) => {
//             // Check all friends online
//             users.forEach((user) => {
//                 const friend = friends.value.find(f => f.id === user.id);
//                 if (friend) {
//                     friend.is_online = true;
//                 }
//             });
//             saveOnlineStatus();
//         })
//         .joining((user) => {
//             // Check joining friends
//             const friend = friends.value.find(f => f.id === user.id);
//             if (friend) {
//                 friend.is_online = true;
//                 saveOnlineStatus();
//             }
//         })
//         .leaving((user) => {
//             // Check leaving friends
//             const friend = friends.value.find(f => f.id === user.id);
//             if (friend) {
//                 friend.is_online = false;
//                 saveOnlineStatus();
//             }
//         });
// }
const sendTypingEvent = () => {
    Echo.private(`chat.${props.current_user.id}`).whisper("typing", {
        userID: selectedRoom.value.other_user.id,
    });
};
onMounted(() => {
    // fetchMessages()
    // connectToChannel()
    // connectToPresenceChannel()
    // loadOnlineStatus()
    console.log( rooms.value)
});

</script>

<template>

    <div class="container mx-auto border my-8">
        <!--Test/-->
<!--        <div class="chat-app">-->
<!--            &lt;!&ndash; Список комнат &ndash;&gt;-->
<!--            <div class="room-list">-->
<!--                <h3>Чаты</h3>-->
<!--                <ul>-->
<!--                    <li-->
<!--                        v-for="room in rooms"-->
<!--                        :key="room.id"-->
<!--                        @click="selectRoom(room)"-->
<!--                        :class="{ 'selected': selectedRoom && selectedRoom.id === room.id }"-->
<!--                    >-->
<!--                        <span class="bg-gray-200 cursor-pointer">{{ room.other_user.name }}</span>-->
<!--                        <span v-if="room.other_user.is_online" class="online-status">Online</span>-->
<!--                    </li>-->
<!--                </ul>-->
<!--            </div>-->

<!--            <div class="chat-window" v-if="selectedRoom">-->
<!--                <h3 class="bg-blue-100">Чат с {{ selectedRoom.other_user.name }}</h3>-->

<!--                &lt;!&ndash; Список сообщений &ndash;&gt;-->
<!--                <div ref="messagesContainer" class="messages-container">-->
<!--                    <div v-for="message in selectedRoom.messages" :key="message.id" :class="{'my-message': message.sender_id === current_user.id}">-->
<!--                        <div v-if="message.sender_id === selectedRoom.other_user.id " class="sender bg-gray-200">sender=other_user{{selectedRoom.other_user.name}}-->
<!--                            <p class="text">{{ message.message }}</p>-->
<!--                        </div>-->
<!--                        <div v-if="message.sender_id === props.current_user.id" class="sender bg-blue-200">sender=current_user-{{props.current_user.name}}-->
<!--                            <p class="text">{{ message.message }}</p>-->
<!--                        </div>-->
<!--&lt;!&ndash;                        <span class="timestamp">{{ message.formatted_time }}</span>&ndash;&gt;-->
<!--                    </div>-->
<!--                </div>-->

<!--                <form @submit.prevent="sendMessage">-->
<!--                    <input type="text" v-model="newMessage" placeholder="Введите сообщение..." @input="sendTypingEvent"/>-->
<!--                    <button type="submit">Отправить</button>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->




<!--         component -->
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <div class="w-1/4 bg-white border-r border-gray-300">
                <!-- Sidebar Header -->
                <header class="p-4 border-b border-gray-300 flex justify-between items-center bg-indigo-500 text-white">
                    <h1 class="text-2xl font-semibold">Chat Web</h1>
                    <div class="relative">
                        <button id="menuButton" class="focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-100" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                <path d="M2 10a2 2 0 012-2h12a2 2 0 012 2 2 2 0 01-2 2H4a2 2 0 01-2-2z" />
                            </svg>
                        </button>
                        <!-- Menu Dropdown -->
                        <div id="menuDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-300 rounded-md shadow-lg hidden">
                            <ul class="py-2 px-3">
                                <li><a href="#" class="block px-4 py-2 text-gray-800 hover:text-gray-400">Option 1</a></li>
                                <li><a href="#" class="block px-4 py-2 text-gray-800 hover:text-gray-400">Option 2</a></li>
                                <!-- Add more menu options here -->
                            </ul>
                        </div>
                    </div>
                </header>

                <!-- Contact List -->
                <div class="overflow-y-auto h-screen p-3 mb-9 pb-20">
                    <div v-for="room in rooms"
                         :key="room.id"
                         @click="selectRoom(room)"
                         :class="{ 'selected': selectedRoom && selectedRoom.id === room.id }"
                         class="flex items-center mb-4 cursor-pointer hover:bg-gray-100 p-2 rounded-md"
                    >
                        <div class="w-12 h-12 bg-gray-300 rounded-full mr-3">
                            <img :src="room.other_user.image" alt="User Avatar" class="w-12 h-12 rounded-full">
                        </div>
                        <div class="flex-1">
                            <h2 class="text-lg font-semibold">{{room.other_user.name}}</h2>
                            <p class="text-gray-600">{{ getLastMessage(room.messages) }}</p>
                        </div>
                    </div>



                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 relative">
                <!-- Chat Header -->
                <header v-if="selectedRoom" class="bg-white p-4 text-gray-700 border-b">
                    <h1 class="text-2xl font-semibold text-center ">{{selectedRoom.other_user.name}}</h1>
                </header>

                <!-- Chat Messages -->
                <div
                    v-if="selectedRoom"
                    ref="messagesContainer"
                    class="overflow-y-auto lg:h-[500px]"
                >
                    <div
                        v-for="message in selectedRoom.messages"
                        :key="message.id"
                        class=" p-2 "
                    >
                        <!-- Incoming Message -->
                        <div v-if="message.sender_id === selectedRoom.other_user.id" class="flex mb-4">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center mr-2">
                                <img :src="selectedRoom.other_user.image" alt="User Avatar" class="w-8 h-8 rounded-full">
                            </div>
                            <div class="flex max-w-96 bg-gray-200 rounded-lg p-3 gap-3">
                                <p class="text-gray-700">{{message.message}}</p>
                            </div>
                        </div>

                        <!-- Outgoing Message -->
                        <div v-else class="flex justify-end mb-4">
                            <div class="flex max-w-96 bg-indigo-500 text-white rounded-lg p-3 gap-3">
                                <p>{{ message.message }}</p>
                            </div>
                            <div class="w-9 h-9 rounded-full flex items-center justify-center ml-2">
                                <img :src="props.current_user.image" alt="My Avatar" class="w-8 h-8 rounded-full">
                            </div>
                        </div>

                    </div>
                </div>

                                <div v-if="isFriendTyping" class="typing-indicator">
                                    {{ selectedRoom.other_user.name }} typing...
                                </div>
<!--                @input="sendTypingEvent"-->
                <!-- Chat Input -->
                <form @submit.prevent="sendMessage" class="bg-white border-t border-gray-300 p-4 absolute bottom-0 w-full">
                    <div class="flex items-center">
                        <input
                            v-model="newMessage"
                            type="text"
                            placeholder="Type a message..."
                            class="w-full p-2 rounded-md border border-gray-400 focus:outline-none focus:border-blue-500"
                            @input="sendTypingEvent"
                        >
                        <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md ml-2">Send</button>
                    </div>
                </form>
            </div>
        </div>



<!--        Old-->
<!--        <div class="flex gap-10">-->
<!--            <div class="w-1/4 p-4">-->

<!--            &lt;!&ndash;List of all friends&ndash;&gt;-->
<!--                    <ul class="max-w-md divide-y divide-gray-200 dark:divide-gray-700 p-3">-->
<!--                        <li v-for="user in friends" :key="user.id" class="py-3 sm:pb-4">-->
<!--                            <a :href="`/chat/${user.id}`" class="flex items-center space-x-4 rtl:space-x-reverse">-->
<!--                                <div class="flex-shrink-0 relative">-->
<!--                                    <img class="w-8 h-8 rounded-full" :src="user.image" alt="Neil image">-->
<!--                                    <span v-if="user.is_online" class="top-0 left-7 absolute w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>-->
<!--                                </div>-->
<!--                                <div class="flex-1 min-w-0">-->
<!--                                    <p class="text-sm font-medium text-gray-900 truncate dark:text-white">-->
<!--                                        {{user.name}}-->
<!--                                    </p>-->
<!--                                    <p class="text-sm text-gray-500 truncate dark:text-gray-400">-->
<!--                                        {{user.email}}-->
<!--                                    </p>-->
<!--                                </div>-->
<!--                                <div class="inline-flex items-center text-base font-semibold text-gray-900 dark:text-white">-->
<!--                                </div>-->
<!--                            </a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--            </div>-->


<!--        <div class=" sm:w-3/4 lg:w-3/4 mx-auto ">-->

<!--            &lt;!&ndash; Chat messages container &ndash;&gt;-->
<!--            <div class=" flex flex-col h-96 justify-end border border-gray-300 rounded-lg shadow-md overflow-hidden"-->
<!--                 :style="{ backgroundImage: `url(https://support.delta.chat/uploads/default/original/1X/768ded5ffbef90faa338761be1c5633d91cc35e3.jpeg)` }"-->
<!--            >-->
<!--                <header class="bg-blue-300 w-full py-4 text-center text-gray-600 text-2xl font-bold">-->
<!--                    {{friend.name}}-->
<!--                </header>-->
<!--&lt;!&ndash;                ref="messagesContainer"&ndash;&gt;-->
<!--                <div  class="flex-1 p-2 overflow-y-auto ">-->

<!--                    &lt;!&ndash;Sent Messages (Right Block)&ndash;&gt;-->
<!--                    <div-->
<!--                        v-for="message in messages"-->
<!--                        :key="message.id"-->
<!--                        class="">-->
<!--                        <div-->
<!--                            v-if="message.sender.id === current_user.id"-->
<!--                            class="flex justify-end items-start gap-2.5 mb-5 mr-5"-->
<!--                        >-->
<!--                            <div class="relative">-->
<!--                                <img class=" w-12 h-12 rounded-full" :src="message.sender.image" alt="Jese image">-->
<!--                                <span class="top-0 left-7 absolute w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>-->
<!--                            </div>-->
<!--                            <div class="text-white bg-blue-200 rounded-lg sm:max-w-md flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 rounded-e-xl rounded-es-xl dark:bg-gray-700">-->
<!--                                <div class="flex items-center space-x-2 rtl:space-x-reverse">-->
<!--                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ message.sender.name }}</span>-->
<!--                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.formatted_time }}</span>-->
<!--                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.formatted_date }}</span>-->
<!--                                </div>-->
<!--                                <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">{{ message.message }}</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        &lt;!&ndash;Received Messages (Left Block)&ndash;&gt;-->
<!--                        <div-->
<!--                            v-else-->
<!--                            class="flex justify-start items-start gap-2.5 mb-5 ml-5"-->
<!--                        >-->
<!--                            <div class="relative">-->
<!--                                <img class=" w-12 h-12 rounded-full" :src="message.sender.image" alt="Jese image">-->
<!--                                <span class="top-0 left-7 absolute w-3.5 h-3.5 bg-green-400 border-2 border-white dark:border-gray-800 rounded-full"></span>-->
<!--                            </div>-->
<!--                            <div class="text-white bg-gray-200 rounded-lg sm:max-w-md flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 rounded-e-xl rounded-es-xl dark:bg-gray-700">-->
<!--                                <div class="flex items-center space-x-2 rtl:space-x-reverse">-->
<!--                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ message.sender.name }}</span>-->
<!--                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.formatted_time }}</span>-->
<!--                                    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">{{ message.formatted_date }}</span>-->
<!--                                </div>-->
<!--                                <p class="text-sm font-normal py-2.5 text-gray-900 dark:text-white">{{ message.message }}</p>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </div>-->
<!--&lt;!&ndash;                    &ndash;&gt;-->
<!--                    &lt;!&ndash; Typing indicator &ndash;&gt;-->
<!--                    <small v-if="isFriendTyping" class=" text-sm text-gray-700 mx-8">-->
<!--                        {{ friend.name }} is typing...-->
<!--                    </small>-->

<!--                    &lt;!&ndash; Chat input area &ndash;&gt;-->
<!--                    <div class="flex items-center mt-4 space-x-2">-->
<!--                        <textarea-->
<!--                            v-model="newMessage"-->
<!--                            @keydown="sendTypingEvent"-->
<!--                            @keyup.enter="sendMessage"-->
<!--                            id="message"-->
<!--                            rows="2"-->
<!--                            class="flex-grow block p-2 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"-->
<!--                            placeholder="Type a message..."-->
<!--                        ></textarea>-->

<!--                        <button-->
<!--                            @click="sendMessage"-->
<!--                            class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600"-->
<!--                        >-->
<!--                            Send-->
<!--                        </button>-->
<!--                    </div>-->

<!--                </div>-->
<!--            </div>-->



<!--        </div>-->

<!--        </div>-->
    </div>


</template>

<style scoped>
@import 'flowbite';

</style>
