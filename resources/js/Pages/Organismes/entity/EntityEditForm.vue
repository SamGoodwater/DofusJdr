<script setup>
/**
 * EntityEditForm Organism
 * 
 * @description
 * Composant réutilisable pour la création/édition des contenus.
 * 
 * @props {Object} entity - Données de l'entité à éditer
 * @props {String} entityType - Type d'entité (item, spell, monster, etc.)
 * @props {Object} fieldsConfig - Configuration des champs à afficher
 * @props {Boolean} isUpdating - Mode édition (true) ou création (false)
 */
import { ref, computed, watch, onUnmounted } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import { useEntityEditEmbed } from '@/Composables/entity/useEntityEditEmbed';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import SelectField from '@/Pages/Molecules/data-input/SelectField.vue';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import EditActionDock from '@/Pages/Molecules/action/EditActionDock.vue';
import ConfirmModal from '@/Pages/Molecules/action/ConfirmModal.vue';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import FormulaHelpHint from '@/Pages/Molecules/entity/FormulaHelpHint.vue';
import EntityEditFormFieldBody from '@/Pages/Molecules/entity/EntityEditFormFieldBody.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import { FORMULA_PLACEHOLDER } from '@/Utils/entity/formula-help';
import { registerSaveShortcut } from '@/Composables/utils/saveShortcutRegistry';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import { getRarityOptions } from '@/Utils/Entity/SharedConstants';
import { LAYOUT_STICKY_ABOVE_MOBILE_DOCK_CLASS } from '@/Composables/layout/viewport-breakpoints';
import { ACTION } from '@/Utils/atomic-design/actionLabels';
import {
    invalidateKrefEntityPreviewCache,
    toKrefPreviewApiEntityType,
} from '@/Composables/richText/krefEntityPreviewCache';

