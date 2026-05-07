<script setup>
import { onMounted, ref } from 'vue';
import FriendsList from './parts/FriendsList.vue';
import GroupsList from './parts/GroupsList.vue';
import { useStatusHandler } from './../utils/statusHandler.js';
import { connectToPresenceChannel, connectToAllPrivateChannels, sendTypingEvent } from './../utils/conectChannel.js';
import MessengerHeader from '@/Components/parts/MessengerHeader.vue';
import Messages from '@/Components/parts/Messages.vue';
import MessageInput from '@/Components/parts/MessageInput.vue';
import Avatar from '@/Components/ui/Avatar.vue';
import { Menu, X, MessageSquare, Settings, LogOut } from 'lucide-vue-next';

const props = defineProps(['current_user', 'rooms', 'profileUrl']);

const isFriendTyping = ref(false);
const isFriendTypingTimer = ref(null);
const rooms = ref([...props.rooms]);
const { loadOnlineStatus, saveOnlineStatus } = useStatusHandler(rooms);
const showSidebar = ref(true);
const unreadCounts = ref({});

const selectedRoom = ref({ room_id: '', other_user: {}, messages: [] });

const selectRoom = (room) => {
    selectedRoom.value = room;
    unreadCounts.value[room.room_id] = 0;
    if (window.innerWidth < 768) showSidebar.value = false;
};

const handleTyping = () => {
    sendTypingEvent(selectedRoom.value.room_id, props.current_user.id);
};

const showBrowserNotification = (room, event) => {
    if (Notification.permission !== 'granted') return;
    const body = event.message
        ? event.message.slice(0, 100)
        : '📷 Image';
    new Notification(room.other_user.name, {
        body,
        icon: room.other_user.image || '/favicon.ico',
        tag: `room-${room.room_id}`,
    });
};

const onNewMessage = (room, event) => {
    const isCurrentRoom = selectedRoom.value.room_id === room.room_id;

    if (!isCurrentRoom) {
        unreadCounts.value[room.room_id] = (unreadCounts.value[room.room_id] || 0) + 1;
    }

    if (!isCurrentRoom || document.hidden) {
        showBrowserNotification(room, event);
    }
};

onMounted(() => {
    if (rooms.value.length) selectRoom(rooms.value[0]);
    connectToPresenceChannel(rooms, saveOnlineStatus);
    connectToAllPrivateChannels(rooms, props, selectedRoom, isFriendTyping, isFriendTypingTimer, onNewMessage);
    loadOnlineStatus();

    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
});
</script>

<template>
    <div class="flex h-screen bg-background overflow-hidden">

        <!-- Sidebar -->
        <aside
            :class="[
                'flex flex-col w-full md:w-72 lg:w-80 bg-sidebar shrink-0',
                'fixed md:relative inset-0 z-30 md:z-auto',
                'transition-transform duration-300 ease-in-out md:transition-none',
                showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
            ]"
        >
                <!-- Sidebar header -->
                <div class="flex items-center gap-3 px-4 py-4 border-b border-white/10">
                    <div class="flex items-center gap-2 flex-1">
                        <div class="h-8 w-8 rounded-xl bg-primary flex items-center justify-center">
                            <MessageSquare class="w-4 h-4 text-white" />
                        </div>
                        <span class="text-sidebar-foreground font-bold text-lg tracking-tight">Maxigram</span>
                    </div>
                    <button @click="showSidebar = false" class="md:hidden p-1.5 rounded-lg hover:bg-white/10 text-sidebar-foreground/70">
                        <X class="w-5 h-5" />
                    </button>
                </div>

                <!-- Lists -->
                <div class="flex-1 overflow-y-auto px-3 py-3 scrollbar-thin">
                    <FriendsList
                        :rooms="rooms"
                        :selectedRoom="selectedRoom"
                        :showFriendsList="showSidebar"
                        :unreadCounts="unreadCounts"
                        @selectRoom="selectRoom"
                        @toggleFriendsList="showSidebar = false"
                    />
                    <GroupsList />
                </div>

                <!-- User profile footer -->
                <div class="px-3 py-3 border-t border-white/10">
                    <div class="flex items-center gap-3 px-2 py-2 rounded-xl hover:bg-white/10 transition-colors group">
                        <Avatar
                            :src="current_user.image"
                            :alt="current_user.name"
                            size="sm"
                        />
                        <div class="flex-1 min-w-0">
                            <p class="text-sidebar-foreground text-sm font-semibold truncate">{{ current_user.name }}</p>
                            <p class="text-sidebar-foreground/50 text-xs truncate">{{ current_user.email }}</p>
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a :href="profileUrl" class="p-1.5 rounded-lg hover:bg-white/10 text-sidebar-foreground/60 hover:text-sidebar-foreground">
                                <Settings class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

        <!-- Overlay (mobile) -->
        <Transition name="fade">
            <div
                v-if="showSidebar"
                class="md:hidden fixed inset-0 z-20 bg-black/50 backdrop-blur-sm"
                @click="showSidebar = false"
            />
        </Transition>

        <!-- Chat area -->
        <div class="flex flex-col flex-1 min-w-0 bg-white">

            <!-- Top bar: hamburger + header -->
            <div class="flex items-stretch border-b border-border">
                <button
                    @click="showSidebar = !showSidebar"
                    class="md:hidden px-4 text-muted-foreground hover:text-foreground hover:bg-accent transition-colors"
                >
                    <Menu class="w-5 h-5" />
                </button>
                <div class="flex-1">
                    <MessengerHeader
                        :room="selectedRoom"
                        :current_user="current_user"
                        :profileUrl="profileUrl"
                    />
                </div>
            </div>

            <!-- Messages -->
            <Messages
                :room="selectedRoom"
                :current_user="current_user"
            />

            <!-- Typing indicator -->
            <Transition name="typing">
                <div v-if="isFriendTyping" class="px-6 pb-1 flex items-center gap-2">
                    <div class="flex gap-1 items-center bg-muted px-3 py-1.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground animate-bounce" style="animation-delay:0ms"/>
                        <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground animate-bounce" style="animation-delay:150ms"/>
                        <span class="w-1.5 h-1.5 rounded-full bg-muted-foreground animate-bounce" style="animation-delay:300ms"/>
                        <span class="text-xs text-muted-foreground ml-1">{{ selectedRoom.other_user.name }} is typing</span>
                    </div>
                </div>
            </Transition>

            <!-- Message input -->
            <MessageInput :room="selectedRoom" @typing="handleTyping" />
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
.typing-enter-active, .typing-leave-active { transition: all 0.2s ease; }
.typing-enter-from, .typing-leave-to { opacity: 0; transform: translateY(4px); }
</style>
