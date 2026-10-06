/**
 * Props pour {@link Badge} sur les options de select (formulaires, filtres tableau).
 * Aligné sur la logique des filtres TanStack (`TableConfig` / `optionBadge`).
 *
 * @param {object} option - `{ value, label, color?, badgeColor? }`
 * @param {object} cfg - configuration `optionBadge` (enabled, color, autoScheme, leadingDot, …)
 * @param {string} [uiColor='primary'] - repli si `cfg.color` absent
 * @returns {object|null} props pour Badge + `stateDotClass` si point d’état
 */
import {
    getEntityStateDotClass,
    getLevelColor,
    getRarityFilterBadgeTailwindColor,
} from '@/Utils/Entity/SharedConstants.js';
import { isValidColor } from '@/Utils/color/Color.js';
import { colorList } from '@/Pages/Atoms/atomMap.js';

/** Couleurs badge (Tailwind `color-shade`) pour les stats créature / jets de sauvegarde. */
const CREATURE_STAT_BADGE_COLORS = Object.freeze({
    strong: 'rose-600',
    intel: 'violet-600',
    chance: 'lime-600',
    agi: 'emerald-600',
    sagesse: 'sky-600',
    vitality: 'red-600',
});

/**
 * Couleur nommée DaisyUI ou token Tailwind `color-shade`.
 *
 * @param {unknown} raw
 * @returns {string|null}
 */
function resolveNamedBadgeColor(raw) {
    if (typeof raw !== 'string') return null;
    const color = raw.trim();
    if (!color) return null;
    if (colorList.includes(color)) return color;
    if (/^[a-z]+-\d+$/.test(color)) return color;
    return null;
}

export function buildSelectOptionBadgeProps(option, cfg, uiColor = 'primary') {
    if (!cfg || typeof cfg !== 'object' || !cfg.enabled) {
        return null;
    }

    const isCreatureStatScheme = String(cfg.autoScheme || '') === 'creatureStat';
    if (isCreatureStatScheme && (option?.value === '' || option?.value === null || option?.value === undefined)) {
        return null;
    }

    const autoLabelFrom = String(cfg.autoLabelFrom || 'label');
    const label =
        autoLabelFrom === 'value'
            ? String(option?.value ?? '')
            : String(option?.label ?? option?.value ?? '');

    const isLevelScheme = String(cfg.autoScheme || '') === 'level';
    const num = Number(option?.value ?? option?.label);
    const isLevelValue = Number.isFinite(num);
    const levelColor = isLevelScheme && isLevelValue ? getLevelColor(num) : null;

    const isRarityScheme = String(cfg.autoScheme || '') === 'rarity';
    const rarityNum = Number(option?.value);
    const rarityTailwind =
        isRarityScheme && Number.isFinite(rarityNum)
            ? getRarityFilterBadgeTailwindColor(rarityNum)
            : null;

    const stateDotClass =
        String(cfg.leadingDot || '') === 'entity-state' &&
        option?.value !== null &&
        option?.value !== undefined &&
        String(option.value) !== ''
            ? getEntityStateDotClass(option.value)
            : '';

    const creatureStatTailwind =
        isCreatureStatScheme && option?.value !== '' && option?.value != null
            ? CREATURE_STAT_BADGE_COLORS[String(option.value)] || null
            : null;

    const optionColorRaw =
        typeof option?.badgeColor === 'string' && option.badgeColor.trim() !== ''
            ? option.badgeColor
            : typeof option?.color === 'string'
              ? option.color
              : null;
    const explicitCss =
        optionColorRaw && isValidColor(optionColorRaw) ? optionColorRaw : null;
    const namedColor = !explicitCss ? resolveNamedBadgeColor(optionColorRaw) : null;

    const resolvedColor =
        levelColor ||
        rarityTailwind ||
        creatureStatTailwind ||
        explicitCss ||
        namedColor ||
        cfg.color ||
        uiColor;

    const hasOptionOwnColor = Boolean(levelColor || rarityTailwind || creatureStatTailwind || explicitCss || namedColor);

    return {
        color: resolvedColor,
        autoLabel: label,
        autoScheme: hasOptionOwnColor ? undefined : cfg.autoScheme,
        autoTone: hasOptionOwnColor ? undefined : cfg.autoTone,
        variant: cfg.variant != null ? cfg.variant : 'soft',
        glassy: Boolean(cfg.glassy),
        strong: Boolean(hasOptionOwnColor),
        textColor: isLevelScheme && isLevelValue ? '#ffffff' : '',
        stateDotClass,
    };
}