const props = defineProps({
    entity: {
        type: Object,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    fieldsConfig: {
        type: Object,
        default: () => ({})
    },
    isUpdating: {
        type: Boolean,
        default: true
    },
    differentFields: {
        type: Array,
        default: () => []
    },
    /**
     * Override optionnel pour les routes (utile pour les "types" ou routes non standards).
     *
     * @example
     * routeNameBase="entities.resource-types"
     * routeParamKey="resourceType"
     */
    routeNameBase: {
        type: String,
        default: null
    },
    routeParamKey: {
        type: String,
        default: null
    },
    /**
     * Clés masquées (non affichées). Par défaut : auto_update, dofus_version.
     * Ex. pour les sorts : `['dofus_version']` pour afficher l’auto-update dans une section dédiée.
     */
    hiddenFieldKeys: {
        type: Array,
        default: null,
    },
    /**
     * Sections du formulaire (cartes avec titre). Ordre des `fieldKeys` = ordre d’affichage.
     * @type {Array<{ id: string, title: string, subtitle?: string, fieldKeys: string[] }>|null}
     */
    fieldSections: {
        type: Array,
        default: null,
    },
    /** Affiche l’état (workflow) dans la barre du haut. */
    showStateToolbar: {
        type: Boolean,
        default: true,
    },
    /** Affiche lecture / écriture dans le pied de formulaire. */
    showAccessLevelsInFooter: {
        type: Boolean,
        default: true,
    },
    /**
     * Groupe Characteristics (ex. `spell`) pour enrichir les booléens (icône / couleur BDD).
     */
    characteristicsGroup: {
        type: String,
        default: null,
    },
    /**
     * Quand false : pas de Ctrl+S ni d’autres raccourcis globaux (Esc, Ctrl+Z) — utile si le
     * formulaire reste monté alors que le conteneur (modal) est fermé.
     */
    shortcutsActive: {
        type: Boolean,
        default: true,
    },
    /** Éditeur dans une modal : annulation sans redirection vers la fiche lecture. */
    embeddedInModal: {
        type: Boolean,
        default: false,
    },
    /**
     * Si fourni, seuls ces champs sont affichés (création courte).
     */
    restrictToFieldKeys: {
        type: Array,
        default: null,
    },
    /**
     * Envoyé au PATCH (ex. sort) pour la redirection Laravel : `index`, `show` ou `edit`.
     */
    redirectAfterUpdate: {
        type: String,
        default: null,
    },
    /** Création via CreateEntityModal : redirige vers la fiche édition après store (Q9). */
    redirectAfterCreate: {
        type: Boolean,
        default: false,
    },
    /**
     * Mise en page dense multi-colonnes (`spell` / `dense` / `capability`).
     * `sheet` = grille 2 colonnes générique ; `spell` est un alias de `sheet`.
     * `dense` = grille multi-colonnes (legacy) ; `capability` = grille 6 cols dédiée.
     */
    layoutProfile: {
        type: String,
        default: null,
        validator: (v) =>
            v == null
            || v === ''
            || v === 'sheet'
            || v === 'spell'
            || v === 'capability'
            || v === 'dense',
    },
    /**
     * Pied d’actions collé en bas du viewport (sort / capacité : long formulaire).
     * Si false : pied en flux normal sous les champs (évite les chevauchements et le « double footer »).
     */
    fixedFooterActions: {
        type: Boolean,
        default: false,
    },
    /**
     * Classes additionnelles sur le conteneur du pied fixe (ex. `lg:left-64` avec barre latérale).
     */
    fixedFooterInsetClass: {
        type: String,
        default: '',
    },
    /**
     * Bouton d'enregistrement flottant, en bas à droite du viewport.
     * Utilisé pour les longs formulaires où la barre de pied peut sortir du flux visuel.
     */
    floatingSaveButton: {
        type: Boolean,
        default: false,
    },
    /** Affiche Retour et Annuler les modifications dans le pied (désactiver si la navigation se fait via l’en-tête). */
    footerSecondaryActions: {
        type: Boolean,
        default: true,
    },
    /** Lecture / écriture : ligne compacte, labels courts, sans texte d’aide sous le champ. */
    compactAccessLevels: {
        type: Boolean,
        default: false,
    },
    /**
     * Exécuté avant le POST/PATCH Inertia du formulaire. Si la Promise est rejetée ou résout `false`, la soumission est annulée.
     * @example Sauvegarde des effets du sort puis mise à jour de la fiche.
     * @type {(() => Promise<boolean|void>) | null}
     */
    beforeSubmitAsync: {
        type: Function,
        default: null,
    },
    /**
     * Masque la barre du haut (aide formules + état + actions lecture).
     * Utile quand le parent (ex. header sort) reprend ces contrôles.
     */
    hideTopToolbar: {
        type: Boolean,
        default: false,
    },
    /**
     * Masque le dock / pied d’actions (Enregistrer, Annuler…).
     * Utile quand le parent expose ces boutons dans son propre header.
     */
    hideActionDock: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['submit', 'cancel']);

const {
    embed: entityEditEmbed,
    isEmbeddedInModal,
    resolvedRedirectAfterUpdate,
} = useEntityEditEmbed(props);
/** Alias template + script (prop ou provide EntitySurfaceHost). */
const embeddedInModal = isEmbeddedInModal;

/** Grille dense / sheet (fiches sort, sheet générique, capacité, dense legacy). */
const isSpellLayout = computed(
    () =>
        props.layoutProfile === 'sheet' ||
        props.layoutProfile === 'spell' ||
        props.layoutProfile === 'capability' ||
        props.layoutProfile === 'dense',
);

/** Grille 2 colonnes (sheet + alias spell). */
const isSheetLayout = computed(
    () => props.layoutProfile === 'sheet' || props.layoutProfile === 'spell',
);

/** Fiche capacité : 1ʳᵉ ligne 3 panneaux égaux, 2ᵉ ligne effets 2/3 + métadonnées 1/3 (grille 6 cols ≥ md). */
const isCapabilityLayout = computed(() => props.layoutProfile === 'capability');

/**
 * Segment `entities.{segment}` (ex. capabilities, pas « capabilitys »).
 *
 * @returns {string}
 */
const entitiesPluralSegment = computed(() => {
    const et = props.entityType;
    if (et === 'panoply') return 'panoplies';
    if (et === 'capability' || et === 'capabilities') return 'capabilities';
    if (et === 'creature-trait' || et === 'creature-traits') return 'creature-traits';
    return `${et}s`;
});

const { dispatchEntityAction, deleteConfirm, confirmPendingDelete, cancelPendingDelete, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher(entitiesPluralSegment, {
        onRefresh: () => router.reload(),
    });
const showReadAction = computed(() => props.isUpdating && Boolean(props.entity?.id));

/** Contexte actions : page d’édition ou modal d’édition embarquée. */
const editActionsContext = computed(() => {
    if (isEmbeddedInModal.value) {
        return { inModal: true, modalMode: 'edit' };
    }
    return { inPage: true, pageMode: 'edit' };
});

/**
 * Clé du paramètre de route Laravel (`capability` pour `/capabilities/{capability}`).
 *
 * @returns {string}
 */
const resolvedRouteParamKey = computed(() => {
    if (props.routeParamKey) return props.routeParamKey;
    if (props.entityType === 'capabilities') return 'capability';
    if (props.entityType === 'creature-trait' || props.entityType === 'creature-traits') return 'creatureTrait';
    return props.entityType;
});

/** Conteneur des sections (grille ou empilé). */
const sectionsContainerClass = computed(() => {
    if (!props.fieldSections?.length) {
        return '';
    }
    if (isCapabilityLayout.value) {
        return 'capability-edit-sections grid w-full min-w-0 grid-cols-1 gap-4 md:grid-cols-6 md:gap-x-4 md:gap-y-5 md:items-stretch';
    }
    if (isSheetLayout.value) {
        return 'grid grid-cols-1 gap-4 lg:grid-cols-2 lg:items-start';
    }
    return isSpellLayout.value
        ? 'grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4'
        : 'space-y-5';
});

/**
 * Portée de colonnes pour une section (profil sheet / sort / capacité).
 *
 * **Capacité** (`layout-profile="capability"`) : grille 6 colonnes à partir de `md` —
 * ligne 1 : généralités, coût, déroulé (2+2+2) ; ligne 2 : effets (4) + admin (2).
 *
 * **Sheet** (`sheet` / alias `spell`) : 2 colonnes ; `span: 2` force la pleine largeur.
 *
 * @param {{ id?: string, span?: number|string }} sec
 * @returns {string}
 */
function spellSectionColClass(sec) {
    if (!isSpellLayout.value || !sec?.id) {
        return '';
    }
    if (isCapabilityLayout.value) {
        if (['general', 'cost_effect', 'gameplay'].includes(sec.id)) {
            return 'min-w-0 md:col-span-2';
        }
        if (sec.id === 'effect_richtext') {
            return 'min-w-0 md:col-span-4';
        }
        if (sec.id === 'admin') {
            return 'min-w-0 md:col-span-2';
        }
        return '';
    }
    if (isSheetLayout.value) {
        if (Number(sec.span) === 2) {
            return 'min-w-0 lg:col-span-2 self-start';
        }
        return 'min-w-0 lg:col-span-1 self-start';
    }
    if (sec.id === 'general') {
        return 'min-w-0 lg:col-span-1';
    }
    if (sec.id === 'spell_properties' || sec.id === 'global_properties') {
        return 'min-w-0 lg:col-span-1';
    }
    if (sec.id === 'fallback_properties' || sec.id === 'base_cast_properties') {
        return 'min-w-0 lg:col-span-2 2xl:col-span-1';
    }
    if (sec.id === 'admin' || sec.id === 'metadata') {
        return 'min-w-0 lg:col-span-2 2xl:col-span-1';
    }
    return '';
}

const sectionCardClass = computed(() =>
    isSpellLayout.value
        ? 'rounded-xl border border-base-300/70 bg-base-100/25 p-3 shadow-sm md:p-3.5'
        : 'rounded-xl border border-base-300/70 bg-base-100/30 p-3 shadow-sm md:p-4',
);

const sectionTitleClass = computed(() =>
    isSpellLayout.value
        ? 'text-sm font-semibold tracking-tight text-base-content'
        : 'text-base font-semibold tracking-tight text-base-content',
);

const sectionSubtitleClass = computed(() =>
    isSpellLayout.value
        ? 'mt-0.5 text-[11px] leading-snug text-base-content/70 md:text-xs'
        : 'mt-0.5 text-xs leading-snug text-base-content/70 md:text-sm',
);

const rootFormClass = computed(() => {
    const parts = ['entity-edit-form'];
    if (isSpellLayout.value) {
        parts.push('entity-edit-form--spell-layout');
    }
    if (props.fixedFooterActions) {
        parts.push('entity-edit-form--fixed-footer');
    }
    return parts.join(' ');
});

/**
 * Dock commun bas-droite (desktop) / flux (mobile) pour Retour / Annuler les modifications / Enregistrer.
 * Désactivé en modal embarquée : barre d’actions classique.
 */
const useEditActionDock = computed(
    () =>
        !props.hideActionDock &&
        (props.fixedFooterActions || props.floatingSaveButton) &&
        !isEmbeddedInModal.value,
);

/** Espace sous le formulaire pour ne pas masquer le dernier champ derrière le pied fixe. */
const formScrollPaddingClass = computed(() => {
    if (!props.fixedFooterActions) {
        return '';
    }
    // Modale : pied en sticky dans la zone scroll → peu de marge basse.
    if (isEmbeddedInModal.value && !useEditActionDock.value) {
        return 'pb-3 sm:pb-4';
    }
    if (useEditActionDock.value) {
        return 'pb-[calc(6.5rem+env(safe-area-inset-bottom,0px))] md:pb-24 lg:pb-32';
    }
    return 'pb-[calc(6.5rem+env(safe-area-inset-bottom,0px))] sm:pb-28 md:pb-32';
});

const footerWrapperClass = computed(() => {
    if (props.fixedFooterActions) {
        const horizontal = props.fixedFooterInsetClass?.trim()
            ? props.fixedFooterInsetClass.split(/\s+/).filter(Boolean)
            : ['left-0', 'right-0'];
        /**
         * `sticky` dans le `<main>` scrollable (`overflow-y-auto` dans Main.vue`) :
         * le pied reste visible en bas de la zone de contenu. Évite les `fixed` orphelins
         * et les doubles décalages `lg:left-64` (sidebar déjà appliquée sur `<main>`).
         * Sur mobile : décalé au-dessus du dock bas (`LAYOUT_STICKY_ABOVE_MOBILE_DOCK_CLASS`).
         */
        return ['sticky', 'z-50', 'w-full', LAYOUT_STICKY_ABOVE_MOBILE_DOCK_CLASS, ...horizontal];
    }
    return ['relative', 'w-full', 'mt-6'];
});

const footerBarClass = computed(() => {
    const base = ['footer-tools-row', 'w-full'];
    if (props.fixedFooterActions) {
        base.push(
            'bg-base-100/80',
            'backdrop-blur-md',
            'border-glass-t-md',
        );
        if (isEmbeddedInModal.value) {
            base.push('px-3', 'py-2', 'sm:px-4');
        } else {
            base.push('px-4', 'py-2.5', 'sm:px-6');
        }
    } else {
        base.push(
            'footer-tools-row--static',
            'rounded-xl',
            'border',
            'border-base-300/60',
            'bg-base-100/35',
        );
        if (isEmbeddedInModal.value) {
            base.push('px-3', 'py-2', 'sm:px-3');
        } else {
            base.push('px-3', 'py-3', 'sm:px-4');
        }
    }
    return base.join(' ');
});

const footerActionsRowClass = computed(() => {
    const parts = [
        'footer-actions',
        'flex',
        'min-w-0',
        'flex-wrap',
        'items-center',
        'gap-3',
        props.footerSecondaryActions ? 'w-full' : 'footer-actions--save-only w-full md:w-auto md:shrink-0 md:ml-auto',
        props.footerSecondaryActions ? 'justify-between' : 'justify-end',
    ];
    return parts;
});

/** Action principale : enregistrer (édition) ou créer (création). */
const primaryAction = computed(() => (props.isUpdating ? ACTION.save : ACTION.create));

/** Libellé du bouton principal du dock (hors état processing). */
const primarySaveLabel = computed(() => primaryAction.value.label);

/** Sortie : ferme la modale embarquée, sinon retour à la fiche. */
const cancelAction = computed(() => (isEmbeddedInModal.value ? ACTION.close : ACTION.back));

/** Actions secondaires du dock (Retour / Annuler les modifications). */
const editDockSecondaryActions = computed(() => {
    if (!props.footerSecondaryActions) {
        return [];
    }
    return [
        {
            key: 'cancel',
            label: cancelAction.value.label,
            iconClass: `${cancelAction.value.icon} mr-2`,
            variant: 'outline',
            color: '',
        },
        {
            key: 'reset',
            label: ACTION.discard.label,
            iconClass: `${ACTION.discard.icon} mr-2`,
            variant: 'outline',
            color: '',
            tooltip:
                'Réinitialise le formulaire : revient aux valeurs chargées au moment de l’ouverture (ou dernière synchro). En multi‑édition, remet les champs ‘valeurs différentes’ en mode ‘ne pas modifier’.',
        },
    ];
});

/**
 * @param {string} key
 */
function onEditDockAction(key) {
    if (key === 'cancel') {
        cancel();
        return;
    }
    if (key === 'reset') {
        resetForm();
    }
}

const notificationStore = useNotificationStore();

/**
 * Multi-edit quand on n'a pas d'ID et qu'on est en mode update.
 * Dans ce mode, on ne doit envoyer que les champs explicitement modifiés.
 */
const isMultiEdit = computed(() => {
    const entityId = props.entity?.id ?? null;
    return !entityId && Boolean(props.isUpdating);
});

// Configuration par défaut des champs selon le type d'entité
const defaultFieldsConfig = computed(() => {
    const baseFields = {
        name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
        description: { type: 'textarea', label: 'Description', required: false, showInCompact: false },
    };

    // Configuration spécifique par type d'entité
    const entitySpecificFields = {
        item: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            rarity: { type: 'select', label: 'Rareté', required: false, showInCompact: true, options: getRarityOptions().map(({ value, label }) => ({ value, label })) },
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        spell: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            ap_cost: { type: 'number', label: 'Coût PA', required: false, showInCompact: true },
            range: { type: 'number', label: 'Portée', required: false, showInCompact: false },
            image: { type: 'file', label: 'Image', required: false, showInCompact: false }
        },
        monster: {
            level: { type: 'number', label: 'Niveau', required: false, showInCompact: true },
            life: { type: 'number', label: 'Vie', required: false, showInCompact: true },
            size: { type: 'number', label: 'Taille', required: false, showInCompact: false },
            is_boss: { type: 'checkbox', label: 'Boss', required: false, showInCompact: true },
        },
        npc: {
            name: { type: 'text', label: 'Nom', required: true, showInCompact: true },
            location: { type: 'text', label: 'Lieu', required: false, showInCompact: true },
            level: { type: 'text', label: 'Niveau', required: false, showInCompact: true },
            hostility: {
                type: 'select',
                label: 'Hostilité',
                required: false,
                showInCompact: true,
                options: [
                    { value: 0, label: 'Amical' },
                    { value: 1, label: 'Curieux' },
                    { value: 2, label: 'Neutre' },
                    { value: 3, label: 'Hostile' },
                    { value: 4, label: 'Agressif' },
                ],
            },
        }
    };

    return {
        ...baseFields,
        ...(entitySpecificFields[props.entityType] || {})
    };
});

// Fusion de la configuration par défaut avec la configuration personnalisée
const fieldsConfig = computed(() => {
    return { ...defaultFieldsConfig.value, ...props.fieldsConfig };
});

const resolvedHiddenFieldKeys = computed(() => {
    if (props.hiddenFieldKeys !== null && props.hiddenFieldKeys !== undefined) {
        return new Set(props.hiddenFieldKeys);
    }
    return new Set(['auto_update', 'dofus_version']);
});
const topRowFieldKeys = ['name'];
const secondaryFieldKeys = new Set([
    'id', 'slug', 'read_level', 'write_level',
    'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at',
    'dofusdb_id', 'dofusdb_type_id',
    'source', 'source_url', 'source_ref',
]);

const isSecondaryField = (fieldKey, fieldConfig) => {
    if (secondaryFieldKeys.has(fieldKey)) return true;
    const group = String(fieldConfig?.group || '').toLowerCase();
    if (!group) return false;
    return group.includes('meta') || group.includes('statut') || group.includes('permission');
};

const visibleFields = computed(() => {
    const allowKeys = Array.isArray(props.restrictToFieldKeys) && props.restrictToFieldKeys.length > 0
        ? new Set(props.restrictToFieldKeys)
        : null;
    const entries = Object.entries(fieldsConfig.value).filter(([fieldKey]) => {
        if (resolvedHiddenFieldKeys.value.has(fieldKey)) return false;
        if (allowKeys && !allowKeys.has(fieldKey)) return false;
        return true;
    });

    if (!allowKeys && props.fieldSections?.length) {
        const entryByKey = new Map(entries.map(([key, config]) => [key, { key, config }]));
        const ordered = [];
        const consumed = new Set();
        for (const sec of props.fieldSections) {
            for (const fieldKey of sec.fieldKeys || []) {
                const entry = entryByKey.get(fieldKey);
                if (entry && !consumed.has(fieldKey)) {
                    ordered.push(entry);
                    consumed.add(fieldKey);
                }
            }
        }
        for (const [fieldKey, fieldConfig] of entries) {
            if (!consumed.has(fieldKey)) {
                ordered.push({ key: fieldKey, config: fieldConfig });
            }
        }

        return ordered;
    }

    const entryByKey = new Map(entries.map(([key, config]) => [key, { key, config }]));
    const consumed = new Set();
    const ordered = [];

    for (const key of topRowFieldKeys) {
        const entry = entryByKey.get(key);
        if (!entry) continue;
        ordered.push(entry);
        consumed.add(key);
    }

    let descriptionEntry = null;
    const important = [];
    const secondary = [];

    for (const [fieldKey, fieldConfig] of entries) {
        if (consumed.has(fieldKey)) continue;
        if (fieldKey === 'description') {
            descriptionEntry = { key: fieldKey, config: fieldConfig };
            continue;
        }

        if (isSecondaryField(fieldKey, fieldConfig)) {
            secondary.push({ key: fieldKey, config: fieldConfig });
            continue;
        }

        important.push({ key: fieldKey, config: fieldConfig });
    }

    ordered.push(...important);
    if (descriptionEntry) ordered.push(descriptionEntry);
    ordered.push(...secondary);

    return ordered;
});

const stateField = computed(() => {
    if (!props.showStateToolbar) {
        return null;
    }
    const entry = visibleFields.value.find((field) => field?.key === 'state');
    return entry?.config ? entry : null;
});

const accessLevelFields = computed(() => {
    if (!props.showAccessLevelsInFooter) {
        return [];
    }
    return visibleFields.value.filter((field) => ['read_level', 'write_level'].includes(field?.key));
});

const mainFields = computed(() => {
    const exclude = [];
    if (props.showStateToolbar) {
        exclude.push('state');
    }
    if (props.showAccessLevelsInFooter) {
        exclude.push('read_level', 'write_level');
    }
    return visibleFields.value.filter((field) => !exclude.includes(field?.key));
});

const resolveFormFieldKeys = () => {
    const allKeys = Object.keys(fieldsConfig.value);
    if (Array.isArray(props.restrictToFieldKeys) && props.restrictToFieldKeys.length > 0) {
        const allow = new Set(props.restrictToFieldKeys);
        return allKeys.filter((key) => allow.has(key));
    }
    return allKeys;
};

// Initialisation du formulaire avec les données de l'entité
const initializeForm = () => {
    // Si c'est une création (pas d'ID), utiliser les valeurs par défaut
    if (!props.isUpdating || !props.entity?.id) {
        const formData = {};
        resolveFormFieldKeys().forEach(key => {
            // En multi-edit: si le champ fait partie des champs différents, valeur neutre
            // => affiche "Valeurs différentes" et évite de pré-remplir une valeur arbitraire.
            if (isMultiEdit.value && props.differentFields.includes(key)) {
                formData[key] = getDefaultValue(fieldsConfig.value[key].type);
                return;
            }
            // Utiliser la valeur de l'entité si fournie, sinon valeur par défaut
            const sourceKey = fieldsConfig.value[key]?.sourceKey || key;
            const fromSource = resolveFormFieldValue(props.entity, sourceKey);
            const fromEntity = fromSource !== undefined
                ? fromSource
                : resolveFormFieldValue(props.entity, key);
            formData[key] = fromEntity !== undefined
                ? fromEntity
                : getDefaultValue(fieldsConfig.value[key].type);
        });
        return formData;
    }
    
    // Si l'entité est une instance de modèle avec toFormData(), l'utiliser
    if (props.entity && typeof props.entity.toFormData === 'function') {
        const modelFormData = props.entity.toFormData();
        const formData = {};
        resolveFormFieldKeys().forEach(key => {
            const sourceKey = fieldsConfig.value[key]?.sourceKey;
            if (sourceKey) {
                const fromSource = resolveFormFieldValue(props.entity, sourceKey);
                if (fromSource !== undefined) {
                    formData[key] = fromSource;
                    return;
                }
            }
            const fromModel = resolveFormFieldValue(modelFormData, key);
            if (fromModel !== undefined) {
                formData[key] = fromModel;
                return;
            }
            const fromEntity = resolveFormFieldValue(props.entity, key);
            formData[key] = fromEntity !== undefined
                ? fromEntity
                : getDefaultValue(fieldsConfig.value[key].type);
        });
        return formData;
    }
    
    // Sinon, utiliser l'accès direct aux propriétés (compatibilité avec objets bruts)
    // Pour l'édition multiple, l'entité peut être un objet simple avec les valeurs communes
    const formData = {};
    resolveFormFieldKeys().forEach(key => {
        const sourceKey = fieldsConfig.value[key]?.sourceKey || key;
        const fromSource = resolveFormFieldValue(props.entity, sourceKey);
        const fromEntity = fromSource !== undefined
            ? fromSource
            : resolveFormFieldValue(props.entity, key);
        formData[key] = fromEntity !== undefined
            ? fromEntity
            : getDefaultValue(fieldsConfig.value[key].type);
    });
    return formData;
};

/**
 * Valeur d’un champ formulaire : clé snake_case, sinon getter camelCase du modèle.
 *
 * @param {object|null|undefined} source
 * @param {string} key
 * @returns {*}
 */
const resolveFormFieldValue = (source, key) => {
    if (!source || typeof source !== 'object') {
        return undefined;
    }
    if (source[key] !== undefined) {
        return source[key];
    }
    if (typeof key === 'string' && key.includes('_')) {
        const camel = key.replace(/_([a-z])/g, (_, letter) => letter.toUpperCase());
        if (camel !== key && source[camel] !== undefined) {
            return source[camel];
        }
    }
    return undefined;
};

const getDefaultValue = (type) => {
    switch (type) {
        case 'number': return null;
        case 'checkbox':
        case 'physiqueWakfu':
            return false;
        case 'select': return null;
        case 'elementPrimaries': return null;
        case 'spellTypesMulti': return [];
        case 'file': return null;
        case 'display': return '';
        default: return '';
    }
};

const form = useForm(initializeForm());
/**
 * Snapshot des valeurs "initiales" du formulaire (au moment de l'ouverture / dernière synchro).
 * Utilisé par le bouton « Annuler les modifications ».
 */
const initialSnapshot = ref(initializeForm());

/**
 * Visibilité conditionnelle (ex. résolution : champs selon `resolution_mode`).
 *
 * @param {string} fieldKey
 * @param {object} fieldConfig
 */
function isFieldVisible(fieldKey, fieldConfig) {
    const rule = fieldConfig?.visibleWhen;
    if (!rule) return true;
    const v = form[rule.field];
    if (Object.prototype.hasOwnProperty.call(rule, 'value')) {
        if (rule.value === true) {
            return v === true || v === 1 || v === '1';
        }
        if (rule.value === false) {
            return v === false || v === 0 || v === '0' || v === '' || v == null;
        }
        return v === rule.value;
    }
    if (Array.isArray(rule.in)) return rule.in.includes(v);
    return true;
}

const mainFieldSections = computed(() => {
    if (!props.fieldSections?.length) return null;
    const mf = mainFields.value;
    const byKey = new Map(mf.map((f) => [f.key, f]));
    return props.fieldSections.map((sec) => ({
        id: sec.id,
        title: sec.title,
        subtitle: sec.subtitle,
        span: sec.span ?? 1,
        collapsedByDefault: Boolean(sec.collapsedByDefault),
        collapsedActionLabel: sec.collapsedActionLabel || 'Afficher',
        expandedActionLabel: sec.expandedActionLabel || 'Replier',
        fields: (sec.fieldKeys || [])
            .map((k) => byKey.get(k))
            .filter(Boolean)
            .filter((f) => isFieldVisible(f.key, f.config)),
    })).filter((sec) => sec.fields.length > 0);
});

const visibleMainFields = computed(() =>
    mainFields.value.filter((f) => isFieldVisible(f.key, f.config)),
);

/**
 * Track des bools modifiés en UI (utile pour gérer un état indeterminate en mode multi-edit).
 * @type {Record<string, boolean>}
 */
const checkboxDirty = ref({});

const onCheckboxUpdate = (key, v) => {
    checkboxDirty.value = { ...(checkboxDirty.value || {}), [key]: true };
    form[key] = Boolean(v);
};

/**
 * Track des champs non-bool modifiés en multi-edit.
 * @type {Record<string, boolean>}
 */
const fieldDirty = ref({});

/**
 * Marque un champ comme modifié (multi-edit).
 * @param {string} key
 */
const markDirty = (key) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: true };
};

