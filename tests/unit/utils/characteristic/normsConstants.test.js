import { describe, it, expect } from 'vitest';
import {
    DEFAULT_POWER_COEFFICIENTS,
    POWER_COEFFICIENT_KEYS,
    POWER_LEVELS,
} from '@/Utils/Characteristic/normsConstants';

describe('normsConstants — coefficients de palier', () => {
    it('expose les 4 coefficients éditables et les défauts attendus', () => {
        expect(POWER_COEFFICIENT_KEYS).toEqual(['very_weak', 'weak', 'strong', 'very_strong']);
        expect(DEFAULT_POWER_COEFFICIENTS).toEqual({
            very_weak: 0.5,
            weak: 0.75,
            strong: 1.3,
            very_strong: 1.7,
        });
        expect(POWER_LEVELS).toContain('neutral');
        expect(POWER_COEFFICIENT_KEYS).not.toContain('neutral');
    });
});
