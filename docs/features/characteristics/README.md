# Caractéristiques

Les caractéristiques décrivent les valeurs numériques et règles de lecture des créatures, objets, sorts, consommables et profils : niveaux, dégâts, PA/PM/PO, limites, formules et normes.

Pour le modèle **formule complète + contexte**, les bonus éphémères, les limites absolues, les formules saisies et le sélecteur de niveau, voir
[COMPUTED_VALUES.md](./COMPUTED_VALUES.md).

## Nature (rôle)

Chaque ligne `characteristics` porte une **nature** indépendante du groupe :

| Nature | Rôle |
| --- | --- |
| `descriptif` | Information sans formule ni lien (nom, prix, zone de sort…) |
| `intrinsic` | Propriété de base d'une créature, d'un sort ou d'un consommable |
| `bonus_malus` | Bonus permanent (équipement, panoplie, profil) |
| `buff_debuff` | Bonus temporaire (sort, consommable) — UI « Buff / Débuff / Vol » |
| `action_value` | Valeur d'action (dégâts, soin, distances…) — `min` ≥ 0 |

Les natures `bonus_malus` et `buff_debuff` pointent l'intrinsèque créature via `intrinsic_characteristic_id` (clé JSON `intrinsic_key`, libellé admin « Bonus de »). Ce lien est une métadonnée (« je suis le bonus de X ») : il est interdit sur une intrinsèque, mais **toute formule peut référencer n'importe quelle clé**.

Les termes `buff_debuff` d'une formule créature sont **éphémères** : ils valent 0 hors combat et ne sont jamais bornés par une limite absolue.

## Règles de formule et de limite (créature)

- Formule **complète** : intrinsèques + `*_object` + `*_consumable` + `*_spell` (script `scripts/apply_characteristic_absolute_limits.py`, idempotent).
- **Limite absolue** (`limit_absolute`) : mods (−2 / ⌊niv/4⌋+2, max 7 ; monstre 10), compétences et sauvegardes ±15 (monstre ±20), bonus de maîtrise 0–6. Les autres limites sont souples.
- `formula_display` : obligatoire dès qu'une formule est renseignée. Côté créature, texte joueur abrégé, sans consommable ni sort ni clé brute ; côté objet, même table que la formule réelle.

## Groupes

`creature`, `object` (équipements + panoplies), `spell`, **`consumable`**, **`profile`** (maîtrises classe / spécialisation / monstre). Les ressources ne font plus partie du système.

## Backend

- Définitions et lecture : `app/Services/Characteristics/`, `app/Support/Characteristics/`.
- Limites : `app/Services/Characteristic/Limit/CharacteristicLimitService.php`.
- Formules : services sous `app/Services/Characteristic/` (`FormulaExpressionParser`, `LevelDomainResolver`).
- Runtime créature : `CreatureRuntimeStatsService` + `ProfileMasteryResolver` (variables `*_mastery_profile`).
- Seeders : `CharacteristicSeeder`, `CreatureCharacteristicSeeder`, `ObjectCharacteristicSeeder`, `SpellCharacteristicSeeder`, `ConsumableCharacteristicSeeder`, `ProfileCharacteristicSeeder`. La base est la source ; réécriture : `project:seed --overwrite`. Export : `scrapping:seeders:export --characteristics`.
- Métadonnées front : `CharacteristicMetaByDbColumnService` (cache `v7`) — `helper`, descriptions, `nature`, `limit_min` / `limit_max` (bornes larges si dépendantes du niveau), `limit_absolute`. Les caractéristiques créature calculées (`db_column = null`) sont découvertes depuis les définitions, sans liste PHP parallèle.
- Qualité : `CharacteristicDefinitionQualityService`, `CharacteristicNatureQualityService`.
- Reprise des totaux existants : `php artisan creatures:derive-context-bonuses`.

Les vues Vue lisent les valeurs calculées dans `CreatureRuntimeStatsService` : elles ne réimplémentent plus les formules, limites ou bonus de compétence. `NpcStatGabarit` lit de son côté les normes `initiative_creature` et `hit_bonus_creature`, indépendamment de la classe d'armure.

## Normes

Les définitions JSON dans `database/seeders/data/characteristic-definitions/` sont la copie versionnée des tables (grilles de normes 5 puissances × 20 niveaux, `norms_grid`). L'audit 2026-07 ne laisse aucun `norms_grid` manquant sur les vraies définitions `creature`, `object` et `spell`; seuls les fichiers `_templates` restent volontairement vides.

### Normes générées par formule (`norms_formula`)

Colonne nullable sur `characteristic_creature` / `characteristic_object` / `characteristic_spell`. Syntaxe identique aux formules runtime (expression ou table JSON `{"characteristic":"level",…}`).

