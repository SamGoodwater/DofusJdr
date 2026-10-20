<script setup>
/**
 * ResourceViewFull — Vue Full pour Resource
 *
 * @description
 * Vue complète d'une ressource avec toutes les informations affichées.
 * Layout : image + 3 lignes (nom+niveau+type / rareté+poids+prix / description),
 * puis ingrédients, autres relations, bloc admin (write permission).
 *
 * @props {Resource} resource - Instance du modèle Resource
 * @props {Boolean} showActions - Afficher les actions (défaut: true)
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import Route from '@/Pages/Atoms/action/Route.vue';
import EntityPropertyDisplay from '@/Pages/Molecules/entity/shared/EntityPropertyDisplay.vue';
import EntityFieldTooltip from '@/Pages/Molecules/entity/shared/EntityFieldTooltip.vue';
import EntityActions from '@/Pages/Organismes/entity/EntityActions.vue';
import EntityViewHeader from '@/Pages/Molecules/entity/shared/EntityViewHeader.vue';
import ImageViewer from '@/Pages/Molecules/data-display/ImageViewer.vue';
import { useCopyToClipboard } from '@/Composables/utils/useCopyToClipboard';
import { useDownloadPdf } from '@/Composables/utils/useDownloadPdf';
import { useEntityActionDispatcher } from '@/Composables/entity/useEntityActionDispatcher';
import EntityDofusdbRefreshPanel from '@/Pages/Molecules/entity/EntityDofusdbRefreshPanel.vue';
import { getEntityRouteConfig, resolveEntityRouteUrl } from '@/Composables/entity/entityRouteRegistry';
import { getResourceFieldDescriptors } from '@/Entities/resource/resource-descriptors';
import { usePermissions } from '@/Composables/permissions/usePermissions';
import { getRarityConfig, getRoleConfig } from '@/Utils/Entity/SharedConstants';
import { resolveEntityFieldUi, resolveEntityBadgeUi } from '@/Utils/Entity/entity-view-ui';
import ResourceIngredientsList from '@/Pages/Molecules/data-display/ResourceIngredientsList.vue';
import { provideCharacteristicRuntime } from '@/Composables/entity/characteristicRuntimeContext';

const props = defineProps({
    resource: { type: Object, required: true },
    showActions: { type: Boolean, default: true },
    inModal: {
        type: Boolean,
        default: false,
    },
    tableMeta: { type: Object, default: () => ({}) },
    /** Payload runtime (ex. Inertia) pour EntityPropertyDisplay */
    characteristicRuntime: { type: Object, default: null },
});


const actionsContext = computed(() =>
    props.inModal
        ? { inPanel: false, inModal: true, surface: 'modal', viewMode: 'full', modalMode: 'view' }
        : { inPanel: false, inPage: true, surface: 'page', viewMode: 'full' },
);

const headerMode = computed(() => (props.inModal ? 'compact' : 'full'));

provideCharacteristicRuntime(computed(() => props.characteristicRuntime));

const emit = defineEmits(['edit', 'copy-link', 'download-pdf', 'refresh', 'view', 'quick-view', 'delete', 'action']);

const { copyToClipboard } = useCopyToClipboard();
const { dispatchEntityAction, refreshConfirm, confirmPendingRefresh, cancelPendingRefresh, submitAiConvert, submitAiInject, confirmUpdateDiffSave, confirmUpdateDiffRestore } =
    useEntityActionDispatcher('resources', {
        onRefresh: () => router.reload(),
    });
const { downloadPdf } = useDownloadPdf('resource');
const permissions = usePermissions();

const ctx = computed(() => ({
    capabilities: {
        viewAny: permissions.can('resources', 'viewAny'),
        createAny: permissions.can('resources', 'createAny'),
        updateAny: permissions.can('resources', 'updateAny'),
        deleteAny: permissions.can('resources', 'deleteAny'),
        manageAny: permissions.can('resources', 'manageAny'),
    },
    meta: { capabilities: {} },
}));

