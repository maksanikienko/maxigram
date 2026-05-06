<script setup>
import Avatar from '@/Components/ui/Avatar.vue';
import { Phone, Video, MoreVertical, ArrowLeft } from 'lucide-vue-next';

const props = defineProps(['room', 'current_user', 'profileUrl']);
const emit = defineEmits(['back']);
</script>

<template>
    <div class="flex items-center gap-3 px-4 py-3 border-b border-border bg-white/80 backdrop-blur-sm shrink-0">
        <button @click="emit('back')" class="md:hidden p-1.5 rounded-lg hover:bg-accent transition-colors">
            <ArrowLeft class="w-5 h-5 text-muted-foreground" />
        </button>

        <div v-if="room?.other_user?.name" class="flex items-center gap-3 flex-1 min-w-0">
            <div class="relative">
                <Avatar :src="room.other_user.image" :alt="room.other_user.name" size="default" />
                <span :class="[
                    'absolute bottom-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white',
                    room.other_user.is_online ? 'bg-emerald-400' : 'bg-gray-400'
                ]" />
            </div>
            <div class="min-w-0">
                <h3 class="font-semibold text-foreground text-sm truncate">{{ room.other_user.name }}</h3>
                <p class="text-xs text-muted-foreground">
                    {{ room.other_user.is_online ? 'Online' : 'Offline' }}
                </p>
            </div>
        </div>

        <div v-else class="flex-1 flex items-center gap-2">
            <div class="h-10 w-10 rounded-full bg-muted animate-pulse" />
            <div class="h-4 w-32 rounded bg-muted animate-pulse" />
        </div>

        <div class="flex items-center gap-1">
            <button class="p-2 rounded-lg hover:bg-accent transition-colors text-muted-foreground hover:text-foreground">
                <Phone class="w-[18px] h-[18px]" />
            </button>
            <button class="p-2 rounded-lg hover:bg-accent transition-colors text-muted-foreground hover:text-foreground">
                <Video class="w-[18px] h-[18px]" />
            </button>
            <button class="p-2 rounded-lg hover:bg-accent transition-colors text-muted-foreground hover:text-foreground">
                <MoreVertical class="w-[18px] h-[18px]" />
            </button>
        </div>
    </div>
</template>
