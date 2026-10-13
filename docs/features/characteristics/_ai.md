# Caractéristiques — IA

> Valeurs, formules, limites, normes et composition base / objets / contexte.

## Quand lire ce nœud

- Modifier une formule, une limite ou une norme de caractéristique.
- Comprendre comment une fiche monstre / PNJ calcule ses stats selon le niveau.
- Brancher l’UI (sélecteur de niveau, popover de décomposition, édition de formules).

## Concepts clés

- **Définitions** : la **base** est la source de vérité ; les JSON sous `database/seeders/data/characteristic-definitions/` sont une copie versionnée. Au seed (ex. `project:dev`), création seule des lignes absentes ; réécriture volontaire via `project:seed --overwrite`. Export base → JSON : `scrapping:seeders:export --characteristics` (aussi branché sur `project:backup` hors prod). Objets ciblés : `item_type_dofus_ids` (IDs DofusDB). Reprise : `php artisan characteristics:definitions-apply --item-types`.
- **Nature** : colonne `characteristics.nature` (`descriptif` | `intrinsic` | `bonus_malus` | `buff_debuff` | `action_value`) + lien `intrinsic_characteristic_id` (bonus/malus et buff/débuff → intrinsèque créature). Indépendant du groupe.
- **Groupes** : `creature`, `object` (item+panoply), `spell`, **`consumable`**, **`profile`** (classe/spé/monstre, maîtrises). Ressources hors système. Seed JSON : `characteristic-definitions/{group}/`.
- **Share Inertia** : `CharacteristicMetaByDbColumnService` expose `helper` / `descriptions`, `nature`, `limit_min` / `limit_max`. Cache `characteristics:frontend:v6`.
- **Limites** : `min`/`max` numériques = plafond absolu (UI + validation + clamp scrapping). Convention signée créature/objet/sort : `min = −|max|` (sauf level/hostility/dés/non numériques). Overlay `monster` : ±50 % de l’amplitude PJ via `MonsterCharacteristicLimitExpander` (conserve le plus large). Pas de clamp post-formule sur le runtime créature : les mods joueur embarquent `⌊niv/4⌋+2` et le max **+7** dans la formule ; scores principaux PJ `max=24` (absolu), le plafond progressif `14+⌊niv/2⌋` est une règle de répartition, pas une formule de colonne. Colonnes créature orphelines (DO/rés. sag·vit, 3 compétences techniques) droppées.
- **Composition** : si la formule contient `*_object`, l'équipement est déjà dans la base (pas de double comptage) ; sinon `total = base + objets + contexte`. Maîtrises via `ProfileMasteryResolver` (`*_mastery_profile`). Détail : [COMPUTED_VALUES.md](./COMPUTED_VALUES.md).
- **DO mult.** : colonne composable `do_fixe_multiple` (`fixed_damage_multiple_creature`), visible dans Dommages.
- **Grammaire** : `{ expression }` + suffixe d’arrondi ; domaines `[x-y]` / `[ndX]` **uniquement sur le niveau**.
- **Affichage tableau** : JSON `{"characteristic","1":…}` décodé par `buildFormulaTableView` (`formulaConfig.js`) en tranches (`1–2`, dernière `16+`). Rendu : `CharacteristicFormulaRichText` (charte, infobulle kref, fiche).
- **Runtime créature** : `CreatureRuntimeStatsService` → `levels[]` pour le sélecteur de niveau. Endpoint `resolved-stats` : `CreaturePolicy::viewResolvedStats` (visibilité monstre/PNJ) + objets `visibleToUser` avant agrégation.
- **Conversion Dofus** : `conversion_formula` puis clamp `min`/`max` du pivot (pas de bornes dans la formule). Signé buff/débuff : `sign(d)*f(|d|)`. Dégâts sort ±80 ; éléments sort (`fixed_damage_*`, `fixed_resistance_*`, `res_*`) tous formulés.
- **Bonus équipement (MJ)** : `EquipmentBonusTableService` projette `formula` JSON par bandes 1–2…19–20 + types d’item ; API `GET /api/characteristics/equipment-bonus-table` (rôle ≥ MJ).
- **Admin (fiche)** : barre collée en haut du panneau (`Index.vue`) — nom, état, Lier, Enregistrer, Supprimer sur une ligne (retour à la ligne si ça ne tient pas). Enregistrer reste visible au scroll et n’est actif qu’après une modification. Clé formule et badge de groupe masqués sous `lg` / `sm`.
- **Runes de forgemagie (public)** : `ForgemagieRuneTableService` filtre `characteristic_object` sur `forgemagie_max > 0` + `rune_price_per_unit` non nul, joint `characteristic_object_item_type` (vide = tous les équipements) ; API `GET /api/characteristics/forgemagie-rune-table`. Source de vérité des prix : la base, pas les règles.
- **Prix équipements / consommables** : `EquipmentPriceCalculator` (bonus × `base_price_per_unit` + 150×niveau + 200×rareté), `ConsumablePriceCalculator` (somme recette), `EntityPriceRecalculator`. Plus de multiplicateur puissance.
- **Normes générées** : colonne `norms_formula` → `NormsGridGenerator` → `norms_grid` (`[level]`, `[power]`, `[clé]`). Cmd `characteristics:regenerate-norms`. Hub admin `/admin/content/balance`.
- **Barèmes sorts** : `dommages_spell` = coef×PV×3/PA ; soin/bouclier/PV temp dérivés ; `vol_vie_spell` = 0,3×dégâts ; invocations = normes monstre. Tour = cast×PA_créature÷3. `SpellActionBudgetService` + `NpcStatGabarit` lisent la DB. Outil : `SpellReferenceScales.vue`. Dés : `diceEquivalents.js`.
- **Bonus équipements** : `items.effect` = objet plat Krosmoz jouable `clé → int` (source runtime, validation API, filtres). `items.bonus` = JSON brut Dofus (traçabilité / reconversion). Agrégation : `KrosmozItemBonusDecoder` (`effect` puis `bonus`). Audit/repair : `items:audit-bonus-compatibility`, `items:repair-bonus-compatibility` (jamais `playable`). Grille IA : `ia:equipment-grid --refresh-drafts` (bonus par slot 2.6.1).
- **Consommables** : groupe dédié (`*_consumable`) ; buff/débuff + valeurs d'action (`soin_consumable`, `bouclier_consumable`, `pv_temporaires_consumable`) + `duration_consumable`. `ObjectEffect` remappé depuis les anciennes clés objet.
- **Wakfu** : formule créature `[mastery_bonus_creature]+min([wakfu_recharge_object],3)`. Lien `intrinsic_characteristic_id` remplace les alias en dur objet→créature.
- **Invocations** : base 1, total plafonné par `mastery_bonus_creature`.
- **Édition** : totaux + `*_context` via `CreatureComposableCharacteristicsPersister` / `CharacteristicFormulaField` (monstre/PNJ). Maîtrises profil persistées sur `monsters` / `breeds` / `specializations` (`ProfileMasteryColumns`) ; plus d’écriture `*_mastery` sur `creatures`.

