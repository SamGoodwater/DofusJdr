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

describe('SpellEditFormContent header', () => {
    it('branche EntityEditHeader et déclenche submit au clic Enregistrer', async () => {
        const submit = vi.fn();
        const EntityEditFormStub = defineComponent({
            name: 'EntityEditForm',
            setup(_, { expose, slots }) {
                expose({
                    submit,
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
                spellHolders: { monsters: [], npcs: [], breeds: [] },
            },
            global: {
                stubs: {
                    EntityEditForm: EntityEditFormStub,
                    SpellDegreesEditor: true,
                    SpellEffectsUnifiedSection: true,
                    EntityActions: true,
                    SpellHoldersPanel: true,
                    SpellViewText: {
                        template: '<span data-cy="spell-view-text">Flamiche</span>',
                    },
                    FormulaHelpHint: true,
                    EntityListBackButton: true,
                    EntityEditContainer: {
                        template: '<div><slot /></div>',
                    },
                    SelectField: true,
                    Btn: {
                        props: ['disabled'],
                        emits: ['click'],
                        template: '<button type="button" @click="$emit(\'click\')"><slot /></button>',
                    },
                },
            },
        });
        wrappers.push(wrapper);
        await flushPromises();

        expect(wrapper.find('[data-cy="spell-edit-header"]').exists()).toBe(true);
        expect(wrapper.find('[data-cy="spell-view-text"]').exists()).toBe(true);
        expect(wrapper.text()).toContain('Annuler');
        expect(wrapper.text()).toContain('Annuler les modifications');
        expect(wrapper.text()).toContain('Supprimer');
        expect(wrapper.text()).toContain('Enregistrer');
        expect(wrapper.text()).not.toContain('Afficher quand même');

        await wrapper.find('[data-cy="spell-edit-save"]').trigger('click');
        expect(submit).toHaveBeenCalled();
    });
});
