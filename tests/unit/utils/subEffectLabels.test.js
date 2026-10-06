// @vitest-environment node
import { describe, expect, it } from 'vitest';
import {
    formatSubEffectSelectLabel,
    getSubEffectActionLabel,
} from '../../../resources/js/Utils/Entity/subEffectLabels.js';

describe('getSubEffectActionLabel', () => {
    it('retourne le libellé français connu', () => {
        expect(getSubEffectActionLabel('frapper')).toBe('Frapper');
        expect(getSubEffectActionLabel('soigner')).toBe('Soigner');
        expect(getSubEffectActionLabel('appliquer-etat')).toBe('Appliquer un état');
    });

    it('repli sur le slug si inconnu', () => {
        expect(getSubEffectActionLabel('action-inconnue')).toBe('action-inconnue');
    });
});

describe('formatSubEffectSelectLabel', () => {
    it('affiche le français et pas le slug seul pour une action connue', () => {
        const label = formatSubEffectSelectLabel({ id: 1, slug: 'frapper' });
        expect(label).toBe('Frapper');
        expect(label).not.toBe('frapper');
    });

    it('repli sur le slug si non listé', () => {
        expect(formatSubEffectSelectLabel({ id: 9, slug: 'custom-slug' })).toBe('custom-slug');
    });

    it('repli sur l’id si slug vide', () => {
        expect(formatSubEffectSelectLabel({ id: 42, slug: '' })).toBe('Sous-effet #42');
    });
});
