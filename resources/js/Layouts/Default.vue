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

    <div>
        <Header />
    </div>

    <nav>
        <Aside />
    </nav>

    <main class="pt-6 pb-20 sm:ml-64 flex justify-center">
        <div class="">
            <slot />
        </div>
    </main>

    <div>
        <Footer />
    </div>

</template>

<style scoped></style>
