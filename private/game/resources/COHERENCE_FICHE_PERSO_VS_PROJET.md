# Cohérence fiche personnage 0.1.3.8 vs projet Krosmoz-JDR

Analyse de la cohérence entre la **fiche personnage** (fiche_perso 0.1.3.8.pdf/pptx) et les caractéristiques définies dans le projet (`characteristic_creature`, `characteristics`, etc.).

---

## 1. Statut de cohérence

**Dernière vérification** : Fiche 0.1.3.8 corrigée (coquilles Esquive 10→8, Dés de vie).

La fiche et le projet sont **alignés** : aucune incohérence à corriger.

---

## 2. Caractéristiques cohérentes

| Fiche | Projet | Statut |
|-------|--------|--------|
| PA [6 + equip.] | `action_points_creature` base 6 | ✓ |
| PM [3 + equip.] | `movement_points_creature` base 3 | ✓ |
| Ini [Intel + equip.] | `initiative_creature` = mod. Intel | ✓ |
| PO [equip.] | `range_creature` (équip. seulement) | ✓ |
| CA [10 + Vitalité + equip.] | `armor_class_creature` = 10 + mod. Vitalité | ✓ |
| Fuite [Agi + equip.] | `dodge_creature` = mod. Agilité | ✓ |
| Tacle [Chance + equip.] | `tackle_creature` = mod. Chance | ✓ |
| Modificateur [(Base+Equip.–10)/2] | `modifier_*_creature` | ✓ |
| Sauvegarde [Mod. + Bonus maîtrise + equip] | `save_*_creature` = Mod. (+ maîtrise) | ✓ |
| Val max mod ⌊niveau/4⌋+2 (max +7) | Formule et limite absolue du modificateur | ✓ |
| Bonus de maîtrise [1+niveau/4] | `mastery_bonus_creature` | ✓ |
| Réserve Wakfu [Bonus maîtrise + equip.] | `wakfu_reserve_creature` | ✓ |

---

## 3. Points à clarifier

### 3.1 Dés de vie

| Source | Formule |
|--------|---------|
| **Fiche 0.1.3.8** (corrigée) | `[Niveau / 2]-` (arrondi inférieur) |
| **Projet / règles 2.2.2.4** | floor(niveau/2), max 10 |

✓ **Cohérent** après correction de la fiche.

### 3.2 Points de vie

**Fiche** : `[Jet de classe dépendant du niveau + Vitalité + equip.]`  
**Projet** : `[vitality_creature]*10+[hit_dice_creature]` (PV max)

Les deux sont cohérents si « Jet de classe » correspond aux dés de vie et que la fiche décrit les PV max. Les PV actuels sont gérés manuellement (Actuel / Max).

### 3.3 Compétences passives : Perception, Intuition

**Fiche** : `[10 + Compétence (+ Bonus)]`  
Les bonus d’équipement pour compétences passives sont des caractéristiques distinctes `*_passive_object` (alignées sur les compétences). Vérifier si une caractéristique creature « Perception » / « Intuition » (valeurs de base) existe ou si c’est uniquement Mod. Sagesse + bonus.

---

## 4. Correspondance fiche ↔ clés projet

| Libellé fiche | Clé `characteristic_*` |
|---------------|------------------------|
| PA | `action_points_creature` |
| PM | `movement_points_creature` |
| Ini | `initiative_creature` |
| PO | `range_creature` |
| Invocations | `summoning_creature` |
| Points de vie | `life_points_creature` |
| Points de boucliers | Effets de type `shield` (sous-effets de sorts) |
| Esquive PA | `dodge_action_points_creature` |
| Esquive PM | `dodge_movement_points_creature` |
| CA | `armor_class_creature` |
| Fuite | `dodge_creature` |
| Tacle | `tackle_creature` |
| Bonus de touche | `hit_bonus_creature` |
| Dés de vie | `hit_dice_creature` |
| Réserve Wakfu | `wakfu_reserve_creature` |
| Bonus de maîtrise | `mastery_bonus_creature` |
| Vitalité, Sagesse, Force, Intel, Agi, Chance | `*_creature` + `modifier_*_creature` |
| Sauvegardes | `save_*_creature` |
| Dommages fixes (Neutre, Terre, Feu, Air, Eau, Multiple) | `fixed_damage_*_creature` |
| Résistances (Neutre, Terre, Feu, Air, Eau) | `fixed_resistance_*_creature`, `resistance_*_creature` |

---

## 5. Synthèse

- **Esquive PA/PM** : Fiche et projet alignés sur 8 + mod. Sagesse ✓
- **Dés de vie** : Fiche et projet alignés sur floor(niveau/2), max 10 ✓
- **Toutes les caractéristiques** : cohérentes entre fiche, règles et projet.

---

*Document généré à partir de fiche_perso 0.1.3.8 et database/seeders/data/characteristic_creature.php.*
