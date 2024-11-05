
import { ref } from 'vue';

export function useFileHandler() {
    const selectedFile = ref("");
    const selectedFileUrl = ref("");

    const attachedFile = (event) => {
        const file = event.target.files[0];
        if (file) {
            selectedFile.value = file;
            selectedFileUrl.value = URL.createObjectURL(file);
        }
    };

    const clearAttachedFile = () => {
        selectedFile.value = null;
        selectedFileUrl.value = '';
    };

    return {
        selectedFile,
        selectedFileUrl,
        attachedFile,
        clearAttachedFile,
    };
}
