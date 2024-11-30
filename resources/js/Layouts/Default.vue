<script setup>
import Header from '@/Layouts/Header.vue';
import Aside from '@/Layouts/Aside.vue';
import Footer from '@/Layouts/Footer.vue';
</script>

<template>

    <Head>
        <meta head-key="description" name="description" content="DofusJDR">
        <meta head-key="keywords" name="keywords" content="Accueil, JDR, Jeu de Rôle, Dofus, Donjon & Dragon" />
    </Head>

    <div>
        <Header />
    </div>

    <nav>
        <Aside />
    </nav>

    <main class="p-2 m-8 sm:ml-72">
        <slot />
    </main>

    <div>
        <Footer />
    </div>

</template>

<style scoped></style>