/**
 * Annule la modification d'un champ (multi-edit) => ne pas modifier.
 * @param {string} key
 * @param {string} type
 */
const resetFieldMultiEdit = (key, type) => {
    if (!isMultiEdit.value) return;
    if (!props.differentFields.includes(key)) return;
    fieldDirty.value = { ...(fieldDirty.value || {}), [key]: false };
    form[key] = getDefaultValue(type);
};

/**
 * Reset d'un champ bool en mode multi-edit : revient à l'état indeterminate (non modifié).
 *
 * @param {string} key
 * @returns {void}
 */
const resetBoolMultiEdit = (key) => {
    if (!key) return;
    checkboxDirty.value = { ...(checkboxDirty.value || {}), [key]: false };
    // Important: en mode "valeurs différentes", `form[key]` n'est pas une valeur fiable.
    // L'état indeterminate (= ne pas modifier) est piloté par `checkboxDirty`.
    form[key] = false;
};

/**
 * Reset global du formulaire :
 * - revient aux valeurs chargées au moment de l'ouverture (ou dernière synchro entity->form)
 * - remet les champs multi-edit en mode "ne pas modifier" (dirty=false)
 */
const resetForm = () => {
    const snap = initialSnapshot.value || {};

    for (const key of Object.keys(fieldsConfig.value || {})) {
        if (Object.prototype.hasOwnProperty.call(snap, key)) {
            form[key] = snap[key];
        } else {
            form[key] = getDefaultValue(fieldsConfig.value?.[key]?.type);
        }
    }

    checkboxDirty.value = {};
    fieldDirty.value = {};
    form.clearErrors?.();
};

