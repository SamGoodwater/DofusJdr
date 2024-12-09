<script setup>

import { computed } from 'vue';

const props = defineProps({
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
        default: 'primary',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
});

const faceClass = computed(() => {
    return props.face ? `btn-${props.face}` : '';
});

const styledClass = computed(() => {
    if (props.styled == "glass") return 'glass';
    return props.styled ? `btn-${props.styled}` : '';
});

</script>

<template>
    <button :type="type" :class="['btn', `btn-${color}`, `btn-${size}`, `${styledClass}`, `${faceClass}`]">
        <span v-if="label">{{ label }}</span>
        <slot v-else name="label" />
    </button>
</template>
