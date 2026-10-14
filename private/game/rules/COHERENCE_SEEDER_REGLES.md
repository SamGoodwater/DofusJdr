# Cohérence Seeder / Règles – Valeurs et formules

Ce document compare les valeurs min/max, formules et limites des caractéristiques entre les seeders (`database/seeders/data/`) et le livre de règles (`private/game/rules/`).

**Alignement noms ↔ clés BDD** : voir [REFERENCE_CLES_CARACTERISTIQUES.md](REFERENCE_CLES_CARACTERISTIQUES.md).

---

## 1. Ce qui est cohérent

| Élément | Règles | Seeder | Statut |
|--------|--------|--------|--------|
| **PA (créature)** | Base 6, max 12, équip. +5, forgemagie +1 | `action_points_creature` min 6, max 12 | OK |
| **PM (créature)** | Base 3, max 6, équip. +2, forgemagie +1 | `movement_points_creature` min 3, max 6 | OK |
| **PO (créature)** | Base 0, max 6, équip. +6, forgemagie +1 | `range_creature` min 0, max 6, formula_display idem | OK |
| **Scores caractéristiques** | Personnages 6–24 ; monstres 6–30 | `*_creature` : max 24 (`*`), max 30 (`monster`) | OK |
| **Modificateurs** | ⌊(Score − 10) / 2⌋ | `modifier_*_creature` : max +7 (`*`), max +10 (`monster`) | OK |
| **Compétences / sauvegardes** | Permanent ±15 (PJ/PNJ), ±20 (monstre) ; effets temporaires après la borne | `*_creature.limit_absolute`, overlays monstre | OK |
| **PV (créature)** | Le livre donne aussi des ordres de grandeur (ex. §5.1.2 création PNJ) ; **formule officielle seed** | `life_points_creature` : `hit_dice_creature + modifier_vitality_creature×level + (level−1)×round(hit_dice/2) + life_points_max_object` | Recaler les textes de règles si une seule formule « canon » est publiée |
| **CA (créature)** | 10 + mod. Vitalité + bouclier (selon règles) | `armor_class_creature` **max 22** en seed (`db_column` `ca`) | Vérifier plafond **22 vs 26** selon version des règles |
| **Initiative** | Au jet : 1d20 + mod. Intelligence + … (règles combat) | Seed : formule calculée **`[modifier_intelligence_creature]`** sur la fiche (le d20 est côté jet, pas dans cette carac) | OK si la carac = bonus fixe avant dé |
| **PA (sorts)** | 0–12 (3.3.2.1) | `characteristic_spell` action_points_spell min 0, max 12 | OK |
| **Niveau créature** | 1–20 (personnages), 1–30 (monstres) | `level_creature` min 1, max 20 (entity *) ; max 30 (`monster`) | OK |

---

## 2. Convention `max` / `forgemagie_max` (objets)

Dans les JSON `*-object-definition.json` :

- **`max`** = bonus **équipement seul**, **hors** forgemagie (validation via `CharacteristicGetterService::getLimits`).
- **`forgemagie_max`** = plafond **forgemagie** à part.
- **Total** (si forgemagie autorisée) = **`max` + `forgemagie_max`**.

Une clé **`_comment_limits`** (préfixe `_`, ignorée au seed) rappelle cette convention. Détail : [CARACTERISTIQUES_CREATION_REFERENCE.md](../../410-%20Ressources/CARACTERISTIQUES_CREATION_REFERENCE.md) §4.

---

## 3. Alignements récents (objet / règles 2.2.2 et 2.6.1)

