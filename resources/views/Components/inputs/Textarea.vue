<script setup>
import { computed, defineProps, ref, onMounted } from 'vue';

let model = defineModel({
    type: String,
    required: true,
});

const props = defineProps({
    theme: {
        type: String,
        default: ''
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

const getClasses = computed(() => {
    let classes = ['textarea'];
    let match;

    if (props.theme) {

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`text-${match.groups.capture}`);
            classes.push(`border-${match.groups.capture}`);
        } else {
            classes.push(`text-main-500`);
            classes.push(`border-main-500`);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`textarea-${match.groups.capture}`);
        } else {
            classes.push(`textarea-md`);
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
            classes.push('textarea-bordered');
        }

        const regexMax = /(?:^|\s)max:(?<capture>[0-9]+)(?:\s|$)/; // max:1000
        match = regexMax.exec(props.theme)
        if (match && match?.groups?.capture) {
            maxRef = match.groups.capture;
        }
    }

    if (!['xs', 'sm', 'md', 'lg'].some(word => props.theme.includes(word))) {
        if (props.size) {
            classes.push(`textarea-${props.size}`);
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
    }

    if (props.bordered) {
        classes.push('textarea-bordered');
    }

    if (props.maxlenght) {
        maxRef = props.maxlenght;
    }

    model.value = props.value;

    return classes.join(' ');
})

const textarea = ref(null);

onMounted(() => {
    if (textarea.value.hasAttribute('autofocus')) {
        textarea.value.focus();
    }
});

defineExpose({ focus: () => textarea.value.focus() });
</script>

<template>
    <label v-if='labelInside' class="form-control">
        <slot v-if='labelInside' name="before" />
        <textarea :required="requiredRef" :autofocus="autofocusRef" v-model="model" ref="textarea" :maxlength="maxRef"
            :data-tip="tooltip" :class="`${getClasses}`" :placeholder="placeholder" ></textarea>
        <slot v-if='labelInside' name="after" />
    </label>
</template>
