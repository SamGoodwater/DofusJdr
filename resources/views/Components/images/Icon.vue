<script setup>
import { IconsGetter } from '../../../js/Utils/IconsGetter';
import { imageExists } from '../../../js/Utils/Images';
import { computed, defineProps, ref } from 'vue';

const props = defineProps({
    source: {
        type: String || Array,
        default: '',
    },
    alt: {
        type: String,
        default: '',
    },
    theme: {
        type: String,
        default: 'button'
    },
    rounded: {
        type: String,
        default: 'rounded-none',
        validator: (value) => ['', 'rounded-sm', 'rounded', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-3xl', 'rounded-3xl', 'rounded-full', 'rounded-none'].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg', 'xl', "2xl", '3xl', '4xl', '5xl', '6xl'].includes(value),
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'bottom',
        validator: (value) => ['', 'top', 'right', 'bottom', 'left'].includes(value),
    }
});

let sourceRef = ref('');
let altRef = ref('');

const getClasses = computed(() => {
    let source = '';
    if (props.source) {

        if (Array.isArray(props.source)) {
            source = IconsGetter.get(props.source);
        } else {
            source = props.source;
        }

        if (imageExists(source)) {
            sourceRef = source;
        } else {
            sourceRef = IconsGetter.get('icons', 'no_icon_found');
        }

    } else {
        sourceRef = IconsGetter.get('icons', 'no_icon_found');
    }

    if (!props.alt) {
        const fileName = source.split('/').pop().split('.').shift();
        altRef = fileName;
    }

    let classes = ['icon'];
    let match;
    if (props.theme) {

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg|xl|2xl|3xl|4xl|5xl|6xl)(?:\s|$)/;
        let match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`icon-${match.groups.capture}`);
        } else {
            classes.push(`icon-md`);
        }

        const regexRounded = /(?:^|\s)(?<capture>|rounded-sm|rounded|rounded-md|rounded-lg|rounded-xl|rounded-3xl|rounded-full|rounded-none)(?:\s|$)/;
        match = regexRounded.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(match.groups.capture);
        } else {
            classes.push(`rounded-none`);
        }
    }

    if (!['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`icon-${props.size}`);
        }
    }

    if (!['rounded-sm', 'rounded', 'rounded-md', 'rounded-lg', 'rounded-xl', 'rounded-3xl', 'rounded-full', 'rounded-none'].some(word => props.theme.includes(word))) {
        if (props.rounded) {
            classes.push(props.rounded);
        }
    }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
})

</script>

<template>
    <img :class="`${getClasses}`" :src="sourceRef" :alt="altRef" :data-tip="tooltip">
</template>

<style scoped lang="scss">
.icon {
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    margin: 0;
    padding: 0;
    display: inline-block;

    &-xs {
        width: auto;
        height: 0.75rem;
    }

    &-sm {
        width: auto;
        height: 1rem;
    }

    &-md {
        width: auto;
        height: 1.5rem;
    }

    &-lg {
        width: auto;
        height: 2rem;
    }

    &-xl {
        width: auto;
        height: 3rem;
    }

    &-2xl {
        width: auto;
        height: 4rem;
    }

    &-3xl {
        width: auto;
        height: 5rem;
    }

    &-4xl {
        width: auto;
        height: 6rem;
    }

    &-5xl {
        width: auto;
        height: 7rem;
    }

    &-6xl {
        width: auto;
        height: 8rem;
    }
}
</style>
