/**
 * @vitest-environment node
 */
import { describe, it, expect, beforeEach, vi } from 'vitest';
import { nextTick, ref } from 'vue';
import axios from 'axios';
import {
    loadCharacteristicCard,
    mergeCardIntoViewModel,
    normalizeCharacteristicCardGroup,
    useCharacteristicCard,
} from '@/Composables/characteristic/useCharacteristicCard.js';

vi.mock('axios', () => ({
    default: {
        get: vi.fn(),
    },
}));

describe('normalizeCharacteristicCardGroup', () => {
    it('accepte les cinq groupes API', () => {
        expect(normalizeCharacteristicCardGroup('creature')).toBe('creature');
        expect(normalizeCharacteristicCardGroup('object')).toBe('object');
        expect(normalizeCharacteristicCardGroup('spell')).toBe('spell');
        expect(normalizeCharacteristicCardGroup('consumable')).toBe('consumable');
        expect(normalizeCharacteristicCardGroup('profile')).toBe('profile');
    });

    it('mappe les alias UI', () => {
        expect(normalizeCharacteristicCardGroup('item')).toBe('object');
        expect(normalizeCharacteristicCardGroup('capability')).toBe('spell');
        expect(normalizeCharacteristicCardGroup('breed')).toBe('profile');
        expect(normalizeCharacteristicCardGroup('monster')).toBe('creature');
    });

    it('renvoie null pour un groupe inconnu', () => {
        expect(normalizeCharacteristicCardGroup('foo')).toBeNull();
        expect(normalizeCharacteristicCardGroup('')).toBeNull();
    });
});

describe('mergeCardIntoViewModel', () => {
    it('enrichit le viewModel avec formula_display et limites', () => {
        const merged = mergeCardIntoViewModel(
            { key: 'initiative_creature', name: 'Ancien' },
            {
                name: 'Initiative',
                formula_display: 'mod. Int + équip.',
                default_value: '0',
                limit_min: -10,
                limit_max: 20,
                limit_absolute: true,
                limits_depend_on_level: false,
                descriptions: 'Ordre de jeu',
            },
        );

        expect(merged.name).toBe('Initiative');
        expect(merged.formulaDisplay).toBe('mod. Int + équip.');
        expect(merged.defaultValue).toBe('0');
        expect(merged.limitMin).toBe(-10);
        expect(merged.limitMax).toBe(20);
        expect(merged.limitAbsolute).toBe(true);
        expect(merged.descriptions).toBe('Ordre de jeu');
    });

    it('conserve le viewModel si la fiche est absente', () => {
        const merged = mergeCardIntoViewModel({ key: 'x', name: 'X' }, null);
        expect(merged).toEqual({ key: 'x', name: 'X' });
    });
});

describe('loadCharacteristicCard / useCharacteristicCard', () => {
    beforeEach(() => {
        vi.clearAllMocks();
        // Réinitialise le cache module en rechargeant via un nouvel appel à une clé unique
        global.route = vi.fn((name, params) => `/api/characteristics/${params.key}/card`);
    });

    it('charge et met en cache la fiche', async () => {
        axios.get.mockResolvedValue({
            data: {
                card: {
                    key: 'cache_test_creature',
                    name: 'Cache',
                    formula_display: '1',
                },
            },
        });

        const first = await loadCharacteristicCard('cache_test_creature', { group: 'creature' });
        const second = await loadCharacteristicCard('cache_test_creature', { group: 'creature' });

        expect(first.name).toBe('Cache');
        expect(second).toBe(first);
        expect(axios.get).toHaveBeenCalledTimes(1);
        expect(global.route).toHaveBeenCalledWith('api.characteristics.card', {
            key: 'cache_test_creature',
        });
    });

    it('expose un enrichedViewModel via le composable', async () => {
        axios.get.mockResolvedValue({
            data: {
                card: {
                    key: 'vm_test_spell',
                    name: 'Dégâts',
                    formula_display: 'coef × PV',
                    group: 'spell',
                },
            },
        });

        const options = ref({
            key: 'vm_test_spell',
            group: 'spell',
            viewModel: { key: 'vm_test_spell', value: 12 },
        });

        const { enrichedViewModel, loading } = useCharacteristicCard(options);
        await nextTick();
        // attendre la fin du chargement async
        await vi.waitFor(() => expect(loading.value).toBe(false));

        expect(enrichedViewModel.value.name).toBe('Dégâts');
        expect(enrichedViewModel.value.formulaDisplay).toBe('coef × PV');
        expect(enrichedViewModel.value.value).toBe(12);
    });
});
