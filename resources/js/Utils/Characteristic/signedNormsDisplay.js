/**
 * Affichage des normes en budget ± (bonus / malus).
 * Quand min < 0 et max > 0, une cellule N > 0 se lit ±N ; 0 reste 0.
 *
 * @example
 * isSignedBudgetDisplay(-5, 5) // true
 * formatSignedBudgetCell(3, true) // '±3'
 * formatSignedBudgetCell(0, true) // '0'
 */

/**
 * @param {number|null|undefined} minNumeric
 * @param {number|null|undefined} maxNumeric
 * @returns {boolean}
 */
export function isSignedBudgetDisplay(minNumeric, maxNumeric) {
    return (
        typeof minNumeric === 'number'
        && typeof maxNumeric === 'number'
        && Number.isFinite(minNumeric)
        && Number.isFinite(maxNumeric)
        && minNumeric < 0
        && maxNumeric > 0
    );
}

/**
 * @param {unknown} value
 * @param {boolean} signedBudget
 * @returns {string}
 */
export function formatSignedBudgetCell(value, signedBudget) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }
    const num = Number(value);
    if (!Number.isFinite(num)) {
        return String(value);
    }
    if (signedBudget && num > 0) {
        return `±${num}`;
    }
    return String(num);
}

/**
 * Libellé accessible pour une cellule de budget ±.
 *
 * @param {number} level
 * @param {string} powerLabel
 * @param {unknown} value
 * @param {boolean} signedBudget
 * @returns {string}
 */
export function signedBudgetAriaLabel(level, powerLabel, value, signedBudget) {
    const num = Number(value);
    if (!Number.isFinite(num)) {
        return `Niveau ${level}, ${powerLabel} : ${value}`;
    }
    if (signedBudget && num > 0) {
        return `Niveau ${level}, ${powerLabel} : budget ±${num} (bonus +${num} ou malus −${num})`;
    }
    return `Niveau ${level}, ${powerLabel} : ${num}`;
}
