<script setup>
import { computed, defineProps, ref } from 'vue';
import { adjustIntensityColor } from '../../../js/Utils/Color';

const props = defineProps({
    theme: {
        type: String,
        default: 'button'
    },
    color: {
        type: String,
        default: 'body-900',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
    },
    outline: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'bottom',
        validator: (value) => ['', 'top', 'right', 'bottom', 'left'].includes(value),
    },
});

const getClasses = computed(() => {
    let classes = ['badge'];
    let match;
    let is_outline = false;
    let color = "";

    if (props.theme) {

        // Outline
        const regexOutline = /(?:^|\s)(?<capture>outline)(?:\s|$)/;
        match = regexOutline.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`badge-outline`);
            is_outline = true;
        }

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            color = match.groups.capture;
        } else {
            color = 'body-900';
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`select-${match.groups.capture}`);
        } else {
            classes.push(`badge-md`);
        }

    }

    if (!['xs', 'sm', 'md', 'lg'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`badge-${props.size}`);
        }
    }
    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    if (props.color) {
        color = props.color;
    }

    if (props.outline) {
        classes.push(`badge-outline`);
        is_outline = true;
    }

    if (is_outline) {
        classes.push(`border-${color}`);
        classes.push(`text-${color}`);
    } else {
        classes.push(`bg-${color}`);
        classes.push(`text-${adjustIntensityColor(color, 4)}`);
    }

    return classes.join(' ');
})

</script>

<template>
    <div :data-tip="tooltip" :class="`${getClasses}`">
        <slot />
    </div>
</template>

<style scoped></style>
