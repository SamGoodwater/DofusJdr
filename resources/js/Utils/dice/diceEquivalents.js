/**
 * Conversion d’une valeur moyenne vers des formules de dés classiques.
 *
 * @description
 * Propose des équivalents en d2, d4, d6, d8, d10, d12 pour une valeur cible
 * (typiquement un budget de sort moins le modificateur de caractéristique).
 * Classes de variance : 1 dé (haute), 2 dés (moyenne), 3+ dés (basse),
 * plus alternatives mixtes et dés + fixe. Critique = dés doublés, fixe inchangé.
 *
 * @example
 * suggestDice(6)
 * // { high: { formula: '1d12', average: 6.5, ... }, mid: { formula: '2d6', ... }, ... }
 */

import { parseDiceFormula } from '@/Utils/dice/diceParser.js';

/** Faces autorisées (d2 = pièce). */
export const CLASSIC_DICE_SIDES = [2, 4, 6, 8, 10, 12];

/** Écart max pour lister une formule dans `all` / alternatives. */
export const AVERAGE_TOLERANCE = 1.05;

/** Nombre max de dés d’un même type dans une proposition pure. */
const MAX_SAME_DICE = 8;

/** Modificateur typique de combat : ⌊niv/4⌋+1. */
export function typicalModifier(level) {
    const lvl = Math.max(1, Math.min(20, Math.trunc(level) || 1));
    return Math.floor(lvl / 4) + 1;
}

/**
 * @param {number} n
 * @param {number} sides
 * @returns {number}
 */
function diceAverage(n, sides) {
    return n * (1 + sides) / 2;
}

/**
 * @param {number} n
 * @param {number} sides
 * @param {number} [fixed=0]
 * @returns {string}
 */
function formatDice(n, sides, fixed = 0) {
    const dice = n === 1 ? `1d${sides}` : `${n}d${sides}`;
    if (!fixed) {
        return dice;
    }
    return fixed > 0 ? `${dice}+${fixed}` : `${dice}${fixed}`;
}

/**
 * @param {string} formula
 * @returns {{ formula: string, min: number, max: number, average: number, criticalFormula: string, criticalAverage: number|null }}
 */
function enrichFormula(formula) {
    const stats = parseDiceFormula(formula);
    const criticalFormula = toCriticalFormula(formula);
    const critStats = parseDiceFormula(criticalFormula);
    return {
        formula,
        min: stats.min,
        max: stats.max,
        average: stats.average,
        criticalFormula,
        criticalAverage: critStats.isValid ? critStats.average : null,
    };
}

/**
 * Double les termes de dés, conserve les fixes.
 *
 * @param {string} formula
 * @returns {string}
 */
export function toCriticalFormula(formula) {
    if (typeof formula !== 'string' || !formula.trim()) {
        return formula;
    }
    // Remplace chaque NdX par (2N)dX
    return formula.replace(/(\d*)d(\d+)/gi, (_, n, sides) => {
        const count = n === '' ? 1 : Number.parseInt(n, 10);
        return `${count * 2}d${sides}`;
    });
}

/**
 * Génère les candidats purs (un seul type de dé ± fixe).
 *
 * @param {number} target
 * @returns {Array<{ formula: string, average: number, diceCount: number, kind: string, distance: number }>}
 */
