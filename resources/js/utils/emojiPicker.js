import { ref, onMounted, onUnmounted } from 'vue';

export function useEmojiPicker() {
    const showEmojiPicker = ref(false);
    const selectedEmoji = ref('');
    const emojiPickerContainer = ref(null);

    const toggleEmojiPicker = () => {
        showEmojiPicker.value = !showEmojiPicker.value;

    };

    const addEmoji = (event) => {
        selectedEmoji.value = event.detail.emoji.unicode;
        showEmojiPicker.value = false;
    };

    const handleClickOutside = (event) => {
        if (emojiPickerContainer.value && !emojiPickerContainer.value.contains(event.target)) {
            showEmojiPicker.value = false;
        }
    };

    onMounted(() => {
        document.addEventListener('click', handleClickOutside);
    });

    onUnmounted(() => {
        document.removeEventListener('click', handleClickOutside);
    });

    return {
        showEmojiPicker,
        selectedEmoji,
        emojiPickerContainer,
        toggleEmojiPicker,
        addEmoji,
    };
}
