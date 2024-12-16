<script setup>
import { computed, defineProps, ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: '#',
    },
    route: {
        type: String,
        default: '',
    },
    target: {
        type: String,
        default: '',
        validator: (value) => ['', '_blank', '_self', '_parent', '_top'].includes(value),
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

let hrefRef = ref(props.href);

const getClasses = computed(() => {
    let classes = [];

    if (props.route) {
        hrefRef.value = route(props.route);
    } else if (props.href) {
        hrefRef.value = props.href;
    }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    return classes.join(' ');
})

</script>

<template>
    <Link :href="hrefRef" :target="target" :data-tip="tooltip" :class="`${getClasses}`">
    <slot />
    </Link>
</template>

<style scoped></style>
