// utils/statusHandler.js

export function useStatusHandler(rooms) {
    const loadOnlineStatus = () => {
        const storedStatus = JSON.parse(localStorage.getItem('friendsStatus'));
        if (storedStatus) {
            rooms.value.forEach(room => {
                const status = storedStatus.find(s => s.id === room.other_user.id);
                room.other_user.is_online = status ? status.is_online : false;
            });
        }
    };

    const saveOnlineStatus = () => {
        const statusToStore = rooms.value.map(room => ({
            id: room.other_user.id,
            is_online: room.other_user.is_online
        }));
        localStorage.setItem('friendsStatus', JSON.stringify(statusToStore));
    };

    return {
        loadOnlineStatus,
        saveOnlineStatus,
    };
}
