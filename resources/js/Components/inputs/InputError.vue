<script setup>

defineProps({
    message: {
        type: String,
        default: '',
    },
    color: {
        type: String,
        default: 'red-600',
    },
    size: {
        type: String,
        default: 'sm',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', '6xl'].includes(value),
    },
});
</script>

<template>
    <div v-show="message">
        <p :class="`text-${size} text-${color}`">
            {{ message }}
        </p>
    </div>
</template>
