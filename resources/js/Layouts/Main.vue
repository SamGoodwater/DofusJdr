<script setup>
import Header from '@/Layouts/Header.vue';
import Aside from '@/Layouts/Aside.vue';
import Footer from '@/Layouts/Footer.vue';
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
const appKeywords = ref(import.meta.env.VITE_APP_KEYWORDS);
</script>

<template>

    <Head>
        <meta head-key="description" name="description" :content="appDescription" />
        <meta head-key="keywords" name="keywords" :content="appKeywords" />
    </Head>

    <div class="relative">

        <Header class="z-10 fixed max-sm:ml-0 ml-64 top-0 w-fit-available" />

        <Aside class="z-20" />

        <main
            class="relative max-sm:ml-0 ml-64 flex justify-center z-0 w-fit-available h-fit-available overflow-hidden">
            <div class="pt-24 pb-24 lg:px-10 md:px-6 sm:px-6 max-sm:px-4">
                <slot />
            </div>
            <div class="background fixed w-screen h-screen overflow-hidden"></div>
        </main>

        <Footer class="z-10 fixed max-sm:ml-0 ml-64 bottom-0 w-fit-available h-fit-available" />

    </div>





</template>

<style scoped>
.background {
    z-index: -1;
    background-image: linear-gradient(195deg, #1e40af 0%, #1e3a8a 3%, #172554 10%, #1e293b 25%, #1e293b 40%, #0f172a 62%, #020617 81%, #020617 100%)
}
</style>
