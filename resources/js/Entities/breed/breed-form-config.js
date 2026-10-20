/**
 * Sections du formulaire classe / breed (édition dense).
 * Les champs viennent des descriptors via createFieldsConfigFromDescriptors.
 *
 * @module Entities/breed/breed-form-config
 */

/** Sections formulaire — édition (grille sheet 2 cols ; état dans le header). */
export const BREED_FORM_FIELD_SECTIONS_EDIT = [
    {
        id: 'general',
        title: 'Généralités',
        subtitle: 'Nom et descriptions.',
        fieldKeys: ['name', 'description_fast', 'description'],
    },
    {
        id: 'gameplay',
        title: 'Règles de classe',
        subtitle: 'Mécanique signature, évolution, dés de vie.',
        fieldKeys: ['mechanics', 'evolution', 'life_dice', 'specificity'],
    },
    {
        id: 'visuals',
        title: 'Visuels',
        subtitle: 'Images et symboles de la classe.',
        fieldKeys: [
            'image',
            'icon',
            'symbol_full',
            'symbol_bw',
            'logo_male',
            'logo_female',
            'image_full_male',
            'image_full_female',
        ],
    },
    {
        id: 'admin',
        title: 'Métadonnées & droits',
        subtitle: 'Identifiants externes, synchro, niveaux d’accès.',
        collapsedByDefault: true,
        collapsedActionLabel: 'Afficher',
        expandedActionLabel: 'Masquer',
        fieldKeys: [
            'dofusdb_id',
            'dofus_version',
            'auto_update',
            'read_level',
            'write_level',
        ],
    },
];