## Fichiers pivots

- `app/Services/Characteristic/Formula/FormulaExpressionParser.php`
- `app/Services/Characteristic/Domain/LevelDomainResolver.php`
- `app/Services/Creature/Runtime/CreatureRuntimeStatsService.php`
- `app/Support/Creature/CreatureComposableColumns.php`
- `app/Support/Profile/ProfileMasteryColumns.php`
- `app/Services/Creature/Runtime/ProfileMasteryResolver.php`
- `app/Enums/CharacteristicNature.php`
- `resources/js/Utils/Entity/creatureCharacteristicGroups.manifest.js`
- `resources/js/Utils/Entity/buildCreatureCharacteristicGroups.js`
- `resources/js/Utils/Entity/buildCreatureCompetenceGroups.js`
- `resources/js/Utils/characteristic/formulaGrammar.js`
- `resources/js/Pages/Molecules/data-input/CharacteristicFormulaField.vue`
- `resources/js/Pages/Molecules/data-display/AbilityScoreStack.vue`
- `resources/js/Pages/Organismes/data-display/CharacteristicsCard.vue`
- `database/seeders/*CharacteristicSeeder.php`
- `app/Services/Characteristic/Reference/EquipmentBonusTableService.php`
- `app/Services/Characteristic/Pricing/EquipmentPriceCalculator.php`
- `app/Services/Characteristic/Pricing/ConsumablePriceCalculator.php`
- `app/Services/Characteristic/Pricing/EntityPriceRecalculator.php`

## Liens

- Doc humaine : [README.md](./README.md), [COMPUTED_VALUES.md](./COMPUTED_VALUES.md)
- Scrapping : [../scrapping/_ai.md](../scrapping/_ai.md)
- Effets : [../effects/_ai.md](../effects/_ai.md)
- Vues entités : [../../frontend/entity-views/_ai.md](../../frontend/entity-views/_ai.md)