// Mise à jour du formulaire quand l'entité change
watch(() => props.entity, () => {
    const formData = initializeForm();
    initialSnapshot.value = formData;
    Object.keys(formData).forEach(key => {
        form[key] = formData[key];
    });
}, { deep: true });

/**
 * Raccourcis clavier
 */
const handleKeydown = (event) => {
    // Esc : Annuler / Fermer
    if (event.key === 'Escape') {
        event.preventDefault();
        emit('cancel');
        return;
    }

    // Ctrl+Z / Cmd+Z : Réinitialiser (si possible)
    if ((event.ctrlKey || event.metaKey) && event.key === 'z' && !event.shiftKey) {
        // Ne pas intercepter si on est dans un input/textarea (laisser le navigateur gérer)
        const target = event.target;
        if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
            return;
        }
        event.preventDefault();
        resetForm();
        return;
    }
};

let unregisterSaveShortcut = () => {};

const attachShortcutListeners = () => {
    unregisterSaveShortcut();
    unregisterSaveShortcut = () => {};
    if (!props.shortcutsActive) {
        return;
    }
    unregisterSaveShortcut = registerSaveShortcut(() => {
        if (!form.processing) {
            submit();
        }
    });
    window.addEventListener('keydown', handleKeydown);
};

const detachShortcutListeners = () => {
    unregisterSaveShortcut();
    unregisterSaveShortcut = () => {};
    window.removeEventListener('keydown', handleKeydown);
};

