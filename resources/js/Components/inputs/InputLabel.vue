<script setup>

defineProps({
    value: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(value),
    },
    color: {
        type: String,
        default: 'primary-200',
    },
});
</script>

<template>
    <label :class="`block font-medium text-${size} text-${color}`">
        <span v-if="value">{{ value }}</span>
        <span v-else>
            <slot />
        </span>
    </label>
</template>