function collectCandidates(target) {
    const candidates = [];
    const seen = new Set();

    function push(formula, average, diceCount, kind, maxDistance) {
        if (seen.has(formula)) {
            return;
        }
        const distance = Math.abs(average - target);
        if (distance > maxDistance) {
            return;
        }
        seen.add(formula);
        candidates.push({ formula, average, diceCount, kind, distance });
    }

    // Purs : n dX (tolérance large pour bas / moy / haut). d2 réservé aux alternatives.
    for (const sides of CLASSIC_DICE_SIDES) {
        for (let n = 1; n <= MAX_SAME_DICE; n += 1) {
            const avg = diceAverage(n, sides);
            const kind = n === 1 ? 'high' : n === 2 ? 'mid' : 'low';
            const maxDist = sides === 2 ? AVERAGE_TOLERANCE : 1.6;
            push(formatDice(n, sides), avg, n, kind, maxDist);
        }
    }

    // Dés + fixe : n dX + k (k ≥ 1), moyenne proche
    for (const sides of CLASSIC_DICE_SIDES) {
        for (let n = 1; n <= 4; n += 1) {
            const base = diceAverage(n, sides);
            for (let k = 1; k <= Math.max(1, Math.ceil(target)); k += 1) {
                const avg = base + k;
                if (avg > target + 1.1) {
                    break;
                }
                push(formatDice(n, sides, k), avg, n, 'fixed_alt', AVERAGE_TOLERANCE);
            }
        }
    }

    // Mixtes : a dA + b dB (A < B), au plus 4 dés au total
    for (let i = 0; i < CLASSIC_DICE_SIDES.length; i += 1) {
        for (let j = i + 1; j < CLASSIC_DICE_SIDES.length; j += 1) {
            const aSides = CLASSIC_DICE_SIDES[i];
            const bSides = CLASSIC_DICE_SIDES[j];
            for (let a = 1; a <= 3; a += 1) {
                for (let b = 1; b <= 3; b += 1) {
                    if (a + b > 4) {
                        continue;
                    }
                    const avg = diceAverage(a, aSides) + diceAverage(b, bSides);
                    const formula = `${formatDice(a, aSides)}+${formatDice(b, bSides)}`;
                    push(formula, avg, a + b, 'mixed', AVERAGE_TOLERANCE);
                }
            }
        }
    }

    return candidates;
}

/**
 * Choisit le meilleur candidat pour une classe de variance.
 *
 * @param {Array} candidates
 * @param {(c: object) => boolean} predicate
 * @returns {ReturnType<typeof enrichFormula>|null}
 */
function primarySides(formula) {
    const match = String(formula).match(/d(\d+)/i);
    return match ? Number.parseInt(match[1], 10) : 0;
}

function bestOf(candidates, predicate, target) {
    const pool = candidates.filter(predicate);
    if (pool.length === 0) {
        return null;
    }
    pool.sort((a, b) => {
        if (a.distance !== b.distance) {
            return a.distance - b.distance;
        }
        // À distance égale : préférer une moyenne ≥ cible (ex. 6 → 1d12 plutôt que 1d10)
        const aUnder = a.average < target ? 1 : 0;
        const bUnder = b.average < target ? 1 : 0;
        if (aUnder !== bUnder) {
            return aUnder - bUnder;
        }
        // Éviter les piles de d2 pour les suggestions principales
        const aD2 = primarySides(a.formula) === 2 ? 1 : 0;
        const bD2 = primarySides(b.formula) === 2 ? 1 : 0;
        if (aD2 !== bD2) {
            return aD2 - bD2;
        }
        // Pour 3+ dés, préférer 3 dés (ex. 3d4) plutôt que 4d2 / 5d2
        if (a.diceCount >= 3 && b.diceCount >= 3 && a.diceCount !== b.diceCount) {
            const aPref = Math.abs(a.diceCount - 3);
            const bPref = Math.abs(b.diceCount - 3);
            if (aPref !== bPref) {
                return aPref - bPref;
            }
        }
        return a.diceCount - b.diceCount;
    });
    return enrichFormula(pool[0].formula);
}

/**
 * Propose des formules de dés pour une valeur moyenne cible.
 *
 * @param {number} value Valeur totale (souvent mod inclus)
 * @param {{ mod?: number }} [options]
 * @returns {{
 *   target: number,
 *   mod: number,
 *   diceTarget: number,
 *   high: object|null,
 *   mid: object|null,
 *   low: object|null,
 *   mixed: object|null,
 *   withFixed: object|null,
 *   all: object[],
 * }}
 *
 * @example
 * suggestDice(6)
 * // high ≈ 1d12, mid ≈ 2d6, low ≈ 3d4
 */
