/**
 * Voix élémentaires et URLs d’icônes (aligné sur config/breed_element_orientations.php).
 *
 * @see config/breed_element_orientations.php
 */

/** @typedef {'air'|'earth'|'fire'|'water'} BreedElementKey */

/** @type {BreedElementKey[]} */
export const BREED_ELEMENT_KEYS = ["air", "earth", "fire", "water"];

/** Libellés FR courts (élément) */
export const BREED_ELEMENT_LABELS = {
    air: "Air",
    earth: "Terre",
    fire: "Feu",
    water: "Eau",
};

/** Libellés « Voie … » (fiche classe) */
export const BREED_ELEMENT_PATH_LABELS = {
    air: "Voie Air",
    earth: "Voie Terre",
    fire: "Voie Feu",
    water: "Voie Eau",
};

/**
 * Caractéristique associée à chaque voix (Force / Agilité / Intelligence / Chance).
 * @type {Record<BreedElementKey, string>}
 */
export const BREED_ELEMENT_CHARACTERISTIC_LABELS = {
    air: "Agilité",
    earth: "Force",
    fire: "Intelligence",
    water: "Chance",
};

/**
 * Surface carte orientation (glass + bordure basse teintée).
 * Classes Tailwind en toutes lettres (pas de concat dynamique).
 * @type {Record<BreedElementKey, string>}
 */
export const BREED_ELEMENT_CARD_SURFACE_CLASS = {
    air: "border border-success-500/20 border-b-2 border-b-success-500/60 bg-success-500/10 backdrop-blur-sm",
    earth: "border border-shop-500/20 border-b-2 border-b-shop-500/60 bg-shop-500/10 backdrop-blur-sm",
    fire: "border border-error-500/20 border-b-2 border-b-error-500/60 bg-error-500/10 backdrop-blur-sm",
    water: "border border-primary-500/20 border-b-2 border-b-primary-500/60 bg-primary-500/10 backdrop-blur-sm",
};

/** Couleur du libellé de voie */
export const BREED_ELEMENT_PATH_TEXT_CLASS = {
    air: "text-success-400",
    earth: "text-shop-400",
    fire: "text-error-400",
    water: "text-primary-400",
};

/** Libellés FR des clés d’orientation (stems fichiers) */
export const BREED_ORIENTATION_LABELS = {
    amelioration: "Amélioration",
    degats: "Dégâts",
    entrave: "Entrave",
    invocation: "Invocation",
    placement: "Placement",
    protection: "Protection",
    soin: "Soin",
    tank: "Tank",
};

const VOICE_ICON_BASE = "/storage/images/icons";

/** @type {Record<BreedElementKey, string>} chemin relatif sous public storage */
export const BREED_ELEMENT_VOICE_ICON_PATH = {
    air: `${VOICE_ICON_BASE}/caracteristics/air.webp`,
    earth: `${VOICE_ICON_BASE}/caracteristics/earth.webp`,
    fire: `${VOICE_ICON_BASE}/caracteristics/fire.webp`,
    water: `${VOICE_ICON_BASE}/caracteristics/water.webp`,
};

/**
 * @param {string|null|undefined} orientationKey
 * @returns {string}
 */
export function breedOrientationLabel(orientationKey) {
    if (orientationKey == null || String(orientationKey).trim() === "") {
        return "—";
    }
    const k = String(orientationKey).trim();
    return BREED_ORIENTATION_LABELS[k] || k.charAt(0).toUpperCase() + k.slice(1);
}

const ORIENTATION_EXT = "png";
const ORIENTATION_BASE = `${VOICE_ICON_BASE}/breed_orientations`;

/**
 * URL publique de l’icône d’orientation (fichier dans breed_orientations).
 *
 * @param {string|null|undefined} orientationKey stem sans extension
 * @returns {string|null}
 */
export function breedOrientationIconUrl(orientationKey) {
    if (orientationKey == null || String(orientationKey).trim() === "") {
        return null;
    }
    const k = String(orientationKey).trim();
    return `${ORIENTATION_BASE}/${k}.${ORIENTATION_EXT}`;
}

/**
 * @param {Record<string, string|null|undefined>|null|undefined} map
 * @returns {Record<BreedElementKey, string|null>}
 */
export function normalizeElementOrientationMap(map) {
    const out = {
        air: null,
        earth: null,
        fire: null,
        water: null,
    };
    if (!map || typeof map !== "object") {
        return out;
    }
    for (const key of BREED_ELEMENT_KEYS) {
        if (Object.prototype.hasOwnProperty.call(map, key)) {
            const v = map[key];
            out[key] = v != null && String(v).trim() !== "" ? String(v).trim() : null;
        }
    }
    return out;
}
