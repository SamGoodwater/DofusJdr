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
    <header class=" h-auto flex justify-between align-items-center p-2 px-4 gap-2">
        <div class="gap-x-4 flex items-center">
            <label class="btn btn-circle swap swap-rotate">
                <!-- this hidden checkbox controls the state -->
                <input type="checkbox" />

                <!-- hamburger icon -->
                <svg class="swap-off fill-current" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                    viewBox="0 0 512 512">
                    <path d="M64,384H448V341.33H64Zm0-106.67H448V234.67H64ZM64,128v42.67H448V128Z" />
                </svg>

                <!-- close icon -->
                <svg class="swap-on fill-current" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                    viewBox="0 0 512 512">
                    <polygon
                        points="400 145.49 366.51 112 256 222.51 145.49 112 112 145.49 222.51 256 112 366.51 145.49 400 256 289.49 366.51 400 400 366.51 289.49 256 400 145.49" />
                </svg>
            </label>

            <Link :href="route('home')">
            <h2 class="align-items-center text-secondary hover:text-secondary/75 text-2xl fw-semibold">
                {{ title }}
            </h2>
            </Link>
        </div>

        <div>
            <searchInput class="max-sm:hidden" />
        </div>

        <div class="flex justify-content-between gap-3 align-items-center">
            <LoginHeaderContainer />

            <!-- Boutton pour déplier ou replier le header -->
            <div class="mr-2 swapHeader">
                <label class="swap swap-rotate">
                    <input type="checkbox" />

                    <svg class="tooltip tooltip-left swap-on h-8 w-8 fill-current" data-tip="Accrocher l'entête"
                        viewBox="-4.8 -4.8 57.60 57.60" xmlns="http://www.w3.org/2000/svg" fill="#94a3b8"
                        stroke="#94a3b8" transform="rotate(180)">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                            stroke-width="0.096"></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>align-bottom</title>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="invisible_box" data-name="invisible box">
                                    <rect width="32" height="32" fill="none"></rect>
                                </g>
                                <g id="Q3_icons" data-name="Q3 icons">
                                    <path d="M42,44H6a2,2,0,0,1,0-4H42a2,2,0,0,1,0,4Z"></path>
                                    <path
                                        d="M16.6,29.5l6,5.9a1.9,1.9,0,0,0,2.8,0l6-5.9a2.1,2.1,0,0,0,.2-2.7,1.9,1.9,0,0,0-3-.2L26,29.2V6a2,2,0,0,0-4,0V29.2l-2.6-2.6a1.9,1.9,0,0,0-3,.2A2.1,2.1,0,0,0,16.6,29.5Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </svg>

                    <svg class="tooltip tooltip-left swap-off h-8 w-8 fill-current" data-tip="Cacher l'entête"
                        viewBox="-4.8 -4.8 57.60 57.60" xmlns="http://www.w3.org/2000/svg" fill="#94a3b8"
                        stroke="#94a3b8">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                            stroke-width="0.096"></g>
                        <g id="SVGRepo_iconCarrier">
                            <title>align-bottom</title>
                            <g id="Layer_2" data-name="Layer 2">
                                <g id="invisible_box" data-name="invisible box">
                                    <rect width="32" height="32" fill="none"></rect>
                                </g>
                                <g id="Q3_icons" data-name="Q3 icons">
                                    <path d="M42,44H6a2,2,0,0,1,0-4H42a2,2,0,0,1,0,4Z"></path>
                                    <path
                                        d="M16.6,29.5l6,5.9a1.9,1.9,0,0,0,2.8,0l6-5.9a2.1,2.1,0,0,0,.2-2.7,1.9,1.9,0,0,0-3-.2L26,29.2V6a2,2,0,0,0-4,0V29.2l-2.6-2.6a1.9,1.9,0,0,0-3,.2A2.1,2.1,0,0,0,16.6,29.5Z">
                                    </path>
                                </g>
                            </g>
                        </g>
                    </svg>
                </label>
            </div>
        </div>
    </header>
</template>

<style scoped>
header {
    backdrop-filter: blur(3px);
    background: linear-gradient(to top, rgba(24, 37, 66, 0), rgba(24, 37, 66, 0.5), rgba(24, 37, 66, 1));
}

.swapHeader {
    align-self: center;
    opacity: 1;
    transition: opacity 0.3s ease-in-out;

    &:hover {
        cursor: pointer;
        opacity: 0.5;
    }
}
</style>
