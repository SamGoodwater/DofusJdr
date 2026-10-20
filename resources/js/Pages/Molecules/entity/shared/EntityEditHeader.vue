<script setup>
/**
 * Header compact d’édition d’entité (retour, titre, état, options, annuler, supprimer, enregistrer).
 *
 * @example
 * <EntityEditHeader
 *   entity-type="items"
 *   :entity="item"
 *   :form-ref="entityEditFormRef"
 *   list-route-name="entities.items.index"
 *   delete-route-name="entities.items.delete"
 *   route-param-key="item"
 *   :can-delete="canDelete"
 * />
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { ACTION } from '@/Utils/atomic-design/actionLabels';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EntityListBackButton from '@/Pages/Atoms/action/EntityListBackButton.vue';
import FormulaHelpHint from '@/Pages/Molecules/entity/FormulaHelpHint.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import { useEntitySurfaceStore } from '@/Composables/store/useEntitySurfaceStore';

const props = defineProps({
    /** Segment pluriel (ex. `spells`, `items`) pour EntityActions. */
    entityType: { type: String, required: true },
    entity: { type: Object, required: true },
    /** Instance exposée d’EntityEditForm (submit, form, stateField…). */
    formRef: { type: Object, default: null },
    title: { type: String, default: '' },
    subtitle: { type: String, default: '' },
    listRouteName: { type: String, required: true },
    deleteRouteName: { type: String, default: '' },
    /** Clé du paramètre de route (`spell`, `item`, `creatureTrait`…). */
    routeParamKey: { type: String, required: true },
    showRouteName: { type: String, default: '' },
    canDelete: { type: Boolean, default: false },
    actionsWhitelist: {
        type: Array,
        default: null,
    },
    embeddedInModal: { type: Boolean, default: false },
    showFormulaHelp: { type: Boolean, default: false },
    deleteConfirmMessage: { type: String, default: '' },
    /** Attribut data-cy du bouton Enregistrer. */
    saveDataCy: { type: String, default: 'entity-edit-save' },
    /** Attribut data-cy du header. */
    headerDataCy: { type: String, default: 'entity-edit-header' },
});

const emit = defineEmits(['cancel', 'deleted']);

const { isEmbeddedInModal, embed: entityEditEmbed } = useEntityEditEmbed(props);
const embeddedInModal = isEmbeddedInModal;

const resolvedActionsWhitelist = computed(() => {
    if (Array.isArray(props.actionsWhitelist) && props.actionsWhitelist.length) {
        return props.actionsWhitelist;
    }
    if (isEmbeddedInModal.value) {
        return ['edit', 'view', 'view-dofusdb', 'refresh', 'copy-link'];
    }
    return ['view', 'view-dofusdb', 'refresh', 'copy-link'];
});

const formApi = computed(() => props.formRef ?? null);

const headerStateField = computed(() => formApi.value?.stateField ?? null);
const headerForm = computed(() => formApi.value?.form ?? null);
const headerProcessing = computed(() => {
    const processing = formApi.value?.processing;
    if (processing && typeof processing === 'object' && 'value' in processing) {
        return Boolean(processing.value);
    }
    return Boolean(processing);
});
const headerSaveLabel = computed(
    () => formApi.value?.primarySaveLabel ?? ACTION.save.label,
);

const resolvedShowRoute = computed(
    () => props.showRouteName || `entities.${props.entityType}.show`,
);

const entityId = computed(() => {
    const id = props.entity?.id;
    if (id == null || id === '') return null;
    return id;
});

const displayTitle = computed(
    () => props.title || props.entity?.name || 'Sans nom',
);

const canShowDelete = computed(
    () => Boolean(props.canDelete && props.deleteRouteName && entityId.value),
);

/**
 * URL relative Ziggy (évite cross-origin localhost ≠ 127.0.0.1).
 *
 * @param {string} name
 * @param {Record<string, unknown>} params
 * @returns {string}
 */
function relativeRoute(name, params) {
    return route(name, params, false);
}

function goToShow() {
    const id = entityId.value;
    if (!id) return;
    router.visit(relativeRoute(resolvedShowRoute.value, { [props.routeParamKey]: id }));
}

async function handleOptionsAction(actionKey) {
    // Modal d’édition : « Agrandir » (action edit) → page Modifier.
    if (actionKey === 'edit' && isEmbeddedInModal.value) {
        if (typeof entityEditEmbed?.onExpand === 'function') {
            entityEditEmbed.onExpand();
        } else {
            useEntitySurfaceStore().expandTopEditToPage();
        }
        return;
    }
    if (actionKey === 'view') {
        goToShow();
        return;
    }
    if (actionKey === 'copy-link') {
        const id = entityId.value;
        if (!id || typeof navigator?.clipboard?.writeText !== 'function') return;
        try {
            const href = relativeRoute(resolvedShowRoute.value, {
                [props.routeParamKey]: id,
            });
            await navigator.clipboard.writeText(
                new URL(href, window.location.origin).toString(),
            );
        } catch {
            // Presse-papiers indisponible (permissions / contexte non sécurisé).
        }
        return;
    }
    if (actionKey === 'refresh' || actionKey === 'view-dofusdb') {
        const dispatch = formApi.value?.dispatchEntityAction;
        if (typeof dispatch === 'function') {
            await dispatch(actionKey, props.entity);
        }
    }
}