watch(
    () => props.shortcutsActive,
    () => {
        detachShortcutListeners();
        attachShortcutListeners();
    },
    { immediate: true },
);

onUnmounted(() => {
    detachShortcutListeners();
});

// Validation pour chaque champ
const getFieldValidation = (fieldKey) => {
    if (!form.errors[fieldKey]) return null;
    return {
        state: 'error',
        message: form.errors[fieldKey],
        showNotification: false
    };
};

const nonFormulaFieldKeys = new Set([
    'id', 'name', 'title', 'slug', 'description', 'effect', 'image', 'icon', 'state', 'decision',
    'read_level', 'write_level', 'created_at', 'updated_at', 'deleted_at',
]);

const isFormulaFriendlyField = (fieldKey, fieldConfig) => {
    if (!fieldConfig || fieldConfig.type !== 'text') return false;
    if (nonFormulaFieldKeys.has(fieldKey)) return false;
    return true;
};

const getFieldPlaceholder = (fieldKey, fieldConfig) => {
    if (props.differentFields.includes(fieldKey)) return 'Valeurs différentes';
    if (fieldConfig?.placeholder) return fieldConfig.placeholder;
    if (isFormulaFriendlyField(fieldKey, fieldConfig)) return FORMULA_PLACEHOLDER;
    return undefined;
};

const isImageField = (fieldKey, fieldConfig) => {
    const key = String(fieldKey || '').toLowerCase();
    const label = String(fieldConfig?.label || '').toLowerCase();
    return key.includes('image') || key.includes('thumbnail') || label.includes('image');
};

const getFieldRenderType = (fieldKey, fieldConfig) => {
    if (fieldConfig?.type === 'display') return 'display';
    if (fieldConfig?.type === 'richtext') return 'richtext';
    if (fieldConfig?.type === 'file') return 'file';
    if (fieldConfig?.type === 'text' && isImageField(fieldKey, fieldConfig)) return 'file';
    if (fieldConfig?.type === 'elementPrimaries') return 'elementPrimaries';
    if (fieldConfig?.type === 'spellTypesMulti') return 'spellTypesMulti';
    if (fieldConfig?.type === 'physiqueWakfu') return 'physiqueWakfu';
    return fieldConfig?.type || 'text';
};

/**
 * @param {unknown} val
 */
const formatDisplayValue = (val) => {
    if (val === null || val === undefined || val === '') return '—';
    return String(val);
};

const getFileCurrentPath = (fieldKey) => {
    const value = form[fieldKey];
    return typeof value === 'string' ? value : null;
};