export function suggestDice(value, options = {}) {
    const mod = Number.isFinite(options.mod) ? Number(options.mod) : 0;
    const raw = Number(value);
    if (!Number.isFinite(raw) || raw <= 0) {
        return {
            target: raw,
            mod,
            diceTarget: 0,
            high: null,
            mid: null,
            low: null,
            mixed: null,
            withFixed: null,
            all: [],
        };
    }

    const diceTarget = Math.max(0.5, raw - mod);
    const candidates = collectCandidates(diceTarget);

    const high = bestOf(
        candidates,
        (c) => c.diceCount === 1 && c.kind !== 'fixed_alt' && c.kind !== 'mixed' && primarySides(c.formula) >= 4,
        diceTarget,
    );
    const mid = bestOf(
        candidates,
        (c) => c.diceCount === 2 && c.kind !== 'mixed' && c.kind !== 'fixed_alt' && primarySides(c.formula) >= 4,
        diceTarget,
    );
    const low = bestOf(
        candidates,
        (c) => c.diceCount >= 3 && c.kind !== 'mixed' && c.kind !== 'fixed_alt' && primarySides(c.formula) >= 4,
        diceTarget,
    );
    const mixed = bestOf(candidates, (c) => c.kind === 'mixed', diceTarget);
    const withFixed = bestOf(candidates, (c) => c.kind === 'fixed_alt', diceTarget);

    const all = candidates
        .filter((c) => c.distance <= AVERAGE_TOLERANCE + 0.01 || c.kind === 'fixed_alt' || c.kind === 'mixed')
        .sort((a, b) => a.distance - b.distance || a.diceCount - b.diceCount)
        .slice(0, 12)
        .map((c) => enrichFormula(c.formula));

    return {
        target: raw,
        mod,
        diceTarget,
        high,
        mid,
        low,
        mixed,
        withFixed,
        all,
    };
}

/**
 * Résumé court des trois variances principales.
 *
 * @param {number} value
 * @param {{ mod?: number }} [options]
 * @returns {string}
 *
 * @example
 * formatDiceSummary(6) // "bas 1d12 · moy 2d6 · haut 3d4"
 */
export function formatDiceSummary(value, options = {}) {
    const s = suggestDice(value, options);
    const parts = [];
    // Convention plan : haute variance = "bas" (1 dé), moyenne = "moy", basse variance = "haut"
    if (s.high) {
        parts.push(`bas ${s.high.formula}`);
    }
    if (s.mid) {
        parts.push(`moy ${s.mid.formula}`);
    }
    if (s.low) {
        parts.push(`haut ${s.low.formula}`);
    }
    return parts.join(' · ') || '—';
}

/**
 * Texte multiligne pour tooltip au survol d’une valeur moyenne (barème).
 * Propose variance haute (−), moyenne (=), basse (+) et optionnellement un fixe.
 *
 * @param {number|string|null|undefined} value — moyenne cible
 * @param {{ mod?: number }} [options]
 * @returns {string}
 *
 * @example
 * formatDiceHoverTooltip(6)
 * // "Moyenne 6 ≈ 1d12 (−) · 2d6 (=) · 3d4 (+)"
 */
export function formatDiceHoverTooltip(value, options = {}) {
    const n = Number(value);
    if (!Number.isFinite(n) || n <= 0) {
        return '';
    }
    const s = suggestDice(n, options);
    const lines = [];
    if (s.high?.formula) {
        lines.push(`${s.high.formula} (−)`);
    }
    if (s.mid?.formula) {
        lines.push(`${s.mid.formula} (=)`);
    }
    if (s.low?.formula) {
        lines.push(`${s.low.formula} (+)`);
    }
    if (s.withFixed?.formula) {
        const already = lines.some((line) => line.startsWith(s.withFixed.formula));
        if (!already) {
            lines.push(`${s.withFixed.formula} (fixe)`);
        }
    }
    if (!lines.length) {
        return '';
    }
    return `Moyenne ${n} ≈ ${lines.join(' · ')}`;
}