Variables de génération : `[level]` (1–20), `[power]` (−2…+2), `[clé]` = norme de cette caractéristique au même niveau et même palier.

Service : `NormsGridGenerator` (ordre topologique, rejet des cycles). Commande : `php artisan characteristics:regenerate-norms`. Après seed des pivots, `RegenerateCharacteristicNormsSeeder` régénère les grilles. Admin : champ formule + aperçu dans `NormsPanel` ; hub **Équilibrage** (`/admin/content/balance`).

Pour les objets, les caractéristiques dont l'aide cible un type d'équipement portent aussi `item_type_dofus_ids` :
- amulettes : `[1]`
- anneaux : `[9]`, ceintures : `[10]`, bottes : `[11]`
- chapeaux : `[16]`, capes : `[17]`, boucliers : `[82]`
- armes : `[2,3,4,5,6,7,8,19,21,22,114,271]`

Ces valeurs sont les identifiants stables de types DofusDB, jamais les clés locales de la table
`item_types`. Une aide mentionnant plusieurs emplacements produit l'union correspondante.

Le tableau MJ des bonus d’équipement (`EquipmentBonusTableService`, template CMS `equipment_bonus_table`)
lit ces pivots et la table JSON `formula` (plus grand seuil ≤ début de bande 1, 3, … 19). Les méta
(nom, description, niveau, rareté, prix, poids) sont ignorées. API : `GET /api/characteristics/equipment-bonus-table` (rôle ≥ MJ).

Le tableau public des runes de forgemagie (`ForgemagieRuneTableService`, template CMS `forgemagie_rune_table`)
lit les mêmes pivots, mais ne retient que les caractéristiques réellement forgemageables : `forgemagie_max > 0`
et `rune_price_per_unit` renseigné. Le prix de la rune est stocké, pas calculé — en base il vaut le double de
`base_price_per_unit`. La colonne « Équipements » vient de `characteristic_object_item_type` ; un pivot sans
type autorisé signifie « tous les équipements ». Une caractéristique déclarée sur plusieurs entités n'apparaît
qu'une fois. API : `GET /api/characteristics/forgemagie-rune-table` (lecture publique, invité compris).

C'est la source de vérité affichée par le livre de règles : `private/game/rules/4-Le-monde-des-douze/4.3-les-metiers/4.3.4-forgemagie.md`
ne recopie plus aucun prix et renvoie vers la page **Les métiers**.

## Surcharges des monstres

Une définition `entities.monster` remplace les limites et la formule génériques uniquement pour les monstres.
Les personnages et PNJ restent plafonnés comme `*` (ex. scores principaux −24…+24 après symétrie). Les monstres élargissent chaque borne de **50 % de l’amplitude** PJ : un score −24…+24 devient **−48…+48**. Si l’overlay monstre était déjà plus large, on conserve le plus souple.

Les tables de formule acceptent des seuils négatifs. La tranche retenue est le plus grand seuil inférieur
ou égal à la valeur source, ce qui permet de convertir les faiblesses Dofus en `-50` ou `-100`.

## Prix des équipements et consommables

`EquipmentPriceCalculator` calcule le prix automatique d’un objet : somme des (valeur JSON `effect` / bonus Krosmoz × `characteristic_object.base_price_per_unit`) + 150 × niveau + 200 × rareté (0–5). Un malus baisse le total ; plancher 0. Les clés courtes du JSON (`strength`) sont résolues comme `strength_object`. `items.bonus` conserve le brut DofusDB.

`ConsumablePriceCalculator` somme les prix d’affichage des ressources de la recette (`consumable_resource.quantity`). `EntityPriceRecalculator` persiste `price_calculated`, remet `price_custom` à null, et synchronise `price` via le trait `HasKamasPrice`.

Les ressources gardent le prix Dofus : pas de formule ni de recalcul.

## Conversion des bonus d'objets

Hors exclusions (`level_*`, `hostility_*`, `hit_dice_*`, `life_dice_*`, bornes non numériques, bool/string),
les bornes littérales des groupes **créature / objet / sort** sont symétriques : `min = −|max|`.
Les six caractéristiques principales vont de `-4` à `+4` sur l'équipement, avec une marge de forgemagie
de `+2` (total ±6). Les PA vont de `-5` à `+5` (forgemagie `1`) et les PM de `-2` à `+2` (forgemagie `1`).
Le critique suit la même convention signée (`-3..3`) côté objet et créature (overlay monstre recalculé ±50 %).
Les libellés FR des dommages fixes utilisent **dommage** (pas « dégât »). Plus de caractéristiques
`failure_hit_*`, puissance/poussée, DO/résist. sagesse·vitalité, ni compétences Artisanat / Herbaliste /
Connaissance des créatures. Les bonus de PV et d'initiative restent sans plafond d'équipement ; leur
forgemagie est limitée respectivement à `20` et `3`.

