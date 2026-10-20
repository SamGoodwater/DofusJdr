<script setup>
/**
 * PageMenuChildrenIndex — Index automatique des sous-pages d'une page parente.
 *
 * @description
 * Affiché en tête de page lorsque menu_collapsible est actif (cartes CMS).
 * Les catalogues Classes / Spécialisations n’envoient pas d’index (tableau entité + menu).
 *
 * @props {Array} items - Entrées renvoyées par PageService::buildMenuChildIndex
 */
import { computed } from 'vue';
import PageChildIndexCard from '@/Pages/Molecules/navigation/PageChildIndexCard.vue';
import BreedViewMinimal from '@/Pages/Molecules/entity/breed/BreedViewMinimal.vue';
import SpecializationViewMinimal from '@/Pages/Molecules/entity/specialization/SpecializationViewMinimal.vue';
import { Breed, Specialization } from '@/Models';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: 'Sous-pages',
    },
});

const normalizedItems = computed(() =>
    (Array.isArray(props.items) ? props.items : []).filter((item) => item?.title),
);

function resolveCardIcon(item) {
    const menuIcon = item?.menu_icon ?? item?.menuIcon ?? '';
    if (menuIcon && !String(menuIcon).startsWith('fa-')) {
        return menuIcon;
    }
    const icon = item?.icon ?? '';
    if (icon && !String(icon).startsWith('fa-')) {
        return icon;
    }
    return menuIcon || icon || '';
}

function toBreedModel(entity) {
    return entity ? new Breed(entity) : null;
}

function toSpecializationModel(entity) {
    return entity ? new Specialization(entity) : null;
}
</script>

<template>
    <section
        v-if="normalizedItems.length > 0"
        class="page-menu-children-index mb-6"
        aria-labelledby="page-menu-children-index-title"
    >
        <h2
            id="page-menu-children-index-title"
            class="mb-3 text-sm font-semibold uppercase tracking-wide text-base-content/55"
        >
            {{ title }}
        </h2>

        <div class="page-menu-children-index__grid grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
            <template v-for="item in normalizedItems" :key="item.id || item.url">
                <BreedViewMinimal
                    v-if="item.kind === 'breed' && item.entity"
                    :breed="toBreedModel(item.entity)"
                    :show-actions="false"
                    display-mode="compact"
                    class="h-full"
                />
                <SpecializationViewMinimal
                    v-else-if="item.kind === 'specialization' && item.entity"
                    :specialization="toSpecializationModel(item.entity)"
                    :show-actions="false"
                    display-mode="compact"
                    class="h-full"
                />
                <PageChildIndexCard
                    v-else
                    :title="item.title"
                    :href="item.url"
                    :icon="resolveCardIcon(item)"
                    :icon-alt="item.title"
                />
            </template>
        </div>
    </section>
</template>
