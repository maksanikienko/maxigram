<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { Users, Plus, X, Check } from 'lucide-vue-next';

const emit = defineEmits(['selectGroup']);

const groups = ref([]);
const showCreate = ref(false);
const groupName = ref('');
const loading = ref(false);

const fetchGroups = async () => {
    try {
        const { data } = await axios.get('/api/groups/get');
        groups.value = data;
    } catch (e) {}
};

fetchGroups();

const createGroup = async () => {
    if (!groupName.value.trim()) return;
    loading.value = true;
    try {
        const { data } = await axios.post('/api/groups/create', { name: groupName.value });
        groups.value.push(data);
        groupName.value = '';
        showCreate.value = false;
    } catch (e) {}
    finally { loading.value = false; }
};
</script>

<template>
    <div class="mt-4 border-t border-white/10 pt-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <Users class="w-4 h-4 text-primary" />
                <h2 class="font-semibold text-sidebar-foreground text-sm">Groups</h2>
            </div>
            <button
                @click="showCreate = !showCreate"
                class="w-6 h-6 flex items-center justify-center rounded-full hover:bg-white/10 text-sidebar-foreground/60 hover:text-sidebar-foreground transition-colors"
            >
                <Plus v-if="!showCreate" class="w-4 h-4" />
                <X v-else class="w-4 h-4" />
            </button>
        </div>

        <Transition name="slide">
            <div v-if="showCreate" class="mb-3 flex gap-2">
                <input
                    v-model="groupName"
                    placeholder="Group name..."
                    @keydown.enter="createGroup"
                    class="flex-1 h-8 px-2 text-xs rounded-lg bg-white/10 border border-white/20 text-sidebar-foreground placeholder:text-sidebar-foreground/50 focus:outline-none focus:ring-1 focus:ring-primary/50"
                />
                <button
                    @click="createGroup"
                    :disabled="loading"
                    class="h-8 w-8 flex items-center justify-center rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-50 transition-colors"
                >
                    <Check class="w-3.5 h-3.5" />
                </button>
            </div>
        </Transition>

        <div class="space-y-0.5">
            <button
                v-for="group in groups"
                :key="group.id"
                @click="emit('selectGroup', group)"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-white/10 text-left transition-all"
            >
                <div class="h-9 w-9 rounded-full bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
                    <Users class="w-4 h-4 text-white" />
                </div>
                <span class="text-sm text-sidebar-foreground truncate">{{ group.name }}</span>
            </button>

            <div v-if="!groups.length" class="py-4 text-center text-sidebar-foreground/40 text-xs">
                No groups yet
            </div>
        </div>
    </div>
</template>

<style scoped>
.slide-enter-active, .slide-leave-active { transition: all 0.15s ease; }
.slide-enter-from, .slide-leave-to { opacity: 0; transform: translateY(-4px); }
</style>
