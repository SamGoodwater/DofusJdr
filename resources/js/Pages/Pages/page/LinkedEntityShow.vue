<script setup>
/**
 * Page CMS liée à une fiche classe ou spécialisation (sous-menu Bibliothèques).
 */
import { Head, usePage } from "@inertiajs/vue3";
import { computed, watch } from "vue";
import PageRenderer from "@/Pages/Organismes/section/PageRenderer.vue";
import BreedViewFull from "@/Pages/Molecules/entity/breed/BreedViewFull.vue";
import SpecializationViewFull from "@/Pages/Molecules/entity/specialization/SpecializationViewFull.vue";
import EntitySectionsRenderer from "@/Pages/Organismes/entity/EntitySectionsRenderer.vue";
import BreedWriteMetaPanel from "@/Pages/Molecules/entity/breed/BreedWriteMetaPanel.vue";
import { Breed } from "@/Models/Entity/Breed";
import { Specialization } from "@/Models/Entity/Specialization";
import { usePageTitle } from "@/Composables/layout/usePageTitle";

const inertiaPage = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    page: { type: Object, required: true },
    pages: { type: Array, default: () => [] },
    linkedEntityType: { type: String, required: true },
    linkedEntity: { type: Object, required: true },
});

const user = computed(() => inertiaPage.props.auth?.user || null);

const pageTitle = computed(() => props.page?.title || props.linkedEntity?.name || "Page");

watch(pageTitle, (t) => setPageTitle(t || "Page"), { immediate: true });

const breed = computed(() => {
    if (props.linkedEntityType !== "breed") return null;
    const raw = props.linkedEntity;
    return raw instanceof Breed ? raw : new Breed(raw);
});

const specialization = computed(() => {
    if (props.linkedEntityType !== "specialization") return null;
    const raw = props.linkedEntity;
    return raw instanceof Specialization ? raw : new Specialization(raw);
});

const cmsSections = computed(() => props.page?.sections ?? []);
const hasCmsSections = computed(() => Array.isArray(cmsSections.value) && cmsSections.value.length > 0);

const linkedEntitySections = computed(() => {
    const entity = breed.value ?? specialization.value;
    const raw = entity?.sections ?? entity?._data?.sections;
    return Array.isArray(raw) ? raw : [];
});
</script>

<template>
    <Head :title="pageTitle" />

    <div class="space-y-8 pb-10">
        <BreedViewFull
            v-if="breed"
            :breed="breed"
            :show-actions="true"
            :characteristic-runtime="inertiaPage.props.characteristicRuntime ?? null"
        />

        <template v-else-if="specialization">
            <SpecializationViewFull :specialization="specialization" :show-actions="true" />
        </template>

        <section
            v-if="linkedEntitySections.length"
            class="space-y-3"
            :aria-label="breed ? 'Contenu de la classe' : 'Contenu de la spécialisation'"
        >
            <h2 v-if="!breed" class="text-lg font-semibold">Sections</h2>
            <EntitySectionsRenderer
                :sections="linkedEntitySections"
                :immersive="Boolean(breed)"
                :empty-message="breed ? '' : 'Aucune section liée à cette spécialisation.'"
            />
        </section>

        <BreedWriteMetaPanel v-if="breed" :breed="breed" />

        <section v-if="hasCmsSections" class="space-y-3 border-t border-base-300/50 pt-6">
            <h2 v-if="breed || specialization" class="text-lg font-semibold">Contenu complémentaire</h2>
            <PageRenderer :page="page" :user="user" :pages="pages" />
        </section>
    </div>
</template>
