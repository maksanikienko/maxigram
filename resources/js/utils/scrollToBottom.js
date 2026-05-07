export const scrollToBottom = (container, behavior = 'smooth') => {
    if (container) {
        container.scrollTo({ top: container.scrollHeight, behavior });
    }
};
