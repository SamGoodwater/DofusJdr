<script setup>
/**
 * Panoply Edit Page
 *
 * @description
 * Édition sheet (header compact + grille 2 colonnes) + pièces / bonus en containers.
 *
 * @props {Object} panoply - Données de la panoplie à éditer
 * @props {Array} bonusCharacteristics - Caractéristiques groupe object pour les bonus
 */
import { computed, ref } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { usePageTitle } from '@/Composables/layout/usePageTitle';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { Panoply } from '@/Models/Entity/Panoply';
import EntityEditForm from '@/Pages/Organismes/entity/EntityEditForm.vue';
import EntityRelationsManager from '@/Pages/Organismes/entity/EntityRelationsManager.vue';
import PanoplyBonusEditor from '@/Pages/Organismes/entity/PanoplyBonusEditor.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import EntityEditHeader from '@/Pages/Molecules/entity/shared/EntityEditHeader.vue';
import EntityEditContainer from '@/Pages/Molecules/entity/shared/EntityEditContainer.vue';
import LevelBadge from '@/Pages/Molecules/data-display/LevelBadge.vue';
import {
    buildPanoplyFormFieldsConfig,
    PANOPLY_FORM_FIELD_SECTIONS_EDIT,
} from '@/Entities/panoply/panoply-form-config';

const page = usePage();
const { setPageTitle } = usePageTitle();
const { canDeleteAny, isAdmin } = usePermissions();

const props = defineProps({
    panoply: {
        type: Object,
        required: true,
    },
    bonusCharacteristics: {
        type: Array,
        default: () => [],
    },
    /** True quand la page est chargée dans EntitySurfaceHost (modal). */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
});
const { isEmbeddedInModal, resolvedRedirectAfterUpdate } = useEntityEditEmbed(props);


const fieldsConfig = computed(() =>
    buildPanoplyFormFieldsConfig({ includeReadonlyMeta: true }),
);
const fieldSections = PANOPLY_FORM_FIELD_SECTIONS_EDIT;
const fixedFooterInsetClass = 'left-0 right-0';
const entityEditFormRef = ref(null);
const canDelete = computed(() => canDeleteAny('panoplies') || isAdmin.value);

const panoply = computed(() => {
    const panoplyData = props.panoply || page.props.panoply || {};
    return new Panoply(panoplyData);
});

const linkedItems = computed(() => {
    const raw = panoply.value?.items;
    return Array.isArray(raw) ? raw : [];
});

const levelValue = computed(() => {
    const lv = panoply.value?.level;
    if (lv === null || lv === undefined || lv === '') {
        return null;
    }
    const n = Number(lv);
    return Number.isFinite(n) ? n : null;
});

setPageTitle(`Modifier la panoplie : ${panoply.value.name || 'Nouvelle panoplie'}`);

function goToShow() {
    const id = panoply.value?.id;
    if (!id) return;
    router.visit(route('entities.panoplies.show', { panoply: id }));
}
</script>

<template>
    <Head :title="`Modifier la panoplie : ${panoply?.name || 'Nouvelle panoplie'}`" />

    <Container class="space-y-4">
        <EntityEditHeader
            entity-type="panoplies"
            :entity="panoply"
            :form-ref="entityEditFormRef"
            list-route-name="entities.panoplies.index"
            delete-route-name="entities.panoplies.delete"
            route-param-key="panoply"
            :can-delete="canDelete"
            delete-confirm-message="Supprimer cette panoplie ? Elle sera placée en corbeille (récupération possible côté admin)."
        >
            <template #title>
                <button
                    type="button"
                    class="flex w-full flex-wrap items-center justify-center gap-2 truncate text-md font-bold text-base-content hover:underline sm:text-lg"
                    @click="goToShow"
                >
                    <span class="truncate">{{ panoply.name || 'Panoplie sans nom' }}</span>
                    <LevelBadge
                        v-if="levelValue != null"
                        :level="levelValue"
                        size="xs"
                        class="shrink-0"
                    />
                </button>
            </template>
        </EntityEditHeader>

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="panoply"
            entity-type="panoply"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            layout-profile="sheet"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :compact-access-levels="true"
            :embedded-in-modal="isEmbeddedInModal"
            :redirect-after-update="resolvedRedirectAfterUpdate"
        >
            <template #after-sections>
                <EntityEditContainer
                    title="Équipements de la panoplie"
                    subtitle="Catalogue et pièces du set (niveau = pièce la plus élevée)."
                    icon="fa-solid fa-shield-halved"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="mt-3 lg:col-span-2"
                >
                    <div class="space-y-4">
                        <p class="text-xs text-base-content/60">
                            Recherchez un équipement dans le catalogue, puis retirez-le si besoin.
                            Le niveau du set est celui de la pièce la plus élevée.
                        </p>
                        <EntityRelationsManager
                            :relations="linkedItems"
                            :entity-id="panoply.id"
                            entity-type="panoplies"
                            relation-type="items"
                            relation-name="Pièces de la panoplie"
                            :show-title="false"
                            :config="{
                                displayFields: ['name', 'description', 'level'],
                                searchFields: ['name', 'description'],
                                relatedEntityType: 'items',
                                searchApiEntityType: 'items',
                                itemLabel: 'équipement',
                                itemLabelPlural: 'équipements',
                            }"
                        />
                    </div>
                </EntityEditContainer>

                <EntityEditContainer
                    title="Bonus de set"
                    subtitle="Caractéristiques par nombre de pièces équipées."
                    icon="fa-solid fa-layer-group"
                    :span="2"
                    collapsible
                    :default-open="false"
                    root-class="lg:col-span-2"
                >
                    <div class="space-y-4">
                        <p class="text-xs text-base-content/60">
                            Même principe que les effets d’équipement : une caractéristique et une valeur,
                            groupées par nombre de pièces équipées (2p, 3p, …).
                        </p>
                        <PanoplyBonusEditor
                            v-if="panoply.id"
                            :panoply-id="panoply.id"
                            :bonus="panoply.bonus"
                            :characteristics="bonusCharacteristics"
                        />
                    </div>
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </Container>
</template>
