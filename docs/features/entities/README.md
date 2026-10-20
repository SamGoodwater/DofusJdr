# Entités JDR

Les « entités de jeu » sont les objets métier du référentiel KrosmozJDR : sorts, objets, ressources, consommables, monstres, PNJ, classes, spécialisations, capacités, conditions, traits, panoplies, boutiques, campagnes, scénarios. Elles partagent un modèle de champs, un système de droits et un pipeline d'affichage communs.

## Champs communs

Toutes les entités principales et leurs typages exposent au minimum :

| Champ | Type | Rôle |
| --- | --- | --- |
| `state` | string | `raw`, `draft`, `auto`, `playable`, `archived` |
| `read_level` | tinyint (0-5) | Rôle minimal pour lire |
| `write_level` | tinyint (0-5) | Rôle minimal pour modifier (≥ `read_level`) |
| `created_by` | FK users | Auteur (nullable) |
| `created_at` / `updated_at` | datetime | Horodatage |
| `deleted_at` | datetime | Soft delete |

De nombreuses entités importables ajoutent `official_id`, `dofusdb_id`, `dofus_version`, `auto_update` (voir [Scrapping](../scrapping/README.md)), ainsi que `image` / `icon`.

Référence exhaustive des champs par entité : `docs/features/entities/README.md` (un fichier `ENTITY_*.md` par type).

## Types d'entités

