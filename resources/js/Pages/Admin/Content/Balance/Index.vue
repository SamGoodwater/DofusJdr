<script setup>
/**
 * Hub d’équilibrage — liens vers les fiches caractéristiques (normes / formules).
 */
import { Head, Link } from '@inertiajs/vue3';
import AdminArea from '@/Pages/Layouts/AdminArea.vue';
import PageHeader from '@/Pages/Molecules/layout/PageHeader.vue';

defineOptions({ layout: AdminArea });

defineProps({
    families: { type: Array, required: true },
});
</script>

<template>
    <div>
        <Head title="Équilibrage" />
        <PageHeader
            title="Équilibrage"
            subtitle="Normes générées par formule (Excel → caractéristiques). Édition via chaque fiche."
        />

        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            <section
                v-for="family in families"
                :key="family.title"
                class="rounded-box border border-base-300 bg-base-100 p-4"
            >
                <h2 class="text-base font-semibold text-title">{{ family.title }}</h2>
                <p class="mt-1 text-sm text-content/70">{{ family.description }}</p>
                <ul class="mt-3 space-y-1">
                    <li v-for="item in family.keys" :key="item.key">
                        <Link
                            :href="route('admin.characteristics.show', item.key)"
                            class="link link-primary text-sm"
                        >
                            {{ item.label }}
                            <span class="font-mono text-xs opacity-60">({{ item.key }})</span>
                        </Link>
                    </li>
                </ul>
            </section>
        </div>

        <p class="mt-6 text-sm text-content/60">
            Remise aux valeurs seed : <code>php artisan project:seed --overwrite</code>
            puis <code>php artisan characteristics:regenerate-norms</code>.
        </p>
    </div>
</template>
