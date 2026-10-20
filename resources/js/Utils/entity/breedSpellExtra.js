/**
 * Reliquat Dofus — sorts « hors grille » : pivot (character_level = 0, slot_index = 1).
 * Non affichés ni éditables sur les fiches classe (variantes uniquement).
 */

export const BREED_SPELL_EXTRA_LEVEL = 0;

export const BREED_SPELL_EXTRA_SLOT = 1;

/**
 * @param {object|null|undefined} pivot
 * @returns {boolean}
 */
export function isBreedExtraSpellPivot(pivot) {
    if (!pivot || typeof pivot !== "object") return false;
    return (
        Number(pivot.character_level) === BREED_SPELL_EXTRA_LEVEL &&
        Number(pivot.slot_index) === BREED_SPELL_EXTRA_SLOT
    );
}
