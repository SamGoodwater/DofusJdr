# Effets

Le système d'effets décrit les effets de sorts et d'objets : degrés, effets (actions), usages et mappings DofusDB.

## Sorts — progression native

- Modèles : `SpellDegree`, `SpellDegreeEffect` (catalogue technique `SubEffect`).
- Un sort a **une** progression de degrés. Vocabulaire UI : **effet** (ex-sous-effet).
- Propriétés de lancement (PA, PO, LdV, zone…) : défauts sur `Spell` ; surcharge par degré via
  `properties_source` (`own` | `previous` | `spell`).
  Élément(s), type de ciblage, rituel disponible, mode de résolution et utilisation en réaction
  restent globaux au sort (jamais sur `spell_degrees`).
- Création d’un degré : copie matérialisée du précédent (propriétés + effets éditables).
  `inherits_effects` reste disponible pour réutiliser les effets du précédent sans duplication.
- Services : `SpellDegreeResolver`, `SpellDegreeService`, `SpellDegreesSerializer`, `SpellDegreeLegacyMigrator`.
- Commande : `php artisan spells:migrate-degrees [--dry-run] [--spell=]`.
- API : `/api/spells/{spell}/degrees` (CRUD + `PUT` bulk + materialize / sync effets).
- UI édition : containers thématiques (`EntityEditContainer`), `SpellDegreesEditor`,
  `SpellCastPropertiesGrid`, `AreaShapePicker`, `SpellEffectEditorRow` ; un seul Enregistrer
  (bulk degrés natifs, puis groupe d’effets legacy en JSON sans visite Inertia, puis sort).
  Le flush groupe ne doit pas passer par Inertia `back()` : ça rafraîchit `spellDegrees` et
  le brouillon des degrés est perdu. Grille 2 colonnes + header compact (aide formules, état,
  options, annuler / reset / supprimer / enregistrer). Affichage : `SpellEffectsJournal`.
- Portée unique (`4` ou `2-8`) ; zone par icônes de formes + paramètres numériques (notation Krosmoz).
- Effets : élément / caractéristique (icônes et couleurs), créature, état ; `value_formula` et
  `value_formula_crit` ; durée (`duration_formula`) ; portée de jeu (`scope`) au même niveau.
- Une invocation manuelle stocke `params.creature_id`. `params.monster_id` reste lu pour la
  compatibilité avec les imports et données antérieures.
- Scraping : après intégration legacy, miroir via `rebuildFromLegacy`.
- Canal affichage préféré : `spell_degrees` ; `effects_definitions` reste en repli legacy.

## Backend (objets & legacy)

- Modèles : `Effect`, `SubEffect`, `EffectDegree`, `EffectUsage`, `ObjectEffect`, `DofusdbEffectMapping`.
- Services : `app/Services/Effect/`, `app/Services/Scrapping/Core/Conversion/SpellEffects/`.
- Admin/API : contrôleurs sous `app/Http/Controllers/Admin/` et `app/Http/Controllers/Api/Effect/`.
- Effets d’objet : `GET /api/object-effects?entity_type=&entity_id=` exige `view` sur la fiche
  (item / consommable / ressource). Un monstre invoqué n’est renvoyé que s’il est `visibleToUser`.
  La route lit la session (`web`) pour que l’éditeur d’une fiche brouillon recharge la liste.
- Canal legacy sorts : pivot `effect_spell` + `effects_definitions` (resource). Les tables
  legacy `spell_effects` / `spell_effect_types` sont droppées.
- Liaison depuis la fiche sort : `GET /api/effects/definitions?q=&exclude_spell_id=` (pas de liste
  complète dans le payload d’édition).
- Création depuis la fiche sort : `POST /api/effects/spell-effects` (MJ+, droit `update` sur le sort).
  La définition, son premier degré et les sous-effets initiaux (`initial_sub_effects`) sont créés puis liés.
  Affinage ensuite dans `EffectGroupEditorForm`, sans ouvrir l’admin des effets.
- Porteurs du sort : la page Modifier expose `spellHolders` (monstres et PNJ via `creature_spell`,
  classes via `breed_spell`), avec lien vers la fiche et l’édition.
- Index API : `GET /api/effects/effects?q=&per_page=` (paginé).
- Prévisualisation : `GET /api/effects/for-entity` et liste `GET /api/effects/usages` exigent
  `view` sur la fiche parente (sort, objet, consommable ou ressource). Un brouillon n’est
  pas enumerable par identifiant.
- Reclassement post-import : `php artisan scrapping:effects:reapply-mappings`.

## Données

Les mappings DofusDB sont stockés en base (`dofusdb_effect_mappings`) et seedés par `DofusdbEffectMappingSeeder`.

### Conversion DofusDB

Dans une instance d'effet DofusDB, `diceNum` et `diceSide` représentent respectivement la borne minimale
et la borne maximale. Malgré leur nom historique, `13` et `18` signifient donc **13–18**, jamais `13d18`.
La conversion applique séparément la formule Krosmoz aux deux bornes, puis produit une notation de dés
jouable.

Les paramètres intégrés distinguent :

