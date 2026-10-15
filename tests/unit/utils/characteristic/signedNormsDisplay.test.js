import { describe, it, expect } from 'vitest';
import {
    formatSignedBudgetCell,
    isSignedBudgetDisplay,
    signedBudgetAriaLabel,
} from '@/Utils/Characteristic/signedNormsDisplay.js';

describe('signedNormsDisplay', () => {
    it('détecte un budget signé', () => {
        expect(isSignedBudgetDisplay(-5, 5)).toBe(true);
        expect(isSignedBudgetDisplay(0, 80)).toBe(false);
        expect(isSignedBudgetDisplay(null, 5)).toBe(false);
    });

    it('formate ±N et laisse 0', () => {
        expect(formatSignedBudgetCell(3, true)).toBe('±3');
        expect(formatSignedBudgetCell(0, true)).toBe('0');
        expect(formatSignedBudgetCell(3, false)).toBe('3');
    });

    it('construit un aria-label explicite', () => {
        expect(signedBudgetAriaLabel(4, 'Neutre', 3, true)).toContain('bonus +3 ou malus −3');
        expect(signedBudgetAriaLabel(4, 'Neutre', 3, false)).toBe('Niveau 4, Neutre : 3');
    });
});
