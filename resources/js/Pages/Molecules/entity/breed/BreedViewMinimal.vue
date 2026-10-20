<script setup>
/**
 * BreedViewMinimal — Vue Minimal pour Breed (classe).
 *
 * @description
 * Compact : icône + nom / (dé de vie · traits · langues) → voies → description rapide.
 * Étendu : + description, mécanique, passif (texte), sorts (vue texte), capacités (vue texte).
 *
 * @props {Object} breed - Instance Breed ou payload
 * @props {Boolean} showActions - Menu d’actions (slot étendu)
 * @props {'compact'|'hover'|'extended'} displayMode
 */
import { computed } from "vue";
import EntityThumb from "@/Pages/Molecules/entity/shared/EntityThumb.vue";
import EntityActions from "@/Pages/Organismes/entity/EntityActions.vue";
import EntityMinimalCard from "@/Pages/Molecules/entity/shared/EntityMinimalCard.vue";
import EntityMinimalTitle from "@/Pages/Molecules/entity/shared/EntityMinimalTitle.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Image from "@/Pages/Atoms/data-display/Image.vue";
import { useEntityMinimalShell } from "@/Composables/entity/useEntityMinimalShell";
import BreedElementOrientationsDisplay from "@/Pages/Molecules/entity/breed/BreedElementOrientationsDisplay.vue";
import BreedVariantsDisplay from "@/Pages/Molecules/entity/breed/BreedVariantsDisplay.vue";
import CapabilityViewText from "@/Pages/Molecules/entity/capability/CapabilityViewText.vue";
import CreatureTraitBadges from "@/Pages/Molecules/entity/creature-trait/CreatureTraitBadges.vue";
import EntityLanguagesInline from "@/Pages/Molecules/entity/language/EntityLanguagesInline.vue";
import RichTextReadonlyView from "@/Pages/Molecules/data-display/RichTextReadonlyView.vue";
import { breedLogoFemaleUrl, breedLogoMaleUrl } from "@/Utils/entity/breedImages";
import { normalizeElementOrientationMap } from "@/Utils/entity/breedOrientations";
import { buildSpellSlotGroups } from "@/Utils/entity/breedSpellSlots";
import { partitionBreedCapabilities } from "@/Utils/entity/breedCapabilitiesPartition";

