
// Connect to presence channel
export const connectToPresenceChannel = (rooms, saveOnlineStatus) => {
    Echo.join('presence-chat.main')
        .here((users) => {
            users.forEach((user) => {
                rooms.value.forEach(room => {
                    if (room.other_user.id === user.id) {
                        room.other_user.is_online = true;
                    }
                });
            });
            saveOnlineStatus();
        })
        .joining((user) => {
            rooms.value.forEach(room => {
                if (room.other_user.id === user.id) {
                    room.other_user.is_online = true;
                    saveOnlineStatus();
                }
            });
        })
        .leaving((user) => {
            rooms.value.forEach(room => {
                if (room.other_user.id === user.id) {
                    room.other_user.is_online = false;
                    saveOnlineStatus();
                }
            });
        });
};
// Connect to private channel
export const connectToPrivateChannel = (roomId, props, selectedRoom, isFriendTyping, isFriendTypingTimer) => {
    Echo.private(`chat.room.${roomId}`)
        .listen("MessageSent", (event) => {
            if (props.current_user.id !== event.sender_id) {
                selectedRoom.value.messages.push(event);
            }
        })
        .listenForWhisper("typing", (event) => {
            isFriendTyping.value = event.userID === selectedRoom.value.other_user.id;

            if (isFriendTypingTimer.value) {
                clearTimeout(isFriendTypingTimer.value);
            }
            isFriendTypingTimer.value = setTimeout(() => {
                isFriendTyping.value = false;
            }, 1000);
        });
};
// Send event typing
export const sendTypingEvent = (roomId, currentUserId) => {
    Echo.private(`chat.room.${roomId}`).whisper("typing", {
        userID: currentUserId,
    });
};
