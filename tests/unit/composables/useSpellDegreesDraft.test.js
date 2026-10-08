import { describe, expect, it } from 'vitest';
import {
    resolveDegreeProperties,
    spellFallbackProperties,
    serializeEffectRowForApi,
} from '@/Composables/entity/useSpellDegreesDraft.js';

describe('useSpellDegreesDraft helpers', () => {
    it('resolveDegreeProperties own utilise le degré puis le sort', () => {
        const spell = spellFallbackProperties({ pa: '2', area: 'point' });
        const degrees = [
            {
                id: 1,
                properties_source: 'own',
                properties: { pa: '4', area: null },
            },
        ];
        const resolved = resolveDegreeProperties(degrees[0], degrees, spell);
        expect(resolved.pa).toBe('4');
        expect(resolved.pa_source).toBe('degree');
        expect(resolved.area).toBe('point');
        expect(resolved.area_source).toBe('spell');
    });

    it('resolveDegreeProperties previous remonte au degré précédent', () => {
        const spell = spellFallbackProperties({ pa: '1' });
        const degrees = [
            { id: 1, properties_source: 'own', properties: { pa: '5' } },
            { id: 2, properties_source: 'previous', properties: { pa: '9' } },
        ];
        const resolved = resolveDegreeProperties(degrees[1], degrees, spell);
        expect(resolved.pa).toBe('5');
        expect(resolved.pa_source).toBe('previous');
    });

    it('serializeEffectRowForApi inclut value_formula_crit', () => {
        const row = serializeEffectRowForApi(
            {
                sub_effect_id: 3,
                scope: 'combat',
                crit_only: true,
                duration_formula: '2',
                params: { value_formula: '1d6', value_formula_crit: '2d6' },
            },
            0,
        );
        expect(row.params.value_formula_crit).toBe('2d6');
        expect(row.crit_only).toBe(true);
    });

    it('serializeEffectRowForApi conserve dés, min/max et logic_group (données migrées)', () => {
        const row = serializeEffectRowForApi(
            {
                sub_effect_id: 8,
                scope: 'general',
                value_min: 12,
                value_max: 18,
                dice_num: 2,
                dice_side: 6,
                logic_group: 'A',
                logic_operator: 'OR',
                params: {},
            },
            1,
        );
        expect(row.value_min).toBe(12);
        expect(row.value_max).toBe(18);
        expect(row.dice_num).toBe(2);
        expect(row.dice_side).toBe(6);
        expect(row.logic_group).toBe('A');
        expect(row.logic_operator).toBe('OR');
        expect(row.params).toEqual({});
    });

    it('serializeEffectRowForApi ne convertit pas une magnitude vide en 0', () => {
        const row = serializeEffectRowForApi(
            {
                sub_effect_id: 1,
                value_min: '',
                dice_num: null,
                dice_side: undefined,
                logic_group: '  ',
            },
            0,
        );
        expect(row.value_min).toBeNull();
        expect(row.dice_num).toBeNull();
        expect(row.dice_side).toBeNull();
        expect(row.logic_group).toBeNull();
    });
});
