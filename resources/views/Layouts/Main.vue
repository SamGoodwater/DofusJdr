<script setup>
import Header from '../Layouts/Header.vue';
import Aside from '../Layouts/Aside.vue';
import Footer from '../Layouts/Footer.vue';
import { useSidebar } from '../../js/composables/useSidebar';

const { isSidebarOpen } = useSidebar();

</script>

<template>

    <div class="relative">

        <Header :class="[isSidebarOpen ? 'ml-64' : 'ml-0']" class="z-10 fixed max-sm:ml-0 top-0 w-fit-available" />

        <Aside class="z-20" />

        <main :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="relative max-sm:ml-0 flex flex-col align-items-center z-0 w-fit-available h-fit-available overflow-x-hidden">
            <div class="mt-24 mb-24 lg:mx-10 md:mx-6 sm:mx-6 max-sm:mx-4 ">
                <slot />
            </div>
            <div class="background fixed w-screen h-screen overflow-hidden"></div>
        </main>

        <Footer :class="[isSidebarOpen ? 'ml-64' : 'ml-0']"
            class="z-10 absolute max-sm:fixed max-sm:ml-0 bottom-0 w-fit-available h-fit-available" />

    </div>

</template>

<style scoped>
.background {
    z-index: -1;
    background-image: linear-gradient(195deg, #1e40af 0%, #1e3a8a 3%, #172554 10%, #1e293b 25%, #1e293b 40%, #0f172a 62%, #020617 81%, #020617 100%)
}
</style>
