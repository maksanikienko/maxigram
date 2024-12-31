<script setup >
import ApplicationLogo from "@/Components/ApplicationLogo.vue";

const props = defineProps(['room','current_user','profileUrl']);

</script>

<template>
    <!-- Messenger block Header -->
    <div class="flex items-center justify-between py-3 border-b-2 border-gray-200">
        <div class="relative md:mx-auto md:ml-0 flex items-center space-x-4">
            <div class="relative">
                            <span v-if="room.other_user.is_online" class="absolute text-green-500 right-0 bottom-0">
                              <svg width="20" height="20">
                                <circle cx="8" cy="8" r="8" fill="currentColor"></circle>
                              </svg>
                            </span>
                <span v-else class="absolute text-green-500 right-0 bottom-0">
                              <svg width="16" height="16">
                                <circle cx="8" cy="8" r="8" fill="#7d7c7c"></circle>
                              </svg>
                            </span>
                <img v-if="room.other_user.image" :src="room.other_user.image ? room.other_user.image : `https://robohash.org/vvvv` " alt="" class="w-10 sm:w-16 h-10 sm:h-16 rounded-full">
<!--                <img v-else src="https://robohash.org/vvvv" alt="avatar" class="w-10 h-10 rounded-full">-->
            </div>
            <div v-if="room" class="flex flex-col leading-tight">
                <div class="text-2xl mt-1 flex items-center">
                    <span class="text-gray-700 mr-3">{{room.other_user.name}}</span>
                </div>
                <span class="text-lg text-gray-600">{{ room.other_user.is_online ? "Online" : "Offline" }}</span>
            </div>
        </div>

        <div class="relative">
            <button id="userDropdown" data-dropdown-toggle="dropdownMenu" class="text-gray-700 font-semibold flex gap-3 ">
                <img v-if="props.current_user.image" class="md:max-w-16 max-w-8 md:max-h-16 max-h-8 rounded-full" :src="props.current_user.image" alt="">
                <ApplicationLogo
                    v-else
                    class="block md:max-w-16 max-w-8 w-auto fill-current text-gray-800"
                />
            </button>
            <div id="dropdownMenu" class="z-10 hidden divide-y divide-gray-100 rounded-lg shadow bg-gray-50">
                <ul class="py-1 text-gray-700" aria-labelledby="userDropdown">
                    <li>
                        <a v-if="profileUrl" :href="profileUrl" class="block px-4 py-2 text-sm">Profile</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>


