
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

// Subscribe to all rooms once on mount.
// Each closure captures its own `room` reference so messages always land
// in the correct room regardless of which chat is currently open.
export const connectToAllPrivateChannels = (rooms, props, selectedRoom, isFriendTyping, isFriendTypingTimer, onNewMessage) => {
    rooms.value.forEach(room => {
        Echo.private(`chat.room.${room.room_id}`)
            .listen("MessageSent", (event) => {
                if (props.current_user.id !== event.sender_id) {
                    room.messages.push(event);
                    onNewMessage?.(room, event);
                }
            })
            .listen("MessageUpdated", (event) => {
                const msg = room.messages.find(m => m.message_id === event.message_id);
                if (msg) {
                    msg.message = event.message;
                    msg.is_edited = true;
                }
            })
            .listen("MessageDeleted", (event) => {
                const idx = room.messages.findIndex(m => m.message_id === event.message_id);
                if (idx !== -1) room.messages.splice(idx, 1);
            })
            .listenForWhisper("typing", (event) => {
                if (selectedRoom.value.room_id !== room.room_id) return;
                isFriendTyping.value = event.userID === selectedRoom.value.other_user.id;
                if (isFriendTypingTimer.value) clearTimeout(isFriendTypingTimer.value);
                isFriendTypingTimer.value = setTimeout(() => { isFriendTyping.value = false; }, 1000);
            });
    });
};

// Send event typing
export const sendTypingEvent = (roomId, currentUserId) => {
    Echo.private(`chat.room.${roomId}`).whisper("typing", {
        userID: currentUserId,
    });
};
