<script setup>
/**
 * Corps de la fiche d’édition d’un sort (header compact, formulaire, degrés).
 *
 * @description
 * Partagé entre la page {@link Pages/entity/spell/Edit} et l’édition embarquée ({@link EntitySurfaceHost}).
 * Enregistrement unique : degrés (bulk) puis formulaire sort.
 */
import { computed, ref } from "vue";
import { Spell } from "@/Models/Entity/Spell";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useEntityEditEmbed } from "@/Composables/entity/useEntityEditEmbed";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import SpellDegreesEditor from "@/Pages/Organismes/entity/SpellDegreesEditor.vue";
import EntityEditHeader from "@/Pages/Molecules/entity/shared/EntityEditHeader.vue";
import EntityEditContainer from "@/Pages/Molecules/entity/shared/EntityEditContainer.vue";
import SpellHoldersPanel from "@/Pages/Molecules/entity/spell/SpellHoldersPanel.vue";
import SpellViewText from "@/Pages/Molecules/entity/spell/SpellViewText.vue";
import SpellEffectsUnifiedSection from "@/Pages/Organismes/entity/SpellEffectsUnifiedSection.vue";
import Collapse from "@/Pages/Atoms/data-display/Collapse.vue";
import {
    buildSpellFormFieldsConfig,
    SPELL_FORM_FIELD_SECTIONS_EDIT,
    mergeSpellTypesFieldIntoSpellFormConfig,
} from "@/Entities/spell/spell-form-config";

