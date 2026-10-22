import { describe, expect, it, vi, afterEach } from 'vitest';
import { flushPromises, mount } from '@vue/test-utils';
import axios from 'axios';
import EffectGroupEditorForm from '@/Pages/Organismes/entity/EffectGroupEditorForm.vue';

const inertiaPatch = vi.fn();

vi.mock('axios', () => ({
    default: {
        patch: vi.fn(() => Promise.resolve({ data: { message: 'ok' } })),
    },
}));

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data) => ({
        ...data,
        processing: false,
        data: () => ({
            common: data.common,
            degrees: data.degrees,
        }),
        patch: inertiaPatch,
        delete: vi.fn(),
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

afterEach(() => {
    while (wrappers.length) {
        wrappers.pop().unmount();
    }
    vi.clearAllMocks();
});

function mountForm(extra = {}) {
    const wrapper = mount(EffectGroupEditorForm, {
        props: {
            patchUrl: '/entities/spells/7/effect-groups/3',
            selectedEffectId: 11,
            saveWithoutInertia: false,
            hideSubmitButton: true,
            groupEffects: [
                {
                    id: 11,
                    effect_id: 3,
                    name: 'Flamiche',
                    slug: 'flamiche-1',
                    description: '',
                    degree: 1,
                    target_type: 'direct',
                    area: 'point',
                    required_creature_level: 1,
                    sub_effects: [],
                },
            ],
            options: {
                sub_effects: [],
                scopes: [],
                characteristics: [],
                monsters: [],
                conditions: [],
            },
            ...extra,
        },
        global: {
            stubs: {
                InputField: true,
                SelectField: true,
                SelectSearchField: true,
                EntityPickerCore: true,
                AreaNotationEditor: true,
                Icon: true,
                Btn: true,
                EditActionDock: true,
            },
        },
    });
    wrappers.push(wrapper);
    return wrapper;
}

describe('EffectGroupEditorForm submitGroupAsync', () => {
    it('PATCH JSON même si saveWithoutInertia est false (pas de visite Inertia)', async () => {
        const wrapper = mountForm();
        await flushPromises();

        const result = await wrapper.vm.submitGroupAsync();
        expect(result).toEqual({ ok: true });
        expect(axios.patch).toHaveBeenCalledTimes(1);
        expect(axios.patch.mock.calls[0][0]).toBe('/entities/spells/7/effect-groups/3');
        expect(inertiaPatch).not.toHaveBeenCalled();
    });
});
