<script setup>
import searchInput from '../Layouts/components/searchInput.vue';
import LoginHeaderContainer from '../Layouts/components/LoginHeaderContainer.vue';
import tooltips from '../Components/feedback/tooltips.vue';
import { useSidebar } from '../../js/composables/useSidebar';
import { useHeader } from '../../js/composables/useHeader';
import Route from '../Components/text/Route.vue';
import { ref, onMounted } from 'vue';

const { toggleSidebar } = useSidebar();
const { toggleHeader, isHeaderOpen } = useHeader();

const title = ref(import.meta.env.VITE_APP_NAME);

onMounted(() => {
    updateTitle();
});

function updateTitle() {
    const pageTitle = document.title;
    title.value = pageTitle;
}

</script>

<template>
    <header class="flex justify-between p-2 px-4 gap-2 align-items-center"
        :class="[isHeaderOpen ? 'header-on' : 'header-off hover:header-on']">
        <div class="flex justify-content-between gap-3 align-items-center">

            <tooltips bgColor="bg-body-900/70" placement="bottom-start">
                <template #reference>
                    <label id="toggleBtnsidebarMenu" @click="toggleSidebar" class="btn btn-circle swap swap-rotate">
                        <!-- this hidden checkbox controls the state -->
                        <input type="checkbox" />

                        <svg class="swap-off fill-current hover:opacity-50 tooltip tooltip-bottom"
                            data-tip="Masquer le menu" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                            viewBox="0 0 512 512">
                            <polygon
                                points="400 145.49 366.51 112 256 222.51 145.49 112 112 145.49 222.51 256 112 366.51 145.49 400 256 289.49 366.51 400 400 366.51 289.49 256 400 145.49" />
                        </svg>

                        <svg class="swap-on fill-current hover:opacity-50 tooltip tooltip-bottom"
                            data-tip="Afficher le menu" xmlns="http://www.w3.org/2000/svg" width="32" height="32"
                            viewBox="0 0 512 512">
                            <path d="M64,384H448V341.33H64Zm0-106.67H448V234.67H64ZM64,128v42.67H448V128Z" />
                        </svg>

                    </label>
                </template>
                <template #content>
                    <p>Masquer ou afficher le menu <span class="flex flex-nowrap align-items-center"><kbd
                                class="kbd kbd-sm">alt</kbd>
                            + <kbd class="kbd kbd-sm">g</kbd></span></p>
                </template>
            </tooltips>


            <div>
                <Route v-if="title='home'" route='home'>
                <h2 id="appTitle" class="align-items-center text-secondary hover:text-secondary/75 text-2xl fw-semibold">
                    {{ title }}
                </h2>
                </Route>
            </div>
        </div>

        <div>
            <searchInput class="max-sm:hidden" />
        </div>

        <div class="flex justify-content-between gap-3 align-items-center">
            <LoginHeaderContainer />

            <!-- Boutton pour déplier ou replier le header -->
            <tooltips bgColor="bg-body-900">
                <template #reference>
                    <div class="mr-2 swapHeader ">
                        <label class="swap swap-rotate" @click="toggleHeader">
                            <input type="checkbox" />

                            <svg class="swap-off h-6 w-6 fill-current" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M3 6L6 6L6 12L10 12L10 6L13 6V5L8 0L3 5L3 6Z" fill="#94a3b8"></path>
                                    <path d="M2 16L14 16V14L2 14V16Z" fill="#94a3b8"></path>
                                </g>
                            </svg>

                            <svg class="swap-on h-6 w-6 fill-current" viewBox="0 0 16 16" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                <g id="SVGRepo_iconCarrier">
                                    <path d="M13 7H10V0H6V7L3 7V8L8 13L13 8V7Z" fill="#94a3b8"></path>
                                    <path d="M14 14H2V16H14V14Z" fill="#e4e4e7"></path>
                                </g>
                            </svg>
                        </label>
                    </div>
                </template>

                <template #content>
                    <div class="w-52 text-center flex flex-col m-2">
                        <p class="text-md text-minor-200 flex justify-content-between gap-3"><span> Masquer ou afficher
                                l'entête</span>
                            <span class="flex flex-nowrap align-items-center"><kbd class="kbd kbd-sm">alt</kbd>
                                + <kbd class="kbd kbd-sm">h</kbd></span>
                        </p>
                        <p class="text-sm text-minor-200/70 my-2">Déplacer la souris vers le haut du site pour
                            faire réaparaître l'entête</p>
                        <div class="flex justify-center relative">
                            <div class="headerVector bg-slate-500 rounded-none"></div>
                            <div class=" h-40 w-40 bg-slate-800 rounded-none"></div>
                            <div class="mouseVector">
                                <svg width="15px" height="15px" viewBox="0 0 20 20" version="1.1"
                                    xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                    fill="#e4e4e7" transform="rotate(270)" stroke="#e4e4e7">
                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                    <g id="SVGRepo_iconCarrier">
                                        <title>mouse_pointer [#6]</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs> </defs>
                                        <g id="Page-1" stroke-width="0.0002" fill="none" fill-rule="evenodd">
                                            <g id="Dribbble-Light-Preview"
                                                transform="translate(-140.000000, -8159.000000)" fill="#e4e4e7">
                                                <g id="icons" transform="translate(56.000000, 160.000000)">
                                                    <polygon id="mouse_pointer-[#6]"
                                                        points="104 7999 97.075 8017.824 92.54 8013.289 86.875 8019 84 8016.125 89.711 8010.46 85.176 8005.925">
                                                    </polygon>
                                                </g>
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>

                </template>
            </tooltips>

        </div>
    </header>
</template>

<style scoped lang="scss">
header {
    backdrop-filter: blur(5px);
    background: linear-gradient(to top, rgba(24, 37, 66, 0), rgba(24, 37, 66, 0.5), rgba(24, 37, 66, 1));
    transition: transform 0.3s ease-in-out;

    &:hover.header-off {
        transform: translateY(0);
        opacity: 1;
    }
}

.header-on {
    transform: translateY(0);
    opacity: 1;
}

.header-off {
    transform: translateY(-60%);
    opacity: 0;
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

.mouseVector {
    position: absolute;
    top: 4rem;
    left: 3rem;
    transform: translate(-50%, -50%);
    animation: moveMouse 4s infinite;
}

.headerVector {
    position: absolute;
    top: 0;
    width: 10rem;
    height: 1.7rem;
    animation: moveHeader 8s infinite;
    transform-origin: top;
}

@keyframes moveMouse {
    0% {
        top: 4rem;
        left: 3rem;
    }

    33% {
        top: 4rem;
        left: 3rem;
    }


    66% {
        top: 1rem;
        left: 6rem;
    }

    100% {
        top: 4rem;
        left: 3rem;
    }
}

@keyframes moveHeader {
    0% {
        transform: scaleY(1);
    }

    26% {
        transform: scaleY(1);
    }

    33% {
        transform: scaleY(0);
    }

    51% {
        transform: scaleY(0);
    }

    79% {
        transform: scaleY(0);
    }

    90% {
        transform: scaleY(1);
    }

    100% {
        transform: scaleY(1);
    }
}
</style>
