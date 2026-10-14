# Matrice des caractéristiques spell — rôles A / B / C

**Date** : 2026-04-30  
**Objectif** : classer chaque clé `*_spell` pour le référentiel normes / conversions et le développement jeu.

## Définitions des rôles

| Rôle | Signification |
|------|----------------|
| **A — Cadre du sort** | Propriétés de la **fiche sort** (coût, portée, zone, cadence, catégorie, résolution hors effet scalaire importé Dofus, etc.). Voir les règles « sorts » dans [420- Règles](../420-%20Règles/3-Jouer/3.3-sorts/). |
| **B — Modificateur sur la cible** | Bonus ou malus **persistants ou stat-like** sur une créature (stats, sauvegardes, résistances, CA, esquives, etc.). Barèmes : [PROPOSITIONS_FORMULES_ET_PROPRIETES.md](./PROPOSITIONS_FORMULES_ET_PROPRIETES.md) et dossier [420- Règles](../420-%20Règles/). |
| **C — Effet d’action** | **Résolution immédiate** typiquement exprimée en dés ou PV : dégâts, soins, vol de vie. Limites par niveau : mêmes sources que B + tables dédiées sorts dans ce dossier. |

**Note** : une même valeur peut être affichée différemment en jeu (ex. bouclier = buffer **B**). Les **normes** (`norms_grid` 5×20) s’appliquent surtout aux caractéristiques **numériques** ; booléens / énums peuvent avoir `norms_grid` null (voir [CARACTERISTIQUES_CREATION_REFERENCE.md](./CARACTERISTIQUES_CREATION_REFERENCE.md)).

---

## Tableau par clé

| Clé | Rôle | Références / remarques |
|-----|------|-------------------------|
| `level_spell` | A | Niveau du sort (échelle Dofus → Krosmoz via conversion ; lié `level_creature`). |
| `action_points_spell` | A | **Coût en PA** pour lancer (`spells.pa`). |
| `action_points_variation_spell` | B | Bonus / retrait de PA sur une cible. Distinct du coût de lancement pour éviter de mélanger économie d’action et fiche sort. |
| `area_spell` | A | Zone / nombre de cases (fiche + effets). |
| `cast_per_turn_spell` | A | Lancers par tour. |
| `cast_per_target_spell` | A | Lancers par cible et par tour. |
| `number_between_two_cast_spell` | A | Tours entre deux lancers (`number_between_two_cast`). |
| `casting_time_spell` | A | Temps d’incantation (texte). |
| `duration_spell` | A | Durée du sort (effet global). |
| `category_spell` | A | Catégorie (énum). |
| `element_spell` | A | Élément (énum). |
| `is_magic_spell` | A | Nature wakfu vs physique (bool). |
| `range_editable_spell` | A | Portée modifiable. |
| `sight_line_spell` | A | Ligne de vue. |
| `ritual_available_spell` | A | Rituel disponible. |
| `spell_range_min_spell` | A | Portée mini. |
| `spell_range_max_spell` | A | Portée maxi. |
| `power_spell` | A | Puissance Krosmoz (`powerful`, ≠ % dégâts Dofus). |
| `spell_type_spell` | A | Type de sort (liaison `spell_types`). |
| `allows_reaction_spell` | A | Utilisable en réaction. |
| `resolution_mode_spell` | A | Mode de résolution (`attack_roll` / `saving_throw` / `auto_success`). *Réf. champ `spells.resolution_mode`.* |
| `attack_characteristic_key_spell` | A | Caractéristique d’attaque au jet (`attack_characteristic_key`). |
| `save_characteristic_key_spell` | A | Caractéristique de sauvegarde (`save_characteristic_key`). |
| `save_dc_formula_spell` | A | Formule du DD (`save_dc_formula`). |
| `auto_success_if_willing_target_spell` | A | Réussite auto si cible consentante (`auto_success_if_willing_target`). |
| `movement_points_spell` | B | Malus/bonus **PM** (effet sur cible ; id Dofus différent du coût PA du sort). |
| `initiative_spell` | B | Initiative. |
| `range_spell` | B | PO / portée « créature » (anneau / bonus PO). |
| `summoning_spell` | B | Invocations max / bonus invocation. |
| `vitality_spell` | B | Vitalité. |
| `sagesse_spell` | B | Sagesse. |
| `strong_spell` | B | Force. |
| `intel_spell` | B | Intelligence. |
| `chance_spell` | B | Chance. |
| `agi_spell` | B | Agilité. |
| `save_*_spell` (×6) | B | Jets de sauvegarde (vitality, wisdom, strength, intelligence, chance, agility). |
| `hit_bonus_spell` | B | Bonus au toucher. |
| `armor_class_spell` | B | Classe d’armure. |
| `dodge_action_points_spell` | B | Esquive PA. |
| `dodge_movement_points_spell` | B | Esquive PM. |
| `dodge_spell` | B | Fuite / esquive (usage carte). |
| `tackle_spell` | B | Tacle. |
| `fixed_damage_*_spell` | B | Dommages fixes par « ligne » élémentaire ou sagesse/vitalité. |
| `do_fixe_multiple_spell` | B | Dommages fixes multi-éléments. |
| `fixed_resistance_*_spell` | B | Résistance fixe par élément. |
| `res_*_spell` | B | Résistance % (échelle Dofus ; pas les paliers 0/50/100 objet sauf évolution métier). |
| `critical_spell` | B | Bonus aux critiques / chances critique effet. |
| `heal_bonus_spell` | B | Bonus aux soins (%). |
| `mastery_bonus_spell` | B | Bonus de maîtrise. |
| `bouclier_spell` | B | Points de bouclier (buffer dissipable). |
| `pv_temporaires_spell` | B | PV temporaires (après bouclier, avant PV ; non dissipables, non cumulables). |
| `acrobatics_spell`, `athletics_spell`, `stealth_spell`, … (×18 actives) | B | Bonus temporaire de compétence active via `booster` / `retirer` / `voler-caracteristiques`. Couche éphémère, hors limite permanente ±15/±20. |
| `wakfu_reserve_spell` | B | Réserve de Wakfu. |
| `push_damage_reduction_spell` | B | Réduction « dégâts de poussée » (mitigation, ≠ distance de poussée). |
| `critical_damage_reduction_spell` | B | Réduction dégâts critiques subis. |
| `dommages_spell` | C | Dégâts (exprimés en dés / valeur d’action). |
| `soin_spell` | C | Soins. |
| `vol_vie_spell` | C | Vol de vie (dommages + soin combinés). |
| `movement_distance_spell` | C | Distance de déplacement générique en cases. |
| `jump_distance_spell` | C | Distance de saut / bond en cases. |
| `teleport_distance_spell` | C | Distance de téléportation en cases. |
| `push_distance_spell` | C | Distance de repousse en cases. |
| `pull_distance_spell` | C | Distance d’attirance en cases. |

