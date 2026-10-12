# Dés / formules — IA

> Parser sécurisé de formules tapées (dés, tranches, opérateurs, nombres). Pas d’`eval`.

## Quand lire

- Modifier min/max/moyenne, conversion `[min-max]` → dés, lancer simulé.
- Brancher plus tard des stats de créatures sur le même service.

## Règle de conversion

`[min, max]` inclusif → `1d(max-min+1)+(min-1)`. Ex. `[2-6]` = `1d5+1`.

## Fichiers

- PHP : `app/Services/Jdr/DiceFormulaService.php`, `DiceNotationService.php`
- JS : `resources/js/Utils/dice/diceParser.js`
- UI : `DiceFormulaStrip.vue` (Valeur + icône ghost Lancer + historique session), `SearchInput.vue`, `DiceRollerModal.vue`
- Conversion moyenne → dés : `diceEquivalents.js` (`suggestDice`, critique = dés ×2) ; UI barèmes : `SpellReferenceScales.vue`
- Reconnu : dé, tranche **ou** opérateur (`50-17`). Un nombre seul (`12`) n’est pas une formule de recherche.
- Historique : props `history` `{ formula, value }[]`, pas de localStorage / cookie.
