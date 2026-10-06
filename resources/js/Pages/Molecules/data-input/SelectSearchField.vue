<script setup>
/**
 * SelectSearchField Molecule (DaisyUI, Atomic Design)
 *
 * @description
 * Select with search, single or multi-select, chips/badges for selected items,
 * deduplication, null-filtering, keyboard navigation.
 *
 * @example
 * <SelectSearchField label="Type" v-model="type" :options="types" />
 * <SelectSearchField label="Tags" v-model="tags" :options="allTags" multiple />
 */
import { computed, ref, useAttrs, watch, nextTick, onMounted, onBeforeUnmount } from 'vue';
import FieldTemplate from '@/Pages/Molecules/data-input/FieldTemplate.vue';
import Badge from '@/Pages/Atoms/data-display/Badge.vue';
import Icon from '@/Pages/Atoms/data-display/Icon.vue';
import useInputField from '@/Composables/form/useInputField';
import { getInputPropsDefinition } from '@/Utils/atomic-design/inputHelper';
import { buildSelectOptionBadgeProps } from '@/Utils/Entity/selectOptionBadge';
import { resolveTooltipTeleportTarget } from '@/Composables/ui/resolveTooltipTeleportTarget';

const props = defineProps({
    ...getInputPropsDefinition('select', 'field'),
    options: { type: Array, default: () => [] },
    multiple: { type: Boolean, default: false },
    searchable: { type: Boolean, default: true },
    maxItems: { type: Number, default: 0 },
    allowDuplicates: { type: Boolean, default: false },
    filterNulls: { type: Boolean, default: true },
});
const emit = defineEmits(['update:modelValue']);
const $attrs = useAttrs();

const {
    currentValue, actionsToDisplay, inputRef, focus, isReadonly,
    inputAttrs, listeners, labelConfig,
    validationState, validationMessage, validate, resetValidation, hasError,
    enableValidation, disableValidation,
    styleProperties, containerClasses,
} = useInputField({
    modelValue: props.modelValue,
    type: 'select',
    mode: 'field',
    props, attrs: $attrs, emit,
});

// ── State ──
const isOpen = ref(false);
const searchQuery = ref('');
const highlightIndex = ref(-1);
const containerEl = ref(null);
const searchInputRef = ref(null);
const dropdownRef = ref(null);

/** Teleport target: dialog parent (top layer) or body. */
const teleportTarget = computed(() => {
    if (!isOpen.value || typeof document === 'undefined') return 'body';
    return resolveTooltipTeleportTarget(containerEl.value);
});

// ── Helpers ──
const normalize = (text) =>
    String(text ?? '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');

const optionValue = (opt) => opt?.value ?? opt;
const optionLabel = (opt) => opt?.label ?? opt?.value ?? String(opt ?? '');
const optionDisabled = (opt) => Boolean(opt?.disabled);

// ── Clean options (filter nulls, deduplicate) ──
const cleanOptions = computed(() => {
    let opts = props.options || [];
    if (props.filterNulls) {
        opts = opts.filter((o) => {
            const v = optionValue(o);
            return v !== null && v !== undefined;
        });
    }
    if (!props.allowDuplicates) {
        const seen = new Set();
        opts = opts.filter((o) => {
            const key = String(optionValue(o));
            if (seen.has(key)) return false;
            seen.add(key);
            return true;
        });
    }
    return opts;
});

// ── Multi-select: current selected values as array ──
const selectedValues = computed(() => {
    if (!props.multiple) return [];
    const raw = props.modelValue;
    if (!raw) return [];
    return (Array.isArray(raw) ? raw : [raw]).filter(
        (v) => v !== null && v !== undefined
    );
});

// ── Filtered options for dropdown ──
const filteredOptions = computed(() => {
    let opts = cleanOptions.value;
    if (props.multiple && !props.allowDuplicates) {
        const selectedSet = new Set(selectedValues.value.map(String));
        opts = opts.filter((o) => !selectedSet.has(String(optionValue(o))));
    }
    const q = normalize(searchQuery.value);
    if (!q) return opts;
    return opts.filter((o) =>
        normalize(optionLabel(o)).includes(q) || normalize(String(optionValue(o))).includes(q)
    );
});

// ── Single-select display ──
const displayedValue = computed(() => props.modelValue ?? currentValue.value);

const selectedOption = computed(() => {
    const val = displayedValue.value;
    if (val === null || val === undefined || val === '') return null;
    return cleanOptions.value.find((o) => String(optionValue(o)) === String(val)) || null;
});

