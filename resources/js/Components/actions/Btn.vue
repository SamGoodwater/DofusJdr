<script setup>

import { computed } from 'vue';
import { adjustIntensityColor } from '../../Utils/color'

const props = defineProps({
    theme: {
        type: String,
        default: 'button'
    },
    label: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: 'button',
        validator: (value) => ["", 'button', 'submit', 'reset', 'radio', 'checkbox'].includes(value),
    },
    face: {
        type: String,
        default: '',
        validator: (value) => ['', "block", "wide", 'square', 'circle'].includes(value),
    },
    styled: {
        type: String,
        default: '',
        validator: (value) => ['', 'glass', 'outline', 'link', 'ghost'].includes(value),
    },
    color: {
        type: String,
        default: 'main',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'bottom',
        validator: (value) => ['top', 'right', 'bottom', 'left'].includes(value),
    }
});

const getClasses = computed(() => {
    let classes = ["btn"];
    let match;
    if (props.theme) {

        // STYLE
        const regexStyled = /(?:^|\s)(?<capture>|outline|ghost|link|glass)(?:\s|$)/;
        match = regexStyled.exec(props.theme)
        if (match && match?.groups?.capture) {
            if (match.groups.capture === 'glass') {
                classes.push('glass');
            } else {
                classes.push('btn-' + match.groups.capture);
            }
        }

        //FACE
        const regexFace = /(?:^|\s)(?<capture>|wide|block|square|circle)(?:\s|$)/;
        match = regexFace.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push('btn-' + match.groups.capture);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`btn-${match.groups.capture}`);
        } else {
            classes.push(`btn-md`);
        }

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>main|minor|validate|cancel|simple)(?:\s|$)/;
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`btn-custom-${match.groups.capture}`);
        }
    }

    if (!['glass', 'outline', 'link', 'ghost'].some(word => props.theme.includes(word))) {
        if (props.styled && props.styled !== 'glass') {
            classes.push(`${props.styled}`);
        }
        if (props.styled === 'glass') {
            classes.push(`glass`);
        }
    }

    if (!["block", "wide", 'square', 'circle'].some(word => props.theme.includes(word))) {
        if (props.face) {
            classes.push(`${props.face}`);
        }
    }

    if (!['xs', 'sm', 'md', 'lg'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`btn-${props.size}`);
        }
    }
    // if (['main', 'minor', 'validate', 'cancel', "simple"].some(word => props.theme.includes(word)) === false) {
    if (props.color) {
        if (props.styled == 'outline' || props.theme.includes('outline')) {
            classes.push(`text-${props.color}`);
            classes.push(`border-${props.color}`);
            classes.push(`hover:text-${adjustIntensityColor(props.color, 2)}`);
            classes.push(`hover:border-${adjustIntensityColor(props.color, 2)}`);
        } else if (props.styled == 'link' || props.theme.includes('link')) {
            classes.push(`text-${props.color}`);
            classes.push(`hover:text-${adjustIntensityColor(props.color, 2)}`);
        } else {
            classes.push(`bg-${props.color}`);
            classes.push(`hover:bg-${adjustIntensityColor(props.color, 2)}`);
        }
    }
    // }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
})

</script>

<template>
    <button :type="type" :class="`${getClasses}`" :data-tip="tooltip">
        <span v-if="label">{{ label }}</span>
        <slot v-else name="label" />
    </button>
</template>

<style scoped lang="scss">
.btn-link {
    background-color: transparent;
    text-decoration: none;
    margin: 0;
    padding: 0;
    height: auto;
    min-height: auto;
    width: auto;
    min-width: auto;
}

.btn-custom {
    border: 0px solid transparent;

    &-main {
        @apply bg-main-800;

        &:hover {
            @apply bg-main-600;
        }

        &.btn-outline {
            background-color: transparent;
            @apply text-main-600 border-main-600;

            &:hover {
                @apply text-main-400 border-main-400;
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            @apply text-main-600;

            &:hover {
                @apply text-main-400;
            }
        }
    }

    &-minor {
        @apply bg-minor-400 text-minor-700;

        &:hover {
            @apply bg-minor-600 text-minor-200;
        }

        &.btn-outline {
            background-color: transparent;
            @apply text-minor-400 border-minor-400;

            &:hover {
                @apply text-minor-200 border-minor-200;
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            @apply text-minor-400;

            &:hover {
                @apply text-minor-200;
            }
        }
    }

    &-validate {
        @apply bg-validate-800;

        &:hover {
            @apply bg-validate-600;
        }

        &.btn-outline {
            background-color: transparent;
            @apply text-validate-600 border-validate-600;

            &:hover {
                @apply text-validate-400 border-validate-400;
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            @apply text-validate-600;

            &:hover {
                @apply text-validate-400;
            }
        }
    }

    &-cancel {
        @apply bg-cancel-800;

        &:hover {
            @apply bg-cancel-600;
        }

        &.btn-outline {
            background-color: transparent;
            @apply text-cancel-600 border-cancel-600;

            &:hover {
                @apply text-cancel-400 border-cancel-400;
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            @apply text-cancel-600;

            &:hover {
                @apply text-cancel-400;
            }
        }
    }

    &-simple {
        @apply bg-gray-400 text-gray-700;

        &:hover {
            @apply bg-gray-600 text-gray-200;
        }

        &.btn-outline {
            background-color: transparent;
            @apply text-gray-400 border-gray-400;

            &:hover {
                @apply text-gray-200 border-gray-200;
            }
        }

        &.btn-link {
            background-color: transparent;
            text-decoration: none;
            @apply text-gray-400;

            &:hover {
                @apply text-gray-200;
            }
        }
    }
}
</style>
