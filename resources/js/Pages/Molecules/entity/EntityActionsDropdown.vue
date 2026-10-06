<script setup>
/**
 * EntityActionsDropdown Molecule
 *
 * @description
 * Menu d’actions + raccourcis inline : autant d’icônes que la largeur
 * entre le titre et le bord le permet ; le reste reste dans le « ⋮ ».
 *
 * @example
 * <EntityActionsDropdown
 *   entity-type="spells"
 *   :actions="availableActions"
 *   :grouped-actions="groupedActions"
 *   display="icon-only"
 *   size="sm"
 *   placement="bottom-end"
 *   @action="handleAction"
 * />
 */
import Dropdown from "@/Pages/Atoms/action/Dropdown.vue";
import Icon from "@/Pages/Atoms/data-display/Icon.vue";
import Btn from "@/Pages/Atoms/action/Btn.vue";
import { computed, ref } from "vue";
import EntityActionMenuList from "@/Pages/Molecules/entity/EntityActionMenuList.vue";
import EntityStateAction from "@/Pages/Molecules/entity/EntityStateAction.vue";
import { useResolvedEntityActionState } from "@/Composables/entity/useResolvedEntityActionState";
import { useHorizontalOverflowCount } from "@/Composables/layout/useHorizontalOverflowCount";

const props = defineProps({
    entityType: {
        type: String,
        default: "",
    },
    actions: {
        type: Array,
        required: true,
        default: () => [],
    },
    groupedActions: {
        type: Object,
        default: () => ({}),
    },
    entity: {
        type: Object,
        default: null,
    },
    display: {
        type: String,
        default: "icon-text",
        validator: (v) => ["icon-only", "icon-text"].includes(v),
    },
    size: {
        type: String,
        default: "sm",
    },
    color: {
        type: String,
        default: "primary",
    },
    placement: {
        type: String,
        default: "bottom-end",
    },
    iconOnlyTrigger: {
        type: Boolean,
        default: true,
    },
    triggerVariant: {
        type: String,
        default: "ghost",
    },
    inlineActionKeys: {
        type: Array,
        default: () => ["state", "pin", "quick-view", "edit", "view-dofusdb", "favorite", "copy-link", "view"],
    },
    showInlineShortcuts: {
        type: Boolean,
        default: true,
    },
    alwaysShowTrigger: {
        type: Boolean,
        default: true,
    },
    triggerLabel: {
        type: String,
        default: "Options",
    },
});

const emit = defineEmits(["action"]);

const sourceActions = computed(() => props.actions);
const { resolvedActions, runLocalAction } = useResolvedEntityActionState(
    computed(() => props.entityType),
    computed(() => props.entity),
    sourceActions,
);

const promotedActions = computed(() => {
    if (!props.showInlineShortcuts) return [];
    const keys = Array.isArray(props.inlineActionKeys) ? props.inlineActionKeys : [];
    const source = resolvedActions.value;
    const out = [];
    for (const key of keys) {
        const action = source.find((a) => a?.key === key);
        if (action) out.push(action);
    }
    return out;
});

const rowRef = ref(null);
const measureRef = ref(null);
const promotedCount = computed(() => promotedActions.value.length);

const { visibleCount, measureWidthPx } = useHorizontalOverflowCount({
    rowRef,
    measureRef,
    itemCount: promotedCount,
    alwaysReserveMore: computed(() => props.alwaysShowTrigger),
    gapFallbackPx: 2,
});

const visiblePromotedActions = computed(() =>
    promotedActions.value.slice(0, visibleCount.value),
);

const overflowPromotedActions = computed(() =>
    promotedActions.value.slice(visibleCount.value),
);

const showMenuTrigger = computed(() => {
    if (props.alwaysShowTrigger) return true;
    return overflowPromotedActions.value.length > 0;
});

const measureBoxStyle = computed(() => {
    const w = measureWidthPx.value;
    if (!Number.isFinite(w) || w < 8) return undefined;
    return { width: `${Math.floor(w)}px` };
});

/**
 * @param {string} actionKey
 */
async function handleShortcutClick(actionKey) {
    if (await runLocalAction(actionKey)) {
        emit("action", actionKey);
        return;
    }
    emit("action", actionKey);
}

