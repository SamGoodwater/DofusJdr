import { describe, it, expect, vi, beforeEach } from 'vitest';
import { computed, toValue } from 'vue';
import { mount, flushPromises } from '@vue/test-utils';
import CharacteristicInfoPanel from '@/Pages/Molecules/data-display/CharacteristicInfoPanel.vue';

vi.mock('@/Composables/characteristic/useCharacteristicCard', () => ({
    useCharacteristicCard: (options) => ({
        enrichedViewModel: computed(() => {
            const o = toValue(options) || {};
            const base = o.viewModel || {};
            return {
                ...base,
                formulaDisplay: base.formulaDisplay || 'mod. Int + équip.',
                descriptions: base.descriptions || 'Description de test',
                defaultValue: base.defaultValue ?? '0',
                limitMin: base.limitMin ?? -5,
                limitMax: base.limitMax ?? 15,
                limitAbsolute: Boolean(base.limitAbsolute),
                limitsDependOnLevel: Boolean(base.limitsDependOnLevel),
                limitsByLevel: base.limitsByLevel || [],
            };
        }),
        card: { value: null },
        loading: { value: false },
        error: { value: null },
        refresh: vi.fn(),
    }),
}));

vi.mock('@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue', () => ({
    default: {
        name: 'CharacteristicFormulaRichText',
        props: ['formula', 'compact'],
        template: '<div data-test="formula-rich">{{ formula }}</div>',
    },
}));

vi.mock('@/Pages/Atoms/data-display/Icon.vue', () => ({
    default: {
        name: 'Icon',
        template: '<span data-test="icon" />',
    },
}));

function mountPanel(model, props = {}) {
    return mount(CharacteristicInfoPanel, {
        props: {
            model,
            fetchCard: false,
            ...props,
        },
    });
}

describe('CharacteristicInfoPanel', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('affiche nom, description, limites et formule sans runtime', async () => {
        const wrapper = mountPanel({
            key: 'initiative_creature',
            name: 'Initiative',
            formulaDisplay: 'mod. Int + équip.',
            descriptions: 'Ordre de jeu',
            defaultValue: '0',
            limitMin: -10,
            limitMax: 20,
        });
        await flushPromises();

        expect(wrapper.text()).toContain('Initiative');
        expect(wrapper.text()).toContain('Ordre de jeu');
        expect(wrapper.text()).toMatch(/défaut/i);
        expect(wrapper.text()).not.toContain('Temporaire');
    });

    it('affiche « Avec tes valeurs » quand un runtime substituted est fourni', async () => {
        const wrapper = mountPanel({
            key: 'initiative_creature',
            name: 'Initiative',
            formulaDisplay: 'mod. Int + équip.',
            substituted: '3 + 2',
            placeholders: [{ key: 'modifier_intelligence_creature', label: 'mod. Int', value: 3 }],
        });
        await flushPromises();

        expect(wrapper.text()).toMatch(/Avec tes valeurs/i);
    });

    it('signale limite absolue et bornes par niveau', async () => {
        const wrapper = mountPanel({
            key: 'modifier_strength_creature',
            name: 'Mod. Force',
            limitsDependOnLevel: true,
            limitsByLevel: [
                { level: 1, min: -2, max: 2 },
                { level: 2, min: -2, max: 3 },
            ],
            limitMin: -2,
            limitMax: 7,
            limitAbsolute: true,
        });
        await flushPromises();

        expect(wrapper.text()).toContain('limite absolue');
    });
});
