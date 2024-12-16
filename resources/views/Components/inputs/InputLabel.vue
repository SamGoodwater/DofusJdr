<script setup>
import { computed, defineProps } from 'vue';

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
    value: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(value),
    },
    color: {
        type: String,
        default: 'primary-200',
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
    let classes = ['block', 'font-medium'];
    let match;

    if (props.theme) {

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
        } else {
            classes.push(`text-red-600`);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
        } else {
            classes.push(`text-md`);
        }
    }

    if (!['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`text-${props.size}`);
        }
    }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    if (props.color) {
        classes.push(`text-${props.color}`);
    }

    return classes.join(' ');
})

</script>

<template>
    <label :class="`${getClasses}`" :data-tip="tooltip">
        <span v-if="value">{{ value }}</span>
        <span v-else>
            <slot />
        </span>
    </label>
</template>
