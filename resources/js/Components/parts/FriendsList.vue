<script setup>
import { ref, computed } from 'vue';
import Avatar from '@/Components/ui/Avatar.vue';
import { Search, MessageSquare } from 'lucide-vue-next';

const props = defineProps(['rooms', 'selectedRoom', 'showFriendsList']);
const emit = defineEmits(['selectRoom', 'toggleFriendsList']);

const search = ref('');

const filtered = computed(() =>
    props.rooms.filter(r =>
        r.other_user.name.toLowerCase().includes(search.value.toLowerCase())
    )
);

const lastMessage = (room) => {
    const msgs = room.messages;
    if (!msgs?.length) return 'No messages yet';
    const last = msgs[msgs.length - 1];
    return last.message || '📷 Image';
};

const truncate = (str, n = 32) => str.length > n ? str.slice(0, n) + '…' : str;
</script>

<template>
    <div class="flex flex-col h-full">
        <div class="flex items-center gap-2 mb-4">
            <MessageSquare class="w-5 h-5 text-primary" />
            <h2 class="font-semibold text-sidebar-foreground text-base">Messages</h2>
        </div>

        <div class="relative mb-3">
            <Search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-sidebar-foreground/50 pointer-events-none" />
            <input
                v-model="search"
                placeholder="Search contacts..."
                class="w-full h-9 pl-8 pr-3 text-sm rounded-lg bg-white/10 border border-white/20 text-sidebar-foreground placeholder:text-sidebar-foreground/50 focus:outline-none focus:ring-1 focus:ring-primary/50"
            />
        </div>

        <div class="flex-1 overflow-y-auto space-y-0.5 scrollbar-thin -mx-1 px-1">
            <button
                v-for="room in filtered"
                :key="room.room_id"
                @click="emit('selectRoom', room); emit('toggleFriendsList')"
                :class="[
                    'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-150 text-left',
                    selectedRoom?.room_id === room.room_id
                        ? 'bg-primary shadow-md'
                        : 'hover:bg-white/10'
                ]"
            >
                <div class="relative shrink-0">
                    <Avatar :src="room.other_user.image" :alt="room.other_user.name" size="default" />
                    <span :class="[
                        'absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2',
                        room.other_user.is_online ? 'bg-emerald-400' : 'bg-gray-500',
                        selectedRoom?.room_id === room.room_id ? 'ring-primary' : 'ring-sidebar'
                    ]" />
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1">
                        <span :class="[
                            'text-sm font-semibold truncate',
                            selectedRoom?.room_id === room.room_id ? 'text-primary-foreground' : 'text-sidebar-foreground'
                        ]">{{ room.other_user.name }}</span>
                        <span :class="[
                            'text-[10px] shrink-0',
                            selectedRoom?.room_id === room.room_id ? 'text-primary-foreground/70' : 'text-sidebar-foreground/50'
                        ]">
                            {{ room.messages?.length ? room.messages[room.messages.length - 1].formatted_time : '' }}
                        </span>
                    </div>
                    <p :class="[
                        'text-xs truncate mt-0.5',
                        selectedRoom?.room_id === room.room_id ? 'text-primary-foreground/80' : 'text-sidebar-foreground/60'
                    ]">{{ truncate(lastMessage(room)) }}</p>
                </div>
            </button>

            <div v-if="!filtered.length" class="py-8 text-center text-sidebar-foreground/50 text-sm">
                No contacts found
            </div>
        </div>
    </div>
</template>
