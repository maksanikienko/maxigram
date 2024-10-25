<script setup>
import {computed, onMounted, ref} from 'vue';
import axios from 'axios';
import Echo from 'laravel-echo';
const echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 8080,
    forceTLS: false,
    disableStats: true,
});
const props = defineProps(['user', 'sentMessages','receivedMessages','all_users']);

        const authUser = props.user || 'No auth user';
        const allUsers = props.all_users;
        const newMessage = ref('');
        const selectedUser = ref(null);

        const sentMessages = props.sentMessages;
        const receivedMessages = props.receivedMessages;

        const selectUser = (userId) => {
            selectedUser.value = userId;
            checkNewMessages()
        };

        const selectedUserById = computed(() => {
            const user = props.all_users.find(user => user.id === selectedUser.value);
            return user ? user.name : 'No user selected';
        });
        const sendMessage = async () => {
            await axios.post('/api/post-messages', {
                sender_id: authUser.id,
                recipient_id: selectedUser.value,
                message: newMessage.value,
            });
            // newMessage.value = '';
        };
        const checkNewMessages = () => {
            if (props.user && selectedUser.value) {
                console.log(props.user.id, selectedUser.value)
                echo.private(`chat.${props.user.id}.${selectedUser.value}`)
                    .listen('MessageSent', (e) => {
                        const message = e.message;

                        if (message.sender_id === props.user.id && message.recipient_id === selectedUser.value) {
                            sentMessages.value.push(message);
                        } else if (message.recipient_id === props.user.id && message.sender_id === selectedUser.value) {
                            receivedMessages.value.push(message);
                        }
                    });
            }
        }

        onMounted(() => {

        });

</script>

<template>
    <div class="container text-center m-48">

        <div class="grid grid-cols-4 gap-4 text-end">
            <div class="col-span-1 p-4 ">
                <!-- First Column: 25% Width -->
                <div class="flex">
                    <!-- List of all users-->
                    <!-- Sidebar -->
                    <div class="w-full bg-white shadow-lg p-4">
                        <h2 class="text-xl font-semibold mb-4">Users</h2>
                        <ul class="space-y-2">
                            <li
                                v-for="user in allUsers"
                                :key="user.id"
                                class="flex items-center justify-between p-2 bg-gray-200 rounded hover:bg-gray-300 transition cursor-pointer"
                                @click="selectUser(user.id)"
                            >
                                <span class="font-medium">{{ user.name }}</span>
                                <button class="btn text-blue-500 hover:underline">Send</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>


            <div class="col-span-3 p-4">
                <!-- Second Column: 75% Width -->
                <!-- Chat bubble right-->
                <div v-for="message in sentMessages" :key="message.id" class="bg-blue-200 flex items-start justify-end gap-2.5  mb-10">
                    <img class="w-8 h-8 rounded-full" src="" alt="Jese image">
                    <div class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="text-sm font-semibold text-gray-900">{{ message.username }}</span>
                            <span class="text-sm font-normal text-gray-500">11:46</span>
                        </div>
                        <p class="text-sm font-normal py-2.5 text-gray-900">{{ message.message }}</p>
                        <span class="text-sm font-normal text-gray-500">Delivered</span>
                    </div>
                </div>

                <div v-for="message in receivedMessages" :key="message.id" class="bg-red-200 flex items-start justify-end gap-2.5  mb-10">
                    <img class="w-8 h-8 rounded-full" src="" alt="Jese image">
                    <div class="flex flex-col w-full max-w-[320px] leading-1.5 p-4 border-gray-200 bg-gray-100 rounded-e-xl rounded-es-xl">
                        <div class="flex items-center space-x-2 rtl:space-x-reverse">
                            <span class="text-sm font-semibold text-gray-900">{{ message.username }}</span>
                            <span class="text-sm font-normal text-gray-500">11:46</span>
                        </div>
                        <p class="text-sm font-normal py-2.5 text-gray-900">{{ message.message }}</p>
                        <span class="text-sm font-normal text-gray-500">Delivered</span>
                    </div>
                </div>

            </div>
        </div>






            <!-- Form for send msg-->
        <form @submit.prevent="sendMessage">

            <div class="mb-5 w-1/2 mx-auto">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Your name</label>
                <input v-model="authUser.name" type="text" id="name" class="w-1/3 mx-auto text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 disabled" placeholder="Name" required />
                <span v-if="selectedUser" type="text" id="name" class="w-1/3 mx-auto text-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 disabled" >
                    {{selectedUserById}}
                </span>
            </div>

            <div class="w-1/2 mx-auto mb-4 border border-gray-200 rounded-lg bg-gray-50">
                <div class="px-4 py-2 bg-white rounded-t-lg">
                    <label for="comment" class="sr-only">Your comment</label>
                    <textarea v-model="newMessage" id="comment" rows="4" class="w-full px-0 text-sm text-gray-900 bg-white border-0 focus:ring-0" placeholder="Write a comment..." required ></textarea>
                </div>
                <div class="flex items-center justify-between px-3 py-2 border-t">
                    <button type="submit" class="inline-flex items-center py-2.5 px-4 text-xs font-medium text-center text-white bg-blue-700 rounded-lg focus:ring-4 focus:ring-blue-200 hover:bg-blue-800">
                        Post comment
                    </button>
                    <div class="flex ps-0 space-x-1 rtl:space-x-reverse sm:ps-2">
                        <button type="button" class="inline-flex justify-center items-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 12 20">
                                <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M1 6v8a5 5 0 1 0 10 0V4.5a3.5 3.5 0 1 0-7 0V13a2 2 0 0 0 4 0V6"/>
                            </svg>
                            <span class="sr-only">Attach file</span>
                        </button>
                        <button type="button" class="inline-flex justify-center items-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                <path d="M8 0a7.992 7.992 0 0 0-6.583 12.535 1 1 0 0 0 .12.183l.12.146c.112.145.227.285.326.4l5.245 6.374a1 1 0 0 0 1.545-.003l5.092-6.205c.206-.222.4-.455.578-.7l.127-.155a.934.934 0 0 0 .122-.192A8.001 8.001 0 0 0 8 0Zm0 11a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                            </svg>
                            <span class="sr-only">Set location</span>
                        </button>
                        <button type="button" class="inline-flex justify-center items-center p-2 text-gray-500 rounded cursor-pointer hover:text-gray-900 hover:bg-gray-100">
                            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M18 0H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2Zm-5.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm4.376 10.481A1 1 0 0 1 16 15H4a1 1 0 0 1-.895-1.447l3.5-7A1 1 0 0 1 7.468 6a.965.965 0 0 1 .9.5l2.775 4.757 1.546-1.887a1 1 0 0 1 1.618.1l2.541 4a1 1 0 0 1 .028 1.011Z"/>
                            </svg>
                            <span class="sr-only">Upload image</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <p class="ms-auto text-xs text-gray-500">Remember, contributions to this topic should follow our <a href="#" class="text-blue-600 hover:underline">Community Guidelines</a>.</p>

    </div>

</template>

<style scoped>

</style>
