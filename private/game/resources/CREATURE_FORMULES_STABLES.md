# Formules créature complètes

## Portée

Les définitions **creature** dont la formule est stockée en BDD (`characteristic_creature.formula`) décrivent toutes les couches connues dans une formule unique.

- **Permanent** : colonnes et caractéristiques `_creature`, maîtrises `_profile`, bonus d’objets `_object`, contexte MJ.
- **Éphémère** : caractéristiques de nature `buff_debuff`, notamment `_consumable` et `_spell`.
- **Affichage** : `formula_display` montre une version joueur abrégée (ex. `mod. Int + équip.`), sans les termes éphémères.

## Runtime

1. `CreatureVariableMapBuilder` construit la carte à partir des colonnes / `characteristic_creature` (clés = `characteristic.key`).
2. `CreatureItemBonusAggregator` somme les bonus JSON des objets (clés courtes).
3. `CreatureObjectBonusToCreatureVariables::mapToCharacteristicKeys` mappe les bonus vers leurs clés créature sans les fusionner dans la base.
4. `CreatureRuntimeStatsService` évalue deux cartes : éphémères à 0 pour le permanent, puis valeurs actives pour le total complet.
5. Une limite `limit_absolute` borne uniquement le permanent ; l’éphémère s’ajoute après.
6. `FormulaVariableResolver::withShortNames('creature', …)` ajoute les noms courts (`[level]` pour `[level_creature]`, etc.).

## Convention de formule (compétences & sauvegardes)

- **Maîtrise** : paliers `0 / 1 / 2` via **`[…_mastery_profile] * [mastery_bonus_creature]`**.
- **Bonus équipement** : **`[…_object]`** aligné sur la clé courte d’agrégation (ex. `acrobatics` → `[acrobatics_object]`).
- **Temporaire** : **`[…_consumable] + […_spell]`**, masqué dans `formula_display`.
- **Limites** : compétences et sauvegardes permanentes ±15 (PJ/PNJ), ±20 (monstre).

## Valeur effective

```text
permanent = borne_absolue(formule(éphémères = 0) + contexte)
éphémère  = formule(valeurs actives) − formule(éphémères = 0)
total      = permanent + éphémère
```

Le futur outil de combat injectera les valeurs `_spell` / `_consumable` au runtime ; hors combat elles valent 0.

## Validation

La commande Artisan vérifie que chaque `[id]` utilisé dans les seed JSON creature est reconnu (liste blanche : caractéristiques, colonnes `creatures`, alias courts, variables bonus français, `d` pour conversions) :

```bash
php artisan characteristics:validate-creature-formula-placeholders
```

## Voir aussi

- [PROPOSITIONS_FORMULES_ET_PROPRIETES.md](./PROPOSITIONS_FORMULES_ET_PROPRIETES.md) — propositions générales formules / objets.
- [SYNTAXE_FORMULES_CARACTERISTIQUES.md](../../10-BestPractices/SYNTAXE_FORMULES_CARACTERISTIQUES.md) — syntaxe moteur.
