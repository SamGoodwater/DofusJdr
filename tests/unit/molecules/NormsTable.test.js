import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import NormsTable from '@/Pages/Molecules/data-display/NormsTable.vue';

vi.mock('@/Pages/Atoms/feedback/Tooltip.vue', () => ({
    default: {
        name: 'Tooltip',
        props: ['content'],
        template: '<div data-test="tooltip" :data-content="content"><slot /></div>',
    },
}));

function makeGrid(value = 6) {
    return {
        very_weak: Array.from({ length: 20 }, () => value - 2),
        weak: Array.from({ length: 20 }, () => value - 1),
        neutral: Array.from({ length: 20 }, () => value),
        strong: Array.from({ length: 20 }, () => value + 1),
        very_strong: Array.from({ length: 20 }, () => value + 2),
    };
}

describe('NormsTable', () => {
    it('émet select-level au clic sur une cellule', async () => {
        const wrapper = mount(NormsTable, {
            props: {
                grid: makeGrid(),
                selectedLevel: null,
                effectiveLevelIndex: null,
            },
        });

        const cellBtn = wrapper.find('tbody button');
        expect(cellBtn.exists()).toBe(true);
        await cellBtn.trigger('click');
        expect(wrapper.emitted('select-level')?.[0]).toEqual([1]);
    });

    it('propose un tooltip dés sur la cellule', () => {
        const wrapper = mount(NormsTable, {
            props: {
                grid: makeGrid(6),
                diceTooltips: true,
            },
        });

        const tips = wrapper.findAll('[data-test="tooltip"]');
        expect(tips.length).toBeGreaterThan(0);
        const withSix = tips.find((t) => String(t.attributes('data-content') || '').includes('Moyenne 6'));
        expect(withSix).toBeTruthy();
        expect(withSix.attributes('data-content')).toContain('1d12 (−)');
        expect(withSix.attributes('data-content')).toContain('2d6 (=)');
        expect(withSix.attributes('data-content')).toContain('3d4 (+)');
    });

    it('affiche ±N quand les bornes sont signées', () => {
        const wrapper = mount(NormsTable, {
            props: {
                grid: makeGrid(3),
                minNumeric: -5,
                maxNumeric: 5,
                diceTooltips: false,
            },
        });

        const buttons = wrapper.findAll('tbody button');
        expect(buttons.some((b) => b.text() === '±3')).toBe(true);
        expect(buttons.some((b) => b.text() === '3')).toBe(false);
        expect(buttons[0].attributes('aria-label')).toContain('budget ±');
    });

    it('laisse 0 tel quel en mode budget ±', () => {
        const zeros = Array.from({ length: 20 }, () => 0);
        const grid = {
            very_weak: zeros,
            weak: zeros,
            neutral: zeros,
            strong: zeros,
            very_strong: zeros,
        };
        const wrapper = mount(NormsTable, {
            props: {
                grid,
                minNumeric: -5,
                maxNumeric: 5,
                diceTooltips: false,
            },
        });

        expect(wrapper.findAll('tbody button').every((b) => b.text() === '0')).toBe(true);
        expect(wrapper.findAll('tbody button').some((b) => b.text().startsWith('±'))).toBe(false);
    });
});
