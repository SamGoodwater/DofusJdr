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
});
