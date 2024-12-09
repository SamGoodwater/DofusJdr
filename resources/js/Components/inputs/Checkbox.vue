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

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>

    <div class="form-control">
        <label class="label cursor-pointer">
            <span v-if="label" class="label-text">
                <slot name="label" />
            </span>
            <input :autofocus="autofocus" type="checkbox" :value="value" v-model="proxyChecked"
                :class="`checkbox-${size} checkbox checkbox-${color}`" />
        </label>
    </div>
</template>
