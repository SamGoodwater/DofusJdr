<script setup>
import { computed, defineProps, ref, onMounted } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
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
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
    },
    label: {
        type: String,
        default: 'Séléctionner une option',
    },
    autofocus: {
        type: Boolean | String,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    tooltip: {
        type: String,
        default: '',
    },
    tooltipPosition: {
        type: String,
        default: 'bottom',
        validator: (value) => ['', 'top', 'right', 'bottom', 'left'].includes(value),
    },
});

const select = ref(null);

let autofocusRef = ref(false);
let requiredRef = ref('');

const getClasses = computed(() => {
    let classes = ['select', 'w-full', 'max-w-xs'];
    let match;

    if (props.theme) {

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
            classes.push(`border-${match.groups.capture}`);
            colorRef = match.groups.capture;
        } else {
            classes.push(`text-main-500`);
            classes.push(`border-main-500`);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`select-${match.groups.capture}`);
        } else {
            classes.push(`select-md`);
        }

        // Autofocus
        const regexAutofocus = /(?:^|\s)(?<capture>autofocus)(?:\s|$)/;
        match = regexAutofocus.exec(props.theme)
        if (match && match?.groups?.capture) {
            autofocusRef = true;
        }

        // Required
        const regexRequired = /(?:^|\s)(?<capture>required)(?:\s|$)/;
        match = regexRequired.exec(props.theme)
        if (match && match?.groups?.capture) {
            requiredRefRef = "required";
        }
    }

    if (!['xs', 'sm', 'md', 'lg'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`select-${props.size}`);
        }
    }

    if (props.autofocus) {
        autofocusRef = true;
    }

    if (props.required === true || requiredRef === "required") {
        autofocusRef = "required";
    }

    if (props.tooltip) {
        classes.push(`tooltip`);
        classes.push(`tooltip-${props.tooltipPosition}`);
    }

    if (props.color) {
        classes.push(`text-${props.color}`);
        classes.push(`border-${props.color}`);
        colorRef = props.color;
    }

    return classes.join(' ');
})

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select :required="requiredRef" :autofocus="autofocusRef" ref="select" v-model="model" :data-tip="tooltip"
        :class="`${getClasses}`">
        <option disabled selected>{{ label }}</option>
        <option v-for="option in value" :key="option.value" :value="option.value">{{ option.text }}</option>
    </select>
</template>
