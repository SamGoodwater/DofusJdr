# Audit de cohérence — Règles, BDD, fiches ressources

Ce document vérifie la cohérence entre :
- **Règles** : `private/game/rules/` (2.2.1, 2.2.2, 2.6.1, etc.)
- **Caractéristiques BDD** : `database/seeders/data/characteristic_creature.php`, `characteristic_object.php`, `characteristics.php`
- **Fiches ressources** : `private/game/resources/` (fiche_perso, Equipements et forgemagie, CaractéristiquesAvecLien avec Dofus)

---

## 1. Synthèse

| Zone | Statut | Détails |
|------|--------|---------|
| **Règles ↔ BDD** | ✓ Cohérent | Formules, min/max, plafonds alignés (sauf point 3) |
| **Règles ↔ Fiche personnage** | ✓ Cohérent | Fiche 0.1.3.8 corrigée, alignée avec règles |
| **BDD ↔ Fiche personnage** | ✓ Cohérent | Voir [COHERENCE_FICHE_PERSO_VS_PROJET.md](COHERENCE_FICHE_PERSO_VS_PROJET.md) |
| **BDD ↔ Fiches ressources (PDF)** | ✓ Cohérent | Formules objet/creature alignées sur PDF Équipements et Caractéristiques |

**Point tranché (sept. 2026)** : Bonus Vitalité/Force/etc. par objet — **+4** max (forgemagie +2). Seeders `*_object` et table CMS/PDF alignés. Plus de +8.

---

## 2. Règles ↔ Caractéristiques BDD

### 2.1 Caractéristiques principales (2.2.1)

| Règle | BDD | Statut |
|-------|-----|--------|
| Modificateur = ⌊(Score−10)/2⌋ | `modifier_*_creature` formula | ✓ |
| Plafond mod = min(⌊niveau/4⌋+2, 7) (monstre 10) | `modifier_*_creature` min/max + `limit_absolute` | ✓ |
| Scores 6–24 (PJ/PNJ), jusqu'à 30 (monstre) | `*_creature` base + overlay monstre | ✓ |
| Équipements +4 max (2.6.1) | Voir section 4 | À trancher |

### 2.2 Caractéristiques secondaires (2.2.2)

| Règle | BDD | Statut |
|-------|-----|--------|
| PA base 6, max 12 | `action_points_creature` | ✓ |
| PM base 3, max 6 | `movement_points_creature` | ✓ |
| PO base 0, max 6 | `range_creature` | ✓ |
| PV = dé de vie + mod. Vitalité × niveau + progression du dé + équipement | `life_points_creature` | ✓ |
| CA = 10 + mod. Vitalité + bouclier | `armor_class_creature` | ✓ |
| Initiative = 1d20 + mod. Intel | `initiative_creature` | ✓ |
| Esquive PA/PM = 8 + mod. Sagesse | `dodge_action_points_creature`, `dodge_movement_points_creature` | ✓ |
| Fuite = 1d20 + mod. Agi | `dodge_creature` | ✓ |
| Tacle = 1d20 + mod. Chance | `tackle_creature` | ✓ |
| Dés de vie = floor(niveau/2), max 10 | `hit_dice_creature` | ✓ |
| Bonus maîtrise = 1+floor(niveau/4), max 6 | `mastery_bonus_creature` | ✓ |
| Compétences / sauvegardes permanentes ±15 (monstre ±20) | `*_creature.limit_absolute` | ✓ |
| Réserve Wakfu = bonus maîtrise + équip. | `wakfu_reserve_creature` | ✓ |

### 2.3 Équipements (2.6.1)

| Règle | BDD (characteristic_object) | Statut |
|-------|----------------------------|--------|
| Chapeaux : Vit/Sag +4 max | vitality_object, wisdom_object max 4 | ✓ +4 / forgemagie +2 |
| Capes : For/Int/Cha/Agi +4 max | idem max 4 | ✓ +4 / forgemagie +2 |
| PA amulettes +6 max | action_points_object max 6 | ✓ |
| PM bottes +3 max | movement_points_object max 3 | ✓ |
| PO anneaux +6 max | range_object max 6 | ✓ |
| Invocations anneaux +5 max | summoning_object max 5 | ✓ |
| Boucliers CA +5 max | armor_class_object max 5 | ✓ |
| Esquive PA amulettes +5 max | dodge_action_points_object | ✓ |
| Esquive PM bottes +5 max | dodge_movement_points_object | ✓ |
| Tacle/Fuite ceintures +10 max | tackle_object, dodge_object max 10 | ✓ |

---

## 3. Fiches ressources ↔ BDD

### 3.1 Fiche personnage (fiche_perso 0.1.3.8)

Alignée avec le projet. Voir [COHERENCE_FICHE_PERSO_VS_PROJET.md](COHERENCE_FICHE_PERSO_VS_PROJET.md).

### 3.2 PDF Équipements et forgemagie

| PDF | BDD | Statut |
|-----|-----|--------|
| Tables niveau→bonus (hit_bonus, dommages, etc.) | Formules-table dans characteristic_object | ✓ Appliquées |
| Prix de base, forgemagie | base_price_per_unit, rune_price_per_unit | ✓ |
| Limites forgemagie | forgemagie_max | ✓ |

### 3.3 PDF CaractéristiquesAvecLien avec Dofus

| PDF | BDD | Statut |
|-----|-----|--------|
| Modificateur (carac−10)/2, limites niv 1-3 | modifier_*_creature formula | ✓ |
| Esquive PA/PM = 8 + mod. Sagesse | dodge_action_points_creature, dodge_movement_points_creature | ✓ |
| Dés de vie = floor(niveau/2), max 10 | hit_dice_creature | ✓ |
| Bonus de maîtrise = 1+floor(niveau/4), max 6 | mastery_bonus_creature | ✓ |
| Jets de sauvegarde = mod. + bonus maîtrise | save_*_creature | ✓ |

---

## 4. Canon : bonus Vitalité/Force/etc. par objet = +4

| Source | Valeur |
|--------|--------|
| **Règles 2.6.1 / 2.2.3** | **+4** maximum par objet, forgemagie +2 (total +6) |
| **Seeder `*_object`** | `max` 4, `forgemagie_max` 2, formules paliers jusqu’à 4 |
| **Grille IA `equipment-grid.json`** | paliers 1–5:+1 · 6–10:+2 · 11–15:+3 · 16–20:+4 |
| **Fiches playable** | bonus de carac principale ≤ 4 |

L’ancien PDF « Équipements et forgemagie » (paliers jusqu’à 8) n’est plus le référent. La table CMS d’équipement suit les seeders.

---

## 5. Fichiers de référence

| Fichier | Rôle |
|---------|------|
| `COHERENCE_FICHE_PERSO_VS_PROJET.md` | Fiche personnage ↔ BDD |
| `COHERENCE_SEEDER_REGLES.md` | Règles ↔ seeders (`private/game/rules/`) |
| `PROPOSITIONS_FORMULES_ET_PROPRIETES.md` | Propositions historiques de formules |

---

## 6. Actions

- [x] Canon **+4** / forgemagie +2 (livre 2.6.1, seeders, grille)
- [x] Documenté dans `COHERENCE_SEEDER_REGLES.md`

---

*Document généré à partir des règles 2.2.1, 2.2.2, 2.6.1, COHERENCE_SEEDER_REGLES, fiches PDF et seeders. Mis à jour sept. 2026.*
