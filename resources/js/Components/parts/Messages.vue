<script setup>
import { nextTick, ref, watch } from "vue";
import Avatar from '@/Components/ui/Avatar.vue';
import { useModalHandler } from '@/utils/modalHandler.js';
import { scrollToBottom } from "@/utils/scrollToBottom.js";
import { X } from 'lucide-vue-next';

const props = defineProps(['room', 'current_user']);
const { isModalOpen, selectedImageUrl, openModal, closeModal } = useModalHandler();

const messagesContainer = ref(null);

watch(
    () => props.room.messages,
    async () => {
        await nextTick();
        setTimeout(() => scrollToBottom(messagesContainer.value), 100);
    },
    { deep: true }
);

const isOwn = (msg) => msg.sender_id === props.current_user.id;

const groupByDate = (messages) => {
    const groups = {};
    messages.forEach(msg => {
        const date = msg.formatted_date || 'Today';
        if (!groups[date]) groups[date] = [];
        groups[date].push(msg);
    });
    return groups;
};
</script>

<template>
    <div
        ref="messagesContainer"
        class="flex-1 overflow-y-auto px-4 py-4 space-y-1 scrollbar-thin bg-gradient-to-b from-slate-50/50 to-white"
    >
        <template v-if="room.messages?.length">
            <template v-for="(msgs, date) in groupByDate(room.messages)" :key="date">
                <!-- Date separator -->
                <div class="flex items-center gap-3 my-4">
                    <div class="flex-1 h-px bg-border" />
                    <span class="text-xs text-muted-foreground px-2 py-0.5 rounded-full bg-muted">{{ date }}</span>
                    <div class="flex-1 h-px bg-border" />
                </div>

                <!-- Messages -->
                <TransitionGroup name="message">
                    <div
                        v-for="msg in msgs"
                        :key="msg.message_id"
                        :class="['flex items-end gap-2 mb-1', isOwn(msg) ? 'justify-end' : 'justify-start']"
                    >
                        <!-- Recipient avatar -->
                        <Avatar
                            v-if="!isOwn(msg)"
                            :src="room.other_user?.image"
                            :alt="room.other_user?.name"
                            size="sm"
                            class="shrink-0 mb-1"
                        />

                        <!-- Bubble -->
                        <div :class="['max-w-[70%] group relative', isOwn(msg) ? 'items-end' : 'items-start']">
                            <div :class="[
                                'px-3 py-2 rounded-2xl shadow-sm relative',
                                isOwn(msg)
                                    ? 'bg-primary text-primary-foreground rounded-br-sm'
                                    : 'bg-white text-foreground border border-border rounded-bl-sm'
                            ]">
                                <p v-if="msg.message" class="text-sm leading-relaxed break-words">{{ msg.message }}</p>
                                <img
                                    v-if="msg.picture_url"
                                    :src="msg.picture_url"
                                    alt="Image"
                                    class="max-w-[220px] max-h-[220px] rounded-xl object-cover cursor-pointer mt-1"
                                    @click="openModal(msg.picture_url)"
                                />
                                <span :class="[
                                    'text-[10px] mt-1 block text-right leading-none',
                                    isOwn(msg) ? 'text-primary-foreground/70' : 'text-muted-foreground'
                                ]">{{ msg.formatted_time }}</span>
                            </div>
                        </div>

                        <!-- Own avatar -->
                        <Avatar
                            v-if="isOwn(msg)"
                            :src="current_user?.image"
                            :alt="current_user?.name"
                            size="sm"
                            class="shrink-0 mb-1"
                        />
                    </div>
                </TransitionGroup>
            </template>
        </template>

        <!-- Empty state -->
        <div v-else class="flex flex-col items-center justify-center h-full gap-3 text-muted-foreground">
            <div class="h-16 w-16 rounded-full bg-muted flex items-center justify-center">
                <svg class="w-8 h-8 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </div>
            <p class="text-sm font-medium">No messages yet</p>
            <p class="text-xs opacity-60">Be the first to say hello! 👋</p>
        </div>
    </div>

    <!-- Image lightbox -->
    <Transition name="modal">
        <div
            v-if="isModalOpen"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-sm p-4"
            @click.self="closeModal"
        >
            <div class="relative max-w-3xl w-full">
                <button
                    @click="closeModal"
                    class="absolute -top-10 right-0 text-white/80 hover:text-white transition-colors"
                >
                    <X class="w-6 h-6" />
                </button>
                <img :src="selectedImageUrl" alt="Full image" class="w-full h-auto rounded-xl shadow-2xl" />
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.message-enter-active { transition: all 0.2s ease-out; }
.message-enter-from { opacity: 0; transform: translateY(8px); }
.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }
</style>
