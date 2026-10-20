<script setup>
/**
 * EntityRelationsManager Organism
 * 
 * @description
 * Composant générique pour gérer les relations many-to-many d'une entité
 * (ajout/retrait d'éléments liés)
 * 
 * @props {Array} relations - Liste des éléments actuellement liés
 * @props {Array} availableItems - Liste de tous les éléments disponibles (pour recherche)
 * @props {Number} entityId - ID de l'entité principale
 * @props {String} entityType - Type d'entité (panoply, creature, scenario, etc.)
 * @props {String} relationType - Type de relation (items, spells, resources, etc.)
 * @props {String} relationName - Nom de la relation (pour les labels et routes)
 * @props {Object} config - Configuration optionnelle (displayFields, searchFields, etc.)
 */
import { ref, computed, watch, shallowRef, defineAsyncComponent } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Composables/store/useNotificationStore';
import Btn from '@/Pages/Atoms/action/Btn.vue';
import EditActionDock from '@/Pages/Molecules/action/EditActionDock.vue';
import InputField from '@/Pages/Molecules/data-input/InputField.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Container from '@/Pages/Atoms/data-display/Container.vue';
import Tooltip from '@/Pages/Atoms/feedback/Tooltip.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import { useEntitySurfaceStore } from "@/Composables/store/useEntitySurfaceStore";
import EntityPickerCore from '@/Pages/Organismes/entity/EntityPickerCore.vue';
import { normalizeEntityType, getEntityResponseAdapter } from '@/Entities/entity-registry';
import { resolveEntityViewComponent } from '@/Utils/entity/resolveEntityViewComponent';
import { warnDev } from '@/Utils/dev-logger';

