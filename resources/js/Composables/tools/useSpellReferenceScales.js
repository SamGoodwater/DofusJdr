/**
 * Constantes partagées pour les extras de barèmes de sorts
 * (dégâts, soin, bouclier, PV temp., vol de vie).
 *
 * L’outil générique CharacteristicScalesTool + SpellScaleExtras remplace
 * l’ancien composable de chargement multi-clés.
 *
 * @example
 * import { SPELL_SCALE_FAMILIES, DISPLAY_BANDS } from '@/Composables/tools/useSpellReferenceScales.js';
 */

/** Familles de caractéristiques de sort avec conversion dés / budget tour. */
export const SPELL_SCALE_FAMILIES = [
    { key: 'dommages_spell', action: 'damage', label: 'Dégâts', short: 'Dmg' },
    { key: 'soin_spell', action: 'heal', label: 'Soins', short: 'Soin' },
    { key: 'bouclier_spell', action: 'shield', label: 'Bouclier', short: 'Bouclier' },
    { key: 'pv_temporaires_spell', action: 'temp_hp', label: 'PV temporaires', short: 'PV temp.' },
    { key: 'vol_vie_spell', action: 'life_steal', label: 'Vol de vie', short: 'Vol vie' },
];

/** Bandes affichées : basse / moyenne / haute = 3ème / 2ème / 1er rôle. */
export const DISPLAY_BANDS = [
    { power: 'weak', label: 'Basse (3ème rôle)', short: 'Bas' },
    { power: 'neutral', label: 'Moyenne (2ème rôle)', short: 'Moy' },
    { power: 'strong', label: 'Haute (1er rôle)', short: 'Haut' },
];
