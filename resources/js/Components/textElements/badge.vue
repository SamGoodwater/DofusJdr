<script setup>

const props = defineProps({
    color: {
        type: String,
        default: 'text-body-900',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
    outiline: {
        type: Boolean,
        default: false,
    },
});

</script>

<template>
    <div :class="['badge', `badge-${size}`, `badge-${color}`, { 'badge-outline': outiline }]">
        <slot />
    </div>
</template>

<style scoped></style>
