# Effets — IA

> Effets de sorts/objets et mappings DofusDB.

## Fichiers pivots

- **Sorts (canal natif)** : `SpellDegree` / `SpellDegreeEffect` — progression unique `Spell → degrés → effets`
  - `app/Models/SpellDegree.php`, `SpellDegreeEffect.php`
  - `app/Services/Spell/SpellDegreeResolver.php`, `SpellDegreeService.php`, `SpellDegreesSerializer.php`
  - `php artisan spells:migrate-degrees` (+ `--dry-run`, `--spell=`) : legacy `effect_spell` → `spell_degrees`
  - API : `GET/POST /api/spells/{spell}/degrees`, `PUT` bulk (`sync-bulk`), PATCH/DELETE degré,
    `materialize-effects`, sync effets. Mutations : `auth` + `SpellPolicy::update` (auteur|admin),
    pas un rôle MJ global — aligné sur le PATCH Inertia du sort.
  - `properties_source` sur `spell_degrees` : `own` | `previous` | `spell` (résolu par
    `SpellDegreeResolver`). Création de degré = copie matérialisée (props + effets, pas d’héritage
    d’effets par défaut).
  - UI édition : `SpellEditFormContent` + `SpellDegreesEditor` + `SpellCastPropertiesGrid` +
    `AreaShapePicker` + `SpellEffectEditorRow` ; enregistrement unique (bulk degrés puis sort).
    Conteneurs partagés : `EntityEditGrid` / `EntityEditContainer`. Porteurs enrichis +
    `SpellHoldersPanel`. Affichage lecture : `SpellEffectsJournal.vue`.
  - Portée UI compacte `x` / `x-y` (`po_min` + `po_max` en stockage) ; propriétés effectives du
    degré actif dans Full, premier degré dans Minimal/Line/table
  - Globaux sur `Spell` (pas sur les degrés) : `element`, `target_type`, `ritual_available`,
    résolution, `allows_reaction`. `area` existe aussi sur `Spell` comme repli sans degré.
    Zone : sélecteur de formes + notation Krosmoz.
  - UI layout : grille 2 cols (descriptif | hors degrés ; communes | métadonnées repliés ;
    degrés & effets pleine largeur). Header compact (retour, porteurs, vue texte, formules,
    état, options, annuler/reset, supprimer, enregistrer).
  - Effets : élément/carac (icônes couleurs), créature (`params.creature_id`), état,
    `value_formula` + `value_formula_crit`, `duration_formula`, `scope` visible (pas dans Avancé);
    `monster_id` legacy OK
  - Vocabulaire UI : **effet** (= ancien sous-effet) ; catalogue technique `SubEffect` conservé
- `app/Models/Effect*.php`, `app/Models/ObjectEffect.php` — encore utilisés objets + legacy sorts
- `GET /api/object-effects` — liste par fiche ; `view` sur le parent + `visibleToUser` sur le monstre invoqué (session `web`)
- `app/Services/Effect/` (`SpellNestedPreviewSerializer` : chips d’aperçu sur sorts liés)
- `app/Services/Scrapping/Core/Conversion/SpellEffects/` — après intégration, `rebuildFromLegacy` miroir natif
- `app/Support/DofusHyperlinkText.php` (libellés d’états `{{spell,…::Nom}}`)
- `app/Support/KrosmozGameTerms.php` — désenvoûtable → dissipable
- `app/Services/Condition/ConditionCanonicalMapper.php` — jeton Dofus → état JDR `playable`
- `php artisan conditions:remap-canonical` — recolle `condition_spell` + `params.condition_id`
- `php artisan spells:sync-elements` — aligne `spells.element` sur les éléments des sous-effets
- Affichage sorts : `spell_degrees` prioritaire ; `effects_definitions` legacy en repli
- `Spell::visibleToUser` / `EntityDisplayVisibilityService::constrainQueryToViewer` (listes)
- `GET /api/effects/definitions` — recherche defs legacy
- `GET /api/effects/effects?q=&per_page=` — index paginé
- `GET /api/effects/for-entity` et `GET /api/effects/usages` : middleware `web` + `view` parent
- `scrapping:effects:reapply-mappings` — reclasse les `autre` déjà mappés
- Canal sorts : `spell_degrees` (natif) + `effects_definitions` (legacy jusqu’à bascule)
- Invisibilité Dofus 150 → `appliquer-etat` (state 250)
- `database/seeders/DofusdbEffectMappingSeeder.php`

## Hors périmètre

- Page contenu : `/admin/content/dofusdb-effect-mappings`
- Triage `autre` / effectId sans clé : [MAPPINGS_HORS_PERIMETRE.md](./MAPPINGS_HORS_PERIMETRE.md)
- Retrait définitif de `effect_spell` pour les sorts : après audit migration complète

## Liens

- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Caractéristiques : [../characteristics/_ai.md](../characteristics/_ai.md)
- Scrap serveur : [../scrapping/SERVER_MASS_SCRAP.md](../scrapping/SERVER_MASS_SCRAP.md)