| Catégorie | Types |
| --- | --- |
| Gameplay | `spells`, `items`, `resources`, `consumables`, `monsters`, `npcs`, `breeds` (classes), `capabilities`, `conditions`, `creature-traits`, `specializations`, `panoplies` |
| Méta | `campaigns`, `scenarios`, `shops` |
| Typages (référentiels) | `item-types`, `resource-types`, `consumable-types`, `spell-types`, `monster-races` |
| Interne | `creatures` (classe mère de `monsters` et `npcs`, pas d'accès direct) |

Note : la table des classes est `breeds` (et la FK `breed_id`) pour éviter le mot réservé `class`.

### États (conditions)

Le scrap DofusDB crée les états en `raw` (jetons de sort, pas le catalogue JDR). Le seeder conserve un noyau `playable` (Pesanteur, Empoisonné, Étourdi, Ralenti, Affaibli). À l’import, le sort et `params.condition_id` pointent vers ce noyau quand le nom ou les flags correspondent (`ConditionCanonicalMapper`) ; le jeton Dofus reste en base (`canonical_condition_id`, `condition_dofusdb_id`). Sans équivalent JDR, pas de liaison sort. Le catalogue masque Brut par défaut. Les flags mécaniques s’affichent en chips. Recollement des données déjà importées : `php artisan conditions:remap-canonical`.

### Spécialisations : paliers, capacités et aptitudes

Une spécialisation progresse sur **7 paliers** : niveaux 1, 3, 6, 9, 12, 15 et 20 (règle §2.4.2). Chaque palier
pose un **choix entre** les capacités garanties (2 au palier 1, puis 1) et un **choix entre 2 options**
(capacités, ou un trait à partir du niveau 12 — **jamais** de points de caractéristique)
et **1 compétence** (3 au palier 1, choisies parmi **5**). Les **aptitudes** sont des bonus acquis **sans choix**, et il y en a
exactement **trois**, aux paliers **3, 9 et 15**.

En base, capacités et aptitudes sont toutes des `Capability` liées par `capability_specialization`, dont le
`level` porte le palier ; l’aptitude se distingue par `is_passive`.

Les **onze fiches jouables** (Érudit, Milicien·ne, Dévot, Artiste, Explorateur·rice, Voleur·euse, Artisan·e, Négociant·e, Sylvain·e, Marin·e, Courtisan·e) sont
rédigées dans `database/seeders/data/playable-specializations/*.php` : le seed les préfère au HTML legacy
et les rafraîchit à chaque passage. Les sections de palier portent des **kref** (capacités cliquables,
jets de sauvegarde, compétences). Une seule section par niveau. Le HTML
`database/seeders/data/legacy-specializations/` (hors dépôt) reste un repli via
`LegacySpecializationRealignService`. `draft-specializations.php` reste un point d’entrée vide pour
d’éventuelles fiches encore en rédaction.

Les **19 classes** ont une section CMS « Texte » rédigée dans `database/seeders/data/playable-breeds.php`
(`ClassSheetContentRenderer`, `BreedSeeder`) : identité, passif en kref, repères, ce que la classe n’est pas,
synergies. Le passif, les voies et les sorts restent sur `BreedViewFull`. Plus de dump « Capacités disponibles ».
L’état de la fiche classe (`draft` / `playable`) reste celui du JSON `entities/breeds/*.json`.

### Métadonnées globales des sorts

Les fiches de sort stockent indépendamment des effets détaillés les contraintes globales de lancement :
`cast_in_line`, `cast_in_diagonal`, `target_type` (`direct`, `trap`, `glyph`), `max_stack` et
`global_cooldown`. Le ciblage, la résolution et l’utilisation en réaction sont globaux et ne varient pas
selon le degré. `area` conserve la zone d’un sort descriptif sans degré. Les deux limites numériques vont
de 0 à 10 ; `max_stack = 0` signifie « non limité ».
Lors d'un import DofusDB, ces valeurs viennent du premier niveau du sort et `target_type` est déduit de ses
triggers. Cette couche descriptive ne modifie pas l'exécution ni la résolution des effets.

### Créer et faire progresser un sort

1. Renseigner les informations propres au sort : nom, description, image, catégorie, état, types et
   nature Physique/Wakfu.
2. Les propriétés globales non redondantes restent visibles dans **Propriétés du sort** : ciblage,
   résolution, réaction et zone par défaut, avec l’aide de notation.
3. Pour un sort uniquement descriptif, ouvrir **Propriétés sans degrés ni effets** et définir ses PA,
   sa portée (`4` ou `2-8`) et ses contraintes de lancer. Ce panneau reste fermé dès
   lors que la progression structurée est utilisée.
4. Pour un sort qui évolue, ajouter un premier degré dans **Degrés & effets**. Les propriétés du
   degré remplacent alors les valeurs par défaut à l’affichage.
5. Ajouter les actions du degré : dégâts/soin avec élément, bonus/malus avec caractéristique,
   invocation avec créature, ou application d’un état. Chaque action possède sa propre durée.
6. Ajouter le degré suivant : ses propriétés sont copiées et ses effets sont hérités par défaut.
   Choisir **Personnaliser les effets** uniquement lorsqu’ils changent.

La fiche affiche le premier degré par défaut puis actualise PA, portée, ligne de vue, zone,
résolution et effets lorsque l’utilisateur change d’onglet. L’éditeur liste aussi les monstres,
PNJ et classes qui utilisent le sort.

## Droits

La logique est centralisée dans `app/Policies/Entity/BaseEntityPolicy.php`. Pour la lecture (`view`) :

1. Un **admin** voit tout ; l'**auteur** (`created_by`) voit toujours sa fiche.
2. Une **matrice « Gérer l'affichage »** (`EntityDisplayVisibilityService`) fixe le rôle minimal par état.
3. Ensuite, selon `state` :
   - `playable` / `archived` : visible si `rôle ≥ read_level`.
   - `raw` / `draft` / `auto` : réservé aux éditeurs (`rôle ≥ write_level`).

Pour l'écriture (`update`/`delete`) : admin, auteur, ou `rôle ≥ write_level`. Les abilities « bulk » (`updateAny`, `deleteAny`, `manageAny`) ciblent game_master/admin. Seuls les **admins** posent `read_level` / `write_level` (formulaires et `PATCH …/bulk`). Le registre des permissions exposées au front est dans `config/entity-permissions.php` (consommé par `EntityPermissionService`, partagé via Inertia → composable `usePermissions`).

La restauration et la suppression définitive (`restore`, `forceDelete`) sont réservées aux admins/super-admins via la policy de base.

## Prix (kamas)

- **Ressources** : `price` issu de Dofus, pas de formule ; une fiche **jouable** ne descend pas sous **1 kama**.
- **Équipements** et **consommables** : `price_calculated` + `price_custom` (trait `HasKamasPrice`) ; la colonne `price` est le total affiché. Si les deux parts sont vides au save, le `price` déjà en base est repris comme `price_custom`. L’édition multiple (`PATCH …/bulk`, champ `price`) pose ce **total**, pas un ajustement additionné à la formule.
- Formule équipement : somme des (valeur JSON `bonus` × `characteristic_object.base_price_per_unit`) + 150 × niveau + 200 × rareté (0–5).
- Formule consommable : somme des prix des ressources liées × quantité (`consumable_resource`). Soins hors combat (pain, poisson, viande, potion) : 11 paliers, recette = 10 × la ressource associée (prix ressource ≈ prix du consommable / 10), JSON `healing-out-of-combat.json`. Parchemins de caractéristique : respec 1/2/3/4 points, sans recette, `price_custom` 1 000 / 3 000 / 5 000 / 10 000, JSON `characteristic-respec-scrolls.json`. Utilitaires (rappel, antidote, bière, café, Wakfu, renaissance, bouclier, PV temporaires) : JSON `utility-playable.json`. Tous via `ConsumableSeeder`. Fiches des 19 classes : JSON `entities/breeds/*.json`, `ClassBreedSeeder` avant `CapabilitySeeder` (passifs) puis `MonsterSeeder` (invocations + bestiaire JDR) puis `SpellSeeder`. Passifs de classe : JSON `entities/capabilities/class-passives.json`. Invocations de classe : JSON `entities/monsters/class-summons.json` (19 fiches + 1 sort-créature). Bestiaire JDR : JSON `entities/monsters/incarnam.json` + `amakna.json` (28 fiches, 1 à 2 sorts-créature, `jdr:bestiary:…`). Sorts de classe (19 classes, 24 sorts = 3×2 au niveau 1 + 9×2 en progression) : JSON `*-level-1.json` et `*-progression.json`, `SpellSeeder` (liaison `invoquer` → fiche).
- Recalcul unitaire : `POST entities.items|{consumables}/{id}/recalculate-price`. Masse : `php artisan entities:recalculate-prices {items|consumables}` (gestion du contenu). Les consommables **jouables** sont exclus (barème JDR : recette gelée ou `price_custom` sans recette).

## Backend (CRUD)

- Un contrôleur web par entité : `app/Http/Controllers/Entity/<Type>Controller.php` (ex. `SpellController`, `ItemController`, `MonsterController`). Pattern : `index`/`show` publics, `create`/`store`/`edit`/`update`/`destroy` sous `auth`, plus des routes relationnelles (ex. sorts d'une classe). L’enregistrement sheet Inertia (`EntityEditForm` → `PATCH …/update`) persiste les champs validés pour les campagnes, scénarios et boutiques (`back()`), comme pour les objets / consommables.
- Validation : Form Requests dédiées dans `app/Http/Requests/Entity/` (une par action).
- Transformation de sortie : `app/Http/Resources/Entity/`.
- Lecture en table : `app/Http/Controllers/Api/<Type>TableController.php` (datasets TanStack server-side), changement d'état via `EntityStateController`.
- Corbeille entités : API générique `api/entities/{entityType}/{id}` (`DELETE` soft delete), `POST .../restore`, `DELETE .../force`, `GET .../delete-impact`. La logique commune est dans `app/Services/Entity/EntityDeletionService.php` et la résolution des modèles dans `app/Support/EntityModelRegistry.php`. Les routes web `DELETE entities/{type}/{id}` déléguent aussi à ce service (notifications + journal admin).
- Soft delete : trait `SoftDeletes` sur les modèles d’entité JDR, y compris `Monster` et `Npc` (colonne `deleted_at`).
- Force delete : détache les relations `BelongsToMany`, supprime les médias Spatie, puis `forceDelete` ; refusé (422) si l’entité n’est pas déjà en corbeille.
- Journal admin : les suppressions/restaurations d'entités alimentent `admin_activity_logs` via `AdminActivityLogger`. La page `/admin/activity-log` affiche les activités récentes et la corbeille centralisée, avec confirmation + récapitulatif d’impact avant restauration / suppression définitive.

## Frontend

- **Registre** : `resources/js/Entities/entity-registry.js` associe à chaque type son modèle (`resources/js/Models/Entity/`), ses descriptors de champs (`resources/js/Entities/<type>/<type>-descriptors.js`), son `responseAdapter` (via `createEntityAdapter`) et des `minimalImportantFields`. `normalizeEntityType()` gère les variantes singulier/pluriel/camelCase.
- **Pages** : `resources/js/Pages/Pages/entity/<type>/` (`Index.vue` table, `Show.vue`, `Edit.vue`).
- **Vues** : 5 vues canoniques résolues par `resources/js/Utils/entity/resolveEntityViewComponent.js` :

  | Vue | Composant | Usage |
  | --- | --- | --- |
  | `minimal` | `*ViewMinimal` | Cartes, grille |
  | `line` | `*LineRow` | Liste dense (table) |
  | `text` | `*ViewText` | Inline + overlay |
  | `full` | `*ViewFull` | Détail page ou modal |
  | `edit` | `EntityEditForm` / pages `Edit.vue` | Édition unitaire |

  Les composants par type sont dans `resources/js/Pages/Molecules/entity/<type>/`. Sorts et capacités (Minimal / Line) : halo coloré par l’élément (`entity-element-ring` : lueur intérieure large, lueur extérieure légère, filet 20 %), dégradé si plusieurs primaires ; le fond glass reste le thème. Notes de règles : `spellTypeRuleNotes` (sorts), `consumableRuleNotes` / `itemRuleNotes` + `EntityRuleNotes`.
- **Table** : `resources/js/Pages/Organismes/table/EntityTanStackTable.vue` (server-side) ; préférences/filtres via `resources/js/Composables/table/*`. Ouverture en vue **minimal** (`useTanStackTablePreferences` v4). Les en-têtes lisent `column.tooltip`. Catalogue objets : image, nom, niveau, type, rareté, bonus (`items.bonus`) ; filtre état en pastilles (défaut Jouable), comme les autres catalogues. Rareté 0–5 : Commun, Peu commun, Rare, Très rare, Légendaire, Unique (mêmes libellés filtres et vues). Catalogue objets / consommables / ressources / monstres / sorts / panoplies : le filtre Type (ou Race) arrive vide (Tous), sans pré-coche `show_in_catalog`. Recettes (ingrédients ressources) : catalogue et fiche lecture ne sérialisent que les ingrédients `visibleToUser` ; l’écran Modifier charge toute la recette. Les ressources des items `playable` sont elles-mêmes `playable` (`MarkPlayableItemRecipeResources`). Panoplies : pièces en vue texte, vignette = images d’équipements ou initiales ; **niveau du set = plus haut niveau des pièces visibles** (badge Minimal compact/déployé, Line, Full ; lecture seule en édition) ; filtres catalogue = niveau (slider min/max), nombre de pièces, types d’objets présents, bonus (sélecteur de caractéristique puis min/max), tous les états ; tri défaut par niveau croissant ; catalogue et fiche lecture ne sérialisent que les pièces `visibleToUser` avec `bonus`/`effect` (l’édition charge toutes les pièces liées). En Minimal déployé, une ligne Total additionne pièces + paliers du set. Un équipement du set affiche `ItemPanoplyMark` (payload `panoplies` via `ItemPanoplyPayload`, limité aux panoplies et pièces `view` pour le visiteur). Édition panoplie : équipements puis bonus en tête (cartes du formulaire, `PanoplyBonusEditor`) ; droits en bas. Recherche catalogue (`EntityPickerCore`).
- **Afficher** : Minimal / Line / Index / CMS (`SectionEntityTableRead`) ouvrent la modal full (`EntityModal`). **Agrandir** (depuis la modal) ou Ctrl+clic mènent à la page Show.
- **Éditer** : raccourci des options → page Modifier. La sélection de lignes (cases toujours visibles) sert au CSV et au PDF. Les raccourcis du tableau n’interfèrent pas avec la recherche.
- **Query tableaux** : listes en `filters[key][]` (CSV encore accepté) ; plages en `filters[key][min]` / `[max]` (`InterpretsEntityTableFilters`). Charte unique : niveau en slider dual (`RangeDualCore`, bornes du catalogue), état en pastilles à droite du titre « Filtres » (défaut Jouable ; conditions hors Brut), types / rareté en menu select, filtres avancés via **Ajouter un filtre** (liste, pas un dump), bonus équipements / panoplies en sélecteur de caractéristique (`picked-range`, `filters[bonus][vitality][min]` ; panoplie = somme des paliers), plus d’input texte (la barre de recherche suffit). Les pastilles de liste (`ui.layout: "chips"`) restent un opt-in. Le tri mappe les ids de colonnes (`item_type`, `monster_race`) vers les FK SQL ; `column.sort.field` côté front. La recherche serveur envoie `search=`. Les inputs de filtres gardent la saisie en cours (défauts seulement si la clé n’est pas déjà posée).
- **Aperçu sort depuis un monstre** : sorts liés avec `effect_usages_chips` (`SpellNestedPreviewSerializer`) ; eager-load table/show filtré `visibleToUser` (un sort brouillon ne fuit pas via un monstre jouable). Clic → `SpellViewMinimal` étendu (effets + actions à droite du titre, overflow dropdown). Fetch `api.tables.spells` seulement si les chips manquent.
- **PNJ** : même schéma de visibilité nested (sorts / équipements / traits `visibleToUser` sur show / table / PDF ; boutique, panoplies, scénarios et campagnes aussi, y compris `has_shop` / compteurs du catalogue ; classe et spécialisation liées aussi — une classe brouillon ne fuit pas via un PNJ jouable ; l’édition charge la liaison). Identité combat sur `Creature` ; rôle, taille, langues et récit sur `Npc`. Catalogue Index en pagination serveur (tri nom / stats via sous-requête créature, filtres hostilité et combat comme les monstres). Création (`NpcController` / `MonsterController`) : `Creature::create` (fiche vide, totaux `null` pour la composition), pas la factory de tests. Édition autonome de la fiche (nom, lieu, hostilité, niveau patchent la Creature). Kit : picker sorts (préfiltre classe si `breed_id`) ; stuff 1/slot (2 anneaux). Pas d’action DofusDB.
- **Classes** : sur show / table / PDF / page bibliothèque CMS (`LinkedEntityShow`), les sorts, capacités, traits et PNJ liés passent par `visibleToUser` (pas de fuite d’un sort brouillon via une classe jouable). L’écran Modifier charge toutes les liaisons.
- **Spécialisations** : sur show / table / PDF / page bibliothèque CMS, les sorts, capacités, traits, objets, consommables, ressources et PNJ liés passent par `visibleToUser` (pas de fuite d’un sort brouillon via une spécialisation jouable). L’écran Modifier charge toutes les liaisons.
- **Édition** : `resources/js/Pages/Organismes/entity/EntityEditForm.vue`, modales (`EntityModal`, `CreateEntityModal`). Création courte : whitelist `getEntityCreateCoreFieldKeys` ; `redirect_after_create=edit` ; JSON `{ id, edit_url }` si `Accept: application/json` (`RedirectsAfterEntityCreate`).
- **Création depuis les bibliothèques CMS** : bouton sur `SectionEntityTableRead` (`can.create`) → `CreateEntityModal` (à la main / Conversion IA admin) puis page Modifier.
- **Suppression UI** : `useEntityActionDispatcher` ouvre une `ConfirmModal` avec le récapitulatif `delete-impact` (relations détachées, médias) avant soft delete depuis page/modal d’entité.

## Exemple : ajouter un champ à une entité

1. Migration + champ sur le modèle Eloquent (`app/Models/Entity/<Type>.php`).
2. Form Request (`app/Http/Requests/Entity/`) pour la validation.
3. Descriptor front (`resources/js/Entities/<type>/<type>-descriptors.js`) pour l'affichage/édition.
4. Si pertinent en table : colonne dans la config TanStack du type.

## Pour aller plus loin

- `docs/features/entities/README.md` — modèle de données détaillé, champs par entité, pivots.
- `docs/frontend/entity-views/README.md` — système de vues.
- `.cursor/rules/entity-views.mdc` — conventions de vues.
- [IA générative](../../IA/README.md) — cadrage LLM ; l’état `auto` (proposition à relire) est dans le code, pas le pipeline.
