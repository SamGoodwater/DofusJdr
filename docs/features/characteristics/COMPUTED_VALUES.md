# Valeurs calculées des caractéristiques

Une caractéristique d’entité peut être un **total explicite**
ou une **composition** de trois couches, pilotée par des formules qui dépendent du niveau.

## Les couches

```text
permanent = borne_absolue(formule(éphémères = 0) + contexte)
éphémère  = formule(buffs actifs) − formule(éphémères = 0)
total     = permanent + éphémère
```

| Couche | Source | Rôle |
| --- | --- | --- |
| **Formule** | `characteristic_creature.formula` | Formule **complète** : intrinsèques (mods, maîtrise…), équipement (`*_object`), consommable (`*_consumable`), sort (`*_spell`). Ex. initiative : `[modifier_intelligence_creature]+[initiative_object]+[initiative_consumable]+[initiative_spell]`. |
| **Objets** | Somme de `items.effect` (fallback `items.bonus` plat) des équipements portés | Injectée dans les variables `*_object` depuis les **clés courtes** d'agrégation (`strength` → `[strength_object]`, `wakfu_recharge` → `[wakfu_recharge_object]`). Si une formule n'a pas de `*_object`, la couche est ajoutée en repli. |
| **Contexte** | Colonne `<db_column>_context` sur `creatures` | Nombre ou formule saisie pour ce monstre / PNJ (permanent) |
| **Éphémère** | Termes de nature `buff_debuff` | Buffs de sort / consommable : 0 hors combat ; `resolve($creature, $entity, ['initiative_spell' => 2])` les injecte. |

### Limites absolues et limites souples

- **Limite absolue** (`characteristic_creature.limit_absolute`) : `min` / `max` bornent la partie **permanente** au runtime, même si score + équipement montent plus haut. Les éphémères s'ajoutent ensuite, sans borne. Valeurs : modificateurs `-2` / `min(floor([level_creature]/4)+2, 7)` (monstre `-2` / `10`), compétences et sauvegardes `±15` (monstre `±20`), bonus de maîtrise `0` / `6`.
- **Plafonds métier post-composition** : la réserve de Wakfu lit ses `min`/`max` (1–9) ; les invocations appliquent aussi le plafond dynamique du bonus de maîtrise.
- **Limite souple** : toutes les autres. `min` / `max` larges servent à la validation, au clamp de conversion / scrapping et à l'UI ; elles sont tenues indirectement par les limites absolues dont elles dépendent.
- Une borne peut dépendre du niveau. Sans niveau connu (scrapping, formulaires, partage front), `getLimits` renvoie l'intervalle le plus large sur les niveaux 1 à 20.
- Les plafonds **d'un seul terme** restent dans la formule (ex. `min([wakfu_recharge_object],3)`).
- Le runtime tient deux jeux de variables : une compétence évalue sa partie permanente avec le modificateur permanent déjà borné ; un buff du modificateur passe dans la part éphémère de la compétence.
- Chaque ligne résolue expose `ephemeral`, `permanent` et `capped` ; la décomposition (popover) affiche « Effets temporaires » et un badge « Plafonné ».

### Formule affichée

`formula_display` accompagne toute formule non vide. Pour une créature, c'est un **texte joueur abrégé** (`mod. Int + équip.`, `mod. Agi + bonus maîtrise × maîtrise + équip.`), **sans** consommable ni sort et sans clé brute. Pour les tables d'objet, il conserve la table réelle afin que le futur rendu riche affiche les mêmes paliers. Cette couverture est vérifiée par `CharacteristicDefinitionQualityService::formulaDisplayCoverageIssue`; le contenu créature l'est par `formulaDisplayIssue`.

### Priorité du total explicite

Si la colonne de total (`ca`, `ini`, `life`…) est **non nulle**, elle est affichée telle quelle
(`source = total_column`). C’est ce que le scrapping écrit aujourd’hui.

Si la colonne de total est **null**, le moteur compose les trois couches
(`source = composed`).

## Grammaire des formules saisies

Portée : bonus contextuel, niveau, et plus tard bonus d’équipement.

| Forme | Exemple | Effet |
| --- | --- | --- |
| Nombre | `12`, `-3` | Valeur fixe |
| Formule | `{[niveau] / 3}` | Expression, arrondi normal |
| Arrondi haut | `{[niveau] / 3}+` | `ceil` |
| Arrondi bas | `{([vitalite] - 10) / 2}-` | `floor` |
| Référence | `[level]`, `[vitalite]`, `[force]` | Alias FR acceptés |
| Fourchette | `{[5-8]}` | Domaine 5…8 (**niveau seulement**) |
| Dé | `{8 + [1d4]}` | Domaine 9…12 (**niveau seulement**) |

Fonctions autorisées : `floor`, `ceil`, `round`, `sqrt`, `abs`, `exp`, `log`,
`cos`, `sin`, `tan`, `asin`, `acos`, `atan`, `pow`, `min`, `max`.

Implémentation :

- PHP : `app/Services/Characteristic/Formula/FormulaExpressionParser.php`
- JS (aperçu UI) : `resources/js/Utils/characteristic/formulaGrammar.js`
- Domaine de niveau : `app/Services/Characteristic/Domain/LevelDomainResolver.php`

