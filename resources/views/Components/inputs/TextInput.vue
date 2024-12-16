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
    type: {
        type: String,
        default: 'text',
        validator: (value) => ['', 'text', 'email', 'password', 'tel', 'url'].includes(value),
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
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
    },
    bordered: {
        type: Boolean,
        default: true,
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
    labelInside: {
        type: Boolean,
        default: false,
    },
});

let autofocusRef = ref(false);
let requiredRef = ref('');
let maxRef = ref(props.maxlenght);
let minRef = ref(props.minlength);
let colorRef = ref(props.color);
let typeRef = ref(props.type);

const getClasses = computed(() => {
    let classes = ['input', 'w-full', 'max-w-xs'];
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

        // Type
        const regexType = /(?:^|\s)(?<capture>text|email|password|tel|url)(?:\s|$)/;
        match = regexType.exec(props.theme)
        if (match && match?.groups?.capture) {
            typeRef = match.groups.capture;
        } else {
            typeRef = 'text';
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`input-${match.groups.capture}`);
        } else {
            classes.push(`input-md`);
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

        const regexBordered = /(?:^|\s)(?<capture>border|bordered)(?:\s|$)/;
        match = regexBordered.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push('input-bordered');
        }

        const regexMax = /(?:^|\s)max:(?<capture>[0-9]+)(?:\s|$)/; // max:1000
        match = regexMax.exec(props.theme)
        if (match && match?.groups?.capture) {
            maxRef = match.groups.capture;
        }

        const regexMin = /(?:^|\s)min:(?<capture>[0-9]+)(?:\s|$)/; // min:0
        match = regexMin.exec(props.theme)
        if (match && match?.groups?.capture) {
            minRef = match.groups.capture;
        }
    }

    if (!['xs', 'sm', 'md', 'lg'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`input-${props.size}`);
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

    if (props.bordered) {
        classes.push('input-bordered');
    }

    if (props.maxlenght) {
        maxRef = props.maxlenght;
    }

    if (props.minlength) {
        minRef = props.minmength;
    }

    return classes.join(' ');
})

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <label v-if='labelInside'
        :class="`input border-${colorRef} text-${colorRef} input-bordered flex items-center gap-2`">
        <slot v-if='labelInside' name="before" />
        <input :required="requiredRef" :autofocus="autofocusRef" v-model="model" ref="input" :type="typeRef"
            :placeholder="placeholder" :value="value" :data-tip="tooltip" :maxlength="maxRef" :minlength="minRef"
            :pattern="pattern" :class="`${getClasses}`" />
        <slot v-if='labelInside' name="after" />
    </label>
</template>