const descriptors = computed(() => getResourceFieldDescriptors(ctx.value));
const autoUpdateValue = computed(() => {
    const v = props.resource?.auto_update ?? props.resource?._data?.auto_update;
    return typeof v === 'boolean' ? v : null;
});

const ingredients = computed(() => {
    const raw = props.resource?.recipe_ingredients ?? props.resource?._data?.recipe_ingredients ?? [];
    return Array.isArray(raw) ? raw : [];
});

const consumables = computed(() => props.resource?.consumables ?? props.resource?._data?.consumables ?? []);
const items = computed(() => props.resource?.items ?? props.resource?._data?.items ?? []);
const creatures = computed(() => props.resource?.creatures ?? props.resource?._data?.creatures ?? []);
const scenarios = computed(() => props.resource?.scenarios ?? props.resource?._data?.scenarios ?? []);
const campaigns = computed(() => props.resource?.campaigns ?? props.resource?._data?.campaigns ?? []);
const shops = computed(() => props.resource?.shops ?? props.resource?._data?.shops ?? []);

const hasOtherRelations = computed(() =>
    consumables.value.length > 0 ||
    items.value.length > 0 ||
    creatures.value.length > 0 ||
    scenarios.value.length > 0 ||
    campaigns.value.length > 0 ||
    shops.value.length > 0,
);

const canShowField = (fieldKey) => {
    const desc = descriptors.value?.[fieldKey];
    if (!desc) return false;
    const visibleIf = desc?.permissions?.visibleIf;
    if (typeof visibleIf === 'function') {
        try {
            return Boolean(visibleIf(ctx.value));
        } catch (e) {
            return false;
        }
    }
    return true;
};

const getFieldUi = (fieldKey) =>
    resolveEntityFieldUi({ fieldKey, descriptors: descriptors.value, tableMeta: props.tableMeta, entityType: 'resource' });
const getFieldIconStyle = (fieldKey) => {
    const color = getFieldUi(fieldKey).color;
    return color ? { color } : {};
};
const getFieldUnit = (fieldKey) => getFieldUi(fieldKey).characteristic?.unit ?? '';

const getCell = (fieldKey) =>
    props.resource.toCell(fieldKey, { size: 'lg', context: 'extended' });

const asTextCell = (cell) => {
    if (!cell) return { type: 'text', value: '-', params: {} };
    const v = cell?.value;
    return {
        type: 'text',
        value: v === null || typeof v === 'undefined' || String(v) === '' ? '-' : String(v),
        params: cell?.params || {},
    };
};

const getBadgeColor = (fieldKey) => {
    const colorMap = {
        resource_type: 'info',
        level: 'warning',
        rarity: 'auto',
        read_level: 'primary',
        write_level: 'secondary',
    };
    return resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
        localColorMap: colorMap,
    }).color;
};

const getBadgeAutoParams = (fieldKey) => {
    const { autoLabel, autoScheme, autoTone } = resolveEntityBadgeUi({
        fieldKey,
        cell: getCell(fieldKey),
        fieldUi: getFieldUi(fieldKey),
    });
    return { autoLabel, autoScheme, autoTone };
};

const levelDisplay = computed(() => {
    const v = props.resource?.level ?? props.resource?._data?.level;
    return v !== null && v !== undefined && v !== '' ? String(v) : '-';
});
const typeDisplay = computed(() => {
    const rt = props.resource?.resourceType ?? props.resource?._data?.resourceType;
    return rt?.name ?? rt?.label ?? '-';
});
const rarityDisplay = computed(() => {
    const r = props.resource?.rarity ?? props.resource?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.label ?? '-';
});
const rarityBadgeColor = computed(() => {
    const r = props.resource?.rarity ?? props.resource?._data?.rarity ?? 0;
    const cfg = getRarityConfig(r);
    return cfg?.color ?? 'neutral';
});
const readLevelLabel = computed(() => {
    const v = props.resource?.read_level ?? props.resource?._data?.read_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});
