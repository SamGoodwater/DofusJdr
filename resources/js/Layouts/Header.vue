<script setup>
import searchInput from '@/Components/layoutComponents/searchInput.vue';
import LoginHeaderContainer from '@/Components/layoutComponents/LoginHeaderContainer.vue';

let title = 'KrosmozJDR';

// import { ref } from 'vue';
// defineProps({
//     title: String
// });

</script>

<template>
    <header class=" h-auto flex justify-between p-2 px-4 gap-2">
        <div class="gap-x-4 flex items-center">
            <button data-drawer-target="default-sidebar" data-drawer-toggle="default-sidebar"
                aria-controls="default-sidebar" type="button"
                class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                    </path>
                </svg>
            </button>

            <Link :href="route('home')">
            <h2 class="align-items-center text-secondary hover:text-secondary/75 text-2xl fw-semibold">
                {{ title }}
            </h2>
            </Link>
        </div>

        <div>
            <searchInput class="max-sm:hidden" />
        </div>

        <div>
            <LoginHeaderContainer />
        </div>
    </header>
</template>

<style scoped>
header {
    backdrop-filter: blur(3px);
    background: linear-gradient(to top, rgba(24, 37, 66, 0), rgba(24, 37, 66, 0.5), rgba(24, 37, 66, 1));
}
</style>