const getFileAccept = (fieldKey, fieldConfig) => {
    if (fieldConfig?.accept) return fieldConfig.accept;
    return isImageField(fieldKey, fieldConfig) ? 'image/*' : undefined;
};

const humanizeFieldKey = (fieldKey) => {
    const raw = String(fieldKey || '')
        .replace(/[_-]+/g, ' ')
        .trim();
    if (!raw) return '';
    return raw.charAt(0).toUpperCase() + raw.slice(1);
};

const getFieldLabel = (fieldKey, fieldConfig) => {
    if (fieldConfig?.label && String(fieldConfig.label).trim() !== '' && fieldConfig.label !== fieldKey) {
        return fieldConfig.label;
    }

    const fallbackLabels = {
        state: 'État',
        read_level: 'Lecture (min.)',
        write_level: 'Écriture (min.)',
        ap_cost: 'Coût en PA',
        pa: 'PA',
        pm: 'PM',
        po: 'PO',
        po_min: 'PO min',
        po_max: 'PO max',
        aoe: 'Zone d’effet',
    };

    return fallbackLabels[fieldKey] || humanizeFieldKey(fieldKey);
};

const getFieldHelper = (fieldKey, fieldConfig) => {
    if (fieldConfig?.help && String(fieldConfig.help).trim() !== '') return fieldConfig.help;

    if (fieldKey === 'read_level') {
        return "Niveau de privilège minimum requis pour consulter ce contenu.";
    }
    if (fieldKey === 'write_level') {
        return "Niveau de privilège minimum requis pour modifier ce contenu.";
    }

    return undefined;
};

/**
 * Libellé des niveaux d’accès (court en mode compact pour le pied de formulaire).
 *
 * @param {string} fieldKey
 * @param {object} fieldConfig
 * @returns {string}
 */
const getAccessLevelLabel = (fieldKey, fieldConfig) => {
    if (!props.compactAccessLevels) {
        return getFieldLabel(fieldKey, fieldConfig);
    }
    if (fieldKey === 'read_level') return 'Lecture';
    if (fieldKey === 'write_level') return 'Écriture';
    return getFieldLabel(fieldKey, fieldConfig);
};

/**
 * Aide sous le sélecteur : masquée en mode compact (tooltips possibles sur les labels si besoin).
 *
 * @param {string} fieldKey
 * @param {object} fieldConfig
 * @returns {string|undefined}
 */
const getAccessLevelHelper = (fieldKey, fieldConfig) => {
    if (props.compactAccessLevels) return undefined;
    return getFieldHelper(fieldKey, fieldConfig);
};

const getFieldWrapperClass = (fieldKey) => ([
    'form-field',
    props.differentFields.includes(fieldKey) ? 'opacity-60' : '',
    fieldKey === 'name' ? 'form-field--wide' : '',
    fieldKey === 'description' ? 'form-field--full' : '',
    fieldKey === 'effect' ? 'form-field--full' : '',
    fieldKey === 'area' ? 'form-field--full' : '',
    ['category', 'state'].includes(fieldKey) ? 'form-field--meta-pair' : '',
    fieldKey === 'element' ? 'form-field--full' : '',
].filter(Boolean));

// Soumission du formulaire
const submit = async () => {
    // Si l'entité n'a pas d'ID (édition multiple), émettre directement les données
    const entityId = props.entity?.id ?? null;
    if (!entityId && props.isUpdating) {
        // Mode édition multiple : n'émettre que les champs explicitement modifiés.
        // (tri-state => indeterminate = ne pas modifier)
        const data = form.data();
        for (const key of (props.differentFields || [])) {
            const type = fieldsConfig.value?.[key]?.type;
            if (type === 'checkbox' || type === 'physiqueWakfu') {
                if (!checkboxDirty.value?.[key]) delete data[key];
            } else {
                if (!fieldDirty.value?.[key]) delete data[key];
            }
        }
        emit('submit', data);
        return;
    }

    if (typeof props.beforeSubmitAsync === 'function') {
        try {
            const pre = await props.beforeSubmitAsync();
            if (pre === false) {
                return;
            }
        } catch (e) {
            notificationStore.error(
                e?.message || 'Une erreur est survenue avant l’enregistrement du formulaire.',
                { duration: 5000, placement: 'top-right' },
            );
            console.error(e);
            return;
        }
    }

    // Construction du nom de route selon le type d'entité
    const base = props.routeNameBase || `entities.${entitiesPluralSegment.value}`;
    const routeName = props.isUpdating ? `${base}.update` : `${base}.store`;

    const routeParams = props.isUpdating ? { [resolvedRouteParamKey.value]: entityId } : {};

    const method = props.isUpdating ? 'patch' : 'post';

    if (resolvedRedirectAfterUpdate.value && method === 'patch') {
        form.redirect_after_update = resolvedRedirectAfterUpdate.value;
    }

    if (props.redirectAfterCreate && method === 'post') {
        form.redirect_after_create = 'edit';
    }

    // URL relative : évite le cross-origin si Ziggy.url (APP_URL) ≠ host navigateur (localhost vs 127.0.0.1).
    form[method](route(routeName, routeParams, false), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success(
                props.isUpdating ? 'Modifications enregistrées avec succès' : 'Création effectuée avec succès',
                { duration: 3000, placement: 'top-right' }
            );
            if (props.isUpdating && entityId != null) {
                invalidateKrefEntityPreviewCache(
                    toKrefPreviewApiEntityType(props.entityType),
                    entityId,
                );
            }
            emit('submit', form.data());
            if (isEmbeddedInModal.value) {
                entityEditEmbed?.onSaved?.();
            }
        },
        onError: (errors) => {
            const first = Object.values(errors || {})
                .flat()
                .find((msg) => typeof msg === 'string' && msg.trim() !== '');
            notificationStore.error(
                first || 'Erreur lors de la sauvegarde (réseau, session ou validation).',
                { duration: 8000, placement: 'top-right' }
            );
            console.error('Erreurs de validation:', errors);
        },
        onFinish: () => {
            if (resolvedRedirectAfterUpdate.value && Object.prototype.hasOwnProperty.call(form, 'redirect_after_update')) {
                delete form.redirect_after_update;
            }
        },
    });
};

// Annulation
const cancel = () => {
    emit('cancel');
    if (isEmbeddedInModal.value) {
        entityEditEmbed?.onCancel?.();
        return;
    }
    // Seulement rediriger si c'est une page d'édition hors modal
    if (props.isUpdating) {
        const entityId = props.entity?.id ?? null;
        if (entityId) {
            router.visit(route(`entities.${entitiesPluralSegment.value}.show`, { [resolvedRouteParamKey.value]: entityId }));
        }
    }
};

async function handleEditPageAction(actionKey) {
    await dispatchEntityAction(actionKey, props.entity);
}

const exposedIsDirty = computed(() => Boolean(form.isDirty));
const exposedProcessing = computed(() => Boolean(form.processing));

defineExpose({
    dispatchEntityAction: (actionKey, entity = props.entity) =>
        dispatchEntityAction(actionKey, entity),
    submit,
    resetForm,
    cancel,
    form,
    stateField,
    markDirty,
    getFieldLabel,
    getFieldValidation,
    isDirty: exposedIsDirty,
    processing: exposedProcessing,
    primarySaveLabel,
    primaryAction,
    cancelAction,
});