---

## Politique bonus / malus

Les mêmes caractéristiques peuvent être utilisées par `booster`, `retirer` et `voler-caracteristiques`, mais elles ne doivent pas produire la même pression en jeu :

- **bonus** (`booster`, soutien) : lecture normale de la grille ;
- **malus** (`retirer`) : conversion réduite à environ la moitié de la valeur convertie, minimum 1 quand l’effet existe ;
- **vol** (`voler-caracteristiques`) : même réduction que les malus, car le lanceur profite souvent d’un double effet (la cible perd, le lanceur gagne).

Cette règle est appliquée dans `SpellEffectsConversionService` via `params.effect_direction` (`bonus`, `malus`, `steal`, `action`). Elle évite de créer une clé `*_malus_spell` pour chaque caractéristique tout en donnant aux outils et à l’UI une propriété explicite pour distinguer les usages.

### Bornes D&D-like

- Les dégâts restent dans une enveloppe comparable à D&D : neutre ≈ `1d6` à `5d6`, très fort jusqu’à environ `8d10` au niveau 20.
- Les soins et boucliers sont inférieurs ou égaux aux pics de dégâts, car ils prolongent la survie.
- Le vol de vie est plus bas que dégâts / soins purs, car il combine deux effets.
- Les grilles de conversion gardent des ordres de grandeur bas (jusqu’à `+8` pour une valeur très forte), mais ce nombre est un **barème de conversion**, pas une limite absolue en jeu. Les effets de sort sont éphémères et s’ajoutent après les limites permanentes.
- Les mouvements ont des grilles séparées : téléportation > déplacement/saut > attirance/repousse, car ils ne contournent pas les mêmes contraintes de terrain et de tacle.

---

## Hors périmètre spell (autres entités)

| Concept | Entité | Note |
|---------|--------|------|
| `time_before_use_again` | **Capability** | Texte libre sur les aptitudes ; **pas** une colonne `spells`. Pour les sorts, utiliser uniquement `number_between_two_cast`. |

---

## Backlog métier (extensions prévues)

- **Déjà en `*-spell-definition.json` (rôle A — résolution)** : `resolution_mode_spell`, `attack_characteristic_key_spell`, `save_characteristic_key_spell`, `save_dc_formula_spell`, `save_success_note_spell`, `auto_success_if_willing_target_spell` (colonnes `spells.*`).

- **Déjà modélisé (rôle B — PV temporaires)** : `pv_temporaires_spell` via sous-effet `donner-pv-temporaires` (charac DofusDB 95). Le max PV via sort reste `vitality_spell` (pas de `life_points_max_spell`).

- **Déjà modélisé (rôle B — compétences actives)** : 18 clés `*_spell` (Acrobaties… Survie), sélectionnables dans booster/retirer/vol via catégorie `skill`. Pas de passives spell (réservées à l’équipement). Pas de mapping DofusDB natif (création manuelle / effets custom).

- **Encore à modéliser** :
  - **Actions plateau** : distance de **poussée**, **téléportation**, **traction** (cases) — dépend des types d’effets `spell_effects` et du mapping DofusDB complets (déjà largement branché en conversion).
  - **Compétences passives** en effet de sort — optionnel ; aujourd’hui réservées à l’équipement.

Voir aussi [CAHIER_DES_CHARGES_NORMES_ENTITES.md](../../50-Fonctionnalités/Characteristics-DB/CAHIER_DES_CHARGES_NORMES_ENTITES.md) pour l’usage des normes en création de contenu.