const selectedLabel = computed(() => {
    if (props.multiple) {
        return selectedValues.value.length
            ? `${selectedValues.value.length} sélectionné(s)`
            : props.placeholder || 'Choisir...';
    }
    const val = displayedValue.value;
    if (val === null || val === undefined || val === '') {
        return props.placeholder || 'Choisir...';
    }
    const opt = selectedOption.value;
    return optionLabel(opt) || val;
});

// ── Multi-select: selected items as option objects for chips ──
const selectedItems = computed(() =>
    selectedValues.value.map((v) => {
        const opt = cleanOptions.value.find((o) => String(optionValue(o)) === String(v));
        return opt || { value: v, label: String(v) };
    })
);

// ── Multi-select chip visuals (color, icon) ──
function chipHasVisual(item) {
    return Boolean(item?.color || item?.hex || item?.iconUrl || item?.icon);
}

/**
 * URL affichable pour <img> : chemins logiques `icons/...` → `/storage/images/...`
 * (évite la résolution relative `/entities/icons/...` sur les pages entités).
 *
 * @param {string|null|undefined} url
 * @returns {string}
 */
function resolvePublicIconUrl(url) {
    if (url == null || typeof url !== 'string') return '';
    const s = url.trim();
    if (!s || s.startsWith('fa-')) return '';
    if (s.startsWith('http://') || s.startsWith('https://') || s.startsWith('/')) return s;
    return `/storage/images/${s.replace(/^\/+/, '')}`;
}

function chipIconUrl(item) {
    if (item?.iconUrl) return resolvePublicIconUrl(item.iconUrl);
    if (item?.icon && typeof item.icon === 'string') {
        const s = item.icon.trim();
        if (s.startsWith('http') || s.startsWith('/') || s.includes('.')) {
            return resolvePublicIconUrl(s);
        }
    }
    return null;
}

function chipStyle(item) {
    const hex = item?.hex || null;
    const color = item?.color || null;
    const raw = hex || color;
    if (!raw) return {};
    if (raw.startsWith('var(')) {
        return { borderColor: raw, color: raw };
    }
    const c = raw.startsWith('#') ? raw : `#${raw}`;
    return {
        borderColor: `${c}99`,
        boxShadow: `0 0 0 1px ${c}22, 0 1px 4px -1px ${c}44`,
        color: c,
    };
}

function chipClass(item) {
    if (chipHasVisual(item)) {
        return 'bg-base-100/90';
    }
    return 'border-base-300 bg-base-100';
}

// ── Badge support ──
const badgesEnabled = computed(() => Boolean(props.optionBadge?.enabled));

const selectedBadgeProps = computed(() => {
    if (!badgesEnabled.value || !selectedOption.value) return null;
    return buildSelectOptionBadgeProps(selectedOption.value, props.optionBadge, props.color);
});

const filteredOptionsWithBadges = computed(() => {
    const opts = filteredOptions.value;
    if (!badgesEnabled.value) return opts.map((raw) => ({ raw, badge: null }));
    return opts.map((raw) => ({
        raw,
        badge: buildSelectOptionBadgeProps(raw, props.optionBadge, props.color),
    }));
});

const isOptionSelected = (optVal) => {
    if (props.multiple) {
        return selectedValues.value.some((v) => String(v) === String(optVal ?? ''));
    }
    const val = displayedValue.value;
    if (val === null || val === undefined) return optVal == null;
    return String(optVal ?? '') === String(val);
};

// ── Open / Close ──
function openDropdown() {
    if (isOpen.value || props.disabled || isReadonly.value) return;
    isOpen.value = true;
    highlightIndex.value = -1;
    nextTick(() => {
        searchInputRef.value?.focus();
        positionDropdown();
    });
}

function closeDropdown() {
    if (!isOpen.value) return;
    isOpen.value = false;
    searchQuery.value = '';
    highlightIndex.value = -1;
}

function toggleDropdown() {
    isOpen.value ? closeDropdown() : openDropdown();
}

