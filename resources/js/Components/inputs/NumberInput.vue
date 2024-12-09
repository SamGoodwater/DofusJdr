<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    placeholder: {
        type: Number,
        default: 0,
    },
    value: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 100000000,
    },
    min: {
        type: Number,
        default: 0,
    },
    step: {
        type: Number,
        default: 1,
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
    autofocus: {
        type: Boolean | String,
        default: false,
    },
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input :autofocus="autofocus" v-model="model" ref="input" type="number" :placeholder="placeholder" :value="value"
        :max="max" :min="min" :step="step"
        :class="`input input-bordered input-${color} input-${size} w-full max-w-xs`" />
</template>
