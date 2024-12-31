<template>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Groups</h2>
            <button
                @click="toggleModal"
                class="p-2 text-gray-500 hover:text-gray-700 bg-gray-100 rounded-md"
            >
                Create Group
            </button>
        </div>
        <ul
            class="space-y-2 divide-y divide-gray-300"
            @click="selectGroup"
        >

            <li
                v-for="group in groups"
                :key="group.id"
                @click="$emit('selectGroup', group)"
                class="p-2 flex items-center space-x-3 rounded-md cursor-pointer hover:bg-gray-300"
            >
                <div class="relative">
                    <img
                        v-if="group.image"
                        :src="group.image"
                        alt="group avatar"
                        class="w-10 h-10 rounded-full"
                    />
                    <img
                        v-else
                        src="https://robohash.org/default-group"
                        alt="group avatar"
                        class="w-10 h-10 rounded-full"
                    />
                </div>

                <div>
                    <div class="font-semibold">{{ group.name }}</div>
                    <div class=" flex text-xs text-gray-500 md:gap-x-1">Members:
                        <ul>
                            <li v-for="member in group.members" :key="member.id">
                                {{ member.name }}
                            </li>
                        </ul>
                    </div>
                </div>

            </li>
        </ul>

        <!-- Modal -->
        <div
            v-if="isModalOpen"
            class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold mb-4">Create New Group</h3>
                <form @submit.prevent="createGroup">
                    <div class="mb-4">
                        <label for="groupName" class="block text-sm font-medium text-gray-700 mb-1">Group Name</label>
                        <input
                            type="text"
                            id="groupName"
                            v-model="groupName"
                            class="block w-full border border-gray-300 rounded-md shadow-sm p-2"
                            placeholder="Enter group name"
                            required
                        />
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            @click="toggleModal"
                            class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600"
                        >
                            Create
                        </button>
                    </div>
                </form>
            </div>

            <div  class="mt-6 p-4 bg-gray-200 rounded-md">
                <h2 class="text-xl font-semibold">Chat with Group: {{ selectedGroup.name }}</h2>
                <div class="bg-white p-4 rounded-md mt-4">
                    <!-- Здесь будет отображаться чат -->
                    <div v-for="message in selectedGroup.messages" :key="message.id" class="mb-2">
                        <strong>{{ message.sender }}:</strong> {{ message.content }}
                    </div>
                </div>
            </div>

        </div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
import axios from 'axios';

const emits = defineEmits(['selectGroup']);

const isModalOpen = ref(false);
const groups = ref([]);
const groupName = ref('');

const toggleModal = () => {
    isModalOpen.value = !isModalOpen.value;
};

const createGroup = async () => {
    try {
        const response = await axios.post('/api/groups/create', { name: groupName.value });
        alert(`Group "${response.data.name}" created successfully!`);
        groupName.value = '';
        toggleModal();
    } catch (error) {
        console.error('Error creating group:', error);
        alert('Failed to create group. Please try again.');
    }
};
const getGroups = async () => {
    try {
        const response = await axios.get('/api/groups/get');
        groups.value = response.data.groups;
    } catch (error) {
        console.error('Failed to fetch groups:', error);
    }
};
const selectedGroup = ref(null);
const selectGroup = (group) => {
    selectedGroup.value = group;
    emits('selectGroup', true);
};
    onMounted(() => {
        getGroups();
    });
</script>