const writeLevelLabel = computed(() => {
    const v = props.resource?.write_level ?? props.resource?._data?.write_level ?? 0;
    const cfg = getRoleConfig(v);
    return cfg?.label ?? '-';
});

const handleAction = async (actionKey) => {
    const resourceId = props.resource.id;
    if (!resourceId) return;
    switch (actionKey) {
        case 'view':
            router.visit(route('entities.resources.show', { resource: resourceId }));
            emit('view', props.resource);
            break;
        case 'quick-view':
            emit('quick-view', props.resource);
            break;
        case 'edit':
            await dispatchEntityAction("edit", props.resource, { context: actionsContext.value });
            emit('edit', props.resource);
            break;
        
        case 'copy-link': {
            const cfg = getEntityRouteConfig('resource');
            const url = resolveEntityRouteUrl('resource', 'show', resourceId, cfg);
            if (url) await copyToClipboard(`${window.location.origin}${url}`, 'Lien copié !');
            emit('copy-link', props.resource);
            break;
        }
        case 'download-pdf':
            await downloadPdf(resourceId);
            emit('download-pdf', props.resource);
            break;
        case 'refresh': {
            await dispatchEntityAction('refresh', props.resource);
            break;
        }
        case 'delete':
            emit('delete', props.resource);
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
                        v-if="resource.image"
                        :src="resource.image"
                        :alt="resource.name || 'Ressource'"
                        :caption="resource.name || ''"
                        preload="hover"
                        :image-props="{ size: 'xl', rounded: 'lg', fit: 'cover', class: 'w-full h-full' }"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center bg-base-200 entity-radius-box">
                        <Icon source="fa-solid fa-gem" :alt="resource.name" size="xl" />
                    </div>
                </div>
            </template>

            <template #title>
                <div class="space-y-1">
                    <!-- Ligne 1 : Nom + Niveau (badge Nvx+val) + Type (badge sans icône) -->
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-2xl font-bold text-primary-100 break-words">{{ resource.name }}</h2>
                        <template v-if="canShowField('level')">
                            <EntityFieldTooltip
                                field-key="level"
                                entity-type="resource"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="getBadgeColor('level')"
                                    :auto-label="levelDisplay"
                                    auto-scheme="level"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    Nvx {{ levelDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                        <template v-if="canShowField('resource_type')">
                            <EntityFieldTooltip
                                field-key="resource_type"
                                entity-type="resource"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    color="auto"
                                    :auto-label="typeDisplay"
                                    auto-scheme="labelHash"
                                    auto-tone="light"
                                    size="sm"
                                >
                                    {{ typeDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                    </div>
                    <!-- Ligne 2 : Rareté (badge), Poids, Prix (texte, pas de badge, collés au label) -->
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm">
                        <template v-if="canShowField('rarity')">
                            <EntityFieldTooltip
                                field-key="rarity"
                                entity-type="resource"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                            >
                                <Badge
                                    :color="rarityBadgeColor"
                                    :auto-label="String(resource.rarity ?? 0)"
                                    auto-scheme="rarity"
                                    auto-tone="mid"
                                    size="sm"
                                >
                                    {{ rarityDisplay }}
                                </Badge>
                            </EntityFieldTooltip>
                        </template>
                        <template v-if="canShowField('weight')">
                            <EntityPropertyDisplay
                                field-key="weight"
                                :entity="resource"
                                entity-type="resource"
                                display-mode="extended"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="sm"
                            />
                        </template>
                        <template v-if="canShowField('price')">
                            <EntityPropertyDisplay
                                field-key="price"
                                :entity="resource"
                                entity-type="resource"
                                display-mode="extended"
                                :descriptors="descriptors"
                                :table-meta="tableMeta"
                                size="sm"
                            />
                        </template>
                    </div>
                </div>
            </template>

            <template #mainInfos />
            <template #subtitle>
                <p v-if="resource.description" class="text-primary-300 mt-2 break-words">{{ resource.description }}</p>
            </template>

            <template #actions>
                <div class="flex items-center gap-2">
                    <EntityActions
                        v-if="showActions"
                        entity-type="resource"
                        :entity="resource"
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

        <!-- Ingrédients (colonne) -->
        <div v-if="ingredients.length > 0" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Ingrédients</h3>
            <ResourceIngredientsList :ingredients="ingredients" />
        </div>

        <!-- Autres relations (colonne) -->
        <div v-if="hasOtherRelations" class="space-y-2">
            <h3 class="text-sm font-semibold uppercase tracking-wide text-primary-300">Autres relations</h3>
            <div class="flex flex-col gap-2 text-sm">
                <div v-if="consumables.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Utilisé dans des recettes (consommables)</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="c in consumables"
                            :key="c.id"
                            :href="route('entities.consumables.show', { consumable: c.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-mug-hot" size="xs" class="text-primary-400" />
                            <span>{{ c.name }}</span>
                        </Route>
                    </div>
                </div>
                <div v-if="items.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Utilisé dans des recettes (équipements)</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="it in items"
                            :key="it.id"
                            :href="route('entities.items.show', { item: it.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-sword" size="xs" class="text-primary-400" />
                            <span>{{ it.name }}</span>
                        </Route>
                    </div>
                </div>
                <div v-if="creatures.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Drop par (créatures)</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="cr in creatures"
                            :key="cr.id"
                            :href="route('entities.monsters.show', { monster: cr.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-dragon" size="xs" class="text-primary-400" />
                            <span>{{ cr.name }}</span>
                        </Route>
                    </div>
                </div>
                <div v-if="scenarios.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Scénarios</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="s in scenarios"
                            :key="s.id"
                            :href="route('entities.scenarios.show', { scenario: s.slug ?? s.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-book" size="xs" class="text-primary-400" />
                            <span>{{ s.name }}</span>
                        </Route>
                    </div>
                </div>
                <div v-if="campaigns.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Campagnes</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="ca in campaigns"
                            :key="ca.id"
                            :href="route('entities.campaigns.show', { campaign: ca.slug ?? ca.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-flag" size="xs" class="text-primary-400" />
                            <span>{{ ca.name }}</span>
                        </Route>
                    </div>
                </div>
                <div v-if="shops.length > 0" class="space-y-1">
                    <span class="text-xs uppercase text-primary-300 font-semibold">Hôtels de vente</span>
                    <div class="flex flex-wrap gap-1">
                        <Route
                            v-for="sh in shops"
                            :key="sh.id"
                            :href="route('entities.shops.show', { shop: sh.id })"
                            color="neutral"
                            class="inline-flex items-center gap-1.5 text-xs text-base-content/90 hover:text-base-content no-underline"
                        >
                            <Icon source="fa-solid fa-store" size="xs" class="text-primary-400" />
                            <span>{{ sh.name }}</span>
                        </Route>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloc admin (write permission) -->
        <section
            v-if="userCanEdit"
            role="region"
            aria-label="Administration"
            class="rounded-box overflow-hidden border border-base-300 bg-base-200/50 border-glass-primary-md"
        >
            <div class="px-5 py-4 flex items-center gap-3 border-b border-base-300/80 bd-glass-xs">
                <div class="flex w-10 h-10 shrink-0 items-center justify-center rounded-lg bg-primary-500/20 text-primary-400">
                    <Icon source="fa-solid fa-shield-halved" size="sm" aria-hidden="true" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold uppercase tracking-wider text-primary-200">Administration</h3>
                    <p class="text-xs text-base-content/60 mt-0.5">Métadonnées et paramètres techniques</p>
                </div>
            </div>
            <div class="p-5 space-y-6">
                <!-- Identification : grille métadonnées -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-500/20 text-primary-400">
                            <Icon source="fa-solid fa-hashtag" size="sm" aria-hidden="true" />
                        </div>
                        <div class="min-w-0">
                            <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">ID</span>
                            <span class="mt-0.5 block truncate font-semibold text-base-content">{{ resource.id }}</span>
                        </div>
                    </div>
                    <template v-if="resource.created_at">
                        <div class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-success-500/15 text-success">
                                <Icon source="fa-solid fa-calendar-plus" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">Créé le</span>
                                <span class="mt-0.5 block text-base-content">{{ new Date(resource.created_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-if="resource.createdBy">
                        <div class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-info-500/15 text-info">
                                <Icon source="fa-solid fa-user" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">Créé par</span>
                                <span class="mt-0.5 block truncate text-base-content">{{ resource.createdBy.name ?? resource.createdBy.email }}</span>
                            </div>
                        </div>
                    </template>
                    <template v-if="resource.updated_at">
                        <div class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-warning-500/15 text-warning">
                                <Icon source="fa-solid fa-pen" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">Modifié le</span>
                                <span class="mt-0.5 block text-base-content">{{ new Date(resource.updated_at).toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Permissions -->
                <div v-if="canShowField('read_level') || canShowField('write_level')" class="space-y-3">
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60">Accès</h4>
                    <div class="flex flex-wrap gap-2">
                        <Badge
                            v-if="canShowField('read_level')"
                            :color="getBadgeColor('read_level')"
                            :auto-label="String(resource.read_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-eye" size="xs" class="mr-1" />
                            {{ readLevelLabel }}
                        </Badge>
                        <Badge
                            v-if="canShowField('write_level')"
                            :color="getBadgeColor('write_level')"
                            :auto-label="String(resource.write_level ?? 0)"
                            auto-scheme="level"
                            size="xs"
                            variant="soft"
                        >
                            <Icon source="fa-solid fa-pen-to-square" size="xs" class="mr-1" />
                            {{ writeLevelLabel }}
                        </Badge>
                    </div>
                </div>

                <!-- Source / Scrapping -->
                <div
                    v-if="canShowField('dofus_version') || canShowField('dofusdb_id') || canShowField('auto_update')"
                    class="space-y-3"
                >
                    <h4 class="text-xs font-medium uppercase tracking-wider text-base-content/60 flex items-center gap-1.5">
                        <Icon source="fa-solid fa-database" size="xs" aria-hidden="true" />
                        Source
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div
                            v-if="canShowField('dofus_version')"
                            class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-secondary-500/15 text-secondary">
                                <Icon source="fa-solid fa-gamepad" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">Dofus</span>
                                <span class="mt-0.5 block truncate font-mono text-sm font-medium text-base-content">{{ resource.dofus_version ?? '—' }}</span>
                            </div>
                        </div>
                        <div
                            v-if="canShowField('dofusdb_id')"
                            class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-accent-500/15 text-accent">
                                <Icon source="fa-solid fa-link" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">DofusDB ID</span>
                                <span class="mt-0.5 block truncate font-mono text-sm font-medium text-base-content">{{ resource.dofusdb_id ?? '—' }}</span>
                            </div>
                        </div>
                        <div
                            v-if="canShowField('auto_update')"
                            class="flex items-center gap-3 min-w-0 rounded-lg bg-base-300/30 p-3 transition-colors duration-150 hover:bg-base-300/50"
                        >
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-success-500/15 text-success">
                                <Icon source="fa-solid fa-arrows-rotate" size="sm" aria-hidden="true" />
                            </div>
                            <div class="min-w-0 flex flex-col gap-0.5">
                                <span class="block text-xs font-medium uppercase tracking-wide text-base-content/60">Auto-update</span>
                                <span v-if="autoUpdateValue !== null" class="mt-0.5 flex items-center gap-1.5">
                                    <Icon
                                        :source="autoUpdateValue ? 'fa-solid fa-check-circle' : 'fa-solid fa-times-circle'"
                                        size="sm"
                                        :class="autoUpdateValue ? 'text-success' : 'text-error'"
                                        :alt="autoUpdateValue ? 'Oui' : 'Non'"
                                        aria-hidden="true"
                                    />
                                    <span :class="autoUpdateValue ? 'text-success' : 'text-error'">{{ autoUpdateValue ? 'Oui' : 'Non' }}</span>
                                </span>
                                <span v-else class="mt-0.5 text-base-content/50">—</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
