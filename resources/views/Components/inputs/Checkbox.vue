<script setup>
import { computed, defineProps, ref } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    theme: {
        type: String,
        default: ''
    },
    checked: {
        type: String | Boolean,
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    color: {
        type: String,
        default: 'main-500',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['', 'xs', 'sm', 'md', 'lg'].includes(value),
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

let checkedRef = ref(props.checked);
let autofocusRef = ref(false);
let requiredRef = ref('');

const getClasses = computed(() => {
    let classes = ['checkbox'];
    let match;

    if (props.theme) {

        // COLOR
        const regexColor = /(?:^|\s)(?<capture>([a-zA-Z]{3,}-((50)|([1-9]00)))|primary|secondary|success|accent|neutral|info|warning|error)(?:\s|$)/
        match = regexColor.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`checkbox-${match.groups.capture}`);
        } else {
            classes.push(`checkbox-main-500`);
        }

        // SiZE
        const regexSize = /(?:^|\s)(?<capture>xs|sm|md|lg)(?:\s|$)/;
        match = regexSize.exec(props.theme)
        if (match && match?.groups?.capture) {
            classes.push(`checkbox-${match.groups.capture}`);
        } else {
            classes.push(`checkbox-md`);
        }

        // CHECKED
        const regexChecked = /(?:^|\s)(?<capture>checked)(?:\s|$)/;
        match = regexChecked.exec(props.theme)
        if (match && match?.groups?.capture) {
            checkedRef = 'checked';
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
            classes.push(`icon-${props.size}`);
        }
    }

    if (props.checked) {
        checkedRef = 'checked';
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
        classes.push(`checkbox-${props.color}`);
    }

    return classes.join(' ');
})

const proxyChecked = computed({
    get() {
        return checkedRef.value;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <div class="form-control">
        <label class="label cursor-pointer" :data-tip="tooltip">
            <span v-if="label" class="label-text">{{ label }}</span>
            <span v-else>
                <slot />
            </span>
            <input :required="requiredRef" :autofocus="autofocusRef" :checked="checkedRef" type="checkbox"
                v-model="proxyChecked" :class="`${getClasses}`" />
        </label>
    </div>
</template>
