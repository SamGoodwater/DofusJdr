<script setup>
/**
 * Corps de la fiche d’édition d’une capacité (header compact + formulaire unique).
 *
 * @description
 * Partagé entre {@link Pages/entity/capability/Edit} et l’édition embarquée ({@link EntitySurfaceHost}).
 * Grille `capability` inchangée ; header partagé via EntityEditHeader.
 */
import { computed, ref } from "vue";
import { Capability } from "@/Models/Entity/Capability";
import { usePermissions } from "@/Composables/permissions/usePermissions";
import { useEntityEditEmbed } from "@/Composables/entity/useEntityEditEmbed";
import EntityEditForm from "@/Pages/Organismes/entity/EntityEditForm.vue";
import ConditionsEditor from "@/Pages/Organismes/entity/ConditionsEditor.vue";
import EntityEditHeader from "@/Pages/Molecules/entity/shared/EntityEditHeader.vue";
import {
    buildCapabilityFormFieldsConfig,
    CAPABILITY_FORM_FIELD_SECTIONS_EDIT,
} from "@/Entities/capability/capability-form-config";
import { invalidateKrefEntityPreviewCache } from "@/Composables/richText/krefEntityPreviewCache";

const props = defineProps({
    capability: { type: Object, required: true },
    availableConditions: { type: Array, default: () => [] },
    embeddedInModal: { type: Boolean, default: false },
    /**
     * Redirection après PATCH : `stay` = modal / même page ; `edit` = page édition.
     * @type {"stay"|"index"|"show"|"edit"|null}
     */
    redirectAfterUpdate: { type: String, default: "edit" },
});

const emit = defineEmits(["cancel", "saved"]);

const {
    embed: entityEditEmbed,
    isEmbeddedInModal,
    resolvedRedirectAfterUpdate,
} = useEntityEditEmbed(props);
const embeddedInModal = isEmbeddedInModal;
const redirectAfterUpdate = resolvedRedirectAfterUpdate;

const { canDeleteAny, isAdmin } = usePermissions();
const canDeleteCapability = computed(() => canDeleteAny("capabilities") || isAdmin.value);

const fieldsConfig = computed(() => buildCapabilityFormFieldsConfig({ includeReadonlyMeta: true }));

const fieldSections = CAPABILITY_FORM_FIELD_SECTIONS_EDIT;

const capabilityModel = computed(() =>
    props.capability instanceof Capability ? props.capability : new Capability(props.capability),
);

/**
 * Le layout {@link Main} décale déjà la colonne principale (`lg:left-64` sur `<main>`).
 * Ne pas redécaler le pied : avec `sticky` dans le flux, un second `lg:left-64` poussait la barre hors cadre.
 */
const fixedFooterInsetClass = "left-0 right-0";

const entityEditFormRef = ref(null);

function onDeleted() {
    const id = capabilityModel.value?.id;
    if (id) {
        invalidateKrefEntityPreviewCache("capabilities", id);
    }
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
    <div class="capability-edit-form-content space-y-4">
        <EntityEditHeader
            entity-type="capabilities"
            :entity="capabilityModel"
            :form-ref="entityEditFormRef"
            :title="capabilityModel.name || 'Capacité sans nom'"
            list-route-name="entities.capabilities.index"
            delete-route-name="entities.capabilities.delete"
            route-param-key="capability"
            :can-delete="canDeleteCapability"
            :embedded-in-modal="embeddedInModal"
            delete-confirm-message="Supprimer cette capacité ? Elle sera placée en corbeille (récupération possible côté admin)."
            @cancel="onHeaderCancel"
            @deleted="onDeleted"
        />

        <EntityEditForm
            ref="entityEditFormRef"
            :entity="capabilityModel"
            entity-type="capability"
            :fields-config="fieldsConfig"
            :is-updating="true"
            :field-sections="fieldSections"
            :show-state-toolbar="true"
            :show-access-levels-in-footer="false"
            :hide-top-toolbar="true"
            :hide-action-dock="true"
            characteristics-group="capability"
            layout-profile="capability"
            :fixed-footer-actions="false"
            :fixed-footer-inset-class="fixedFooterInsetClass"
            :floating-save-button="false"
            :embedded-in-modal="embeddedInModal"
            :redirect-after-update="redirectAfterUpdate || undefined"
            :shortcuts-active="!embeddedInModal"
            @cancel="onHeaderCancel"
            @submit="onFormSaved"
        />

        <!-- Même éditeur d’états en page et en modal (source unique). -->
        <ConditionsEditor
            v-if="capabilityModel.id"
            :relations="capabilityModel.conditions || []"
            :available-items="availableConditions"
            :entity-id="capabilityModel.id"
            route-name="entities.capabilities.updateConditions"
            route-param-name="capability"
            title="États appliqués"
            help="États que cette capacité peut appliquer. La description de la capacité précise leur interaction avec les créatures."
        />
    </div>
</template>