// ── Positioning ──
function positionDropdown() {
    if (!containerEl.value || !dropdownRef.value) return;
    const trigger = containerEl.value;
    const panel = dropdownRef.value;
    const rect = trigger.getBoundingClientRect();
    const viewportH = window.innerHeight;
    const spaceBelow = viewportH - rect.bottom;
    const spaceAbove = rect.top;

    panel.style.position = 'fixed';
    panel.style.left = `${rect.left}px`;
    panel.style.width = `${Math.max(rect.width, 288)}px`;

    if (spaceBelow >= 280 || spaceBelow >= spaceAbove) {
        panel.style.top = `${rect.bottom + 4}px`;
        panel.style.bottom = 'auto';
        panel.style.maxHeight = `${Math.min(spaceBelow - 8, 320)}px`;
    } else {
        panel.style.bottom = `${viewportH - rect.top + 4}px`;
        panel.style.top = 'auto';
        panel.style.maxHeight = `${Math.min(spaceAbove - 8, 320)}px`;
    }
}

// ── Selection ──
function handleSelect(value) {
    if (props.multiple) {
        const current = [...selectedValues.value];
        if (props.maxItems > 0 && current.length >= props.maxItems) return;
        current.push(value);
        emit('update:modelValue', current);
        searchQuery.value = '';
        highlightIndex.value = -1;
        nextTick(() => searchInputRef.value?.focus());
    } else {
        emit('update:modelValue', value);
        searchQuery.value = '';
        closeDropdown();
    }
}

function removeItem(value) {
    if (!props.multiple) return;
    const current = selectedValues.value.filter((v) => String(v) !== String(value));
    emit('update:modelValue', current.length ? current : []);
    nextTick(() => searchInputRef.value?.focus());
}

function clearAll() {
    emit('update:modelValue', props.multiple ? [] : null);
    searchQuery.value = '';
}

// ── Keyboard Navigation ──
function handleKeydown(e) {
    const opts = filteredOptions.value;

    switch (e.key) {
        case 'ArrowDown':
            e.preventDefault();
            if (!isOpen.value) { openDropdown(); return; }
            highlightIndex.value = Math.min(highlightIndex.value + 1, opts.length - 1);
            scrollHighlightedIntoView();
            break;
        case 'ArrowUp':
            e.preventDefault();
            if (!isOpen.value) { openDropdown(); return; }
            highlightIndex.value = Math.max(highlightIndex.value - 1, 0);
            scrollHighlightedIntoView();
            break;
        case 'Enter':
            e.preventDefault();
            if (isOpen.value && highlightIndex.value >= 0 && highlightIndex.value < opts.length) {
                const opt = opts[highlightIndex.value];
                if (!optionDisabled(opt)) handleSelect(optionValue(opt));
            } else if (!isOpen.value) {
                openDropdown();
            }
            break;
        case 'Escape':
            e.preventDefault();
            closeDropdown();
            break;
        case 'Backspace':
            if (props.multiple && searchQuery.value === '' && selectedValues.value.length > 0) {
                removeItem(selectedValues.value[selectedValues.value.length - 1]);
            }
            break;
    }
}

function scrollHighlightedIntoView() {
    nextTick(() => {
        const el = dropdownRef.value?.querySelector('[data-highlighted="true"]');
        el?.scrollIntoView({ block: 'nearest' });
    });
}

// ── Click outside ──
function handleClickOutside(e) {
    if (!isOpen.value) return;
    if (containerEl.value?.contains(e.target)) return;
    if (dropdownRef.value?.contains(e.target)) return;
    closeDropdown();
}

// ── Resize/scroll reposition ──
function handleScrollResize() {
    if (isOpen.value) positionDropdown();
}

// ── Reset highlight on search change ──
watch(searchQuery, () => { highlightIndex.value = 0; });

onMounted(() => {
    document.addEventListener('mousedown', handleClickOutside, true);
    window.addEventListener('scroll', handleScrollResize, true);
    window.addEventListener('resize', handleScrollResize);
});
onBeforeUnmount(() => {
    document.removeEventListener('mousedown', handleClickOutside, true);
    window.removeEventListener('scroll', handleScrollResize, true);
    window.removeEventListener('resize', handleScrollResize);
});

defineExpose({ enableValidation, disableValidation, resetValidation, focus, validate });
</script>

