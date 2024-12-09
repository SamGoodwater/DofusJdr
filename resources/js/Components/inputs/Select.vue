<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    value: {
        type: Array,
        default: [],
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
    label: {
        type: String,
        default: 'Séléctionner une option',
    },
    autofocus: {
        type: Boolean | String,
        default: false,
    },
});

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select :autofocus="autofocus" ref="select" v-model="model"
        :class="`select select-${color} select-${size} w-full max-w-xs`">
        <option disabled selected>{{ label }}</option>
        <option v-for="option in value" :value="option.value">{{ option.text }}</option>
    </select>
</template>
