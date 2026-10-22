import { describe, expect, it, vi, afterEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import axios from 'axios';
import SpellDegreesEditor from '@/Pages/Organismes/entity/SpellDegreesEditor.vue';

vi.mock('axios', () => ({
    default: {
        get: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        post: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        put: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        patch: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
        delete: vi.fn(() => Promise.resolve({ data: { data: { degrees: [], default_degree_id: null } } })),
    },
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
    vi.clearAllMocks();
});

const subEffects = [
    {
        id: 1,
        slug: 'frapper',
        type_slug: 'frapper',
        param_schema: {
            params: [
                { key: 'characteristic', categories: ['element'] },
                { key: 'value' },
                { key: 'life_steal_formula' },
            ],
        },
    },
];

const effectFormOptions = {
    sub_effects: subEffects,
    characteristics: [
        { key: 'fire', label: 'Feu', category: 'element' },
        { key: 'earth', label: 'Terre', category: 'element' },
    ],
    characteristics_object: [],
    scopes: [
        { value: 'general', label: 'Général' },
        { value: 'combat', label: 'Combat' },
    ],
};

function baseDegree(overrides = {}) {
    return {
        id: 10,
        position: 1,
        required_level: 1,
        inherits_effects: false,
        properties_source: 'own',
        properties: {
            pa: '3',
            po_min: '1',
            po_max: '6',
            sight_line: true,
            cast_per_turn: '1',
            number_between_two_cast: '2',
            casting_time: '0',
            global_cooldown: 0,
            area: 'point',
        },
        rows: [
            {
                sub_effect_id: 1,
                scope: 'general',
                crit_only: false,
                duration_formula: '1',
                logic_operator: '',
                params: {
                    characteristic: 'fire',
                    value_formula: '2d6',
                    value_formula_crit: '',
                    life_steal_formula: '',
                },
                sub_effect: subEffects[0],
            },
        ],
        ...overrides,
    };
}

function mountEditor(degrees = [baseDegree()], extra = {}) {
    const wrapper = mount(SpellDegreesEditor, {
        props: {
            spellId: 42,
            spell: { pa: '3', po_min: '0', po_max: '0', area: 'point' },
            spellDegrees: { degrees, default_degree_id: degrees[0]?.id ?? null },
            effectFormOptions,
            ...extra,
        },
        global: {
            stubs: {
                SpellEffectEditorRow: {
                    props: ['row', 'index', 'options'],
                    template:
                        '<div data-cy="spell-degree-effect-row" class="effect-stub">{{ row.sub_effect_id }}</div>',
                },
                SpellZonePreview: true,
            },
        },
    });
    wrappers.push(wrapper);
    return wrapper;
}

describe('SpellDegreesEditor', () => {
    it('affiche une portée compacte unique', async () => {
        const wrapper = mountEditor();
        await flushPromises();
        const po = wrapper.find('[data-cy="spell-cast-po-range"]');
        expect(po.exists()).toBe(true);
        expect(po.element.value).toBe('1-6');
        expect(wrapper.text()).toContain('PO 1-6');
    });

    it('expose les champs de fréquence dans la grille propriétés', async () => {
        const wrapper = mountEditor();
        await flushPromises();
        expect(wrapper.text()).toContain('Temps de relance');
        expect(wrapper.text()).toContain('Lancers / tour');
        expect(wrapper.text()).toContain('Lancers / cible');
        expect(wrapper.text()).toContain('Temps d’incantation');
        expect(wrapper.text()).not.toContain('Mode de résolution');
        expect(wrapper.text()).not.toContain('Utilisable en réaction');
        expect(wrapper.text()).not.toContain('Élément(s)');
        expect(wrapper.text()).not.toContain('Type de ciblage');
        expect(wrapper.text()).not.toContain('Rituel disponible');
        expect(wrapper.text()).toContain('Lancer en ligne');
        expect(wrapper.find('[data-cy="area-shape-picker"]').exists()).toBe(true);
    });

    it('préserve l’héritage des effets du degré précédent', async () => {
        const wrapper = mountEditor([
            baseDegree({ id: 1, position: 1, required_level: 1 }),
            baseDegree({
                id: 2,
                position: 2,
                required_level: 5,
                inherits_effects: true,
                rows: [],
            }),
        ]);
        await flushPromises();
        const tabs = wrapper.findAll('[role="tab"]');
        expect(tabs).toHaveLength(2);
        await tabs[1].trigger('click');
        expect(wrapper.text()).toContain('Reprendre les effets du degré précédent');
        expect(wrapper.text()).toContain('réutilise les effets');
    });

    it('change d’onglet sans confirmation même si dirty', async () => {
        const confirmSpy = vi.spyOn(globalThis, 'confirm').mockReturnValue(false);
        const wrapper = mountEditor([
            baseDegree({ id: 1 }),
            baseDegree({ id: 2, position: 2, required_level: 3 }),
        ]);
        await flushPromises();
        const po = wrapper.find('[data-cy="spell-cast-po-range"]');
        await po.setValue('2-8');
        await wrapper.findAll('[role="tab"]')[1].trigger('click');
        expect(confirmSpy).not.toHaveBeenCalled();
        expect(wrapper.findAll('[role="tab"]')[1].classes()).toContain('tab-active');
        confirmSpy.mockRestore();
    });

    it('flushSave envoie le PUT bulk lorsque dirty', async () => {
        axios.put.mockResolvedValueOnce({
            data: {
                data: {
                    degrees: [baseDegree({ properties: { pa: '4', po_min: '2', po_max: '8' } })],
                    default_degree_id: 10,
                },
            },
        });
        const wrapper = mountEditor();
        await flushPromises();
        await wrapper.find('[data-cy="spell-cast-po-range"]').setValue('2-8');
        const ok = await wrapper.vm.flushSave();
        expect(ok).toBe(true);
        expect(axios.put).toHaveBeenCalled();
        const body = axios.put.mock.calls[0][1];
        expect(body.degrees[0].po_min).toBe('2');
        expect(body.degrees[0].po_max).toBe('8');
    });

    it('un nouveau snapshot spellDegrees (visite Inertia) remet dirty à false sans PUT', async () => {
        const degrees = [baseDegree()];
        const wrapper = mountEditor(degrees);
        await flushPromises();
        await wrapper.find('[data-cy="spell-cast-po-range"]').setValue('2-8');
        await wrapper.setProps({
            spellDegrees: {
                degrees: [{ ...degrees[0] }],
                default_degree_id: degrees[0].id,
            },
        });
        await flushPromises();
        const ok = await wrapper.vm.flushSave();
        expect(ok).toBe(true);
        expect(axios.put).not.toHaveBeenCalled();
    });

    it('affiche un badge hérité quand properties_source = spell', async () => {
        const wrapper = mountEditor([
            baseDegree({
                properties_source: 'spell',
                properties: { pa: '9', po_min: '9', po_max: '9' },
            }),
        ], {
            spell: { pa: '3', po_min: '1', po_max: '2', area: 'circle-0-1' },
        });
        await flushPromises();
        expect(wrapper.text()).toContain('Hérité du sort de base');
        expect(wrapper.find('[data-cy="spell-cast-po-range"]').exists()).toBe(false);
        expect(wrapper.text()).toContain('3');
    });
});
