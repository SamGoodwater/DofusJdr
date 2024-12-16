<script setup>
defineProps({
    pages: {
        type: Array,
        required: true
    }
});
</script>

<template>
    <h1>Liste des pages</h1>
    <ul>
        <li v-for="page in pages" :key="page.id">
            <Link :href="route('/Pages/Show/', page.slug)">
            {{ page.slug }}
            </Link>
        </li>
    </ul>
</template>
