# Dés et formules

Le moteur de formules de dés sert la barre de recherche et l’outil **Lanceur de dés**. Il restera le point d’entrée pour d’autres calculs JDR (stats de créatures, plus tard).

## Saisie

Toute la chaîne doit être une formule. Caractères acceptés après normalisation : chiffres, `d`, `.`, `[ ]`, `+ - * /`.

| Élément | Exemples |
| --- | --- |
| Dé | `d12`, `3d8` |
| Nombre | `3`, `1.5`, `2,5` |
| Tranche | `[2-6]` |
| Opérateurs | `+` `-` `*` `/` et alias `x` `×` `:` `÷` |

Priorité : `*` `/` avant `+` `-`. Pas de parenthèses, pas de `eval`.

Une tranche entière `[min-max]` s’affiche en dés exacts : chaque valeur est équiprobable. `[2-6]` → `1d5+1` (un `1d4+2` donnerait 3–6). Min / max / moyenne et le lancer utilisent la fourchette réelle.

## Backend

- `App\Services\Jdr\DiceFormulaService` : `analyze()` (min, max, moyenne, équivalents) et `roll()`.
- `App\Services\Jdr\DiceNotationService` : `fromInclusiveRange()` (conversion exacte) et `toDiceNotation()` (approximation d4–d20 pour le scrapping).
- Singletons dans `AppServiceProvider`.
- Plafonds : 64 caractères, 40 dés, 1000 faces, 12 termes. Liste blanche, jamais `eval`.

## Frontend

Miroir JS : `resources/js/Utils/dice/diceParser.js` (`parseDiceFormula`, `rollDiceFormula`).

- Recherche globale : bande `DiceFormulaStrip` sous le champ si un dé, une tranche **ou un opérateur** est reconnu (`50-17`, `1+4*7`). Un nombre seul (`12`) reste une recherche.
- Outil footer : `DiceRollerModal` — raccourcis ndX, texte d’aide, même bande.
- Conversion valeur → dés (barèmes de sorts) : `diceEquivalents.js` + outil **Barèmes de sorts** (`SpellReferenceScalesModal`).
- Lancer : libellé **Valeur :** + résultat + icône ghost (sans contour, ombre au survol, tooltip « Lancer »). Pas de pastille autour du cluster. Historique en mémoire seulement (`formule = valeur`, une opération après l’autre) ; fermer la recherche ou le modal l’efface.

## Tests

- `tests/Unit/Services/Jdr/DiceFormulaServiceTest.php`
- `tests/Unit/Services/Jdr/DiceNotationServiceTest.php`
- `tests/unit/utils/diceParser.test.js`