const props = defineProps({
    relations: {
        type: Array,
        default: () => []
    },
    availableItems: {
        type: Array,
        default: () => []
    },
    entityId: {
        type: Number,
        required: true
    },
    entityType: {
        type: String,
        required: true
    },
    relationType: {
        type: String,
        required: true
    },
    relationName: {
        type: String,
        required: true
    },
    config: {
        type: Object,
        default: () => ({
            displayFields: ['name', 'description', 'level'],
            searchFields: ['name', 'description'],
            routeName: null, // Si null, construit automatiquement : entities.{entityType}.update{RelationType}
            relatedEntityType: null, // Type de l'entité liée (fallback: relationType)
            itemLabel: 'élément',
            itemLabelPlural: 'éléments',
            pivotFields: null, // Array de champs de pivot (ex: ['quantity'] ou ['quantity', 'price', 'comment'])
            /** Si défini (ex: `spells`), complète la recherche locale via `api.tables.{type}`. */
            searchApiEntityType: null,
        })
    },
    /**
     * Affiche le titre interne (`relationName`). Mettre à false si le parent fournit déjà un titre de section.
     */
    showTitle: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['update:relations']);

const notificationStore = useNotificationStore();

const relationId = (item) => {
    const n = Number(item?.id ?? item?._data?.id);
    return Number.isFinite(n) ? n : null;
};

const relationsIdsKey = (list) =>
    (Array.isArray(list) ? list : [])
        .map((item) => relationId(item))
        .filter((id) => id != null)
        .sort((a, b) => a - b)
        .join(',');

// Relations locales (copie pour modification)
const localRelations = ref([...(props.relations || [])]);
const selectedRelationIds = computed(() =>
    localRelations.value.map((item) => relationId(item)).filter((id) => id != null)
);

const pickerValue = ref(null);

const relationEntityType = computed(() =>
    normalizeEntityType(props.config.relatedEntityType || props.relationType)
);

const relationTextViewComponent = shallowRef(null);

const ENTITY_TEXT_PROP_MAP = {
    resources: 'resource',
    'resource-types': 'resourceType',
    items: 'item',
    spells: 'spell',
    monsters: 'monster',
    npcs: 'npc',
    breeds: 'breed',
    consumables: 'consumable',
    campaigns: 'campaign',
    scenarios: 'scenario',
    conditions: 'condition',
    'creature-traits': 'creature-trait',
    panoplies: 'panoply',
    capabilities: 'capability',
    specializations: 'specialization',
    shops: 'shop',
};

const relatedEntityPropName = computed(() => {
    return ENTITY_TEXT_PROP_MAP[relationEntityType.value] || null;
});

const toRelationViewEntity = (item) => {
    const relatedType = relationEntityType.value;
    const adapter = getEntityResponseAdapter(relatedType);
    if (typeof adapter !== 'function') {
        return item;
    }
    try {
        const adapted = adapter({ entities: [item] });
        return adapted?.rows?.[0]?.rowParams?.entity || item;
    } catch (error) {
        warnDev('[EntityRelationsManager] adaptation relation impossible', { relatedType, item, error });
        return item;
    }
};

const relationEntries = computed(() =>
    localRelations.value.map((item) => ({
        item,
        viewEntity: toRelationViewEntity(item),
    }))
);

watch(
    relationEntityType,
    async (type) => {
        if (!type || !relatedEntityPropName.value) {
            relationTextViewComponent.value = null;
            return;
        }
        const component = await resolveEntityViewComponent(type, 'text');
        relationTextViewComponent.value = component
            ? defineAsyncComponent(() => Promise.resolve(component))
            : null;
    },
    { immediate: true }
);

const entitySurface = useEntitySurfaceStore();

const canConsultRelation = computed(() =>
    Boolean(relationTextViewComponent.value && relatedEntityPropName.value)
);

const openRelationModal = (viewEntity) => {
    if (!canConsultRelation.value) return;
    const id = viewEntity?.id ?? viewEntity?._data?.id;
    if (id == null || !relationEntityType.value) return;
    entitySurface.openView({
        entityType: relationEntityType.value,
        entityId: id,
        entity: viewEntity,
    });
};

const getRelationTextProps = (viewEntity) => {
    const propName = relatedEntityPropName.value;
    if (!propName) return {};
    return { [propName]: viewEntity };
};

// Valeurs de pivot pour chaque relation (item_id => { field: value })
const pivotValues = ref({});

const PIVOT_DEFAULTS = {
    character_level: '1',
    slot_index: '1',
    choice_order: '0',
};

const getPivotDefault = (field) => PIVOT_DEFAULTS[field] ?? '';

const getPivotLabel = (field) => {
    const map = {
        quantity: 'Quantité',
        price: 'Prix',
        comment: 'Commentaire',
        character_level: 'Niveau PJ',
        slot_index: 'Emplacement',
        choice_order: 'Ordre',
    };
    return map[field] || field;
};

const pivotInputType = (field) => {
    if (field === 'comment') return 'text';
    if (
        ['quantity', 'price', 'character_level', 'slot_index', 'choice_order'].includes(field)
    ) {
        return 'number';
    }
    return 'text';
};

// Initialiser les valeurs de pivot depuis les relations existantes
const initializePivotValues = () => {
    if (!props.config.pivotFields || props.config.pivotFields.length === 0) {
        return;
    }

    const pivots = {};
    props.relations.forEach((item) => {
        pivots[item.id] = {};
        props.config.pivotFields.forEach((field) => {
            const raw = item.pivot?.[field];
            if (raw !== undefined && raw !== null && raw !== '') {
                pivots[item.id][field] = String(raw);
            } else {
                pivots[item.id][field] = getPivotDefault(field);
            }
        });
    });
    pivotValues.value = pivots;
};

// Initialiser au montage
initializePivotValues();

// Recherche
const searchQuery = ref('');
const remoteSearchItems = ref([]);
const remoteSearchLoading = ref(false);
let remoteSearchAbort = null;

const searchApiEntityType = computed(() => {
    const raw = props.config?.searchApiEntityType;
    return raw ? normalizeEntityType(raw) : null;
});

let remoteSearchTimer = null;

async function runRemoteSearch(query) {
    const entityKey = searchApiEntityType.value;
    if (!entityKey || String(query || '').trim().length < 2) {
        remoteSearchItems.value = [];
        return;
    }
    remoteSearchAbort?.abort();
    remoteSearchAbort = new AbortController();
    remoteSearchLoading.value = true;
    try {
        $params = new URLSearchParams({
            format: 'entities',
            limit: '40',
            search: String(query).trim(),
        });
        const extraParams = props.config?.searchApiParams;
        if (extraParams && typeof extraParams === 'object') {
            Object.entries(extraParams).forEach(([key, value]) => {
                if (value == null || value === '') return;
                params.set(key, String(value));
            });
        }
        const res = await fetch(`${route(`api.tables.${entityKey}`)}?${params}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
            signal: remoteSearchAbort.signal,
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const payload = await res.json();
        const adapter = getEntityResponseAdapter(entityKey);
        const adapted = typeof adapter === 'function' ? adapter(payload) : payload;
        const rows = Array.isArray(adapted?.rows)
            ? adapted.rows.map((r) => r?.rowParams?.entity || r).filter(Boolean)
            : Array.isArray(payload?.entities)
                ? payload.entities
                : [];
        remoteSearchItems.value = rows.map((row) => (row?._data ? row._data : row));
    } catch (e) {
        if (e?.name === 'AbortError') return;
        warnDev('[EntityRelationsManager] remote search failed', e);
        remoteSearchItems.value = [];
    } finally {
        remoteSearchLoading.value = false;
    }
}

watch(searchQuery, (q) => {
    if (!searchApiEntityType.value) {
        remoteSearchItems.value = [];
        return;
    }
    if (remoteSearchTimer) clearTimeout(remoteSearchTimer);
    remoteSearchTimer = setTimeout(() => {
        runRemoteSearch(q);
    }, 250);
});

// Éléments filtrés selon la recherche (local + API optionnelle)
const filteredAvailableItems = computed(() => {
    if (!searchQuery.value) {
        return [];
    }
    const query = searchQuery.value.toLowerCase();
    const searchFields = props.config.searchFields || ['name', 'description'];
    const selectedIds = new Set(localRelations.value.map((item) => relationId(item)).filter((id) => id != null));

    const matchesLocal = (props.availableItems || []).filter((item) => {
        if (selectedIds.has(Number(item.id))) return false;
        return searchFields.some((field) => {
            const value = item[field];
            return value && String(value).toLowerCase().includes(query);
        });
    });

    if (!searchApiEntityType.value) {
        return matchesLocal;
    }

    const localIds = new Set(matchesLocal.map((item) => relationId(item)).filter((id) => id != null));
    const fromRemote = (remoteSearchItems.value || []).filter((item) => {
        const id = relationId(item);
        if (id == null || selectedIds.has(id) || localIds.has(id)) return false;
        return true;
    });

    return [...matchesLocal, ...fromRemote];
});

// Construire le nom de la route
const routeName = computed(() => {
    if (props.config.routeName) {
        return props.config.routeName;
    }
    // Construire automatiquement : entities.panoplies.updateItems
    // Pour "items" -> "Items", pour "classes" -> "Classes", etc.
    // Gérer les cas spéciaux comme "spellTypes" -> "SpellTypes"
    let relationTypeCamel = props.relationType;
    // Si le type contient plusieurs mots (camelCase), capitaliser chaque mot
    if (relationTypeCamel.includes(/([a-z])([A-Z])/)) {
        // Déjà en camelCase, juste capitaliser la première lettre
        relationTypeCamel = relationTypeCamel.charAt(0).toUpperCase() + relationTypeCamel.slice(1);
    } else {
        // Simple, juste capitaliser la première lettre
        relationTypeCamel = relationTypeCamel.charAt(0).toUpperCase() + relationTypeCamel.slice(1);
    }
    
    // Par convention dans ce projet, `entityType` est déjà au pluriel dans les usages
    // (ex: shops, scenarios, campaigns). On garde un fallback si quelqu'un passe le singulier.
    const entityTypePlural = props.entityType.endsWith('s')
        ? props.entityType
        : (props.entityType.endsWith('y') ? props.entityType.slice(0, -1) + 'ies' : props.entityType + 's');

    return `entities.${entityTypePlural}.update${relationTypeCamel}`;
});

const normalizePickedEntity = (raw) => {
    if (!raw || typeof raw !== 'object') return null;
    const data = raw._data && typeof raw._data === 'object' ? { ...raw._data, ...raw } : raw;
    const id = relationId(data);
    if (id == null) return null;
    return {
        ...data,
        id,
        name: data.name ?? data.title ?? `#${id}`,
    };
};

const addItem = (item) => {
    const normalized = normalizePickedEntity(item);
    if (!normalized) return;
    if (localRelations.value.some((i) => relationId(i) === normalized.id)) {
        return;
    }
    localRelations.value.push(normalized);

    if (props.config.pivotFields && props.config.pivotFields.length > 0) {
        if (!pivotValues.value[normalized.id]) {
            pivotValues.value[normalized.id] = {};
            props.config.pivotFields.forEach((field) => {
                pivotValues.value[normalized.id][field] = getPivotDefault(field);
            });
        }
    }

    emit('update:relations', localRelations.value);
    searchQuery.value = '';
    pickerValue.value = null;
    notificationStore.success(
        `${props.config.itemLabel.charAt(0).toUpperCase() + props.config.itemLabel.slice(1)} "${normalized.name}" ajouté`,
        {
            duration: 3000,
            placement: 'top-center',
        }
    );
};

const onPickerSelectedEntities = (entities) => {
    const entity = Array.isArray(entities) ? entities[0] : null;
    if (!entity) return;
    addItem(entity);
};

const removeItem = (itemId) => {
    const targetId = Number(itemId);
    const item = localRelations.value.find((i) => relationId(i) === targetId);
    if (item) {
        localRelations.value = localRelations.value.filter((i) => relationId(i) !== targetId);

        if (pivotValues.value[targetId]) {
            delete pivotValues.value[targetId];
        }

        emit('update:relations', localRelations.value);
        notificationStore.success(
            `${props.config.itemLabel.charAt(0).toUpperCase() + props.config.itemLabel.slice(1)} "${item.name || item.id}" retiré`,
            {
                duration: 3000,
                placement: 'top-center',
            }
        );
    }
};

watch(
    () => relationsIdsKey(props.relations),
    () => {
        localRelations.value = [...(props.relations || [])];
        initializePivotValues();
    }
);

const hasUnsavedRelationChanges = computed(() => {
    const pivotFields = props.config.pivotFields;
    if (!pivotFields?.length) {
        const a = [...localRelations.value].map((i) => i.id).sort((x, y) => x - y);
        const b = [...props.relations].map((i) => i.id).sort((x, y) => x - y);
        return JSON.stringify(a) !== JSON.stringify(b);
    }
    const sortById = (arr) => [...arr].sort((x, y) => x.id - y.id);
    const loc = sortById(localRelations.value);
    const orig = sortById(props.relations);
    if (loc.length !== orig.length) return true;
    for (let i = 0; i < loc.length; i++) {
        if (loc[i].id !== orig[i].id) return true;
        for (const f of pivotFields) {
            const current = String(pivotValues.value[loc[i].id]?.[f] ?? '');
            const originalVal = orig[i].pivot?.[f];
            const original =
                originalVal !== undefined && originalVal !== null && originalVal !== ''
                    ? String(originalVal)
                    : getPivotDefault(f);
            if (current !== original) return true;
        }
    }
    return false;
});

// Formulaire pour la sauvegarde
const relationsForm = useForm({
    [props.relationType]: []
});

// Sauvegarder les changements
const saveRelations = () => {
    const itemIds = localRelations.value.map(item => item.id);
    
    // Si des pivots sont définis, envoyer les données avec les pivots
    if (props.config.pivotFields && props.config.pivotFields.length > 0) {
        // Format: { item_id: { pivot_field: value } }
        const dataWithPivots = {};
        itemIds.forEach(itemId => {
            dataWithPivots[itemId] = {};
            props.config.pivotFields.forEach((field) => {
                const value = pivotValues.value[itemId]?.[field] ?? '';
                if ((field === 'quantity' || field === 'price') && value !== '') {
                    dataWithPivots[itemId][field] = Number(value) || 0;
                } else if (field === 'character_level' || field === 'slot_index') {
                    const n = Number(value);
                    dataWithPivots[itemId][field] = Number.isFinite(n) && n >= 1 ? n : 1;
                } else if (field === 'choice_order') {
                    const n = Number(value);
                    dataWithPivots[itemId][field] = Number.isFinite(n) && n >= 0 ? Math.floor(n) : 0;
                } else {
                    dataWithPivots[itemId][field] = value;
                }
            });
        });
        relationsForm[props.relationType] = dataWithPivots;
    } else {
        // Pas de pivot, format simple (array d'IDs)
        relationsForm[props.relationType] = itemIds;
    }
    
    // Construire les paramètres de route (panoply pour panoplies, creature pour creatures, etc.)
    const entityParamName = props.entityType.endsWith('ies') 
        ? props.entityType.slice(0, -3) + 'y' 
        : props.entityType.replace(/s$/, '');
    const routeParams = { [entityParamName]: props.entityId };
    
    relationsForm.patch(route(routeName.value, routeParams), {
        preserveScroll: true,
        onSuccess: () => {
            notificationStore.success(
                `${props.config.itemLabelPlural.charAt(0).toUpperCase() + props.config.itemLabelPlural.slice(1)} mis à jour avec succès`,
                {
                    duration: 3000,
                    placement: 'top-right'
                }
            );
        },
        onError: (errors) => {
            notificationStore.error('Erreur lors de la mise à jour', {
                duration: 5000,
                placement: 'top-center'
            });
            warnDev('[EntityRelationsManager] Erreurs sauvegarde relations', errors);
        }
    });
};

// Afficher un champ d'un élément
const displayField = (item, field) => {
    if (field.includes('.')) {
        // Support pour les relations imbriquées (ex: 'itemType.name')
        const parts = field.split('.');
        let value = item;
        for (const part of parts) {
            value = value?.[part];
        }
        return value;
    }
    return item[field];
};
</script>

<template>
    <Container class="space-y-4">
        <div class="flex justify-between items-center">
            <h3 v-if="showTitle" class="text-lg font-semibold">{{ relationName }}</h3>
            <Badge :content="localRelations.length.toString()" color="primary" class="ml-auto" />
        </div>

        <!-- Liste des éléments actuels -->
        <div v-if="localRelations.length > 0" class="space-y-2">
            <div
                v-for="entry in relationEntries"
                :key="entry.item.id"
                class="p-3 bg-base-200 rounded-box hover:bg-base-300 transition-colors"
            >
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <button
                            v-if="canConsultRelation"
                            type="button"
                            class="group inline-flex max-w-full items-center gap-2 rounded-md px-1 py-1 text-left transition-colors hover:bg-base-300/60"
                            @click="openRelationModal(entry.viewEntity)"
                        >
                            <component
                                :is="relationTextViewComponent"
                                v-bind="getRelationTextProps(entry.viewEntity)"
                            />
                            <Tooltip content="Consulter dans une modale" placement="top">
                                <Icon
                                    source="fa-solid fa-up-right-from-square"
                                    size="xs"
                                    class="opacity-0 transition-opacity group-hover:opacity-70"
                                />
                            </Tooltip>
                        </button>

                        <div v-else class="font-medium">
                            {{ displayField(entry.item, config.displayFields[0] || 'name') }}
                        </div>
                        <div
                            v-if="config.displayFields[1] && displayField(entry.item, config.displayFields[1])"
                            class="text-sm text-base-content/70 line-clamp-1"
                        >
                            {{ displayField(entry.item, config.displayFields[1]) }}
                        </div>
                        <div
                            v-if="config.displayFields[2] && displayField(entry.item, config.displayFields[2])"
                            class="text-xs text-base-content/50 mt-1"
                        >
                            {{ config.displayFields[2] === 'level' ? `Niveau ${displayField(entry.item, config.displayFields[2])}` : displayField(entry.item, config.displayFields[2]) }}
                        </div>
                        
                        <!-- Champs de pivot -->
                        <div v-if="config.pivotFields && config.pivotFields.length > 0" class="mt-3 space-y-2 pt-3 border-t border-base-300">
                            <div 
                                v-for="pivotField in config.pivotFields" 
                                :key="pivotField"
                                class="flex items-center gap-2"
                            >
                                <label class="text-xs font-medium text-base-content/70 w-28 shrink-0">
                                    {{ getPivotLabel(pivotField) }}
                                </label>
                                <InputField
                                    v-if="pivotField !== 'comment'"
                                    v-model="pivotValues[entry.item.id][pivotField]"
                                    :type="pivotInputType(pivotField)"
                                    :placeholder="
                                        pivotField === 'quantity'
                                            ? '1'
                                            : pivotField === 'price'
                                              ? '0'
                                              : pivotField === 'character_level' || pivotField === 'slot_index'
                                                ? '1'
                                                : pivotField === 'choice_order'
                                                  ? '0'
                                                  : ''
                                    "
                                    size="sm"
                                    class="flex-1"
                                />
                                <InputField
                                    v-else
                                    v-model="pivotValues[entry.item.id][pivotField]"
                                    type="text"
                                    placeholder="Commentaire..."
                                    size="sm"
                                    class="flex-1"
                                />
                            </div>
                        </div>
                    </div>
                    <Tooltip :content="`Retirer cet ${config.itemLabel}`" placement="top">
                        <Btn
                            variant="ghost"
                            size="sm"
                            color="error"
                            class="shrink-0"
                            :aria-label="`Retirer cet ${config.itemLabel}`"
                            @click.stop="removeItem(entry.item.id)"
                        >
                            <i class="fa-solid fa-trash-can" aria-hidden="true"></i>
                            <span class="sr-only">Retirer</span>
                        </Btn>
                    </Tooltip>
                </div>
            </div>
        </div>
        <div v-else class="text-center py-8 text-base-content/50">
            <p>Aucun {{ config.itemLabel }} dans cette relation</p>
        </div>

        <!-- Recherche et ajout d'éléments -->
        <div class="border-t pt-4">
            <div v-if="searchApiEntityType" class="space-y-2">
                <p class="text-sm font-medium">
                    Rechercher un {{ config.itemLabel }} à ajouter
                </p>
                <EntityPickerCore
                    :model-value="pickerValue"
                    :entity-type="searchApiEntityType"
                    :multiple="false"
                    variant="extended"
                    :blacklist="selectedRelationIds"
                    :placeholder="`Rechercher un ${config.itemLabel}…`"
                    size="sm"
                    @update:model-value="pickerValue = $event"
                    @update:selected-entities="onPickerSelectedEntities"
                />
            </div>
            <div v-else class="space-y-2">
                <InputField
                    v-model="searchQuery"
                    :label="`Rechercher un ${config.itemLabel} à ajouter`"
                    :placeholder="`Tapez le nom d'un ${config.itemLabel}...`"
                >
                    <template #helper>
                        <span class="text-xs text-base-content/60">
                            Recherchez parmi les {{ config.itemLabelPlural }} disponibles
                        </span>
                    </template>
                </InputField>

                <!-- Résultats de recherche -->
                <div
                    v-if="searchQuery && filteredAvailableItems.length > 0"
                    class="max-h-60 overflow-y-auto border border-base-300 rounded-box bg-base-100 shadow-lg z-10"
                >
                    <div
                        v-for="item in filteredAvailableItems.slice(0, 10)"
                        :key="item.id"
                        @mousedown.prevent="addItem(item)"
                        class="p-3 hover:bg-base-200 cursor-pointer transition-colors border-b border-base-300 last:border-b-0"
                    >
                        <div class="font-medium">
                            {{ displayField(item, config.displayFields[0] || 'name') }}
                        </div>
                        <div
                            v-if="config.displayFields[1] && displayField(item, config.displayFields[1])"
                            class="text-sm text-base-content/70 line-clamp-2 mt-1"
                        >
                            {{ displayField(item, config.displayFields[1]) }}
                        </div>
                        <div
                            v-if="config.displayFields[2] && displayField(item, config.displayFields[2])"
                            class="text-xs text-base-content/50 mt-1"
                        >
                            {{ config.displayFields[2] === 'level' ? `Niveau ${displayField(item, config.displayFields[2])}` : displayField(item, config.displayFields[2]) }}
                        </div>
                    </div>
                    <div v-if="filteredAvailableItems.length > 10" class="p-2 text-center text-xs text-base-content/50">
                        {{ filteredAvailableItems.length - 10 }} autres résultats...
                    </div>
                </div>
                <div
                    v-else-if="searchQuery && filteredAvailableItems.length === 0"
                    class="p-4 text-center text-base-content/50 text-sm border border-base-300 rounded-box"
                >
                    Aucun {{ config.itemLabel }} trouvé ou tous les {{ config.itemLabelPlural }} sont déjà liés
                </div>
            </div>
        </div>

        <!-- Bouton de sauvegarde -->
        <div class="flex justify-end border-t pt-4">
            <EditActionDock
                :processing="relationsForm.processing"
                :disabled="!hasUnsavedRelationChanges"
                :show-secondary="false"
                :secondary-actions="[]"
                :fixed-on-desktop="false"
                @primary="saveRelations"
            />
        </div>
    </Container>

</template>

<style scoped>
.line-clamp-1 {
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

