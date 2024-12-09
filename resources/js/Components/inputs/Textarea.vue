<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
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

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <textarea :autofocus="autofocus" v-model="model" ref="textarea" :maxlength="maxlenght"
        :class="`textarea textarea-bordered textarea-${color} textarea-${size}`"
        :placeholder="placeholder">{{ value }}</textarea>
</template>
