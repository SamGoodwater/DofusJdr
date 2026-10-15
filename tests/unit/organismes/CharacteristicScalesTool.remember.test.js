import { describe, it, expect, beforeEach, vi } from 'vitest';
import { mount, flushPromises } from '@vue/test-utils';
import CharacteristicScalesTool from '@/Pages/Organismes/tools/CharacteristicScalesTool.vue';

const STORAGE_KEY = 'krosmoz-characteristic-scales-selection';

vi.mock('axios', () => ({
    default: {
        get: vi.fn(async () => ({
            data: {
                items: [
                    { key: 'acrobatics_consumable', name: 'Acrobaties', group: 'consumable' },
                    { key: 'dommages_spell', name: 'Dégâts', group: 'spell' },
                ],
            },
        })),
    },
}));

vi.mock('@/Pages/Organismes/data-display/CharacteristicGmPanel.vue', () => ({
    default: {
        name: 'CharacteristicGmPanel',
        template: '<div data-test="gm" />',
        props: ['characteristicKey', 'group', 'entity', 'tabs'],
    },
}));

describe('CharacteristicScalesTool — mémorisation', () => {
    beforeEach(() => {
        localStorage.clear();
        global.route = vi.fn(() => '/api/characteristics/norms-catalog/all');
    });

    it('restaure la dernière sélection depuis localStorage', async () => {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                filterGroup: 'consumable',
                key: 'acrobatics_consumable',
                itemGroup: 'consumable',
                search: '',
            }),
        );

        const wrapper = mount(CharacteristicScalesTool, {
            props: { initialGroup: 'all', initialKey: '' },
        });
        await flushPromises();

        expect(wrapper.find('select').element.value).toBe('consumable');
        expect(wrapper.find('[data-scales-selected="true"]').text()).toContain('Acrobaties');
    });

    it('enregistre la sélection au clic', async () => {
        const wrapper = mount(CharacteristicScalesTool, {
            props: { initialGroup: 'all', initialKey: '' },
        });
        await flushPromises();

        const buttons = wrapper.findAll('button').filter((b) => b.text().includes('Dégâts'));
        await buttons[0].trigger('click');
        await flushPromises();

        const stored = JSON.parse(localStorage.getItem(STORAGE_KEY));
        expect(stored.key).toBe('dommages_spell');
        expect(stored.itemGroup).toBe('spell');
    });

    it('n’ouvre pas l’onglet Calcul (le barème suffit)', async () => {
        const wrapper = mount(CharacteristicScalesTool, {
            props: { initialGroup: 'all', initialKey: '' },
        });
        await flushPromises();

        const panel = wrapper.findComponent({ name: 'CharacteristicGmPanel' });
        expect(panel.exists()).toBe(true);
        expect(panel.props('tabs')).toEqual(['bareme', 'conversion', 'proprietes']);
        expect(wrapper.text()).not.toContain('Extras sort');
    });

    it('ne restaure pas si une clé CMS est forcée', async () => {
        localStorage.setItem(
            STORAGE_KEY,
            JSON.stringify({
                filterGroup: 'consumable',
                key: 'acrobatics_consumable',
                itemGroup: 'consumable',
            }),
        );

        const wrapper = mount(CharacteristicScalesTool, {
            props: { initialGroup: 'spell', initialKey: 'dommages_spell' },
        });
        await flushPromises();

        expect(wrapper.find('[data-scales-selected="true"]').text()).toContain('Dégâts');
    });
});
