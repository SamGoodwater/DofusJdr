/**
 * EntityActions Organism
 *
 * @description
 * Composant flexible pour afficher les actions d'une entité.
 * Supporte différents formats : boutons, dropdown, menu contextuel.
 * Overflow : les raccourcis inline tiennent dans la largeur disponible,
 * le reste reste dans le dropdown. Format buttons masque le « ⋮ » si tout tient.
 *
 * @example
 * Format buttons: entity-type="spells" :entity="entity" format="buttons" display="icon-only"
 */

<script setup>
import { computed } from "vue";
import { useEntityActions } from "@/Composables/entity/useEntityActions";
import { useDofusDbReferenceStore } from "@/Composables/store/useDofusDbReferenceStore";
import EntityActionsDropdown from "@/Pages/Molecules/entity/EntityActionsDropdown.vue";
import EntityActionMenuList from "@/Pages/Molecules/entity/EntityActionMenuList.vue";

const props = defineProps({
  entityType: {
    type: String,
    required: true,
  },
  entity: {
    type: Object,
    default: null,
  },
  format: {
    type: String,
    default: "dropdown",
    validator: (v) => ["buttons", "dropdown", "context"].includes(v),
  },
  display: {
    type: String,
    default: "icon-text",
    validator: (v) => ["icon-only", "icon-text"].includes(v),
  },
  whitelist: {
    type: Array,
    default: null,
  },
  blacklist: {
    type: Array,
    default: null,
  },
  context: {
    type: Object,
    default: () => ({}),
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
  inlineActionKeys: {
    type: Array,
    default: () => ["state", "pin", "quick-view", "edit", "view-dofusdb", "refresh", "favorite", "copy-link", "view"],
  },
  showInlineShortcuts: {
    type: Boolean,
    default: true,
  },
  /** Affiche le libellé à côté du « ⋮ » (menu Options de la page Modifier). */
  labeledTrigger: {
    type: Boolean,
    default: false,
  },
  triggerLabel: {
    type: String,
    default: "Options",
  },
  contextPosition: {
    type: Object,
    default: null,
  },
  contextVisible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "action",
  "close",
  "view",
  "quick-view",
  "edit",
  "expand",
  "copy-link",
  "state",
  "favorite",
  "download-pdf",
  "refresh",
  "view-dofusdb",
  "minimize",
  "delete",
]);

const dofusDbStore = useDofusDbReferenceStore();

const { availableActions, groupedActions } = useEntityActions(
  () => props.entityType,
  () => props.entity,
  {
    whitelist: () => props.whitelist,
    blacklist: () => props.blacklist,
    context: () => props.context,
  }
);

const handleAction = (actionKey) => {
  const key = typeof actionKey === "string" ? actionKey : actionKey?.key;
  if (key === "view-dofusdb") {
    dofusDbStore.openPanel(props.entityType, props.entity);
  }
  emit("action", key, props.entity);
  if (typeof key === "string") {
    emit(key, props.entity);
  }
};

const getEntityName = () => {
  if (!props.entity) return null;
  if (props.entity && typeof props.entity._data !== "undefined") {
    return props.entity.name || props.entity.title || null;
  }
  return props.entity?.name || props.entity?.title || null;
};

const entityName = computed(() => getEntityName());
const showEntityName = computed(() => Boolean(entityName.value));

const buttonInlineKeys = computed(() =>
  (availableActions.value || []).map((action) => action?.key).filter(Boolean),
);

const contextMenuStyle = computed(() => {
  if (props.format !== "context" || !props.contextPosition) {
    return {};
  }
  return {
    position: "fixed",
    left: `${props.contextPosition.x}px`,
    top: `${props.contextPosition.y}px`,
    zIndex: 9999,
  };
});
</script>

<template>
  <EntityActionsDropdown
    v-if="format === 'buttons' || format === 'dropdown'"
    :entity-type="entityType"
    :actions="availableActions"
    :grouped-actions="groupedActions"
    :entity="entity"
    :display="display"
    :size="size"
    :color="color"
    :placement="placement"
    :icon-only-trigger="!labeledTrigger"
    :trigger-label="triggerLabel"
    :inline-action-keys="format === 'buttons' ? buttonInlineKeys : inlineActionKeys"
    :show-inline-shortcuts="format === 'buttons' ? true : showInlineShortcuts"
    :always-show-trigger="format === 'dropdown'"
    @action="handleAction"
  />

  <Transition v-else-if="format === 'context'" name="entity-context-menu">
    <div
      v-if="contextVisible"
      :style="contextMenuStyle"
      class="dropdown dropdown-open pointer-events-auto"
      @pointerdown.stop
      @mousedown.stop
      @mouseup.stop
      @click.stop
      @contextmenu.prevent.stop
    >
      <EntityActionMenuList
        :entity-type="entityType"
        :entity="entity"
        :actions="availableActions"
        :grouped-actions="groupedActions"
        :display="display"
        :size="size"
        :entity-name="showEntityName ? entityName : ''"
        :focus-on-mount="contextVisible"
        menu-class="dropdown-content menu bg-base-100 rounded-box pointer-events-auto z-[9999] w-56 p-2 shadow-xl border border-base-300 outline-none origin-top-left"
        @action="handleAction"
        @close="emit('close')"
      />
    </div>
  </Transition>
</template>

<style scoped>
.entity-context-menu-enter-active,
.entity-context-menu-leave-active {
  transition: opacity 0.16s ease, transform 0.16s ease;
}
.entity-context-menu-enter-from,
.entity-context-menu-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
.entity-context-menu-enter-to,
.entity-context-menu-leave-from {
  opacity: 1;
  transform: scale(1);
}
</style>
