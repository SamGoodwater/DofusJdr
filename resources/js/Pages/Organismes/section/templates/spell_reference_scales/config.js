/**
 * Configuration du template SpellReferenceScales
 *
 * Affiche l’outil de barèmes de sorts (dégâts, soin, bouclier, PV temp., vol de vie)
 * avec conversion en dés et ajusteur — même composant que le modal Outils.
 */
export default {
    name: 'Barèmes de sorts',
    description:
        'Barèmes de référence (basse / moyenne / haute) par niveau, conversion en dés et ajusteur de conditions.',
    icon: 'fa-solid fa-table',
    value: 'spell_reference_scales',
    supportsAutoSave: true,
    defaultSettings: {
        compact: false,
    },
    defaultData: {},
    parameters: [
        {
            key: 'compact',
            type: 'boolean',
            label: 'Mode compact',
            description: 'Réduit la densité typographique',
            default: false,
        },
    ],
};
