// @vitest-environment node
import { describe, expect, it } from 'vitest';
import {
    buildAreaNotation,
    getAreaShapeEditorHelp,
    isValidAreaNotation,
    parseAreaNotationParts,
} from '../../../resources/js/Utils/Entity/areaNotation.js';

describe('isValidAreaNotation', () => {
    it('accepte vide ou null', () => {
        expect(isValidAreaNotation(null)).toBe(true);
        expect(isValidAreaNotation('')).toBe(true);
        expect(isValidAreaNotation('   ')).toBe(true);
    });

    it('accepte les formes documentées', () => {
        expect(isValidAreaNotation('point')).toBe(true);
        expect(isValidAreaNotation('line-1x1')).toBe(true);
        expect(isValidAreaNotation('line-1x99')).toBe(true);
        expect(isValidAreaNotation('cross-0-2')).toBe(true);
        expect(isValidAreaNotation('cross-1-2')).toBe(true);
        expect(isValidAreaNotation('circle-0-2')).toBe(true);
        expect(isValidAreaNotation('circle-2-2')).toBe(true);
        expect(isValidAreaNotation('rect-1x1')).toBe(true);
        expect(isValidAreaNotation('rect-3x4')).toBe(true);
        expect(isValidAreaNotation('shape-99')).toBe(true);
        expect(isValidAreaNotation('shape-12-0-5')).toBe(true);
    });

    it('refuse les notations invalides', () => {
        expect(isValidAreaNotation('foo')).toBe(false);
        expect(isValidAreaNotation('line-2x3')).toBe(false);
        expect(isValidAreaNotation('line-1x0')).toBe(false);
        expect(isValidAreaNotation('cross-2-1')).toBe(false);
        expect(isValidAreaNotation('circle-3-1')).toBe(false);
        expect(isValidAreaNotation('rect-0x1')).toBe(false);
        expect(isValidAreaNotation('shape-0')).toBe(false);
        expect(isValidAreaNotation('shape-1-2')).toBe(false);
        expect(isValidAreaNotation('point-1')).toBe(false);
    });
});

describe('parseAreaNotationParts / buildAreaNotation', () => {
    it('lit et reconstruit line-1x3', () => {
        expect(parseAreaNotationParts('line-1x3')).toEqual({
            mode: 'known',
            shape: 'line',
            a: 3,
            b: null,
            raw: 'line-1x3',
        });
        expect(buildAreaNotation('line', 3)).toBe('line-1x3');
    });

    it('lit et reconstruit circle-0-2', () => {
        expect(parseAreaNotationParts('circle-0-2')).toEqual({
            mode: 'known',
            shape: 'circle',
            a: 0,
            b: 2,
            raw: 'circle-0-2',
        });
        expect(buildAreaNotation('circle', 0, 2)).toBe('circle-0-2');
    });

    it('conserve shape-43-1-0 en mode texte brut', () => {
        expect(parseAreaNotationParts('shape-43-1-0')).toEqual({
            mode: 'shape',
            shape: null,
            a: null,
            b: null,
            raw: 'shape-43-1-0',
        });
        expect(isValidAreaNotation('shape-43-1-0')).toBe(true);
    });

    it('construit cross et rect', () => {
        expect(buildAreaNotation('cross', 1, 2)).toBe('cross-1-2');
        expect(buildAreaNotation('rect', 2, 3)).toBe('rect-2x3');
        expect(buildAreaNotation('point')).toBe('point');
    });
});

describe('getAreaShapeEditorHelp', () => {
    it('donne une phrase par forme connue', () => {
        expect(getAreaShapeEditorHelp('point')).toContain('rien à saisir');
        expect(getAreaShapeEditorHelp('line')).toContain('Longueur');
        expect(getAreaShapeEditorHelp('cross')).toContain('proche');
        expect(getAreaShapeEditorHelp('circle')).toContain('Rayon');
        expect(getAreaShapeEditorHelp('rect')).toContain('Largeur');
    });
});
