<script setup>
import Header from '@/Layouts/Header.vue';
import Aside from '@/Layouts/Aside.vue';
import Footer from '@/Layouts/Footer.vue';
</script>

<template>

    <Head>
        <meta head-key="description" name="description" content="KrosmosJDR">
        <meta head-key="keywords" name="keywords" content="Accueil, JDR, Jeu de Rôle, Dofus, Donjon & Dragon" />
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