</script>

<template>
    <Container :class="rootFormClass">
        <div v-if="!hideTopToolbar" class="flex flex-col gap-3 mb-4">
            <div class="top-tools-row">
                <div class="top-tools-row__formula rounded-(--radius-field) border border-base-300 bg-base-100/60 px-3 py-2">
                    <FormulaHelpHint placement="bottom-start" />
                </div>

                <div
                    v-if="stateField?.config"
                    class="state-field w-full md:w-[180px] md:min-w-[180px]"
                >
                    <SelectField
                        v-model="form[stateField.key]"
                        @update:model-value="() => markDirty(stateField.key)"
                        :label="getFieldLabel(stateField.key, stateField.config)"
                        :options="stateField.config.options || []"
                        :option-badge="stateField.config.optionBadge || null"
                        :required="stateField.config.required"
                        :validation="getFieldValidation(stateField.key)"
                        :searchable="false"
                    />
                </div>

                <div v-if="showReadAction" class="top-tools-row__actions">
                    <EntityActions
                        :entity-type="entitiesPluralSegment"
                        :entity="entity"
                        format="buttons"
                        display="icon-only"
                        size="sm"
                        color="primary"
                        :whitelist="['view', 'view-dofusdb']"
                        :context="editActionsContext"
                        @action="handleEditPageAction"
                    />
                </div>
            </div>
        </div>

        <!-- Formulaire -->
        <form @submit.prevent="submit" :class="['space-y-5', formScrollPaddingClass]">
            <div v-if="mainFieldSections?.length" :class="sectionsContainerClass">
                <section
                    v-for="sec in mainFieldSections"
                    :key="sec.id"
                    :class="[sectionCardClass, spellSectionColClass(sec)]"
                >
                    <details :open="!sec.collapsedByDefault" class="group">
                        <summary
                            class="mb-2.5 cursor-pointer list-none border-b border-base-300/50 pb-2 md:mb-3 [&::-webkit-details-marker]:hidden"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h2 :class="sectionTitleClass">
                                        {{ sec.title }}
                                    </h2>
                                    <p v-if="sec.subtitle" :class="sectionSubtitleClass">
                                        {{ sec.subtitle }}
                                    </p>
                                </div>
                                <span class="btn btn-ghost btn-xs h-auto min-h-0 group-open:hidden shrink-0 px-2 py-1 text-xs">
                                    {{ sec.collapsedActionLabel }}
                                </span>
                                <span class="text-xs text-base-content/50 hidden group-open:inline shrink-0 pt-1">
                                    {{ sec.expandedActionLabel }}
                                </span>
                            </div>
                        </summary>
                        <div class="form-fields">
                            <template v-for="field in sec.fields" :key="field.key">
                                <div v-if="field?.config" :class="getFieldWrapperClass(field.key)">
                                    <EntityEditFormFieldBody
                                        :field="field"
                                        :form="form"
                                        :is-multi-edit="isMultiEdit"
                                        :different-fields="props.differentFields"
                                        :field-dirty="fieldDirty"
                                        :checkbox-dirty="checkboxDirty"
                                        :get-field-label="getFieldLabel"
                                        :get-field-helper="getFieldHelper"
                                        :get-field-validation="getFieldValidation"
                                        :get-field-placeholder="getFieldPlaceholder"
                                        :get-field-render-type="getFieldRenderType"
                                        :get-file-current-path="getFileCurrentPath"
                                        :get-file-accept="getFileAccept"
                                        :format-display-value="formatDisplayValue"
                                        :mark-dirty="markDirty"
                                        :reset-field-multi-edit="resetFieldMultiEdit"
                                        :reset-bool-multi-edit="resetBoolMultiEdit"
                                        :on-checkbox-update="onCheckboxUpdate"
                                        :characteristics-group="characteristicsGroup"
                                    />
                                </div>
                            </template>
                        </div>
                    </details>
                </section>
            </div>
            <div v-else class="form-fields">
                <template v-for="field in visibleMainFields" :key="field.key">
                    <div v-if="field?.config" :class="getFieldWrapperClass(field.key)">
                        <EntityEditFormFieldBody
                            :field="field"
                            :form="form"
                            :is-multi-edit="isMultiEdit"
                            :different-fields="props.differentFields"
                            :field-dirty="fieldDirty"
                            :checkbox-dirty="checkboxDirty"
                            :get-field-label="getFieldLabel"
                            :get-field-helper="getFieldHelper"
                            :get-field-validation="getFieldValidation"
                            :get-field-placeholder="getFieldPlaceholder"
                            :get-field-render-type="getFieldRenderType"
                            :get-file-current-path="getFileCurrentPath"
                            :get-file-accept="getFileAccept"
                            :format-display-value="formatDisplayValue"
                            :mark-dirty="markDirty"
                            :reset-field-multi-edit="resetFieldMultiEdit"
                            :reset-bool-multi-edit="resetBoolMultiEdit"
                            :on-checkbox-update="onCheckboxUpdate"
                            :characteristics-group="characteristicsGroup"
                        />
                    </div>
                </template>
            </div>

            <!-- Contenu métier entre les sections et le pied (ex. degrés de sort). -->
            <slot name="after-sections" />

            <!-- Dock flottant (pas de barre pleine largeur) vs pied classique -->
            <template v-if="useEditActionDock">
                <div
                    v-if="accessLevelFields.length"
                    class="sticky bottom-0 z-40 mt-4 w-full border-t border-base-300/20 bg-transparent pt-3"
                >
                    <div :class="['access-levels', compactAccessLevels && 'access-levels--compact']">
                        <div
                            v-for="field in accessLevelFields"
                            :key="field.key"
                            class="access-levels__item rounded-(--radius-field) border border-base-300/70 bg-base-100/35 px-2.5 py-1.5"
                        >
                            <SelectField
                                v-model="form[field.key]"
                                @update:model-value="() => markDirty(field.key)"
                                :label="getAccessLevelLabel(field.key, field.config)"
                                :options="field.config.options || []"
                                :required="field.config.required"
                                :helper="getAccessLevelHelper(field.key, field.config)"
                                :validation="getFieldValidation(field.key)"
                            />
                        </div>
                    </div>
                </div>
                <EditActionDock
                    :primary-label="primarySaveLabel"
                    :processing-label="primaryAction.processing"
                    :processing="form.processing"
                    :show-secondary="footerSecondaryActions"
                    :secondary-actions="editDockSecondaryActions"
                    :fixed-on-desktop="true"
                    @primary="submit"
                    @action="onEditDockAction"
                />
            </template>

            <div v-else-if="!hideActionDock" :class="footerWrapperClass">
                <div :class="footerBarClass">
                    <div
                        v-if="accessLevelFields.length"
                        :class="['access-levels', compactAccessLevels && 'access-levels--compact']"
                    >
                        <div
                            v-for="field in accessLevelFields"
                            :key="field.key"
                            class="access-levels__item rounded-(--radius-field) border border-base-300/70 bg-base-100/35 px-2.5 py-1.5"
                        >
                            <SelectField
                                v-model="form[field.key]"
                                @update:model-value="() => markDirty(field.key)"
                                :label="getAccessLevelLabel(field.key, field.config)"
                                :options="field.config.options || []"
                                :required="field.config.required"
                                :helper="getAccessLevelHelper(field.key, field.config)"
                                :validation="getFieldValidation(field.key)"
                            />
                        </div>
                    </div>

                    <div :class="footerActionsRowClass">
                        <div
                            v-if="footerSecondaryActions"
                            class="footer-actions__start flex flex-wrap items-center"
                            :class="embeddedInModal ? 'gap-2' : 'gap-2 sm:gap-3'"
                        >
                            <Btn
                                type="button"
                                variant="outline"
                                :size="embeddedInModal ? 'sm' : ''"
                                @click="cancel"
                            >
                                <i :class="cancelAction.icon" class="mr-2" aria-hidden="true"></i>
                                {{ cancelAction.label }}
                            </Btn>
                            <Tooltip
                                content="Réinitialise le formulaire : revient aux valeurs chargées au moment de l’ouverture (ou dernière synchro). En multi‑édition, remet les champs ‘valeurs différentes’ en mode ‘ne pas modifier’."
                                placement="top"
                            >
                                <Btn
                                    type="button"
                                    variant="outline"
                                    :size="embeddedInModal ? 'sm' : ''"
                                    @click="resetForm"
                                >
                                    <i :class="ACTION.discard.icon" class="mr-2" aria-hidden="true"></i>
                                    {{ ACTION.discard.label }}
                                </Btn>
                            </Tooltip>
                        </div>
                        <div
                            class="footer-actions__end flex flex-wrap items-center"
                            :class="embeddedInModal ? 'gap-2' : 'gap-2 sm:gap-3'"
                        >
                            <Btn
                                type="submit"
                                color="primary"
                                :size="embeddedInModal ? 'sm' : ''"
                                :disabled="form.processing"
                            >
                                <i :class="primaryAction.icon" class="mr-2" aria-hidden="true"></i>
                                {{ form.processing ? primaryAction.processing : primaryAction.label }}
                            </Btn>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </Container>

    <ConfirmModal
        :open="deleteConfirm.open"
        :title="deleteConfirm.title"
        :message="deleteConfirm.message"
        :details="deleteConfirm.details"
        confirm-label="Mettre en corbeille"
        confirm-color="error"
        confirm-icon="fa-solid fa-trash-can"
        @confirm="confirmPendingDelete"
        @cancel="cancelPendingDelete"
        @close="cancelPendingDelete"
    />
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