const props = defineProps({
    breed: {
        type: Object,
        required: true,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    displayMode: {
        type: String,
        default: "extended",
        validator: (v) => ["compact", "hover", "extended"].includes(v),
    },
    tableMeta: {
        type: Object,
        default: () => ({}),
    },
    characteristicRuntime: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["edit", "view", "delete", "action", "quick-view"]);

const entity = computed(() => props.breed);
const rawBreed = computed(() => entity.value?._data ?? entity.value);

const compactImageUrl = computed(() => breedLogoMaleUrl(entity.value) || null);
const expandedImageUrl = computed(() => breedLogoFemaleUrl(entity.value) || null);

const orientationMap = computed(() =>
    normalizeElementOrientationMap(rawBreed.value?.element_orientations),
);

const descriptionFast = computed(() => {
    const t = entity.value?.descriptionFast ?? rawBreed.value?.description_fast;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const descriptionFull = computed(() => {
    const d = entity.value?.description ?? rawBreed.value?.description;
    return d != null && String(d).trim() !== "" ? String(d).trim() : "";
});

const mechanicsHtml = computed(() => {
    const t = entity.value?.mechanics ?? rawBreed.value?.mechanics;
    return t != null && String(t).trim() !== "" ? String(t).trim() : "";
});

const lifeDiceLabel = computed(() => {
    const d = entity.value?.lifeDice ?? rawBreed.value?.life_dice;
    return d != null && String(d).trim() !== "" ? String(d).trim() : "";
});

const lifeDiceMeta = computed(
    () => entity.value?.lifeDiceMeta ?? rawBreed.value?.life_dice_meta ?? null,
);

const lifeDiceIcon = computed(() => {
    const meta = lifeDiceMeta.value;
    return meta?.icon || meta?.image || "";
});

const linkedCreatureTraits = computed(() => {
    const raw = rawBreed.value?.creatureTraits ?? entity.value?.creatureTraits;
    return Array.isArray(raw) ? raw : [];
});

const linkedLanguages = computed(() => {
    const raw = rawBreed.value?.languages ?? entity.value?.languages;
    return Array.isArray(raw) ? raw : [];
});

const hasMetaLine = computed(
    () =>
        Boolean(lifeDiceLabel.value) ||
        linkedCreatureTraits.value.length > 0 ||
        linkedLanguages.value.length > 0,
);

const partitionedCapabilities = computed(() =>
    partitionBreedCapabilities(rawBreed.value?.capabilities ?? entity.value?.capabilities),
);

const passiveCapabilities = computed(() => partitionedCapabilities.value.passiveCapabilities);
const otherCapabilities = computed(() => partitionedCapabilities.value.otherCapabilities);

const hasSpellSlots = computed(() => buildSpellSlotGroups(rawBreed.value).length > 0);

/**
 * Texte brut d’un passif (effet ou description).
 * @param {object} capability
 * @returns {string}
 */
function passivePlainText(capability) {
    const raw =
        capability?.effect ??
        capability?._data?.effect ??
        capability?.description ??
        capability?._data?.description ??
        "";
    if (!raw) return "";
    return String(raw)
        .replace(/<[^>]+>/g, " ")
        .replace(/&nbsp;/gi, " ")
        .replace(/\s+/g, " ")
        .trim();
}

function passiveName(capability) {
    return capability?.name || capability?._data?.name || "Passif";
}

const {
    minimalActionsContext,
    minimalActionWhitelist,
    openQuickView,
    handleMinimalAction,
} = useEntityMinimalShell({
    entityTypePlural: "breeds",
    showRoute: "entities.breeds.show",
    editRoute: "entities.breeds.edit",
    routeParam: "breed",
    emit,
    getEntity: () => entity.value,
});

const handleAction = async (actionKey) => {
    await handleMinimalAction(actionKey);
};
</script>

<template>
    <EntityMinimalCard
        :display-mode="displayMode"
        pinned-entity-type="breeds"
        :pinned-entity-id="entity?.id"
        @open-quick-view="openQuickView"
    >
        <template #compact>
            <div
                data-cy="entity-minimal-card-compact"
                class="relative flex flex-col gap-1.5 p-2 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="compactImageUrl || ''"
                        :label="entity?.name ?? 'Classe'"
                    />
                    <div class="flex min-w-0 flex-1 flex-col gap-0.5 pl-0.5">
                        <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                        <div
                            v-if="hasMetaLine"
                            class="flex min-w-0 flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[11px] leading-snug text-base-content/75"
                        >
                            <span
                                v-if="lifeDiceLabel"
                                class="inline-flex items-center gap-0.5 tabular-nums shrink-0"
                                :title="`Dé de vie : ${lifeDiceLabel}`"
                            >
                                <Image
                                    v-if="lifeDiceIcon"
                                    :source="lifeDiceIcon"
                                    alt=""
                                    fit="contain"
                                    class="h-3.5 w-3.5 shrink-0"
                                />
                                <Icon
                                    v-else
                                    source="fa-solid fa-dice-d20"
                                    size="xs"
                                    class="opacity-70"
                                />
                                <span>{{ lifeDiceLabel }}</span>
                            </span>
                            <CreatureTraitBadges
                                v-if="linkedCreatureTraits.length"
                                :traits="linkedCreatureTraits"
                                show-level
                                size="xs"
                            />
                            <EntityLanguagesInline
                                v-if="linkedLanguages.length"
                                :languages="linkedLanguages"
                                variant="minimal"
                                :show-label="false"
                            />
                        </div>
                    </div>
                </div>

                <BreedElementOrientationsDisplay
                    :orientation-map="orientationMap"
                    size="xs"
                    variant="icons"
                    class="w-full"
                />

                <p
                    v-if="descriptionFast"
                    class="text-[11px] leading-snug text-base-content/70 line-clamp-2"
                    :title="descriptionFast"
                >
                    {{ descriptionFast }}
                </p>
            </div>
        </template>

        <template #expanded>
            <div
                data-cy="entity-minimal-card-expanded"
                class="relative flex flex-col gap-2 p-2 transition-colors"
            >
                <div class="flex gap-2">
                    <EntityThumb
                        size="compact"
                        :src="expandedImageUrl || ''"
                        :label="entity?.name ?? 'Classe'"
                    />
                    <div class="flex min-w-0 flex-1 flex-col gap-0.5 pl-0.5">
                        <div class="flex w-full min-w-0 items-center gap-1.5">
                            <div class="min-w-0">
                                <EntityMinimalTitle :label="entity?.name ?? '—'" @open="openQuickView" />
                            </div>
                            <div
                                v-if="showActions"
                                data-entity-actions
                                class="ml-auto flex min-w-8 flex-1 justify-end"
                                @click.stop
                            >
                                <EntityActions
                                    entity-type="breeds"
                                    :entity="entity"
                                    format="dropdown"
                                    display="icon-only"
                                    size="xs"
                                    :whitelist="minimalActionWhitelist"
                                    :context="minimalActionsContext"
                                    @action="(k) => handleAction(k)"
                                />
                            </div>
                        </div>
                        <div
                            v-if="hasMetaLine"
                            class="flex min-w-0 flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[11px] leading-snug text-base-content/75"
                        >
                            <span
                                v-if="lifeDiceLabel"
                                class="inline-flex items-center gap-0.5 tabular-nums shrink-0"
                                :title="`Dé de vie : ${lifeDiceLabel}`"
                            >
                                <Image
                                    v-if="lifeDiceIcon"
                                    :source="lifeDiceIcon"
                                    alt=""
                                    fit="contain"
                                    class="h-3.5 w-3.5 shrink-0"
                                />
                                <Icon
                                    v-else
                                    source="fa-solid fa-dice-d20"
                                    size="xs"
                                    class="opacity-70"
                                />
                                <span>{{ lifeDiceLabel }}</span>
                            </span>
                            <CreatureTraitBadges
                                v-if="linkedCreatureTraits.length"
                                :traits="linkedCreatureTraits"
                                show-level
                                size="xs"
                            />
                            <EntityLanguagesInline
                                v-if="linkedLanguages.length"
                                :languages="linkedLanguages"
                                variant="minimal"
                                :show-label="false"
                            />
                        </div>
                    </div>
                </div>

                <BreedElementOrientationsDisplay
                    :orientation-map="orientationMap"
                    size="xs"
                    variant="icons"
                    class="w-full"
                />

                <p
                    v-if="descriptionFast"
                    class="text-[11px] leading-snug text-primary-200/90"
                    :title="descriptionFast"
                >
                    {{ descriptionFast }}
                </p>

                <p
                    v-if="descriptionFull"
                    class="text-xs leading-snug text-base-content/80 line-clamp-5"
                    :title="descriptionFull"
                >
                    {{ descriptionFull }}
                </p>

                <div
                    v-if="mechanicsHtml"
                    class="rounded-md border border-accent-500/30 bg-accent-500/8 px-2 py-1.5 space-y-1"
                >
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-accent-300">
                        Mécanique
                    </div>
                    <RichTextReadonlyView
                        :html="mechanicsHtml"
                        :enable-rich-references="true"
                        class="text-[11px] leading-snug text-base-content/90 [&_.prose]:text-[11px] [&_.prose]:leading-snug"
                    />
                </div>

                <div
                    v-if="passiveCapabilities.length"
                    class="space-y-1 border-l-2 border-warning/50 pl-2"
                    role="region"
                    aria-label="Passifs de classe"
                >
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-warning/90">
                        Passif
                    </div>
                    <p
                        v-for="cap in passiveCapabilities"
                        :key="`pas-${cap.id}`"
                        class="text-[11px] leading-snug text-base-content/85"
                    >
                        <span class="font-medium text-warning-content/90">{{ passiveName(cap) }}.</span>
                        <span v-if="passivePlainText(cap)" class="ml-1">{{ passivePlainText(cap) }}</span>
                    </p>
                </div>

                <div v-if="hasSpellSlots" class="space-y-1" role="region" aria-label="Sorts">
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">
                        Sorts
                    </div>
                    <BreedVariantsDisplay
                        :breed="rawBreed"
                        density="text"
                        :show-temple-note="false"
                        :characteristic-runtime="characteristicRuntime"
                    />
                </div>

                <div
                    v-if="otherCapabilities.length"
                    class="space-y-1"
                    role="region"
                    aria-label="Capacités"
                >
                    <div class="text-[10px] font-semibold uppercase tracking-wide text-base-content/50">
                        Capacités
                    </div>
                    <ul class="flex list-none flex-wrap gap-x-3 gap-y-1 p-0 m-0">
                        <li
                            v-for="cap in otherCapabilities"
                            :key="`cap-${cap.id}`"
                            class="min-w-0 max-w-full"
                        >
                            <CapabilityViewText :capability="cap" />
                        </li>
                    </ul>
                </div>
            </div>
        </template>
    </EntityMinimalCard>
</template>
