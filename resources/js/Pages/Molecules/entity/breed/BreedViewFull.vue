<script setup>
/**
 * BreedViewFull — Vue Full pour Breed
 *
 * En-tête + blocs gameplay structuraux (voix, passif, mécanique, traits, langues, choix de sorts).
 * Le contenu narratif (spécificité, dé de vie, évolution) est dans les sections liées.
 */
import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import EntityPropertyDisplay from "@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityViewHeader from "@/Pages/Molecules/entity/shared/EntityViewHeader.vue";
import ImageViewer from "@/Pages/Molecules/data-display/ImageViewer.vue";
import { resolveEntityFieldUi } from "@/Utils/Entity/entity-view-ui";
import { useCopyToClipboard } from "@/Composables/utils/useCopyToClipboard";
import { useDownloadPdf } from "@/Composables/utils/useDownloadPdf";
import { useEntityActionDispatcher } from "@/Composables/entity/useEntityActionDispatcher";
import EntityDofusdbRefreshPanel from "@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue";
import { getEntityRouteConfig, resolveEntityRouteUrl } from "@/Composables/entity/entityRouteRegistry";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { getBreedFieldDescriptors } from "@/Entities/breed/breed-descriptors";
import { provideCharacteristicRuntime } from "@/Composables/entity/characteristicRuntimeContext";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedCapabilitiesDisplay from "@/Pages/Molecules/entity/breed/BreedCapabilitiesDisplay.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import BreedMechanicsDisplay from "@/Pages/Molecules/entity/breed/BreedMechanicsDisplay.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { breedFullUrl, breedHasBothFullImages } from "@/Utils/entity/breedImages";

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    inModal: {
        type: Boolean,
        default: false,
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: { type: Object, default: null },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? "compact" : "full"));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits([
    "edit",
    "copy-link",
    "download-pdf",
    "refresh",
    "view",
    "quick-view",
    "delete",
    "action",
]);

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher("breeds", {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf("breed");
const permissions = usePermissions();

const ctx = computed(() => {
    const capabilities = {
        viewAny: permissions.can("breeds", "viewAny"),
        createAny: permissions.can("breeds", "createAny"),
        updateAny: permissions.can("breeds", "updateAny"),
        deleteAny: permissions.can("breeds", "deleteAny"),
        manageAny: permissions.can("breeds", "manageAny"),
    };
    return { capabilities, meta: { capabilities } };
});

const descriptors = computed(() => getBreedFieldDescriptors(ctx.value));

const fullGender = ref("m");

const mediaSrc = computed(() => breedFullUrl(props.breed, fullGender.value));

const hasBothFullImages = computed(() => breedHasBothFullImages(props.breed));

const spellSlotGroups = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    return buildSpellSlotGroups(raw);
});

const hasSpellSlots = computed(() => spellSlotGroups.value.length > 0);