- `dofus_value_formula` : plage source conservée pour diagnostic ;
- `value_converted` : valeur moyenne Krosmoz ;
- `dice_formula` et `value_formula` : formule Krosmoz réellement exécutable ;
- les variantes `*_crit` : mêmes informations pour le jet critique.

Les bonus, malus et vols de caractéristiques sont distingués avant le flag générique `boost` de DofusDB.
Les retraits de résistances relatives restent sur les paliers −100/−50/0/50/100 et ne peuvent jamais
produire une valeur intermédiaire comme 25. Le vol de vie utilise la courbe réduite `vol_vie_spell`, puis
rend autant de PV que les dégâts réellement infligés, conformément aux règles Krosmoz.

La durée numérique importée est également copiée dans `effect_sub_effect.duration_formula`, qui constitue
la valeur commune au moteur de résolution, à l’éditeur et à l’affichage. Un réimport met à jour les pivots
existants (durée, params, condition liée) au lieu de les ignorer. Les états DofusDB créent/mettent à jour
l’entité `Condition` (état `raw` à la création, sans écraser un `playable` existant). La liaison
`condition_spell` et `params.condition_id` ciblent l’état JDR de base s’il existe (Pesanteur, Empoisonné,
Étourdi, Ralenti, Affaibli) ; le jeton Dofus reste en `raw` (`canonical_condition_id`). Sans canon, pas
de liaison JDR. Les flags restent en snake_case
(`cant_be_moved`, `cant_switch_position`, etc.). Les noms d’états DofusDB parfois fournis sous forme
d’hyperlien Ankama (`{{spell,id,level::Libellé}}`) sont normalisés vers le libellé affichable à
l’import, à la résolution et à l’affichage (`App\Support\DofusHyperlinkText`). Maintenance ponctuelle :
`php artisan conditions:strip-dofus-hyperlinks` ; recollement vers le noyau JDR :
`php artisan conditions:remap-canonical`. Les identifiants élémentaires DofusDB utilisent une
correspondance centralisée : 0 neutre, 1 feu, 2 eau, 3 terre et 4 air.

Les effets « Bouclier » DofusDB (`1020`, `1039`, `1040`) sont mappés vers `protéger`, même lorsqu’ils
portent le flag générique `boost`. Ils ne doivent plus être importés comme `booster` sans caractéristique.

Les PV temporaires sont un sous-effet distinct `donner-pv-temporaires` → `pv_temporaires_spell`
(caractéristique DofusDB `95` / `maxLifePoints`, ou libellé « vie temporaire »). Ils partagent le budget
survie des soins / boucliers, mais restent non dissipables et non cumulables (règles 3.2.4.2). Le max PV
via sort reste `vitality_spell` ; il n’y a pas de `life_points_max_spell`.

Les compétences actives Krosmoz (Acrobaties, Discrétion, etc.) sont sélectionnables comme caractéristique
de `booster` / `retirer` / `voler-caracteristiques` (catégorie `skill`). Elles n’ont en général pas
d’équivalent DofusDB : usage manuel / contenu custom.

Les caractéristiques Dofus sans équivalent Krosmoz (apparence, prospection, % stats, etc.) sont classées
en `autre` / `none` plutôt qu’en booster orphelin. Détail : [MAPPINGS_HORS_PERIMETRE.md](./MAPPINGS_HORS_PERIMETRE.md).

### Résolution du sort (jet d'attaque / sauvegarde)

DofusDB n'expose pas de champ `isMagic`. `SpellResolutionInferenceService` déduit donc la résolution
Krosmoz à partir des sous-effets convertis, conformément aux règles 3.3.2.3 :

- dégâts monocibles (même avec retrait PA/PM) → `attack_roll` (touche vs CA + esquive) ;
- états hostiles, zone hostile ou placement offensif → `saving_throw`, DD =
  `8 + modificateur de caractéristique + bonus de maîtrise` ;
- un retrait PA/PM seul ne force pas une sauvegarde ;
- soutien pur (soin, bouclier, PV temporaires, boost, auto-état) → `auto_success`.

`is_magic` suit cette inférence : `false` pour un jet d'attaque, `true` sinon.

Lorsque DofusDB fournit `minPlayerLevel` et `apCost`, les dégâts, soins, boucliers et PV temporaires
convertis sont recalés sur le budget de PV du niveau Krosmoz correspondant. Le budget par lancement vaut :

`budget par tour × coût en PA / PA disponibles au niveau`

Les lignes élémentaires d'un même lancement se partagent ce total proportionnellement à leur puissance
Dofus convertie. Cette étape remplace les anciennes valeurs indépendantes des PV tout en conservant les
plages Dofus d'origine dans les paramètres de diagnostic.

La commande `scrapping:effects:map` régénère les propositions depuis l'API et renseigne les clés connues.
Les caractéristiques sans équivalent Krosmoz (apparence, prospection, capture, orientation, etc.) restent
explicitement en revue manuelle ; elles ne doivent pas recevoir une clé arbitraire.

## Frontend

Pages admin effets et composants de configuration sous `resources/js/Pages/Admin/`.
