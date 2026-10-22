import { describe, expect, it, vi, afterEach, beforeEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import { defineComponent, h } from 'vue';
import SpellEditFormContent from '@/Pages/Organismes/entity/SpellEditFormContent.vue';

vi.mock('@inertiajs/vue3', () => ({
    router: { visit: vi.fn(), delete: vi.fn() },
    useForm: (data) => ({
        ...data,
        processing: false,
        isDirty: false,
        errors: {},
        clearErrors: vi.fn(),
        data: () => ({ ...data }),
        put: vi.fn(),
        post: vi.fn(),
        patch: vi.fn(),
    }),
}));

vi.mock('@/Composables/permissions/usePermissions', () => ({
    usePermissions: () => ({
        canDeleteAny: () => true,
        isAdmin: { value: true },
    }),
}));

vi.mock('@/Composables/store/useNotificationStore', () => ({
    useNotificationStore: () => ({
        success: vi.fn(),
        error: vi.fn(),
        info: vi.fn(),
    }),
}));

/** @type {import('@vue/test-utils').VueWrapper[]} */
const wrappers = [];

beforeEach(() => {
    global.route = vi.fn((name, params) => `/${name}/${params ? Object.values(params).join('/') : ''}`);
});

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
});

describe('SpellEditFormContent save order', () => {
    it('flush les degrés natifs avant le groupe d’effets', async () => {
        const order = [];
        let beforeSubmitAsync = null;

        const EntityEditFormStub = defineComponent({
            name: 'EntityEditForm',
            props: {
                beforeSubmitAsync: { type: Function, default: null },
            },
            setup(props, { expose, slots }) {
                beforeSubmitAsync = () => props.beforeSubmitAsync?.();
                expose({
                    submit: vi.fn(),
                    resetForm: vi.fn(),
                    cancel: vi.fn(),
                    dispatchEntityAction: vi.fn(),
                    processing: { value: false },
                    primarySaveLabel: 'Enregistrer',
                    stateField: null,
                    form: {},
                    markDirty: vi.fn(),
                    getFieldLabel: () => 'État',
                    getFieldValidation: () => null,
                });
                return () => h('div', { 'data-cy': 'entity-edit-form-stub' }, slots['after-sections']?.());
            },
        });

        const SpellDegreesEditorStub = defineComponent({
            name: 'SpellDegreesEditor',
            setup(_, { expose }) {
                expose({
                    flushAll: vi.fn(async () => {
                        order.push('degrees');
                        return true;
                    }),
                    errorMessage: '',
                });
                return () => h('div', { 'data-cy': 'spell-degrees-editor-stub' });
            },
        });

        const SpellEffectsUnifiedSectionStub = defineComponent({
            name: 'SpellEffectsUnifiedSection',
            setup(_, { expose }) {
                expose({
                    flushEffectGroupSave: vi.fn(async () => {
                        order.push('groups');
                        return true;
                    }),
                    isDirty: { value: false },
                });
                return () => h('div', { 'data-cy': 'spell-effects-section-stub' });
            },
        });

        const wrapper = mount(SpellEditFormContent, {
            props: {
                spell: {
                    id: 7,
                    name: 'Flamiche',
                    state: 'draft',
                    category: 1,
                    spellTypes: [],
                },
                spellDegrees: { degrees: [], default_degree_id: null },
                spellEffectGroups: [
                    { label: 'Flamiche', anchor_effect_id: 3, group_effects: [{ id: 11 }] },
                ],
                spellHolders: { monsters: [], npcs: [], breeds: [] },
            },
            global: {
                stubs: {
                    EntityEditForm: EntityEditFormStub,
                    SpellDegreesEditor: SpellDegreesEditorStub,
                    SpellEffectsUnifiedSection: SpellEffectsUnifiedSectionStub,
                    EntityActions: true,
                    SpellHoldersPanel: true,
                    SpellViewText: { template: '<span>Flamiche</span>' },
                    FormulaHelpHint: true,
                    EntityListBackButton: true,
                    EntityEditContainer: { template: '<div><slot /></div>' },
                    Collapse: { template: '<div><slot name="content" /></div>' },
                    SelectField: true,
                    Btn: true,
                },
            },
        });
        wrappers.push(wrapper);
        await flushPromises();

        expect(typeof beforeSubmitAsync).toBe('function');
        await beforeSubmitAsync();
        expect(order).toEqual(['degrees', 'groups']);
    });
});
