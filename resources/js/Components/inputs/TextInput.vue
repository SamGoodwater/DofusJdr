<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    type: {
        type: String,
        default: 'text',
        validator: (value) => ['text', 'email', 'password', 'tel', 'url'].includes(value),
    },
    placeholder: {
        type: String,
        default: '',
    },
    value: {
        type: String,
        default: '',
    },
    maxlenght: {
        type: Number,
        default: 255,
    },
    minlength: {
        type: Number,
        default: 0,
    },
    pattern: {
        type: String,
        default: '',
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
    <input :autofocus="autofocus" v-model="model" ref="input" :type="type" :placeholder="placeholder" :value="value"
        :maxlength="maxlenght" :minlength="minlength" :pattern="pattern"
        :class="`input input-bordered input-${color} input-${size} w-full max-w-xs`" />
</template>
