import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import CharacteristicGmPanel from '@/Pages/Organismes/data-display/CharacteristicGmPanel.vue';

vi.mock('axios', () => ({
    default: {
        get: vi.fn(),
    },
}));

vi.mock('@/Composables/characteristic/useCharacteristicCard', () => ({
    loadCharacteristicCard: vi.fn(async () => ({
        key: 'dommages_spell',
        name: 'Dégâts',
        group: 'spell',
        entity: '*',
        nature: 'action_value',
        formula: '[x]',
        formula_display: 'coef × PV',
        conversion_formula: null,
    })),
}));

vi.mock('@/Pages/Organismes/data-display/NormsViewer.vue', () => ({
    default: { name: 'NormsViewer', template: '<div data-test="norms" />' },
}));

vi.mock('@/Pages/Molecules/data-display/CharacteristicFormulaRichText.vue', () => ({
    default: { name: 'CharacteristicFormulaRichText', template: '<div data-test="formula" />' },
}));

vi.mock('@/Pages/Molecules/data-display/CharacteristicDecompositionBody.vue', () => ({
    default: { name: 'CharacteristicDecompositionBody', template: '<div data-test="decomp" />' },
}));

vi.mock('@/Pages/Admin/characteristics/FormulaChart.vue', () => ({
    default: { name: 'FormulaChart', template: '<div data-test="chart" />' },
}));

import axios from 'axios';
import { loadCharacteristicCard } from '@/Composables/characteristic/useCharacteristicCard';

describe('CharacteristicGmPanel', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        axios.get.mockResolvedValue({
            data: {
                characteristic: { name: 'Dégâts', key: 'dommages_spell' },
                norms: { grid: { neutral: Array.from({ length: 20 }, (_, i) => i + 1) } },
            },
        });
        global.route = vi.fn((name, params) => {
            if (name === 'api.characteristics.norms') {
                return `/api/characteristics/${params.key}/norms/${params.entity}`;
            }
            return `/${name}`;
        });
    });

    it('affiche les onglets Barème, Calcul et Propriétés pour un sort', async () => {
        const wrapper = mount(CharacteristicGmPanel, {
            props: {
                characteristicKey: 'dommages_spell',
                group: 'spell',
                entity: '*',
            },
        });
        await flushPromises();

        expect(loadCharacteristicCard).toHaveBeenCalled();
        const text = wrapper.text();
        expect(text).toContain('Barème');
        expect(text).toContain('Calcul');
        expect(text).toContain('Propriétés');
        expect(text).not.toContain('Conversion');
        expect(wrapper.find('[data-test="norms"]').exists()).toBe(true);
    });

    it('masque Conversion si absente et limite les onglets via props.tabs', async () => {
        const wrapper = mount(CharacteristicGmPanel, {
            props: {
                characteristicKey: 'dommages_spell',
                group: 'spell',
                tabs: ['bareme', 'proprietes'],
            },
        });
        await flushPromises();

        expect(wrapper.text()).toContain('Barème');
        expect(wrapper.text()).toContain('Propriétés');
        expect(wrapper.text()).not.toContain('Calcul');
    });

    it('montre la décomposition dans Calcul si runtimeModel fourni', async () => {
        const wrapper = mount(CharacteristicGmPanel, {
            props: {
                characteristicKey: 'initiative_creature',
                group: 'creature',
                tabs: ['calcul'],
                runtimeModel: {
                    key: 'initiative_creature',
                    name: 'Initiative',
                    layers: { base: 1 },
                },
            },
        });
        await flushPromises();

        const calculBtn = wrapper.findAll('button').find((b) => b.text().includes('Calcul'));
        if (calculBtn) {
            await calculBtn.trigger('click');
            await flushPromises();
        }
        expect(wrapper.find('[data-test="decomp"]').exists()).toBe(true);
    });
});