Les résistances fixes de bouclier sont limitées à `±7` hors forgemagie, avec `±3` supplémentaires en
forgemagie.

Les résistances en pourcentage des objets individuels ne produisent aucun palier Krosmoz. Seuls les bonus
de panoplie sont convertis : `< -50 %` donne `-2`, `-50..-20 %` donne `-1`, `-19..7 %` donne `0`,
`8..12 %` donne `1` et `>= 13 %` donne `2`.

L'ID DofusDB `0` n'est pas associé aux PV d'objet, car il est réutilisé par de nombreux effets techniques.
Les dommages fixes multi-éléments utilisent l'ID caractéristique `16` (`allDamageBonus`), et non l'ID `103`
(`weaponPower`).

## Conversion des caractéristiques de sorts

Les paramètres globaux de DofusDB sont ramenés aux limites jouables de Krosmoz : niveau minimal ÷ 10 sur
une échelle 1–20, portée 0–20, coût 0–12 PA, 6 lancers par tour, 4 par cible et 10 tours de relance au
maximum.

Les valeurs utilisées par les sous-effets suivent les mêmes unités que les créatures et les objets :

- critique temporaire signé de −3 à +3 ;
- résistances relatives limitées aux paliers −100, −50, 0, 50 et 100 ;
- résistances fixes de 0 à 10 ;
- bonus de portée de 0 à 6 et bonus de soin de 0 à 7 ;
- initiative directe sans plafond artificiel ;
- dégâts fixes élémentaires ou multi-éléments de 0 à 5.

Une entrée nulle reste nulle pour toutes les formules numériques. Les zones, le temps d'incantation et la
durée sont des notations textuelles, pas des quantités numériques. Les restrictions de ligne et diagonale,
le type de cible, le cumul maximal et le délai global disposent désormais de définitions dédiées.

### Budgets dégâts et soins (normes de sort)

Les `norms_grid` de `dommages_spell`, `soin_spell`, `bouclier_spell`, `pv_temporaires_spell` et
`vol_vie_spell` sont la **source de vérité runtime** (générées depuis `norms_formula` Excel : coef × PV,
soin/bouclier/PV temp dérivés, vol de vie = 0,3 × dégâts). Lancement de référence à **3 PA**.

### Conversion Dofus → Krosmoz

- Les bornes **`min` / `max`** du pivot clampent après formule : **pas de `min(N,…)` / `max(0,…)` de résultat** dans `conversion_formula`.
- Valeurs **signées** (buff `+` / débuff `−`) : `([d]/max(abs([d]),1)) * f(abs([d]))` (ou table JSON déjà signée, ex. résistances %).
- **`dommages_spell`** : `([d]/max(abs([d]),1))*(round(2*pow(abs([d]),0.5)))` puis `convertToDice` ; bornes ±80.
- **Éléments sort** (tous présents, signés) : `fixed_damage_{air,earth,fire,water,neutral}` + `do_fixe_multiple` ; `fixed_resistance_*` ; `res_*` (paliers −100…100).
- **Dommages fixes créature / monstre** : compression équipement, bornes ±10 / ±20.

- **Par tour** = cast × PA(niveau, palier) ÷ 3 — PA lus dans `action_points_creature` (`SpellActionBudgetService`).
- **Paliers** : `weak` / `neutral` / `strong` = 3ème / 2ème / 1er rôle.
- **Conditions** (`norms_conditions`) : décalent niveau ou palier (PA, zone, portée, LoS, fréquence, etc.).
- **Invocations** : mêmes normes que les monstres (pas de clé PV d’invocation dédiée).
- Consommables objet (`life_points_restore`, `shield_points`, `temporary_life_points`) = 1 lancement équivalent.
- Outil UI : **Outils → Barèmes de sorts** + template `spell_reference_scales` ; hub admin **Équilibrage**.
- Conversion dés : `resources/js/Utils/dice/diceEquivalents.js`.
- IA PNJ : `NpcStatGabarit` lit les mêmes normes (PV/PA/PM/CA).

Les compétences actives (18 clés `*_spell`, ex. `acrobatics_spell`) sont utilisables dans les sous-effets
`booster` / `retirer` / `voler-caracteristiques` via la catégorie `skill` de `config/effect_sub_effects.php`.
Plafond ±5, sans passives spell.

## Frontend

- Store Pinia : `resources/js/Composables/store/useCharacteristicsPiniaStore.js`.
- Affichages : composants `Characteristic*` dans les Atoms/Molecules.
- Admin : pages `resources/js/Pages/Admin/characteristics/`.

## Lien avec d'autres features

- Scrapping : conversion DofusDB → caractéristiques Krosmoz.
- Effets : effets de sorts/objets liés à des caractéristiques.
