<script setup>
import { nextTick, ref, watch } from "vue";
import axios from 'axios';
import Avatar from '@/Components/ui/Avatar.vue';
import { useModalHandler } from '@/utils/modalHandler.js';
import { scrollToBottom } from "@/utils/scrollToBottom.js";
import { X, Pencil, Trash2 } from 'lucide-vue-next';

const props = defineProps(['room', 'current_user']);
const { isModalOpen, selectedImageUrl, openModal, closeModal } = useModalHandler();

const messagesContainer = ref(null);

watch(
    () => props.room.room_id,
    async () => {
        await nextTick();
        scrollToBottom(messagesContainer.value, 'instant');
    },
    { immediate: true }
);

watch(
    () => props.room.messages?.length,
    async (newLen, oldLen) => {
        if (oldLen === undefined || newLen <= oldLen) return;
        await nextTick();
        scrollToBottom(messagesContainer.value, 'smooth');
    }
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

// ── Edit ──────────────────────────────────────────────────────────────
const editingId  = ref(null);
const editText   = ref('');
const editInputRef = ref(null);

const startEdit = (msg) => {
    editingId.value = msg.message_id;
    editText.value  = msg.message || '';
    nextTick(() => editInputRef.value?.focus());
};

const cancelEdit = () => {
    editingId.value = null;
    editText.value  = '';
};

const saveEdit = async (msg) => {
    const text = editText.value.trim();
    if (!text || text === msg.message) { cancelEdit(); return; }
    try {
        await axios.patch(`/chat/message/${msg.message_id}`, { message: text });
        msg.message   = text;
        msg.is_edited = true;
    } catch (e) { console.error(e); }
    cancelEdit();
};

const onEditKeydown = (e, msg) => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); saveEdit(msg); }
    if (e.key === 'Escape') cancelEdit();
};

// ── Delete ────────────────────────────────────────────────────────────
const confirmingDelete = ref(null);

const deleteMsg = async (msg) => {
    try {
        await axios.delete(`/chat/message/${msg.message_id}`);
        const idx = props.room.messages.findIndex(m => m.message_id === msg.message_id);
        if (idx !== -1) props.room.messages.splice(idx, 1);
    } catch (e) { console.error(e); }
    confirmingDelete.value = null;
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
                        :class="['flex items-end gap-2 mb-1 group', isOwn(msg) ? 'justify-end' : 'justify-start']"
                    >
                        <!-- Recipient avatar -->
                        <Avatar
                            v-if="!isOwn(msg)"
                            :src="room.other_user?.image"
                            :alt="room.other_user?.name"
                            size="sm"
                            class="shrink-0 mb-1"
                        />

                        <!-- Action buttons (own messages, appear on hover, left of bubble) -->
                        <div
                            v-if="isOwn(msg) && editingId !== msg.message_id"
                            class="flex items-center gap-0.5 self-end mb-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
                        >
                            <button
                                v-if="msg.message"
                                @click.stop="startEdit(msg)"
                                title="Edit"
                                class="p-1.5 rounded-lg text-muted-foreground hover:text-foreground hover:bg-muted transition-colors"
                            >
                                <Pencil class="w-3.5 h-3.5" />
                            </button>

                            <!-- Delete with inline confirmation -->
                            <div class="relative">
                                <button
                                    @click.stop="confirmingDelete = confirmingDelete === msg.message_id ? null : msg.message_id"
                                    title="Delete"
                                    class="p-1.5 rounded-lg text-muted-foreground hover:text-destructive hover:bg-muted transition-colors"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                </button>

                                <Transition name="pop">
                                    <div
                                        v-if="confirmingDelete === msg.message_id"
                                        class="absolute bottom-full right-0 mb-2 bg-white rounded-2xl shadow-xl border border-border p-3 w-44 z-30"
                                        @click.stop
                                    >
                                        <p class="text-xs font-medium text-foreground mb-2">Delete this message?</p>
                                        <div class="flex gap-1.5">
                                            <button
                                                @click="deleteMsg(msg)"
                                                class="flex-1 text-xs py-1.5 bg-destructive text-white rounded-xl font-semibold hover:bg-destructive/90 transition-colors"
                                            >Delete</button>
                                            <button
                                                @click="confirmingDelete = null"
                                                class="flex-1 text-xs py-1.5 bg-muted rounded-xl text-muted-foreground hover:bg-muted/70 transition-colors"
                                            >Cancel</button>
                                        </div>
                                    </div>
                                </Transition>
                            </div>
                        </div>

                        <!-- Bubble -->
                        <div :class="['max-w-[70%] relative', isOwn(msg) ? 'items-end' : 'items-start']">
                            <div :class="[
                                'px-3 py-2 rounded-2xl shadow-sm',
                                isOwn(msg)
                                    ? 'bg-primary text-primary-foreground rounded-br-sm'
                                    : 'bg-white text-foreground border border-border rounded-bl-sm'
                            ]">
                                <!-- Inline edit mode -->
                                <template v-if="editingId === msg.message_id">
                                    <textarea
                                        ref="editInputRef"
                                        v-model="editText"
                                        @keydown="onEditKeydown($event, msg)"
                                        rows="2"
                                        class="w-full bg-transparent text-sm leading-relaxed resize-none outline-none border-b border-primary-foreground/40 pb-0.5 text-primary-foreground placeholder:text-primary-foreground/50"
                                    />
                                    <div class="flex justify-end gap-2 mt-1.5">
                                        <button @click="cancelEdit" class="text-[10px] text-primary-foreground/60 hover:text-primary-foreground transition-colors">
                                            Cancel
                                        </button>
                                        <button @click="saveEdit(msg)" class="text-[10px] font-semibold text-primary-foreground hover:opacity-80 transition-opacity">
                                            Save
                                        </button>
                                    </div>
                                </template>

                                <!-- Normal view -->
                                <template v-else>
                                    <p v-if="msg.message" class="text-sm leading-relaxed break-words">{{ msg.message }}</p>
                                    <img
                                        v-if="msg.picture_url"
                                        :src="msg.picture_url"
                                        alt="Image"
                                        class="max-w-[220px] max-h-[220px] rounded-xl object-cover cursor-pointer mt-1"
                                        @click="openModal(msg.picture_url)"
                                    />
                                    <div :class="[
                                        'flex items-center justify-end gap-1 mt-1',
                                        isOwn(msg) ? 'text-primary-foreground/60' : 'text-muted-foreground'
                                    ]">
                                        <span v-if="msg.is_edited" class="text-[10px] italic">edited</span>
                                        <span class="text-[10px] leading-none">{{ msg.formatted_time }}</span>
                                    </div>
                                </template>
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
                <button @click="closeModal" class="absolute -top-10 right-0 text-white/80 hover:text-white transition-colors">
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
.pop-enter-active, .pop-leave-active { transition: all 0.15s ease; }
.pop-enter-from, .pop-leave-to { opacity: 0; transform: scale(0.9) translateY(4px); }
</style>