const linkedCreatureTraits = computed(() => {
    const raw = props.breed?._data?.creatureTraits ?? props.breed?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedCreatureTraits = computed(() => linkedCreatureTraits.value.length > 0);

const linkedLanguages = computed(() => {
    const raw = props.breed?._data?.languages ?? props.breed?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasLinkedLanguages = computed(() => linkedLanguages.value.length > 0);

const descriptionFast = computed(() => {
    const b = props.breed?._data ?? props.breed;
    const t = b?.description_fast;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const descriptionFull = computed(() => {
    const b = props.breed?._data ?? props.breed;
    const t = b?.description ?? props.breed?.description;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

/** HTML TipTap — mécanique signature (vide = bloc masqué). */
const mechanicsHtml = computed(() => {
    const b = props.breed?._data ?? props.breed;
    const t = b?.mechanics ?? props.breed?.mechanics;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const orientationMap = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    return normalizeElementOrientationMap(raw?.element_orientations);
});

const linkedCapabilities = computed(() => {
    const raw = props.breed?._data ?? props.breed;
    const list = raw?.capabilities;
    return Array.isArray(list) ? list : [];
});

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf ?? desc?.visibleIf;
    if (typeof visibleIf === "function") {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            console.warn("[BreedViewFull] visibleIf failed for", fieldKey, e);
            return false;
        }
    }
    return true;
};

/** Résumé sorts uniquement si pas encore de variantes structurées. */
const summaryMetaFields = computed(() =>
    hasSpellSlots.value ? [] : ["breed_summary_relations"].filter(canShowField),
);

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({
        fieldKey,
        descriptors: descriptors.value,
        tableMeta: props.tableMeta,
        entityType: "breed",
    });

const handleAction = async (actionKey) => {
    const breedId = props.breed.id;
    if (!breedId) return;

    switch (actionKey) {
        case "view":
            router.visit(route("entities.breeds.show", { breed: breedId }));
            emit("view", props.breed);
            break;
        case "edit":
            await dispatchEntityAction("edit", props.breed, { context: actionsContext.value });
            emit("edit", props.breed);
            break;
        
        case "copy-link": {
            const cfg = getEntityRouteConfig("breed");
            const url = resolveEntityRouteUrl("breed", "show", breedId, cfg);
            if (url) {
                await copyToClipboard(`${window.location.origin}${url}`, "Lien de la classe copié !");
            }
            emit("copy-link", props.breed);
            break;
        }
        case "download-pdf":
            await downloadPdf(breedId);
            emit("download-pdf", props.breed);
            break;
        case "refresh": {
            await dispatchEntityAction("refresh", props.breed);
            break;
        }
        case "delete":
            emit("delete", props.breed);
            break;
    }
};
</script>

<template>
    <div class="space-y-4">
        <EntityViewHeader :mode="headerMode">
            <template #media>
                <div class="group relative w-36 h-36 md:w-48 md:h-48 lg:w-56 lg:h-56">
                    <ImageViewer
                        v-if="mediaSrc"
                        :source="mediaSrc"
                        :alt="breed.name || 'Classe'"
                        :caption="breed.name || ''"
                        preload="hover"
                        :image-props="{
                            size: 'xl',
                            rounded: 'lg',
                            fit: 'cover',
                            class: 'w-full h-full',
                        }"
                    />

                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-graduation-cap" :alt="breed.name" size="xl" />
                    </div>

                    <div
                        v-if="hasBothFullImages"
                        class="absolute bottom-2 right-2 z-10 join"
                        role="group"
                        aria-label="Genre du personnage"
                    >
                        <button
                            type="button"
                            class="btn btn-xs join-item"
                            :class="fullGender === 'm' ? 'btn-primary' : 'btn-ghost'"
                            @click="fullGender = 'm'"
                        >
                            M
                        </button>
                        <button
                            type="button"
                            class="btn btn-xs join-item"
                            :class="fullGender === 'f' ? 'btn-primary' : 'btn-ghost'"
                            @click="fullGender = 'f'"
                        >
                            F
                        </button>
                    </div>
                </div>
            </template>

            <template #title>
                <h2 class="text-2xl font-bold text-primary-100 break-words">{{ breed.name }}</h2>
            </template>

            <template #subtitle>
                <div
                    v-if="descriptionFast || descriptionFull"
                    class="mt-3 max-w-3xl space-y-2 border-t border-base-300/60 pt-3"
                >
                    <p
                        v-if="descriptionFast"
                        class="text-sm leading-relaxed text-primary-300 break-words"
                    >
                        {{ descriptionFast }}
                    </p>
                    <p
                        v-if="descriptionFull && descriptionFull !== descriptionFast"
                        class="text-sm leading-relaxed whitespace-pre-wrap text-primary-200/90 break-words"
                    >
                        {{ descriptionFull }}
                    </p>
                </div>
            </template>

            <template #mainInfos>
                <div
                    v-if="summaryMetaFields.length > 0"
                    class="mt-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2"
                >
                    <EntityPropertyDisplay
                        v-for="fieldKey in summaryMetaFields"
                        :key="fieldKey"
                        :field-key="fieldKey"
                        :entity="breed"
                        entity-type="breed"
                        display-mode="extended"
                        :descriptors="descriptors"
                        :table-meta="tableMeta"
                        size="sm"
                        class="max-w-[18rem] whitespace-normal break-words"
                    />
                </div>
            </template>

            <template #actions>
                <div v-if="showActions">
                    <EntityActions
                        entity-type="breeds"
                        :entity="breed"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :context="actionsContext"
                        @action="handleAction"
                    />
                </div>
            </template>
        </EntityViewHeader>

        <div class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-3">
            <div>
                <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Orientations</h3>
                <p class="text-xs text-primary-400/90 mt-1">
                    Chaque voie (Air, Terre, Feu, Eau) porte un rôle à la création : dégâts, soin, placement…
                </p>
            </div>
            <BreedElementOrientationsDisplay :orientation-map="orientationMap" variant="cards" />
        </div>

        <BreedCapabilitiesDisplay
            v-if="linkedCapabilities.length"
            :capabilities="linkedCapabilities"
            density="full"
            :show-other-section="false"
            :characteristic-runtime="characteristicRuntime"
        />

        <div
            v-if="hasLinkedCreatureTraits"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Traits de classe"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Traits</h3>
            <CreatureTraitBadges :traits="linkedCreatureTraits" show-level size="sm" />
        </div>

        <div
            v-if="hasLinkedLanguages"
            class="rounded-box border border-base-300 bg-base-100/40 p-4 space-y-2"
            role="region"
            aria-label="Langues"
        >
            <h3 class="text-xs font-semibold uppercase tracking-wide text-primary-300">Langues</h3>
            <EntityLanguagesInline :languages="linkedLanguages" :show-label="false" />
        </div>

        <BreedMechanicsDisplay :html="mechanicsHtml" />

        <BreedVariantsDisplay
            v-if="hasSpellSlots"
            :breed="breed?._data ?? breed"
            density="full"
            :characteristic-runtime="characteristicRuntime"
        />
    </div>
    <EntityDofusdbRefreshPanel
        :open="refreshConfirm.open"
        :loading="refreshConfirm.loading"
        :applying="refreshConfirm.applying"
        :preview="refreshConfirm.preview"
        :error="refreshConfirm.error"
        :playable="refreshConfirm.playable"
        :entity-label="refreshConfirm.entityLabel"
        :entity-type="refreshConfirm.entityType"
        :show-dofusdb="refreshConfirm.showDofusdb"
        :show-ai="refreshConfirm.showAi"
        :show-json="refreshConfirm.showJson"
        :ai-brief="refreshConfirm.aiBrief"
        :ai-submitting="refreshConfirm.aiSubmitting"
        :ai-error="refreshConfirm.aiError"
        :ai-success="refreshConfirm.aiSuccess"
        :ai-estimate="refreshConfirm.aiEstimate"
        :ai-usage="refreshConfirm.aiUsage"
        :ai-action-label="refreshConfirm.aiActionLabel"
        :diff="refreshConfirm.diff"
        :diff-busy="refreshConfirm.diffBusy"
        @confirm="confirmPendingRefresh"
        @save-diff="confirmUpdateDiffSave"
        @restore-diff="confirmUpdateDiffRestore"
        @close="cancelPendingRefresh"
        @update:ai-brief="(v) => (refreshConfirm.aiBrief = v)"
        @convert="submitAiConvert"
        @inject="submitAiInject"
    />
</template>

<style scoped>
.entity-radius-box {
    border-radius: var(--radius-box, 0.1rem);
}
</style>
