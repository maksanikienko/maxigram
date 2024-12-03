<template>

    <!--Burger Menu-->
    <button
        @click="$emit('toggleFriendsList')"
        class="absolute top-4 left-4 p-2 text-gray-500 bg-gray-200 rounded md:hidden">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Friends list -->
    <div
        :class="{'hidden': !showFriendsList}"
        class="absolute md:relative top-0 left-0 md:top-auto md:left-auto md:block bg-gray-200 border-r border-gray-300 p-4 w-3/4 md:w-1/4 h-full z-10 transition-all duration-300">
        <div class="flex mb-4 justify-between items-center">
            <h2 class="text-lg font-semibold  ">Friends</h2>
            <button class=" md:hidden block p-2 text-gray-500 hover:text-gray-700 focus:outline-none"
                    @click="$emit('toggleFriendsList')"
            ><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>

        <ul class="space-y-2 divide-y divide-gray-300 ">
            <li
                v-for="room in rooms"
                :key="room.id"
                @click="$emit('selectRoom', room)"
                :class="{ 'selected': selectedRoom && selectedRoom.room_id === room.room_id, 'bg-blue-300': selectedRoom.room_id === room.room_id }"
                class="p-2 flex items-center space-x-3 rounded-md cursor-pointer hover:bg-gray-300 ">
                <div class="relative">
                    <img v-if="room.other_user.image" :src="room.other_user.image" alt="avatar" class="w-10 h-10 rounded-full">
                    <img v-else src="https://robohash.org/vvvv" alt="avatar" class="w-10 h-10 rounded-full">
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
                    <div v-if="selectedRoom.room_id !== room.room_id" class="text-xs text-gray-500">{{ getLastMessage(room.messages) }}</div>
                </div>
            </li>

        </ul>
    </div>
</template>

<script setup>
const props = defineProps(['rooms', 'selectedRoom', 'showFriendsList']);
const emits = defineEmits(['toggleFriendsList', 'selectRoom']);
const getLastMessage = (messages) => {
    return messages.length > 0
        ? messages[messages.length - 1].message
        : 'No Messages';
};
</script>