const props = defineProps({
    spell: { type: Object, required: true },
    availableSpellTypes: { type: Array, default: () => [] },
    availableEffects: { type: Array, default: () => [] },
    effectEntityType: { type: String, default: "spell" },
    effectFormOptions: { type: Object, default: () => ({}) },
    spellDegrees: { type: Object, default: () => ({ degrees: [], default_degree_id: null }) },
    spellEffectGroups: { type: Array, default: () => [] },
    spellHolders: { type: Object, default: () => ({}) },
    /** Quand true : annulation sans redirection vers la fiche lecture. */
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH réussi : `edit` = rester sur l’éditeur (page fiche) ; `index` = liste (modal).
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved", "effects-changed"]);

const {
    embed: entityEditEmbed,
    isEmbeddedInModal,
    resolvedRedirectAfterUpdate,
} = useEntityEditEmbed(props);
const embeddedInModal = isEmbeddedInModal;
const redirectAfterUpdate = resolvedRedirectAfterUpdate;

const { canDeleteAny, isAdmin } = usePermissions();
const canDeleteSpell = computed(() => canDeleteAny("spells") || isAdmin.value);

const fieldsConfig = computed(() =>
    mergeSpellTypesFieldIntoSpellFormConfig(
        buildSpellFormFieldsConfig({ includeReadonlyMeta: true }),
        props.availableSpellTypes || [],
    ),
);

const fieldSections = computed(() =>
    SPELL_FORM_FIELD_SECTIONS_EDIT.map((s) => ({ ...s })),
);

const spellModel = computed(() =>
    props.spell instanceof Spell ? props.spell : new Spell(props.spell),
);

/**
 * Le layout décale déjà `<main>` sous la sidebar ; pas de second décalage sur le pied.
 * @see CapabilityEditFormContent — même raison (`sticky` dans la colonne scrollable).
 */
const fixedFooterInsetClass = "left-0 right-0";

const spellDegreesEditorRef = ref(null);
const spellEffectsSectionRef = ref(null);
const entityEditFormRef = ref(null);

/**
 * Enregistre les degrés natifs puis le groupe d’effets legacy, avant le PATCH entité.
 * Les degrés d’abord : même si le flush groupe a un effet de bord sur les props,
 * le PUT `/degrees` a déjà persisté le brouillon.
 */
async function beforeSpellSubmitAsync() {
    const editor = spellDegreesEditorRef.value;
    const fn = editor?.flushAll || editor?.flushSave;
    if (typeof fn === "function") {
        const ok = await fn();
        if (ok === false) {
            const detail =
                editor?.errorMessage?.value ||
                editor?.errorMessage ||
                "Enregistrement des degrés impossible.";
            throw new Error(typeof detail === "string" ? detail : "Enregistrement des degrés impossible.");
        }
    }

    const flushGroups = spellEffectsSectionRef.value?.flushEffectGroupSave;
    if (typeof flushGroups === "function") {
        const groupsOk = await flushGroups();
        if (groupsOk === false) {
            throw new Error("Enregistrement des effets du sort impossible.");
        }
    }
    return true;
}

function onHeaderDeleted() {
    if (isEmbeddedInModal.value) {
        emit("cancel");
        entityEditEmbed?.onCancel?.();
    }
}

function onHeaderCancel() {
    emit("cancel");
    entityEditEmbed?.onCancel?.();
}

function onFormSaved() {
    emit("saved");
    entityEditEmbed?.onSaved?.();
}
</script>

<template>
    <div class="spell-edit-form-content space-y-4">
        <EntityEditHeader
            entity-type="spells"
            :entity="spellModel"
            :form-ref="entityEditFormRef"
            list-route-name="entities.spells.index"
            delete-route-name="entities.spells.delete"
            route-param-key="spell"
            :can-delete="canDeleteSpell"
            :embedded-in-modal="embeddedInModal"
            :show-formula-help="true"
            save-data-cy="spell-edit-save"
            header-data-cy="spell-edit-header"
            delete-confirm-message="Supprimer ce sort ? Il sera placé en corbeille (récupération possible côté admin)."
            @cancel="onHeaderCancel"
            @deleted="onHeaderDeleted"
        >
            <template #left>
                <SpellHoldersPanel :spell-holders="spellHolders" />
            </template>
            <template #title>
                <SpellViewText :spell="spellModel" />
            </template>
        </EntityEditHeader>

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="spellModel"
            entity-type="spell"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :hidden-field-keys="['dofus_version']"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            characteristics-group="spell"
            layout-profile="sheet"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :embedded-in-modal="embeddedInModal"
            :redirect-after-update="redirectAfterUpdate || undefined"
            :before-submit-async="beforeSpellSubmitAsync"
            @cancel="onHeaderCancel"
            @submit="onFormSaved"
        >
            <template #after-sections>
                <Collapse
                    arrow
                    :default-open="true"
                    bg-off="bg-base-100"
                    bg-on="bg-base-100"
                    class="mt-3 border border-base-300 lg:col-span-2"
                    data-cy="spell-effects-section"
                >
                    <template #title>Effets du sort (définitions)</template>
                    <template #content>
                        <SpellEffectsUnifiedSection
                            ref="spellEffectsSectionRef"
                            hide-effect-group-submit-button
                            :available-effects="availableEffects"
                            :effect-form-options="effectFormOptions"
                            :spell-effect-groups="spellEffectGroups"
                            :entity-type="effectEntityType"
                            :entity-id="spellModel.id"
                            :suggested-effect-name="spellModel.name || ''"
                            :embedded-in-modal="embeddedInModal"
                            @effects-changed="emit('effects-changed')"
                        />
                    </template>
                </Collapse>
                <EntityEditContainer
                    title="Degrés & effets"
                    subtitle="Onglets par niveau, propriétés de lancement et sous-effets."
                    icon="fa-solid fa-layer-group"
                    :span="2"
                    root-class="mt-3 lg:col-span-2"
                >
                    <SpellDegreesEditor
                        ref="spellDegreesEditorRef"
                        :spell-id="Number(spellModel.id)"
                        :spell="spellModel"
                        :spell-degrees="spellDegrees"
                        :effect-form-options="effectFormOptions"
                        :embedded-in-modal="embeddedInModal"
                    />
                </EntityEditContainer>
            </template>
        </EntityEditForm>
    </div>
</template>
