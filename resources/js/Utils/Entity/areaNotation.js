/**
 * Validation de la notation de zone Krosmoz (alignée avec App\Support\AreaNotation).
 *
 * @see docs/features/effects/README.md
 */

/** Message d’aide pour les champs zone (UI). */
export const AREA_NOTATION_HELP =
    'Formes : point ; line-1xL (L≥1) ; cross-a-b et circle-a-b (a≤b) ; rect-WxH (W,H≥1) ; shape-ID ou shape-ID-p1-p2 (ID≥1).';

/**
 * Chaîne vide ou uniquement des espaces = valide (champ optionnel).
 *
 * @param {string|null|undefined} area
 * @returns {boolean}
 */
export function isValidAreaNotation(area) {
    if (area == null) return true;
    if (typeof area !== 'string') return false;
    const s = area.trim();
    if (s === '') return true;
    if (s.length > 64) return false;

    if (s === 'point') return true;

    let m = /^line-1x(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    m = /^cross-(\d+)-(\d+)$/.exec(s);
    if (m) {
        const a = parseInt(m[1], 10);
        const b = parseInt(m[2], 10);
        return a <= b;
    }

    m = /^circle-(\d+)-(\d+)$/.exec(s);
    if (m) {
        const a = parseInt(m[1], 10);
        const b = parseInt(m[2], 10);
        return a <= b;
    }

    m = /^rect-(\d+)x(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1 && parseInt(m[2], 10) >= 1;

    m = /^shape-(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    m = /^shape-(\d+)-(\d+)-(\d+)$/.exec(s);
    if (m) return parseInt(m[1], 10) >= 1;

    return false;
}

/**
 * @param {string|null|undefined} raw
 * @returns {string}
 */
export function normalizeAreaInput(raw) {
    if (raw == null || typeof raw !== 'string') return '';
    return raw.trim();
}

/**
 * Décompose une notation en forme + paramètres numériques (ou mode shape brut).
 *
 * @param {string|null|undefined} area
 * @returns {{
 *   mode: 'empty'|'known'|'shape'|'invalid',
 *   shape: string|null,
 *   a: number|null,
 *   b: number|null,
 *   raw: string
 * }}
 *
 * @example
 * parseAreaNotationParts('circle-0-2')
 * // → { mode: 'known', shape: 'circle', a: 0, b: 2, raw: 'circle-0-2' }
 */
export function parseAreaNotationParts(area) {
    const raw = normalizeAreaInput(area);
    if (raw === '') {
        return { mode: 'empty', shape: null, a: null, b: null, raw: '' };
    }
    if (raw === 'point') {
        return { mode: 'known', shape: 'point', a: null, b: null, raw };
    }
    let m = /^line-1x(\d+)$/.exec(raw);
    if (m) {
        return { mode: 'known', shape: 'line', a: parseInt(m[1], 10), b: null, raw };
    }
    m = /^cross-(\d+)-(\d+)$/.exec(raw);
    if (m) {
        return {
            mode: 'known',
            shape: 'cross',
            a: parseInt(m[1], 10),
            b: parseInt(m[2], 10),
            raw,
        };
    }
    m = /^circle-(\d+)-(\d+)$/.exec(raw);
    if (m) {
        return {
            mode: 'known',
            shape: 'circle',
            a: parseInt(m[1], 10),
            b: parseInt(m[2], 10),
            raw,
        };
    }
    m = /^rect-(\d+)x(\d+)$/.exec(raw);
    if (m) {
        return {
            mode: 'known',
            shape: 'rect',
            a: parseInt(m[1], 10),
            b: parseInt(m[2], 10),
            raw,
        };
    }
    if (/^shape-\d+(-\d+-\d+)?$/.test(raw)) {
        return { mode: 'shape', shape: null, a: null, b: null, raw };
    }
    return { mode: 'invalid', shape: null, a: null, b: null, raw };
}

/**
 * Construit la notation à partir d’une forme et de 0–2 paramètres.
 *
 * @param {string} shape - point | line | cross | circle | rect
 * @param {number|string|null|undefined} a
 * @param {number|string|null|undefined} b
 * @returns {string}
 *
 * @example
 * buildAreaNotation('line', 3)
 * // → 'line-1x3'
 */
export function buildAreaNotation(shape, a = null, b = null) {
    const s = String(shape || '').trim();
    if (s === 'point') return 'point';
    const nA = a === '' || a == null ? null : Number(a);
    const nB = b === '' || b == null ? null : Number(b);
    if (s === 'line') {
        if (nA == null || !Number.isFinite(nA) || nA < 1) return '';
        return `line-1x${Math.trunc(nA)}`;
    }
    if (s === 'cross' || s === 'circle') {
        if (nA == null || nB == null || !Number.isFinite(nA) || !Number.isFinite(nB)) return '';
        return `${s}-${Math.trunc(nA)}-${Math.trunc(nB)}`;
    }
    if (s === 'rect') {
        if (nA == null || nB == null || !Number.isFinite(nA) || !Number.isFinite(nB)) return '';
        if (nA < 1 || nB < 1) return '';
        return `rect-${Math.trunc(nA)}x${Math.trunc(nB)}`;
    }
    return '';
}

/**
 * Phrase d’aide selon la forme choisie.
 *
 * @param {string|null|undefined} shape
 * @returns {string}
 */
export function getAreaShapeEditorHelp(shape) {
    switch (String(shape || '')) {
        case 'point':
            return 'Une seule case : rien à saisir.';
        case 'line':
            return 'Longueur de la ligne en cases (ex. 3 → line-1x3).';
        case 'cross':
            return 'Case la plus proche, puis la plus lointaine (ex. 1 et 2 → cross-1-2).';
        case 'circle':
            return 'Rayon intérieur, puis rayon extérieur (ex. 0 et 2 → circle-0-2).';
        case 'rect':
            return 'Largeur puis hauteur en cases (ex. 2 et 3 → rect-2x3).';
        default:
            return AREA_NOTATION_HELP;
    }
}
