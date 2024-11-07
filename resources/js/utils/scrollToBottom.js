export const scrollToBottom = (container) => {
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: "smooth",
        });
    }
};
