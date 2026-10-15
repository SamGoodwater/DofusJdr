import { describe, it, expect, vi, beforeEach, afterEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import { nextTick } from 'vue';
import NormsPanel from '@/Pages/Admin/characteristics/NormsPanel.vue';
import { DEFAULT_POWER_COEFFICIENTS } from '@/Utils/Characteristic/normsConstants';

const postMock = vi.fn();

vi.mock('axios', () => ({
    default: {
        post: (...args) => postMock(...args),
    },
}));

vi.mock('@/Pages/Atoms/action/Btn.vue', () => ({
    default: {
        name: 'Btn',
        props: ['disabled', 'size', 'color', 'variant'],
        emits: ['click'],
        template: '<button type="button" :disabled="disabled" @click="$emit(\'click\')"><slot /></button>',
    },
}));

vi.mock('@/Pages/Molecules/data-input/SelectSearchField.vue', () => ({
    default: {
        name: 'SelectSearchField',
        props: ['modelValue', 'options', 'size', 'placeholder'],
        emits: ['update:modelValue'],
        template: '<input data-test="select-search" />',
    },
}));

function mountPanel(modelValue = {}) {
    return mount(NormsPanel, {
        props: {
            modelValue: {
                entity: '*',
                norms_grid: null,
                norms_formula: null,
                norms_power_coefficients: null,
                norms_conditions: [],
                norms_description: '',
                norms_help_section_id: null,
                ...modelValue,
            },
            characteristicKey: 'dommages_spell',
            characteristicsList: [],
        },
        global: {
            stubs: {
                // keep default
            },
        },
    });
}

describe('NormsPanel — coefficients de palier', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        vi.useFakeTimers();
        global.route = vi.fn((name, key) => `/admin/characteristics/${key}/norms/preview`);
        postMock.mockResolvedValue({
            data: {
                grid: {
                    very_weak: Array(20).fill(2),
                    weak: Array(20).fill(2),
                    neutral: Array(20).fill(3),
                    strong: Array(20).fill(4),
                    very_strong: Array(20).fill(5),
                },
            },
        });
    });

    afterEach(() => {
        vi.useRealTimers();
        delete global.route;
    });

    it('applique les coefficients par défaut et les émet', async () => {
        const wrapper = mountPanel();
        const buttons = wrapper.findAll('button');
        const defaultsBtn = buttons.find((b) => b.text().includes('Valeurs par défaut'));
        expect(defaultsBtn).toBeTruthy();
        await defaultsBtn.trigger('click');
        await flushPromises();

        const emitted = wrapper.emitted('update:modelValue');
        expect(emitted).toBeTruthy();
        const last = emitted[emitted.length - 1][0];
        expect(last.norms_power_coefficients).toEqual(DEFAULT_POWER_COEFFICIENTS);
    });

    it('désactive les coefficients quand la formule contient [power]', async () => {
        const wrapper = mountPanel({
            norms_formula: 'round(10+[level]+[power])',
        });
        await nextTick();

        expect(wrapper.text()).toMatch(/incompatibles|désactivés/i);
        const defaultsBtn = wrapper.findAll('button').find((b) => b.text().includes('Valeurs par défaut'));
        expect(defaultsBtn.attributes('disabled')).toBeDefined();
    });

    it('envoie les coefficients dans l’aperçu', async () => {
        const wrapper = mountPanel({
            norms_formula: 'round(3*pow([level],0.633))',
            norms_power_coefficients: { ...DEFAULT_POWER_COEFFICIENTS },
        });
        await nextTick();

        const previewBtn = wrapper.findAll('button').find((b) => b.text().includes('Aperçu'));
        await previewBtn.trigger('click');
        await flushPromises();

        expect(postMock).toHaveBeenCalled();
        const [, payload] = postMock.mock.calls[0];
        expect(payload.norms_formula).toContain('pow');
        expect(payload.norms_power_coefficients).toEqual(DEFAULT_POWER_COEFFICIENTS);
    });
});
