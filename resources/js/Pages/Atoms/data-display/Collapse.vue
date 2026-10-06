<script setup>
/**
 * Collapse Atom (DaisyUI)
 *
 * @description
 * Composant atomique Collapse conforme DaisyUI (v5.x) et Atomic Design.
 * - Mode checkbox (clic pour ouvrir/fermer, persiste l'état)
 * - Props DaisyUI : arrow (flèche), plus (plus/moins), forcedOpen (collapse-open), defaultOpen (état initial)
 * - Props custom : bgOff, bgOn (couleurs de fond DaisyUI/Tailwind)
 * - Utilitaires custom : shadow, backdrop, opacity
 * - Accessibilité : ariaLabel, role, tabindex, id, etc.
 * - Slots : #title (titre), #content (contenu)
 *
 * @see https://daisyui.com/components/collapse/
 * @version DaisyUI v5.x
 *
 * @example
 * <Collapse arrow bgOff="bg-base-100" bgOn="bg-base-100" :defaultOpen="true">
 *   <template #title>Mon titre</template>
 *   <template #content>Contenu du collapse</template>
 * </Collapse>
 *
 * @props {Boolean} arrow - Affiche la flèche (collapse-arrow)
 * @props {Boolean} plus - Affiche le plus/moins (collapse-plus)
 * @props {String} bgOff - Couleur de fond fermé (classe DaisyUI/Tailwind)
 * @props {String} bgOn - Couleur de fond ouvert (classe DaisyUI/Tailwind, même surface que fermé)
 * @props {Boolean} forcedOpen - Force l'ouverture (ajoute collapse-open)
 * @props {Boolean} defaultOpen - Commence ouvert (checkbox checked par défaut)
 * @props {String} shadow, backdrop, opacity - utilitaires custom
 * @props {String} id, ariaLabel, role, tabindex - accessibilité
 * @slot title - Titre du collapse
 * @slot content - Contenu du collapse
 *
 * @note Toutes les classes DaisyUI et utilitaires custom sont explicites, pas de concaténation dynamique non couverte par Tailwind.
 */
import { ref, computed } from 'vue';
import { getCommonProps, getCommonAttrs, getCustomUtilityProps, getCustomUtilityClasses, mergeClasses } from '@/Utils/atomic-design/uiHelper';

const props = defineProps({
    ...getCommonProps(),
    ...getCustomUtilityProps(),
    arrow: { type: Boolean, default: false },
    plus: { type: Boolean, default: false },
    bgOff: { type: String, default: 'bg-base-100' },
    bgOn: { type: String, default: 'bg-base-100' },
    forcedOpen: { type: Boolean, default: false },
    defaultOpen: { type: Boolean, default: false },
});

const isOpen = ref(props.defaultOpen);

function toggle() {
    if (!props.forcedOpen) isOpen.value = !isOpen.value;
}

const atomClasses = computed(() =>
    mergeClasses(
        [
            'collapse',
            props.arrow && 'collapse-arrow',
            props.plus && 'collapse-plus',
            (props.forcedOpen || isOpen.value) && 'collapse-open',
        ].filter(Boolean),
        getCustomUtilityClasses(props),
        props.class
    )
);

const titleClasses = computed(() => {
    // bgOn si ouvert, sinon bgOff
    return [
        'collapse-title',
        'font-semibold',
        (props.forcedOpen || isOpen.value) ? props.bgOn : props.bgOff,
    ].join(' ');
});

const contentClasses = computed(() => {
    return [
        'collapse-content',
        'text-sm',
        (props.forcedOpen || isOpen.value) ? props.bgOn : props.bgOff,
    ].join(' ');
});

const attrs = computed(() => getCommonAttrs(props));
</script>

<template>
    <div :class="atomClasses" v-bind="attrs" v-on="$attrs">
        <!-- Mode checkbox pour ouverture/fermeture -->
        <input v-if="!props.forcedOpen" type="checkbox" class="peer" :checked="isOpen" @change="toggle"
            tabindex="-1" style="display:none;" />
        <div :class="titleClasses" @click="toggle" :tabindex="props.tabindex" style="cursor:pointer;">
            <slot name="title" />
        </div>
        <div :class="contentClasses">
            <slot name="content" />
        </div>
    </div>
</template>

<style scoped>
.collapse {
    transition: box-shadow 0.2s, background 0.2s;
}

.collapse-title {
    transition: background 0.2s, color 0.2s;
}

.collapse-content {
    transition: background 0.2s, color 0.2s, max-height 0.3s;
}
</style>
