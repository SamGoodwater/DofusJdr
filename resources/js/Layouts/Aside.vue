<script setup>
import searchInput from '@/Components/layoutComponents/searchInput.vue';
import Tooltips from '@/Components/feedback/tooltips.vue';
import { ref } from 'vue';

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appSlogan = ref(import.meta.env.VITE_APP_SLOGAN);
</script>

<template>

    <div>

        <aside id="default-sidebar"
            class="px-2 pt-4 fixed top-0 left-0 bottom-0 z-40 w-64 transition-transform -translate-x-full sm:translate-x-0 bg-base-200"
            aria-label="Sidenav">

            <Tooltips placement="bottom-center">
                <template #reference>
                    <Link class="hover:scale-105 focus:scale-95" :href="route('home')">
                    <figure>
                        <img class="w-auto px-8" src="storage/logos/logo.png" alt="Logo de {{ appName }}" />
                    </figure>
                    <h2 class="text-sm text-secondary/50 text-center">{{ appSlogan }}</h2>
                    </Link>
                </template>
                <template #content>
                    Aller à la page d'accueil
                </template>
            </Tooltips>

            <div id="nav" class="my-10">
                <ul>
                    <li class="my-2">
                        <Link :href="route('home')" class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                            :class="{ 'bg-primary/10': $page.component.includes('Home') }">
                        Accueil
                        </Link>
                    </li>
                    <li class="my-2">
                        <Link :href="route('page.index')" class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                            :class="{ 'bg-primary/10': $page.component.startsWith('/page') }">Pages
                        </Link>
                    </li>
                    <li class="my-2">
                        <Link :href="route('page.create')" class="w-full p-2 ps-8 block rounded hover:bg-primary/25"
                            :class="{ 'bg-primary/10': $page.component.startsWith('/page/create') }">Créer
                        une page</Link>
                    </li>
                </ul>
            </div>

            <div id="footer">
                <searchInput class="max-sm:block hidden" />

                <div class="btm-nav bg-transparent border-t-2 border-base-300">
                    <button>
                        <label class="swap swap-rotate">
                            <!-- this hidden checkbox controls the state -->
                            <input type="checkbox" />
                            <!-- sun icon -->
                            <svg class="swap-on h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24">
                                <path
                                    d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z" />
                            </svg>
                            <!-- moon icon -->
                            <svg class="swap-off h-5 w-5 fill-current" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24">
                                <path
                                    d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z" />
                            </svg>
                        </label>
                        <span class="btm-nav-label">Dark Mode</span>
                    </button>
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="btm-nav-label">Warnings</span>
                    </button>
                    <button>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span class="btm-nav-label">Statics</span>
                    </button>
                </div>
            </div>

        </aside>

    </div>

</template>

<style scoped>
aside {
    background-image: linear-gradient(195deg, #1e40af 0%, #1e3a8a 3%, #172554 10%, #1e293b 25%, #1e293b 40%, #0f172a 62%, #020617 81%, #020617 100%)
}
</style>
