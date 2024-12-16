<script setup>
import { useFloating, autoUpdate, flip, shift, offset } from "@floating-ui/vue";
import { ref } from 'vue';

const placementType = [
    'top',
    'top-start',
    'top-end',
    'right',
    'right-start',
    'right-end',
    'bottom',
    'bottom-start',
    'bottom-end',
    'left',
    'left-start',
    'left-end',
]

const referenceRef = ref(null);
const floatingRef = ref(null);
const isHidden = ref(true);

const props = defineProps({
    placement: {
        type: String,
        default: 'bottom-center',
    },
    bgColor: {
        type: String,
        default: 'bg-body-900/75',
    },
});

const { floatingStyles } = useFloating(referenceRef, floatingRef, {
    whileElementsMounted: autoUpdate,
    placement: placementType.includes(props.placement) ? props.placement : 'bottom-center',
    middleware: [
        offset(5),
        flip(),
        shift({ padding: 5 })
    ],
});

function hideTooltips() {
    isHidden.value = true;
}
function showTooltips() {
    isHidden.value = false;
}

</script>

<template>
    <div>
        <div ref="referenceRef" @mouseenter="showTooltips" @mouseleave="hideTooltips" @focus="showTooltips"
            @blur="hideTooltips">
            <slot name="reference" />
        </div>
        <div ref="floatingRef" :style="floatingStyles"
            :class="[props.bgColor, 'bg-blur-3 w-max absolute p-2 px-4 z-50 left-0 top-0 rounded-lg text-secondary', isHidden && 'hidden']">
            <div>
                <slot name="content" />
            </div>
        </div>
    </div>
</template>

<style scoped></style>
