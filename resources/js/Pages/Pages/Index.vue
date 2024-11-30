<script setup>
import Layout from '@/Layouts/Default.vue';
defineOptions({
    layout: Layout
});
defineProps({
    pages: {
        type: Array,
        required: true
    }
});
</script>

<template>

    <Head title="Pages">
        <meta head-key="description" name="description" content="Liste des pages" />
        <meta head-key="keywords" name="keywords" content="Accueil, JDR, Jeu de Rôle, Dofus" />
    </Head>
    <h1>Liste des pages</h1>
    {{ pages }}
    <ul>
        <!-- <li v-for="page in pages" :key="page.id">
            <Link :href="route('/Pages/Show/', page.slug)">
            {{ page.slug }}
            </Link>
        </li> -->
    </ul>
</template>
