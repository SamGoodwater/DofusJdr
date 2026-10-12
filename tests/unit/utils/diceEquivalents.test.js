// @vitest-environment node
import { describe, expect, it } from 'vitest';
import {
    formatDiceSummary,
    suggestDice,
    toCriticalFormula,
    typicalModifier,
} from '../../../resources/js/Utils/dice/diceEquivalents.js';

describe('typicalModifier', () => {
    it('suit ⌊niv/4⌋+1', () => {
        expect(typicalModifier(1)).toBe(1);
        expect(typicalModifier(4)).toBe(2);
        expect(typicalModifier(8)).toBe(3);
        expect(typicalModifier(20)).toBe(6);
    });
});

describe('toCriticalFormula', () => {
    it('double les dés et conserve le fixe', () => {
        expect(toCriticalFormula('2d6+5')).toBe('4d6+5');
        expect(toCriticalFormula('1d12')).toBe('2d12');
        expect(toCriticalFormula('1d4+2d6')).toBe('2d4+4d6');
    });
});

describe('suggestDice', () => {
    it('propose bas / moy / haut pour 6 (ex. plan)', () => {
        const s = suggestDice(6);
        expect(s.high?.formula).toBe('1d12');
        expect(s.mid?.formula).toBe('2d6');
        expect(s.low?.formula).toBe('3d4');
        expect(s.diceTarget).toBe(6);
    });

    it('soustrait le modificateur avant de chercher les dés', () => {
        const s = suggestDice(5, { mod: 1 });
        expect(s.diceTarget).toBe(4);
        expect(s.high?.formula).toMatch(/d/);
    });

    it('propose des alternatives mixtes ou avec fixe', () => {
        const s = suggestDice(10);
        expect(s.withFixed || s.mixed).toBeTruthy();
        if (s.withFixed) {
            expect(s.withFixed.formula).toMatch(/\+/);
            expect(s.withFixed.criticalFormula).toBe(toCriticalFormula(s.withFixed.formula));
        }
    });

    it('formatte un résumé lisible', () => {
        expect(formatDiceSummary(6)).toContain('1d12');
        expect(formatDiceSummary(6)).toContain('2d6');
        expect(formatDiceSummary(6)).toContain('3d4');
    });
});
