<script setup>
/**
 * Fiche lecture d’une classe (Breed), vue large.
 *
 * @props {Object} breed - Payload BreedResource
 */
import { computed } from "vue";
import { Head, router, usePage } from "@inertiajs/vue3";
import { usePageTitle } from "@/Composables/layout/usePageTitle";
import { Breed } from "@/Models/Entity/Breed";
import Container from "@/Pages/Atoms/data-display/Container.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import EntityViewFullWrapper from "@/Pages/Molecules/entity/shared/EntityViewFullWrapper.vue";
import BreedViewFull from "@/Pages/Molecules/entity/breed/BreedViewFull.vue";
import EntitySectionsRenderer from "@/Pages/Organismes/entity/EntitySectionsRenderer.vue";
import BreedWriteMetaPanel from "@/Pages/Molecules/entity/breed/BreedWriteMetaPanel.vue";

const page = usePage();
const { setPageTitle } = usePageTitle();

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
});

const breed = computed(() => {
    const raw = props.breed || page.props.breed || {};
    return raw instanceof Breed ? raw : new Breed(raw);
});

const breedSections = computed(() => {
    const sections = breed.value?.sections;
    return Array.isArray(sections) ? sections : [];
});

const characteristicRuntime = computed(() => page.props.characteristicRuntime ?? null);

setPageTitle(`Classe : ${breed.value.name || "-"}`);

const goEdit = () => {
    if (!breed.value.id) return;
    router.visit(route("entities.breeds.edit", { breed: breed.value.id }));
};
</script>

<template>
    <Head :title="`Classe : ${breed?.name || '-'}`" />

    <Container class="space-y-6 pb-8">
        <EntityViewFullWrapper :show-back-button="true" back-route="entities.breeds.index">
            <div class="space-y-6">
                <div class="flex justify-end gap-2">
                    <Btn v-if="breed?.can?.update" color="primary" @click="goEdit">
                        <i class="fa-solid fa-pen mr-2" aria-hidden="true"></i>
                        Modifier
                    </Btn>
                </div>

                <BreedViewFull
                    :breed="breed"
                    :show-actions="true"
                    :characteristic-runtime="characteristicRuntime"
                />

                <section v-if="breedSections.length" class="space-y-3" aria-label="Contenu de la classe">
                    <EntitySectionsRenderer
                        :sections="breedSections"
                        :immersive="true"
                        empty-message=""
                    />
                </section>

                <BreedWriteMetaPanel :breed="breed" />
            </div>
        </EntityViewFullWrapper>
    </Container>
</template>
