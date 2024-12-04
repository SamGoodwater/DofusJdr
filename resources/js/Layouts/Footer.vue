<script setup>
import { ref } from 'vue';

const convertStability = {
    alpha: "α",
    beta: "β",
    rc: "rc",
    stable: ""
};

const appName = ref(import.meta.env.VITE_APP_NAME);
const appVersion = ref(import.meta.env.VITE_APP_VERSION);
const appDescription = ref(import.meta.env.VITE_APP_DESCRIPTION);
const appStability = ref(convertStability[import.meta.env.VITE_APP_STABILITY]);
</script>


<template>
    <footer
        class="bg-base-200 h-14 py-2 m-o mt-2 p-0 flex flex-auto justify-center fixed bottom-0 left-64 sm:left-0 right-0 border-t-2 border-base-300">

        <!-- Si l'affichage est plus grand que sm : alors on affiche un footer classique -->
        <div class="block max-sm:hidden content-center">
            <p class="text-sm">{{ appName }} - version {{ appVersion + " " + appStability }} - {{ new
                Date().getFullYear() }}</p>
        </div>

        <!-- Si l'affichage est plus petit ou  égale à sm : alors on affiche le menu-->
        <div class="hidden max-sm:block">
            <div class="btm-nav">
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="btm-nav-label">Menu</span>
                </button>
                <button class="active">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="btm-nav-label">Accueil</span>
                </button>
                <button>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span class="btm-nav-label">Mon Compte</span>
                </button>
            </div>
        </div>

    </footer>
</template>