/**
 * @param {string} actionKey
 */
function handleMenuAction(actionKey) {
    emit("action", actionKey);
}

const getEntityName = () => {
    if (!props.entity) return null;
    if (props.entity && typeof props.entity._data !== "undefined") {
        return props.entity.name || props.entity.title || null;
    }
    return props.entity?.name || props.entity?.title || null;
};

const entityName = computed(() => getEntityName());
</script>

<template>
    <div
        ref="rowRef"
        class="relative ml-auto flex min-w-8 items-center justify-end"
        :class="showInlineShortcuts ? 'w-full' : 'w-max shrink-0'"
    >
        <div
            v-if="promotedActions.length"
            ref="measureRef"
            class="pointer-events-none absolute top-0 right-0 flex items-center gap-0.5 overflow-hidden opacity-0"
            :style="measureBoxStyle"
            aria-hidden="true"
        >
            <Btn
                v-for="action in promotedActions"
                :key="'measure-' + action.key"
                :size="size"
                variant="ghost"
                :color="color"
                class="btn-square shrink-0"
                tabindex="-1"
            >
                <Icon :source="action.icon || 'fa-solid fa-circle'" :size="size" />
            </Btn>
            <Btn
                :size="size"
                :variant="triggerVariant"
                :color="color"
                class="btn-square shrink-0"
                tabindex="-1"
            >
                <Icon source="fa-solid fa-ellipsis-vertical" :size="size" />
            </Btn>
        </div>

        <div
            class="ml-auto flex items-center justify-end gap-0.5"
            :class="showInlineShortcuts ? 'min-w-0' : 'shrink-0'"
        >
            <template v-for="action in visiblePromotedActions" :key="action.key">
                <EntityStateAction
                    v-if="action.key === 'state'"
                    :entity-type="entityType"
                    :entity="entity"
                    :action="action"
                    display="icon-only"
                    :size="size"
                    @action="handleShortcutClick"
                />
                <Btn
                    v-else
                    :size="size"
                    variant="ghost"
                    :color="color"
                    class="btn-square shrink-0"
                    :class="{
                        'pin-active text-primary!': action.active,
                    }"
                    :title="action.tooltip || action.label"
                    @click.stop="handleShortcutClick(action.key)"
                >
                    <Icon
                        :source="action.icon"
                        :size="size"
                        :class="{
                            'pin-active-icon': action.active,
                        }"
                    />
                </Btn>
            </template>
            <Dropdown
                v-if="showMenuTrigger"
                :placement="placement"
                :close-on-content-click="false"
            >
                <template #trigger>
                    <Btn
                        :size="size"
                        :variant="triggerVariant"
                        :color="color"
                        :class="iconOnlyTrigger ? 'btn-square shrink-0' : 'shrink-0 gap-1.5'"
                        :title="iconOnlyTrigger ? 'Autres actions' : triggerLabel"
                        :aria-label="iconOnlyTrigger ? 'Autres actions' : triggerLabel"
                    >
                        <Icon source="fa-solid fa-ellipsis-vertical" :size="size" />
                        <span v-if="!iconOnlyTrigger">{{ triggerLabel }}</span>
                    </Btn>
                </template>
                <template #content>
                    <EntityActionMenuList
                        :entity-type="entityType"
                        :entity="entity"
                        :actions="actions"
                        :grouped-actions="groupedActions"
                        :display="display"
                        :size="size"
                        :entity-name="entityName || ''"
                        menu-class="menu bg-base-100 rounded-box z-1 w-56 p-2 shadow-lg border border-base-300"
                        @action="handleMenuAction"
                    />
                </template>
            </Dropdown>
        </div>
    </div>
</template>

<style scoped>
.pin-active {
    text-shadow: 0 0 8px color-mix(in srgb, var(--color-primary, #60a5fa) 70%, transparent);
}

.pin-active-icon {
    transform: rotate(-38deg);
    transition: transform 180ms ease, filter 180ms ease;
    filter: drop-shadow(0 0 6px color-mix(in srgb, var(--color-primary, #60a5fa) 78%, transparent));
}
</style>
