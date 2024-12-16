<script setup>
import { computed, defineProps, ref, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: '',
        validator: (value) => ['left', 'end', 'right', '', 'top', 'bottom'].includes(value),
    },
    color: {
        type: String,
        default: 'base-100',
    },
    label: {
        type: String,
        default: '',
    },

});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

</script>

<template>
    <div class="dropdown">
        <div v-if="label" tabindex="0" role="button" class="btn m-1">
            {{ label }}
        </div>
        <div v-else tabindex="0" role="button">
            <slot name="label" />
        </div>
        <ul tabindex="0" :class="`dropdown-content menu bg-${color} rounded-box z-[1] w-52 p-2 shadow`">
            <slot name="list" />
        </ul>
    </div>
</template>
