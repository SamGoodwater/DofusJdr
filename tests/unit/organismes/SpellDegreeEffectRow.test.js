import { describe, expect, it, vi, afterEach } from 'vitest';
import { mount } from '@vue/test-utils';
import SpellDegreeEffectRow from '@/Pages/Organismes/entity/SpellEffectEditorRow.vue';

vi.mock('@/Composables/store/useNotificationStore', () => ({
    useNotificationStore: () => ({
        success: vi.fn(),
        error: vi.fn(),
    }),
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
});

const options = {
    sub_effects: [
        {
            id: 1,
            slug: 'frapper',
            param_schema: {
                params: [
                    { key: 'characteristic', categories: ['element'], label: 'Élément' },
                    { key: 'value' },
                    { key: 'life_steal_formula' },
                ],
            },
        },
        {
            id: 2,
            slug: 'booster',
            param_schema: {
                params: [{ key: 'characteristic', label: 'Caractéristique' }, { key: 'value' }],
            },
        },
        {
            id: 3,
            slug: 'invoquer',
            param_schema: { params: [{ key: 'monster' }] },
        },
        {
            id: 4,
            slug: 'appliquer-etat',
            param_schema: { params: [{ key: 'condition' }] },
        },
    ],
    characteristics: [
        { key: 'fire', label: 'Feu', category: 'element' },
        { key: 'earth', label: 'Terre', category: 'element' },
        { key: 'agi', label: 'Agilité', category: 'stat' },
    ],
    characteristics_spell_buff: [
        { key: 'action_points_spell', label: 'PA', category: 'resource' },
        { key: 'vitality_spell', label: 'Vitalité', category: 'stat' },
    ],
    characteristics_object: [],
    scopes: [{ value: 'general', label: 'Général' }],
};

function makeRow(subEffectId, params = {}) {
    return {
        sub_effect_id: subEffectId,
        scope: 'general',
        crit_only: false,
        duration_formula: '2',
        logic_operator: 'AND',
        logic_condition: '',
        params: {
            characteristic: '',
            value_formula: '1d6',
            value_formula_crit: '',
            life_steal_formula: '',
            monster_id: '',
            condition_id: '',
            condition_name: '',
            dispellable: false,
            cells_formula: '',
            movement_kind: 'movement',
            teleport: false,
            ...params,
        },
        sub_effect: options.sub_effects.find((s) => s.id === subEffectId),
        _editor_condition_q: '',
    };
}

function mountRow(row, index = 0) {
    const wrapper = mount(SpellDegreeEffectRow, {
        props: { row, index, options },
        global: {
            stubs: {
                EffectContextCharacteristic: {
                    props: ['label', 'options', 'modelValue'],
                    template:
                        '<div data-cy="char-select">{{ label }}|{{ (options || []).map(o => o.value || o.key).join(",") }}</div>',
                },
                SelectSearchField: true,
                EntityPickerCore: {
                    props: ['entityType'],
                    template: '<div data-cy="entity-picker">{{ entityType }}</div>',
                },
            },
        },
    });
    wrappers.push(wrapper);
    return wrapper;
}

describe('SpellDegreeEffectRow', () => {
    it('limite le sélecteur élément pour frapper', () => {
        const wrapper = mountRow(makeRow(1, { characteristic: 'fire' }));
        expect(wrapper.find('[data-cy="char-select"]').text()).toContain('Élément');
        expect(wrapper.find('[data-cy="char-select"]').text()).toContain('fire,earth');
        expect(wrapper.find('[data-cy="char-select"]').text()).not.toContain('agi');
        expect(wrapper.text()).toContain('Vol de vie');
        expect(wrapper.text()).toContain('Durée de l’effet');
        expect(wrapper.text()).toContain('Valeur critique');
        expect(wrapper.text()).toContain('Portée de jeu');
        expect(wrapper.text()).not.toContain('Avancé');
    });

    it('utilise characteristics_spell_buff pour booster', () => {
        const wrapper = mountRow(makeRow(2, { characteristic: 'vitality_spell' }));
        expect(wrapper.find('[data-cy="char-select"]').text()).toContain('Caractéristique');
        expect(wrapper.find('[data-cy="char-select"]').text()).toContain('action_points_spell');
        expect(wrapper.find('[data-cy="char-select"]').text()).not.toContain('fire');
    });

    it('affiche le picker créature pour invoquer', () => {
        const wrapper = mountRow(makeRow(3, { monster_id: 9 }));
        expect(wrapper.find('[data-cy="entity-picker"]').text()).toBe('creatures');
        expect(wrapper.find('[data-cy="char-select"]').exists()).toBe(false);
    });

    it('affiche recherche d’état et dissipable pour appliquer-etat', () => {
        const wrapper = mountRow(makeRow(4, { condition_id: 5, condition_name: 'Empoisonné' }));
        expect(wrapper.find('[data-cy="entity-picker"]').text()).toBe('conditions');
        expect(wrapper.text()).toContain('Dissipable');
        expect(wrapper.text()).toContain('Créer');
    });
});