<template>
    <FieldTemplate
        :container-classes="containerClasses"
        :label-config="labelConfig"
        :input-attrs="inputAttrs"
        :listeners="listeners"
        :input-ref="inputRef"
        :actions-to-display="actionsToDisplay"
        :style-properties="styleProperties"
        :validation-state="validationState"
        :validation-message="validationMessage"
        :helper="props.helper"
    >
        <template #core>
            <div ref="containerEl" class="ssf-container w-full">
                <!-- ═══ TRIGGER ═══ -->
                <div
                    v-if="!multiple"
                    role="combobox"
                    :aria-expanded="isOpen"
                    aria-haspopup="listbox"
                    :aria-labelledby="inputAttrs.id ? `${inputAttrs.id}-label` : undefined"
                    :aria-label="!inputAttrs.id && (props.label || props.ariaLabel) ? (typeof props.label === 'string' ? props.label : props.ariaLabel) : undefined"
                    :class="[
                        'ssf-trigger select select-bordered w-full max-w-none text-left min-h-0',
                        badgesEnabled ? 'ssf-trigger--badges py-1.5' : '',
                        props.size === 'xs' ? 'select-xs' : props.size === 'sm' ? 'select-sm' : props.size === 'lg' ? 'select-lg' : props.size === 'xl' ? 'select-xl' : '',
                        hasError ? 'select-error' : '',
                        (isReadonly || props.disabled) ? 'select-disabled opacity-50 cursor-not-allowed' : 'cursor-pointer',
                    ]"
                    tabindex="0"
                    @click="toggleDropdown"
                    @keydown="handleKeydown"
                    ref="inputRef"
                >
                    <span
                        v-if="badgesEnabled && selectedBadgeProps && selectedOption"
                        class="inline-flex min-w-0 max-w-full items-center gap-1.5"
                    >
                        <img
                            v-if="selectedOption.iconUrl"
                            :src="resolvePublicIconUrl(selectedOption.iconUrl)"
                            :alt="selectedLabel"
                            class="h-5 w-5 shrink-0 object-contain opacity-95"
                        />
                        <Icon
                            v-else-if="selectedOption.iconFa"
                            :source="selectedOption.iconFa"
                            :alt="selectedLabel"
                            size="sm"
                            class="shrink-0 opacity-95"
                        />
                        <span
                            v-if="selectedBadgeProps.stateDotClass"
                            class="h-2 w-2 shrink-0 rounded-full opacity-90 ring-1 ring-base-300"
                            :class="selectedBadgeProps.stateDotClass"
                            aria-hidden="true"
                        />
                        <Badge
                            :color="selectedBadgeProps.color"
                            :auto-label="selectedBadgeProps.autoLabel"
                            :auto-scheme="selectedBadgeProps.autoScheme || undefined"
                            :auto-tone="selectedBadgeProps.autoTone || undefined"
                            :variant="selectedBadgeProps.variant"
                            :glassy="Boolean(selectedBadgeProps.glassy)"
                            :strong="Boolean(selectedBadgeProps.strong)"
                            :text-color="selectedBadgeProps.textColor || ''"
                            size="sm"
                            class="max-w-full min-w-0"
                        >
                            <span class="truncate">{{ selectedLabel }}</span>
                        </Badge>
                    </span>
                    <span v-else :class="{ 'opacity-50': displayedValue == null || displayedValue === '' }">
                        {{ selectedLabel }}
                    </span>
                </div>

                <!-- ═══ MULTI TRIGGER ═══ -->
                <div
                    v-else
                    role="combobox"
                    :aria-expanded="isOpen"
                    aria-haspopup="listbox"
                    :aria-labelledby="inputAttrs.id ? `${inputAttrs.id}-label` : undefined"
                    :aria-label="!inputAttrs.id && (props.label || props.ariaLabel) ? (typeof props.label === 'string' ? props.label : props.ariaLabel) : undefined"
                    :class="[
                        'ssf-multi-trigger flex flex-wrap items-center gap-1 px-2 py-1 min-h-10 w-full rounded-btn border transition-colors',
                        isOpen ? 'border-primary/60 ring-1 ring-primary/30' : 'border-base-300',
                        hasError ? 'border-error' : '',
                        (isReadonly || props.disabled) ? 'opacity-50 cursor-not-allowed bg-base-200' : 'cursor-text bg-base-100',
                        props.size === 'xs' ? 'min-h-7 text-xs' : props.size === 'sm' ? 'min-h-8 text-sm' : '',
                    ]"
                    tabindex="0"
                    @click="searchable ? openDropdown() : toggleDropdown()"
                >
                    <span
                        v-for="item in selectedItems"
                        :key="'chip-' + optionValue(item)"
                        class="ssf-chip inline-flex items-center gap-1 shrink-0 rounded-full border px-2 py-0.5 text-xs font-medium leading-tight transition-colors"
                        :class="chipClass(item)"
                        :style="chipStyle(item)"
                    >
                        <img
                            v-if="chipIconUrl(item)"
                            :src="chipIconUrl(item)"
                            :alt="optionLabel(item)"
                            class="h-3.5 w-3.5 shrink-0 object-contain"
                        />
                        <span class="truncate max-w-40">{{ optionLabel(item) }}</span>
                        <button
                            type="button"
                            class="ssf-chip-remove cursor-pointer opacity-60 hover:opacity-100 hover:text-error"
                            :disabled="props.disabled || isReadonly"
                            @click.stop="removeItem(optionValue(item))"
                            aria-label="Retirer"
                        >&times;</button>
                    </span>
                    <input
                        v-if="searchable && !props.disabled && !isReadonly"
                        ref="searchInputRef"
                        type="search"
                        class="ssf-inline-search flex-1 min-w-16 border-none bg-transparent outline-none p-0 text-sm"
                        :placeholder="selectedValues.length === 0 ? (props.placeholder || 'Choisir...') : 'Rechercher…'"
                        v-model="searchQuery"
                        @focus="openDropdown"
                        @keydown="handleKeydown"
                        autocomplete="off"
                    />
                    <span
                        v-else-if="selectedValues.length === 0"
                        class="flex-1 text-sm opacity-50"
                    >{{ props.placeholder || 'Choisir...' }}</span>
                    <span v-else class="flex-1" />
                    <!-- Chevron dropdown (always visible when no search input) -->
                    <svg
                        v-if="!searchable"
                        class="ml-auto h-4 w-4 shrink-0 opacity-40 transition-transform"
                        :class="isOpen ? 'rotate-180' : ''"
                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                    </svg>
                </div>

                <!-- ═══ DROPDOWN PANEL ═══ -->
                <Teleport :to="teleportTarget">
                    <div
                        v-show="isOpen"
                        ref="dropdownRef"
                        class="ssf-dropdown"
                        @mousedown.prevent
                    >
                        <div class="p-2 space-y-1.5">
                            <!-- Search (single mode only — multi has inline search) -->
                            <div v-if="!multiple && searchable" class="relative">
                                <input
                                    ref="searchInputRef"
                                    type="search"
                                    class="input input-sm input-bordered w-full pr-8"
                                    placeholder="Rechercher…"
                                    v-model="searchQuery"
                                    @keydown="handleKeydown"
                                    autocomplete="off"
                                />
                                <svg class="absolute right-2.5 top-1/2 -translate-y-1/2 h-4 w-4 opacity-40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" /><path d="m21 21-4.3-4.3" />
                                </svg>
                            </div>

                            <!-- Empty option (single, not required) -->
                            <button
                                v-if="!multiple && !props.required"
                                type="button"
                                class="ssf-option flex w-full items-center rounded px-2 py-1.5 text-left text-sm transition-colors"
                                :class="isOptionSelected(null) ? 'bg-primary/20 font-medium' : 'hover:bg-base-200'"
                                @click="handleSelect(null)"
                            >
                                <span class="opacity-60">{{ props.placeholder || 'Aucun' }}</span>
                            </button>

                            <!-- Options list -->
                            <div class="ssf-options-list overflow-y-auto pr-0.5" role="listbox">
                                <button
                                    v-for="(row, i) in filteredOptionsWithBadges"
                                    :key="'opt-' + String(optionValue(row.raw))"
                                    type="button"
                                    role="option"
                                    :aria-selected="isOptionSelected(optionValue(row.raw))"
                                    :data-highlighted="highlightIndex === i ? 'true' : undefined"
                                    :class="[
                                        'ssf-option flex w-full items-center gap-1.5 rounded px-2 py-1.5 text-left text-sm transition-colors',
                                        optionDisabled(row.raw) ? 'cursor-not-allowed opacity-40' : '',
                                        highlightIndex === i ? 'bg-primary/15' : '',
                                        isOptionSelected(optionValue(row.raw)) ? 'bg-primary/20 font-medium' : 'hover:bg-base-200',
                                    ]"
                                    :disabled="optionDisabled(row.raw)"
                                    @click="!optionDisabled(row.raw) && handleSelect(optionValue(row.raw))"
                                    @mouseenter="highlightIndex = i"
                                >
                                    <template v-if="row.badge">
                                        <img
                                            v-if="row.raw.iconUrl"
                                            :src="resolvePublicIconUrl(row.raw.iconUrl)"
                                            :alt="String(optionLabel(row.raw))"
                                            class="h-5 w-5 shrink-0 object-contain opacity-95"
                                        />
                                        <Icon
                                            v-else-if="row.raw.iconFa"
                                            :source="row.raw.iconFa"
                                            :alt="String(optionLabel(row.raw))"
                                            size="sm"
                                            class="shrink-0 opacity-95"
                                        />
                                        <span
                                            v-if="row.badge.stateDotClass"
                                            class="h-2 w-2 shrink-0 rounded-full opacity-90 ring-1 ring-base-300"
                                            :class="row.badge.stateDotClass"
                                            aria-hidden="true"
                                        />
                                        <Badge
                                            :color="row.badge.color"
                                            :auto-label="row.badge.autoLabel"
                                            :auto-scheme="row.badge.autoScheme || undefined"
                                            :auto-tone="row.badge.autoTone || undefined"
                                            :variant="row.badge.variant"
                                            :glassy="Boolean(row.badge.glassy)"
                                            :strong="Boolean(row.badge.strong)"
                                            :text-color="row.badge.textColor || ''"
                                            size="sm"
                                            class="min-w-0 max-w-full"
                                        >
                                            {{ optionLabel(row.raw) }}
                                        </Badge>
                                    </template>
                                    <template v-else-if="chipHasVisual(row.raw)">
                                        <img
                                            v-if="chipIconUrl(row.raw)"
                                            :src="chipIconUrl(row.raw)"
                                            :alt="String(optionLabel(row.raw))"
                                            class="h-4 w-4 shrink-0 object-contain"
                                        />
                                        <span :style="chipStyle(row.raw)?.color ? { color: chipStyle(row.raw).color } : {}">
                                            {{ optionLabel(row.raw) }}
                                        </span>
                                    </template>
                                    <span v-else>{{ optionLabel(row.raw) }}</span>
                                </button>

                                <div
                                    v-if="filteredOptions.length === 0"
                                    class="text-sm text-base-content/50 text-center py-3"
                                >
                                    Aucune option trouvée
                                </div>
                            </div>
                        </div>
                    </div>
                </Teleport>
            </div>
        </template>

        <template v-if="$slots.overStart" #overStart>
            <slot name="overStart" />
        </template>
        <template v-if="$slots.overEnd" #overEnd>
            <slot name="overEnd" />
        </template>
        <template #helper>
            <slot name="helper" />
        </template>
    </FieldTemplate>
