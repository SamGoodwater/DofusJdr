<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
    label: {
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

const proxyToggle = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <div class="form-control w-52">
        <label class="label cursor-pointer">
            <span v-if="label" class="label-text">{{ label }}</span>
            <input type="checkbox" :autofocus="autofocus" :class="`toggle-${size} toggle toggle-${color}`"
                :value="value" v-model="proxyToggle" />
        </label>
    </div>
</template>