| Zone | Règles | Seeder (objet) | Remarque |
|------|--------|----------------|----------|
| **PA** | +5 équip., +1 forgem. (2.2.2) | `action_points_object` `max` 5, `forgemagie_max` 1 | Total maximal +6. |
| **Caracs principales** (chapeau / cape) | ±4 équip., ±2 forgem. (2.6.1) | `*_object` : `min` -4, `max` 4, `forgemagie_max` 2 ; conversion signée | Les malus suivent la même amplitude. Total maximal ±6 avec forgemagie. |
| **Compétences (actives)** | +5 équip., +3 forgem. (2.2.2) | `acrobatics_object`, etc. : `max` 5, `forgemagie_max` 3 | Ancien `max` 8 = total 5+3. |
| **Compétences passives** | bonus équip. / forgem. distincts | `*_passive_object` : `max` 3, `forgemagie_max` 2 | Inchangé (déjà séparé). |
| **Tacle / Fuite** | +10 équip., +2 forgem. (2.2.2) | `tackle_object`, `dodge_object` : `max` 10, `forgemagie_max` 2 ; formule étendue jusqu’à 20 | Ancien `max` 8 cumulait mal la règle globale. |
| **Esquive PA / PM** | +3 équip., +2 forgem. (2.2.2) | `dodge_action_points_object`, `dodge_movement_points_object` : `max` 3 | Ancien `max` 5 était incohérent. |
| **Résistances fixes** | ±7 par bouclier, ±3 forgem. (2.6.1) | `fixed_resistance_*_object` : `min` -7, `max` 7, `forgemagie_max` 3 | Le plafond de définition décrit l'objet seul, avant forgemagie. |
| **Dommages fixes** | +10 équip., +5 forgem. (2.2.2) | `fixed_damage_*` (éléments) : `max` 10 | Ancien `max` 5 + `forgemagie_max` 5 = total OK mais `max` sous-plafonnait l’équipement. |
| **Bonus de soins** | 7 au total, dont +2 forgem. (2.2.2) | `heal_bonus_object` : `max` 5, `forgemagie_max` 2 | Ancien `max` 7 incluait la forgemagie. |
| **Critique** | critique ±3 ; sans forgem. (2.2.2 / fiche) | `critical_hit_object` / `critical_hit_creature` : `min` -3, `max` 3 ; `forgemagie_max` 0 | Un malus relève le seuil ; plus de caractéristique d’échec critique séparée. |
| **PV d'objet** | bonus/malus non plafonné, forgem. ±20 (2.2.2) | `life_points_max_object` : `min` -∞, `max` ∞, `forgemagie_max` 20 | L'ID technique DofusDB 0 reste exclu de la conversion. |

---

## 4. Alignement des caractéristiques de sorts

| Zone | Règle Krosmoz | Définition `*_spell` |
|------|----------------|------------------------|
| **Niveau** | 1–20 | niveau minimal Dofus ÷ 10, borné à 1–20 |
| **Portée de fiche** | 0–20 cases | portée min/max bornée à 20 |
| **Cadence** | 0 = illimité ; 6/tour, 4/cible, délai 10 max | valeurs Dofus conservées puis bornées |
| **Critique temporaire** | bonus ou malus de seuil −3 à +3 | paliers Dofus signés, indépendants du niveau du sort |
| **Résistance relative** | −100/−50/0/50/100 | conversion dans les cinq paliers Krosmoz |
| **Résistance fixe** | réduction 0–10 | conversion compressée, bornée à 10 |
| **Initiative** | pas de plafond | valeur Dofus conservée directement |
| **Bonus de soin** | 0–7 | progression linéaire de 5–40 Dofus vers 0–7 |
| **Lancement** | ligne, diagonale, cible, cumul et délai global | caractéristiques de fiche explicites ; le moteur d’effets les exploitera séparément |
| **Résolution** | touche → 1d20 vs CA (dégâts) et vs esquive PA/PM (retraits) ; sauvegarde → 1d20 vs DD, esquive ≥ DD ignore le retrait même si save ratée ; soutien → auto | `SpellResolutionInferenceService` : un retrait PA/PM **ne** force **pas** une sauvegarde ; `is_magic` / `resolution_*` inférés des sous-effets (DofusDB n’a pas `isMagic`) |

Les zones et durées restent des notations textuelles structurées : elles ne doivent pas être traitées comme des nombres par le convertisseur de caractéristiques.

---

## 5. Formules et références

- **Modificateur** : `floor((score−10)/2)` — identique règles (2.2.1.2) et seeders (`formula_display` modifier_*_creature).
- **Plafond modificateur** : `min(⌊(score−10)/2⌋, ⌊niveau/4⌋+2, 7)` pour PJ/PNJ ; plafond +10 pour les monstres. La formule et ses bornes sont portées par `modifier_*_creature`.
- **PV** : Vitalité×10 n’est **plus** la formule jouable. Canon fiche : `hit_dice + mod. Vita × niveau + (niveau−1)×round(hit_dice/2) + équipement` (`life_points_creature`). Les textes 2.2.2 / 3.2.4 / Essentiel sont alignés. `hit_dice-creature-definition.json` (helper) peut encore citer l’ancienne phrase — hors livre.

---

## 5. Vérifications recommandées

- [ ] Utilisation de `characteristic_object.max` dans le code : validation côté app (création d’objets, équipement) doit respecter les plafonds **par emplacement** (2.6.1) et, le cas échéant, le **total** `max` + `forgemagie_max` pour la ligne.
- [ ] UI / outils MJ : si un écran doit afficher le plafond « jouable » total, combiner explicitement **`max` + `forgemagie_max`** (et ne pas supposer que `max` est déjà le total).
- [ ] Après modification des seeders : relancer les seeds / tests et mettre à jour ce document.
