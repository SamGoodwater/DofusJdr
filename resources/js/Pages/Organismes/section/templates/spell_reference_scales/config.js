/**
 * Configuration du template Barèmes (ex. spell_reference_scales).
 *
 * Affiche l’outil générique multi-groupes, avec présélection sort par défaut
 * (même composant que le modal Outils → Barèmes).
 */
export default {
    name: 'Barèmes',
    description:
        'Barèmes indicatifs des caractéristiques (tous groupes), avec extras sort (dés, budget tour) quand pertinent.',
    icon: 'fa-solid fa-table',
    value: 'spell_reference_scales',
    supportsAutoSave: true,
    defaultSettings: {
        compact: false,
        group: 'spell',
        characteristic_key: '',
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
        {
            key: 'group',
            type: 'string',
            label: 'Groupe initial',
            description: 'all | creature | object | spell | consumable | profile',
            default: 'spell',
        },
        {
            key: 'characteristic_key',
            type: 'string',
            label: 'Caractéristique initiale',
            description: 'Clé optionnelle (ex. dommages_spell)',
            default: '',
        },
    ],
};
