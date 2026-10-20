import { BREED_SPELL_EXTRA_LEVEL, BREED_SPELL_EXTRA_SLOT } from "@/Utils/entity/breedSpellExtra";

/**
 * Regroupe les sorts d'une classe par emplacement (pivot breed_spell).
 *
 * @param {object} breed - Données Breed (spells, spell_slots, _data)
 * @returns {Array<{ character_level: number, slot_index: number, spells: object[] }>}
 */
export function buildSpellSlotGroups(breed) {
    const direct = breed?.spell_slots ?? breed?._data?.spell_slots;
    if (Array.isArray(direct) && direct.length > 0) {
        return direct.map((g) => ({
            ...g,
            spells: Array.isArray(g.spells) ? [...g.spells] : [],
        }));
    }
    const raw = breed?.spells ?? breed?._data?.spells;
    const spells = Array.isArray(raw) ? raw : [];
    if (!spells.length) return [];

    const map = new Map();
    for (const s of spells) {
        const p = s.pivot || {};
        const level = Number(p.character_level ?? 1);
        const slot = Number(p.slot_index ?? 1);
        const key = `${level}|${slot}`;
        if (!map.has(key)) {
            map.set(key, { character_level: level, slot_index: slot, spells: [] });
        }
        map.get(key).spells.push(s);
    }
    const out = Array.from(map.values()).sort((a, b) =>
        a.character_level !== b.character_level
            ? a.character_level - b.character_level
            : a.slot_index - b.slot_index
    );
    for (const g of out) {
        g.spells.sort((a, b) => {
            const oa = Number(a.pivot?.choice_order ?? 0) || 0;
            const ob = Number(b.pivot?.choice_order ?? 0) || 0;
            if (oa !== ob) return oa - ob;
            return String(a.name || "").localeCompare(String(b.name || ""));
        });
    }
    return out;
}

/**
 * Sépare les groupes « variantes » (progression) du pivot 0/1 (reliquat Dofus, non affiché).
 *
 * @param {object} breed - Données Breed
 * @returns {{ variantGroups: Array<{ character_level: number, slot_index: number, spells: object[] }>, alwaysAvailableGroups: typeof variantGroups }}
 */
export function splitBreedSpellSlotGroups(breed) {
    const groups = buildSpellSlotGroups(breed);
    const variantGroups = [];
    const alwaysAvailableGroups = [];
    for (const g of groups) {
        const cl = Number(g.character_level);
        const si = Number(g.slot_index);
        if (cl === BREED_SPELL_EXTRA_LEVEL && si === BREED_SPELL_EXTRA_SLOT) {
            alwaysAvailableGroups.push(g);
        } else {
            variantGroups.push(g);
        }
    }
    return { variantGroups, alwaysAvailableGroups };
}

/** Emplacements de sorts de classe : 3 au niveau 1, puis 2–12 hors 3 et 9. */
export const CLASS_SPELL_UNLOCK_LEVELS = Object.freeze([1, 2, 4, 5, 6, 7, 8, 10, 11, 12]);

/**
 * Grille d’emplacements officielle (§2.3.2 / §5.2.3) : 3 au niveau 1, puis un par palier 2–12.
 * Les 12 emplacements sont numérotés Choix 1–12 (1 à 4 variantes selon la classe).
 *
 * @param {number} [_unused] - conservé pour les appels existants ; ignoré
 * @returns {Array<{ character_level: number, slot_index: number, choice_number: number, label: string }>}
 */
export function getStandardBreedSlotDefinitions(_unused = 12) {
    const slots = [];
    let choice_number = 1;
    for (let slot_index = 1; slot_index <= 3; slot_index++) {
        slots.push({
            character_level: 1,
            slot_index,
            choice_number,
            label: `Choix ${choice_number} · Niveau 1`,
        });
        choice_number += 1;
    }
    for (const L of CLASS_SPELL_UNLOCK_LEVELS.slice(1)) {
        slots.push({
            character_level: L,
            slot_index: 1,
            choice_number,
            label: `Choix ${choice_number} · Niveau ${L}`,
        });
        choice_number += 1;
    }
    return slots;
}

/**
 * Numéro de choix 1–12 pour un emplacement de la grille, ou null hors grille.
 *
 * @param {number} characterLevel
 * @param {number} slotIndex
 * @returns {number|null}
 */
export function getBreedChoiceNumber(characterLevel, slotIndex) {
    const found = getStandardBreedSlotDefinitions().find(
        (s) => s.character_level === Number(characterLevel) && s.slot_index === Number(slotIndex)
    );
    return found ? found.choice_number : null;
}

/**
 * Titre d’un groupe de variantes : « Choix 1 · Niveau 1 · 4 variantes ».
 *
 * @param {{ character_level: number, slot_index: number, spells?: object[] }} group
 * @returns {string}
 */
export function breedVariantGroupTitle(group) {
    const level = Number(group?.character_level);
    const slot = Number(group?.slot_index);
    const count = Array.isArray(group?.spells) ? group.spells.length : 0;
    const variantLabel = count <= 1 ? "1 variante" : `${count} variantes`;
    const choice = getBreedChoiceNumber(level, slot);
    if (choice != null) {
        return `Choix ${choice} · Niveau ${level} · ${variantLabel}`;
    }
    return `Niveau ${level} · ${variantLabel}`;
}

/**
 * @param {number} characterLevel
 * @param {number} slotIndex
 * @param {ReturnType<typeof getStandardBreedSlotDefinitions>} standardSlots
 */
export function isStandardSlot(characterLevel, slotIndex, standardSlots) {
    return standardSlots.some(
        (s) => s.character_level === characterLevel && s.slot_index === slotIndex
    );
}
