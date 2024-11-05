// utils/modalHandler.js

import { ref } from 'vue';

export function useModalHandler() {
    const isModalOpen = ref(false);
    const selectedImageUrl = ref(null);

    const openModal = (url) => {
        selectedImageUrl.value = url;
        isModalOpen.value = true;
    };

    const closeModal = () => {
        isModalOpen.value = false;
        selectedImageUrl.value = null;
    };

    return {
        isModalOpen,
        selectedImageUrl,
        openModal,
        closeModal,
    };
}