<style scoped lang="scss">
.entity-edit-form {
    .top-tools-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: flex-start;
    }

    .top-tools-row__formula {
        flex: 1 1 320px;
        min-width: 0;
    }

    .top-tools-row__actions {
        display: flex;
        align-items: center;
        min-height: 2.5rem;
    }

    .form-fields {
        display: flex;
        flex-wrap: wrap;
        gap: 0.875rem;
    }

    .form-field {
        width: 100%;
    }

    .footer-tools-row {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .footer-tools-row--static {
        box-shadow: 0 1px 0 0 rgb(0 0 0 / 0.04);
    }

    .footer-actions {
        width: 100%;
    }

    .footer-actions--save-only {
        @media (min-width: 768px) {
            width: auto;
            flex: 0 0 auto;
        }
    }

    .footer-actions__start,
    .footer-actions__end {
        min-width: 0;
    }

    .access-levels {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        width: 100%;
    }

    .access-levels__item {
        width: 100%;
    }

    .access-levels--compact {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.5rem 0.75rem;

        .access-levels__item {
            flex: 1 1 8rem;
            min-width: 7.25rem;
            max-width: 11rem;
            padding: 0.35rem 0.5rem;
        }

        :deep(label) {
            font-size: 0.7rem;
            line-height: 1.2;
        }

        :deep(.select),
        :deep(input),
        :deep(button.select) {
            min-height: 2.25rem;
            height: 2.25rem;
            font-size: 0.8rem;
        }
    }

    @media (min-width: 768px) {
        .form-field {
            flex: 1 1 calc(50% - 0.625rem);
            max-width: calc(50% - 0.625rem);
        }

        .form-field--meta-pair {
            flex: 1 1 calc(50% - 0.4375rem);
            max-width: calc(50% - 0.4375rem);
        }

        .form-field--wide {
            flex-basis: calc(66.666% - 0.42rem);
            max-width: calc(66.666% - 0.42rem);
        }

        .form-field--full {
            flex-basis: 100%;
            max-width: 100%;
        }

        .footer-tools-row {
            flex-direction: row;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
        }

        .access-levels {
            width: auto;
            flex: 1 1 auto;
        }

        .access-levels__item {
            width: auto;
            min-width: 220px;
            max-width: 260px;
            flex: 1 1 220px;
        }

        .access-levels--compact .access-levels__item {
            min-width: 7.25rem;
            max-width: 11rem;
            flex: 0 1 10rem;
        }

        .footer-actions {
            flex: 1 1 auto;
            min-width: 0;
            justify-content: space-between;
        }

        .footer-actions--save-only {
            flex: 0 0 auto;
            min-width: 0;
            justify-content: flex-end;
        }
    }

    :deep(.field-template),
    :deep(.field-template > div),
    :deep(.input),
    :deep(.select),
    :deep(.textarea),
    :deep(.file-input),
    :deep(button.select) {
        width: 100%;
        max-width: none;
    }

    :deep(.input),
    :deep(.select),
    :deep(.file-input),
    :deep(button.select) {
        min-height: 2.75rem;
    }

    :deep(.textarea) {
        min-height: 8rem;
    }

    .state-field :deep(label) {
        display: none;
    }

    .access-levels :deep(label) {
        font-size: 0.75rem;
        opacity: 0.85;
    }

    .state-field :deep(.field-template) {
        gap: 0;
    }

    .state-field :deep(.helper),
    .state-field :deep(.field-template .text-xs),
    .state-field :deep(.field-template .text-sm) {
        display: none;
    }

    .state-field :deep(.select),
    .state-field :deep(input),
    .state-field :deep(button.select) {
        min-height: 2.5rem;
        height: 2.5rem;
        font-size: 0.85rem;
    }

    .access-levels :deep(.select),
    .access-levels :deep(input),
    .access-levels :deep(button.select) {
        min-height: 2rem;
        height: 2rem;
        font-size: 0.8rem;
    }

    /**
     * Sort : champs empilés verticalement dans chaque section.
     * Évite les labels longs (ex. « Coût PA ») qui empiètent sur la colonne suivante
     * quand plusieurs .form-field étaient côte à côte (50 % / 33 %).
     */
    &--spell-layout {
        .form-fields {
            flex-direction: column;
            flex-wrap: nowrap;
        }

        .form-field,
        .form-field--meta-pair,
        .form-field--wide,
        .form-field--full {
            width: 100%;
            max-width: 100%;
            flex: 1 1 100%;
        }
    }

    &--fixed-footer {
        .footer-tools-row {
            flex-direction: column;
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .footer-tools-row {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }
        }
    }
}
</style>

