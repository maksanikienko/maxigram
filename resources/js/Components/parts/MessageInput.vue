<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { useFileHandler } from '@/utils/fileHandler.js';
import { Smile, Paperclip, Send, X } from 'lucide-vue-next';
import 'emoji-picker-element';

const props = defineProps(['room']);
const emit = defineEmits(['typing']);

const { selectedFile, selectedFileUrl, attachedFile, clearAttachedFile } = useFileHandler();

const newMessage = ref('');
const sending = ref(false);
const fileInput = ref(null);

const showEmojiPicker = ref(false);
const emojiButtonRef = ref(null);
const pickerStyle = ref({});

const toggleEmojiPicker = () => {
    if (!showEmojiPicker.value && emojiButtonRef.value) {
        const rect = emojiButtonRef.value.getBoundingClientRect();
        const pickerWidth = 340;
        const leftPos = Math.max(4, Math.min(rect.left, window.innerWidth - pickerWidth - 8));
        pickerStyle.value = {
            bottom: `${window.innerHeight - rect.top + 8}px`,
            left: `${leftPos}px`,
        };
    }
    showEmojiPicker.value = !showEmojiPicker.value;
};

const addEmoji = (event) => {
    newMessage.value += event.detail.emoji.unicode;
    showEmojiPicker.value = false;
};

const closeEmojiPicker = () => { showEmojiPicker.value = false; };
onMounted(() => document.addEventListener('click', closeEmojiPicker));
onUnmounted(() => document.removeEventListener('click', closeEmojiPicker));

const sendMessage = async () => {
    if ((!newMessage.value.trim() && !selectedFile.value) || sending.value) return;
    sending.value = true;
    const formData = new FormData();
    formData.append('message', newMessage.value);
    if (selectedFile.value) formData.append('image', selectedFile.value);

    try {
        const { data } = await axios.post(`/chat/room/${props.room.room_id}/send`, formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        props.room.messages.push(data);
        newMessage.value = '';
        clearAttachedFile();
    } catch (e) {
        console.error(e);
    } finally {
        sending.value = false;
    }
};

const onKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendMessage();
    }
};
</script>

<template>
    <div class="shrink-0 px-4 pt-2 bg-white border-t border-border"
         style="padding-bottom: max(1rem, env(safe-area-inset-bottom))">

        <!-- Image preview -->
        <Transition name="slide">
            <div v-if="selectedFileUrl" class="mb-2 flex items-start gap-2 p-2 bg-muted rounded-xl">
                <div class="relative">
                    <img :src="selectedFileUrl" alt="preview" class="h-16 w-16 rounded-lg object-cover" />
                    <button
                        @click="clearAttachedFile"
                        class="absolute -top-1.5 -right-1.5 h-4 w-4 bg-destructive text-destructive-foreground rounded-full flex items-center justify-center"
                    >
                        <X class="w-2.5 h-2.5" />
                    </button>
                </div>
                <span class="text-xs text-muted-foreground pt-1">Image attached</span>
            </div>
        </Transition>

        <!-- Emoji picker (teleported to body to avoid overflow clipping) -->
        <Teleport to="body">
            <div
                v-if="showEmojiPicker"
                :style="{ position: 'fixed', zIndex: 9999, ...pickerStyle }"
                @click.stop
            >
                <emoji-picker @emoji-click="addEmoji" />
            </div>
        </Teleport>

        <!-- Input row -->
        <div v-if="room.room_id" class="flex items-end gap-2">
            <button
                ref="emojiButtonRef"
                @click.stop="toggleEmojiPicker"
                type="button"
                class="p-2.5 rounded-xl text-muted-foreground hover:text-foreground hover:bg-accent transition-colors shrink-0"
            >
                <Smile class="w-5 h-5" />
            </button>

            <textarea
                v-model="newMessage"
                @input="emit('typing')"
                @keydown="onKeydown"
                placeholder="Write a message..."
                rows="1"
                class="flex-1 resize-none rounded-2xl border border-input bg-muted/40 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-ring focus:bg-background transition-all min-h-[42px] max-h-32 leading-5"
                style="overflow-y:auto"
            />

            <button
                type="button"
                @click="fileInput.click()"
                class="p-2.5 rounded-xl text-muted-foreground hover:text-foreground hover:bg-accent transition-colors shrink-0"
            >
                <Paperclip class="w-5 h-5" />
            </button>
            <input ref="fileInput" type="file" @input="attachedFile" accept="image/*" class="hidden" />

            <button
                @click="sendMessage"
                type="button"
                :disabled="sending || (!newMessage.trim() && !selectedFile)"
                class="p-2.5 rounded-xl bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-40 disabled:cursor-not-allowed transition-all shrink-0 shadow-sm"
            >
                <Send class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.2s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-4px); }
</style>