## Seul le niveau porte un domaine variable

Toute caractéristique peut **dépendre** d’autres caractéristiques, mais la seule valeur
modifiable à la main sous forme de fourchette ou de dé est le **niveau**.
Les autres caractéristiques se calculent ensuite niveau par niveau.

L’UI affiche d’abord la valeur du **premier niveau possible**, avec un sélecteur qui
recalcule toute la fiche sans nouvel aller-retour (payload `levels[]` de
`GET /entities/creatures/{id}/resolved-stats`). L’endpoint est public pour les fiches
consultables, mais `CreaturePolicy::viewResolvedStats` n’autorise que si le **monstre
ou le PNJ lié** est `view` pour le visiteur (un brouillon n’est pas lisible par id
créature). Les bonus d’objets agrégés ne gardent que les équipements `visibleToUser`.

## Décomposition UI

Sur les vues `full` / `line` / `minimal`, un **popover** (clic) montre :

- base, objets (détail par équipement), contexte, total ;
- la formule et sa résolution ;
- le tableau des valeurs par niveau.

Composants : `Popover.vue`, `CharacteristicDecompositionBody.vue`,
`CharacteristicsCard` + `CharacteristicGroup` (branchement de `levelEffective`).

## Affichage UI (densités)

`CharacteristicsCard` / `CharacteristicGroup` :

| Densité | Usage | Contenu |
| --- | --- | --- |
| `icon` | Minimal, line, pin | Icône + valeur |
| `labeled` | Modal full | Icône + label + valeur |
| `spacious` | Page full | Idem + aération |

Groupes canoniques : `creatureCharacteristicGroups.manifest.js`
(Combat → Caractéristiques/`abilityStack` → Résistances → Dommages → Contrôle).

Résumé Minimal (`mode: summary`) :

1. **Modificateurs** des 6 stats (mis en avant) ;
2. Combat : `pa`, `pm`, `life`, `ca`, `po`, `ini`, `invocation`.

Le groupe **Caractéristiques** empile par colonne : score → modificateur → sauvegarde
(`AbilityScoreStack.vue`). Les valeurs DB nulles sont résolues via le runtime
(formule complète + contexte) ; la CA utilise `10+[modifier_vitality_creature]+[armor_class_object]+…`.

Résistances : fixe + code relatif entre parenthèses seulement si ≠ 0 %
(`V` Vulnérable / `F` Faiblesse / `R` Résistant / `I` Invulnérable).

Les groupes **Résistances**, **Dommages** et **Contrôle** répartissent leurs
caractéristiques sur toute la largeur (`spread`). Les dommages fixe Sagesse /
Vitalité ne sont plus affichés (hors usage JDR).

Compétences (UI) : le total, sa maîtrise et sa limite permanente viennent uniquement du runtime serveur. Le front ne recalcule ni compétence, ni sauvegarde, ni statistique dérivée ; tant que le runtime n'est pas chargé, il affiche `—`. Affichage résolu : `+N`, `+N (M)`, `+N (E)`.

## Édition

- Champ dédié : `CharacteristicFormulaField.vue` (validation live + aperçu + aide « ? »).
- Éditeur monstre/PNJ : `CreatureComposableCharacteristicsEditor.vue` (branché sur les pages Edit).
- Persistance : `CreatureComposableCharacteristicsPersister` + validation `ValidatesCreatureComposableCharacteristics`.
- Pour chaque caractéristique composable : deux saisies distinctes —
  **total explicite** (colonne existante) et **bonus contextuel** (`*_context`).
- Le niveau accepte les domaines ; le contexte ne les accepte pas.

## Modèle de données

Migration `2026_08_07_220000_add_creature_context_bonus_columns` :

- ajoute `<colonne>_context` (**TEXT** nullable) pour chaque colonne composable ;
  (TEXT plutôt que VARCHAR pour rester sous la limite MySQL de taille de ligne) ;
- convertit aussi les totaux en TEXT et les rend **nullable** (null ≠ 0).

Migration `2026_08_13_020000_add_creature_do_fixe_multiple_columns` :

- ajoute `do_fixe_multiple` + `do_fixe_multiple_context` (Dommage fixe Multiples / DO mult.) ;
- relie `fixed_damage_multiple_creature.db_column` sans reseed global.

Liste des colonnes : `App\Support\Creature\CreatureComposableColumns`.

## Runtime

`CreatureRuntimeStatsService` :

1. résout le domaine de niveau ;
2. pour chaque niveau, sépare objets (`X_object`) et contexte ;
3. priorise le total explicite s’il existe ;
4. sinon compose base + objets + contexte ;
5. renvoie `levels[{ level, characteristics }]` + `items.lines` (rétrocompat `computed`).

## Reprise de l’existant

```bash
php artisan creatures:derive-context-bonuses --entity=monster --dry-run
php artisan creatures:derive-context-bonuses --entity=monster --clear-total --report=storage/logs/derive-context.md
```

Pour chaque caractéristique : `contextuel = total − base − objets`.
Sans `--clear-total`, le total reste prioritaire à l’affichage.

## Hors périmètre

- Buffs / effets temporaires de combat (4ᵉ couche).
- Modèle joueur dédié.
- Renommage physique des colonnes de total.