</template>

<style scoped lang="scss">
.ssf-container {
    position: relative;
}

.ssf-trigger {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-field, 0.1rem);
    transition: all 0.2s ease-in-out;
    min-height: 2.75rem;

    &:hover:not(:disabled):not(.select-disabled) {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
    }

    &:focus:not(:disabled):not(.select-disabled) {
        outline: 2px solid var(--color-primary, #3b82f6);
        outline-offset: 2px;
    }

    &.select-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    &.ssf-trigger--badges {
        min-height: 2.25rem;
    }
}

.ssf-multi-trigger {
    &:focus-within {
        border-color: var(--color-primary, #3b82f6);
        box-shadow: 0 0 0 1px color-mix(in srgb, var(--color-primary, #3b82f6) 30%, transparent);
    }
}

.ssf-inline-search {
    height: 1.5rem;
    font-size: inherit;

    &::placeholder {
        opacity: 0.5;
    }

    &::-webkit-search-cancel-button {
        display: none;
    }
}

.ssf-chip {
    animation: ssf-chip-in 0.15s ease-out;
}

@keyframes ssf-chip-in {
    from { opacity: 0; transform: scale(0.85); }
    to { opacity: 1; transform: scale(1); }
}

.ssf-chip-remove {
    font-size: 1.1em;
    line-height: 1;
    padding: 0 1px;
}

.ssf-dropdown {
    z-index: 9999;
    background: var(--color-base-100, #fff);
    border: 1px solid var(--color-base-300, #d1d5db);
    border-radius: 0.5rem;
    box-shadow:
        0 10px 15px -3px rgba(0, 0, 0, 0.1),
        0 4px 6px -4px rgba(0, 0, 0, 0.1);
    overflow: hidden;

    // Glassmorphism subtle backdrop
    backdrop-filter: blur(16px);
    background: color-mix(in srgb, var(--color-base-100, #fff) 92%, transparent);
}

.ssf-options-list {
    max-height: 240px;
}

.ssf-option {
    &[data-highlighted="true"] {
        background-color: color-mix(in srgb, var(--color-primary, #3b82f6) 12%, transparent);
    }
}
</style>