async function confirmDelete() {
    if (!canShowDelete.value) return;
    const id = entityId.value;
    const message =
        props.deleteConfirmMessage
        || 'Supprimer cette fiche ? Elle sera placée en corbeille (récupération possible côté admin).';
    if (!window.confirm(message)) return;

    // Modal : API JSON pour rester sur la liste (évite une navigation Inertia + historique cassé).
    if (isEmbeddedInModal.value) {
        try {
            await axios.delete(
                `/api/entities/${encodeURIComponent(props.entityType)}/${encodeURIComponent(String(id))}`,
                { headers: { Accept: 'application/json' } },
            );
            emit('deleted');
        } catch (error) {
            const detail =
                error?.response?.data?.message
                || Object.values(error?.response?.data?.errors || {})?.flat()?.[0]
                || 'Impossible de placer l’entité en corbeille.';
            window.alert(typeof detail === 'string' ? detail : 'Impossible de placer l’entité en corbeille.');
        }
        return;
    }

    // Page édition : replace évite « Précédent » → fiche soft-deleted → 404 « Page introuvable ».
    router.delete(relativeRoute(props.deleteRouteName, { [props.routeParamKey]: id }), {
        replace: true,
        onSuccess: () => emit('deleted'),
    });
}

function onHeaderCancel() {
    const cancel = formApi.value?.cancel;
    if (typeof cancel === 'function') {
        cancel();
        return;
    }
    emit('cancel');
}

function onHeaderReset() {
    formApi.value?.resetForm?.();
}

function onHeaderSave() {
    if (headerProcessing.value) return;
    const submit = formApi.value?.submit;
    if (typeof submit !== 'function') return;
    submit();
}
</script>

<template>
    <div
        class="sticky top-0 z-20 px-3 py-2 bg-glass-3xl backdrop-blur-md border-glass-b-md sm:px-4"
        style="--bg-color: var(--color-base-100)"
        :data-cy="headerDataCy"
    >
        <div
            class="grid grid-cols-1 gap-3 lg:grid-cols-[1fr_auto_1fr] lg:items-center"
        >
            <div class="flex min-w-0 flex-wrap items-center gap-2 justify-self-start">
                <EntityListBackButton
                    v-if="!embeddedInModal"
                    :route-name="listRouteName"
                />
                <slot name="left" />
            </div>

            <div class="min-w-0 justify-self-center text-center max-w-xl">
                <slot name="title">
                    <button
                        type="button"
                        class="block w-full truncate text-md font-bold text-base-content hover:underline sm:text-lg"
                        @click="goToShow"
                    >
                        {{ displayTitle }}
                    </button>
                </slot>
                <p class="text-xs text-base-content/70 mt-0.5">
                    <slot name="subtitle">
                        Édition · #{{ entityId }}
                        <span v-if="subtitle"> · {{ subtitle }}</span>
                    </slot>
                </p>
            </div>

            <div
                class="flex min-w-0 flex-wrap items-center gap-2 justify-self-start lg:justify-self-end"
            >
                <slot name="right-extra" />

                <div
                    v-if="showFormulaHelp"
                    class="rounded-(--radius-field) border border-base-300 bg-base-100/60 px-2 py-1.5"
                >
                    <FormulaHelpHint placement="bottom-end" />
                </div>

                <div
                    v-if="headerStateField?.config && headerForm"
                    class="state-field w-[9.5rem] shrink-0"
                >
                    <SelectField
                        v-model="headerForm[headerStateField.key]"
                        :label="
                            formApi?.getFieldLabel?.(
                                headerStateField.key,
                                headerStateField.config,
                            ) || headerStateField.config.label || 'État'
                        "
                        :options="headerStateField.config.options || []"
                        :option-badge="headerStateField.config.optionBadge || null"
                        :required="headerStateField.config.required"
                        :validation="
                            formApi?.getFieldValidation?.(headerStateField.key) || null
                        "
                        :searchable="false"
                        @update:model-value="
                            () => formApi?.markDirty?.(headerStateField.key)
                        "
                    />
                </div>

                <div class="flex items-center gap-1">
                    <span class="text-xs text-base-content/70 sr-only sm:not-sr-only">Options</span>
                    <EntityActions
                        :entity-type="entityType"
                        :entity="entity"
                        format="dropdown"
                        display="icon-text"
                        size="sm"
                        color="neutral"
                        :whitelist="resolvedActionsWhitelist"
                        :context="
                            embeddedInModal
                                ? { inModal: true, modalMode: 'edit' }
                                : { inPage: true, pageMode: 'edit' }
                        "
                        @action="handleOptionsAction"
                    />
                </div>

                <div class="flex flex-col gap-1">
                    <Btn
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1"
                        @click="onHeaderCancel"
                    >
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        Annuler
                    </Btn>
                    <Btn
                        color="neutral"
                        variant="outline"
                        size="xs"
                        type="button"
                        class="gap-1"
                        @click="onHeaderReset"
                    >
                        <i :class="ACTION.discard.icon" aria-hidden="true"></i>
                        Annuler les modifications
                    </Btn>
                </div>

                <Btn
                    v-if="canShowDelete"
                    color="error"
                    variant="outline"
                    size="xs"
                    type="button"
                    class="gap-1.5"
                    data-cy="entity-edit-delete"
                    @click="confirmDelete"
                >
                    <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                    Supprimer
                </Btn>

                <Btn
                    color="primary"
                    size="sm"
                    type="button"
                    class="gap-1.5"
                    :disabled="headerProcessing || !formApi"
                    :data-cy="saveDataCy"
                    @click="onHeaderSave"
                >
                    <i :class="ACTION.save.icon" aria-hidden="true"></i>
                    {{ headerProcessing ? ACTION.save.processing : headerSaveLabel }}
                </Btn>
            </div>
        </div>
    </div>
</template>
