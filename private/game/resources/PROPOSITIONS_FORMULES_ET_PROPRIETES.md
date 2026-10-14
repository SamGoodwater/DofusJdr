# Propositions de modifications — Formules et propriétés

> **Statut : historique.** Les formules et limites jouées sont celles du système de caractéristiques en base et des chapitres 2.2 / 4.2. Les anciennes propositions ci-dessous ne constituent plus une seconde source de vérité.

Ce document propose des modifications pour aligner les seeders et les caractéristiques sur les règles du jeu (docs 400) et les références PDF : **Equipements et forgemagie.pdf** et **CaractéristiquesAvecLien avec Dofus.pdf**.

Pour le **groupe sort**, la classification A/B/C (cadre / modificateur cible / effet d’action) et le lien vers les règles de sorts : [MATRICE_ROLES_CARACTERISTIQUES_SPELL.md](./MATRICE_ROLES_CARACTERISTIQUES_SPELL.md). Les **normes** 5×20 côté produit : [CAHIER_DES_CHARGES_NORMES_ENTITES.md](../../50-Fonctionnalités/Characteristics-DB/CAHIER_DES_CHARGES_NORMES_ENTITES.md).

---

## 1. Formules objet : tableaux par niveau (PDF Équipements)

Le PDF « Equipements et forgemagie » décrit des **tables discrètes** par plage de niveau (1–2, 3–4, 5–6, … 19–20). Les formules actuelles du type `[level] * (X/20)` sont linéaires et ne reproduisent pas exactement ces paliers.

### Proposition 1.1 : Formules table pour bonus par niveau

Remplacer les formules linéaires par des **formules-table** (syntaxe JSON supportée par le FormulaEvaluator) alignées sur le PDF.

| Caractéristique | PDF (niveau → bonus) | Formule actuelle | Proposition |
|-----------------|----------------------|------------------|-------------|
| **hit_bonus_object** | 1–2:0, 3–4:1, 5–6:2, 7–8:3, 9–10:4, 11–20:5 | `[level]*(5/20)` | Table : `{"1":"0","3":"1","5":"2","7":"3","9":"4","11":"5","characteristic":"level"}` |
| **fixed_damage_*_object** | 1–2:1, 3–4:2, 5–6:3, 7–8:4, 9–10:5 (max 5, forgemagie +5) | `floor(-0.1 + 1.78*pow(...))` | Table équivalente au PDF pour conversion Dofus ; `formula` pour objet créé à la main : table niveau→bonus |
| **fixed_damage_multiple_object** | 1–10:0, 11–12:1, 13–14:2, 15–16:3, 17–18:4, 19–20:5 | `[level]*(3/20)` | Table : `{"1":"0","11":"1","13":"2","15":"3","17":"4","19":"5","characteristic":"level"}` |
| **life_points_max_object** | 1–2:1, 3–4:2, … 19–20:5 | `floor(-0.4+ 13.8587 * pow(...))` | Table niveau→bonus pour création manuelle ; conserver formule de conversion Dofus pour le scrapping |
| **vitality_object, wisdom_object** (et For/Int/Cha/Agi) | Ancien PDF jusqu’à 8 — **abandonné**. Canon livre **+4** (2.6.1) | paliers jusqu’à 4 | Table : 1→0, 3→1, 5→1, 7→2, 9→2, 11→3, 13→3, 15→4, 17→4, 20→4 |
| **initiative_object** | 1–2:0, 3–4:1, … 19–20:3 | `floor(0.1973 * pow([d], 0.4519))` | Pour objet : table niveau→bonus. Conserver formule Dofus pour conversion |
| **tackle_object, dodge_object** | 1–2:1, 3–4:2, … 19–20:8 | `floor(1.1 + 2* pow(([d]-1)/12, 0.6))` | Table : 1→1, 3→2, 5→3, 7→4, 9→5, 11→6, 13→7, 15→8 |
| **fixed_resistance_*_object** (boucliers) | 1–2:1, 3–4:2, … 19–20:7 | `floor(1.1361 + 3.5* pow(...))` | Table : 1→1, 3→2, 5→3, 7→4, 9→5, 11→6, 13→7 |
| **summoning_object** (anneaux) | 1–2:0, 3–4:1, … 19–20:3 | `[d]` | Conversion Dofus OK ; `formula` si calcul par niveau : table |
| **range_object** (anneaux) | 1–2:0, 3–4:1, … 19–20:3 | `[d]` | Idem |

**Effet** : Les objets générés ou créés manuellement respecteront exactement les paliers du PDF.

---

## 2. Incohérences règles vs seeders (COHERENCE_SEEDER_REGLES)

### 2.1 Bonus par objet : stats principales (RÉSOLU — sept. 2026)

| Source | Vitalité / Force / etc. (chapeaux, capes) |
|--------|-------------------------------------------|
| **Règles 2.2.3 / 2.6.1** | **+4** maximum (équipement) + **2** (forgemagie) = +6 total |
| **Seeder `*_object`** | `min` −4, `max` 4, `forgemagie_max` 2 ; formules paliers jusqu’à 4 |

**Décision** : le livre **prime**. Ne pas remettre le PDF « jusqu’à +8 ». `max` = équipement seul ; total jouable = `max` + `forgemagie_max`.

### 2.2 Anneaux : PO et invocations

| Source | PO (anneaux) | Invocations (anneaux) |
|--------|--------------|-----------------------|
| **PDF Équipements** | Maximum 5 (table : 1–2:0 … 19–20:3) | Maximum 5 (table idem) |
| **Règles 2.6.1** | +6 maximum | +5 maximum |
| **Règles 2.2.2** | PO créature max 6 (équipement +6) | Invocations max = bonus maîtrise, équip. +5 |

**Proposition** : Le PDF parle de « maximum 5 » pour les anneaux (2 par personnage). Les règles 2.2.2 disent PO max 6 au total. Garder **range_object max 6** et **summoning_object max 5** pour rester cohérent avec 2.2.2.

---

## 3. Dés de vie (hit_dice_creature)

| Source | Formule |
|--------|---------|
| **Règles 2.2.2.4** | Niveau / 2 (arrondi à l’inférieur), max 10 |
| **PDF Caractéristiques** | [niveau / 2] – 10 (ambigu : floor ou max ?) |
| **Seeder** | formula `8`, formula_display `floor(niveau/2), max 10` |

**Proposition** :  
- Clarifier dans le PDF : « Dés de vie = floor(niveau/2) » avec max 10.  
- Dans le seeder : `formula` = `floor([level_creature]/2)` ou table selon le niveau.  
- Le `8` actuel semble être le **type de dé** (d8) par défaut, pas le nombre de dés. À documenter clairement.

---

## 4. Prix de base et forgemagie (PDF Équipements)

Vérifier que `base_price_per_unit` et `rune_price_per_unit` dans `characteristic_object` correspondent au PDF :

| Caractéristique | PDF Prix unité | PDF Rune | Seeder actuel |
|-----------------|----------------|----------|----------------|
| Bonus de touche | 1 200 | – | 1200, NULL |
| Dommage fixe | 700 | 1 400 | 700, 1400 |
| PV max | 50 | 100 | 50, 100 |
| Vitalité, Sagesse | 600 | 1 200 | 600, 1200 |
| Compétences | 400 | 800 | 400, 800 |
| Initiative | 100 | 200 | 100, 200 |
| PA | 1 300 | 2 600 | 1300, 2600 |
| Esquive PA/PM | 300 | 600 | 300, 600 |
| PM | 1 000 | 2 000 | 1000, 2000 |
| Tacle / Fuite | 300 | 600 | 300, 600 |
| Réserve Wakfu | 1 500 | – | 1500, NULL |
| CA (bouclier) | 1 100 | – | 1100, NULL |
| Résistance fixe | 600 | 1 200 | 600, 1200 |

**Statut** : Les valeurs semblent déjà alignées. Vérification manuelle recommandée après toute modification des seeders.

---

## 5. Limites forgemagie (forgemagie_max)

Comparaison PDF Équipements vs seeder :

| Caractéristique | PDF forgemagie max | Seeder forgemagie_max |
|-----------------|--------------------|------------------------|
| Bonus de touche | 0 | 0 ✓ (pas de forgemagie, cf. 2.2.2) |
| Dommage fixe (arme) | 5 | 5 ✓ |
| Dommage fixe multiple | 2 | 2 ✓ |
| PV max | 20 | 20 ✓ |
| Vitalité, Sagesse | 2 | 2 ✓ |
| Compétences | 3 | 3 ✓ |
| Initiative | 3 | 3 ✓ |
| PA | 1 | 1 ✓ |
| Esquive PA/PM | 2 | 2 ✓ |
| PM | 1 | 1 ✓ |
| Tacle / Fuite | 2 | 2 ✓ |
| Résistance fixe (bouclier) | 3 | 3 ✓ |

**Statut** : Cohérent.

---

## 6. Caractéristiques creature : système de formules (PDF Caractéristiques)

### 6.1 Modificateurs (modifier_*_creature)

**PDF Caractéristiques** :
- Formule : `[(Carac - 10) / 2]` (arrondi à l’inférieur)
- Limite basse (niv. 1–3) : minimum -1 ou -2
- Limite haute : `min(floor(niveau/4) + 2, 7)` (monstre : 10)

**Formule exploitable** :
```php
min(max(floor(([vitality_creature]-10)/2), -2), floor([level_creature]/4)+2, 7)
```

Même logique pour Sagesse, Force, Intelligence, Chance, Agilité (remplacer `vitality_creature` par la caractéristique correspondante).

### 6.2 Jets de sauvegarde (save_*_creature)

**PDF Caractéristiques** : Jet = 1d20 + mod. carac. (+ bonus maîtrise si maîtrise)

**Formule du bonus** (sans le d20) : `[modifier_X_creature]` (base). Le bonus de maîtrise est ajouté au moment du jet si la créature maîtrise ce jet.

**Formule exploitable** (bonus affiché, sans maîtrise) :
```php
[modifier_vitality_creature]
```
Idem pour save_wisdom, save_strength, save_intelligence, save_chance, save_agility.

### 6.3 Bonus de maîtrise (mastery_bonus_creature)

**PDF** : `[1 + niveau / 4]`, max 6.

**Formule exploitable** :
```php
min(6, 1+floor([level_creature]/4))
```

### 6.4 Résistances % (resistance_*_creature)

PDF Caractéristiques : 0 %, 50 %, 100 % ; équipement peut donner 50 % ou 100 %.

**Proposition** :  
- Vérifier que les `value_available` ou les valeurs autorisées pour ces champs permettent bien 0, 50, 100.  
- Documenter que les résistances 50 % et 100 % viennent des boucliers (PDF Équipements).

---

## 7. Résumé des actions proposées

| Priorité | Action | Fichier(s) |
|----------|--------|------------|
| **Haute** | Remplacer formules linéaires `[level]*(X/20)` par tables niveau→bonus (PDF Équipements) | `characteristic_object` |
| **Haute** | Clarifier dés de vie : formule floor(level/2), max 10, et sens du `8` (type de dé) | `characteristic_creature`, doc règles |
| **Moyenne** | Canon **+4** par objet (2.6.1), seeders alignés | COHERENCE_SEEDER_REGLES.md |
| **Moyenne** | Vérifier tous les prix base/rune vs PDF | `characteristic_object` |
| **Résolu** | Limite absolue du modificateur `floor(niveau/4)+2`, max 7 (monstre 10) | Système de caractéristiques |

---

## 8. Exemple de formule-table pour `hit_bonus_object`

Pour un équipement créé manuellement (sans conversion Dofus) :

```json
{
  "1": "0",
  "3": "1",
  "5": "2",
  "7": "3",
  "9": "4",
  "11": "5",
  "characteristic": "level"
}
```

Interprétation : niveau 1–2 → 0, 3–4 → 1, 5–6 → 2, etc.

La clé `characteristic` indique la variable utilisée (ici `level` = niveau de l’objet).

---

## 9. Système creature : modificateurs et jets de sauvegarde (PDF Caractéristiques)

### 9.1 Modificateurs (modifier_*_creature)

Le PDF définit : **Modificateur = (carac − 10) / 2** (arrondi à l’inférieur), avec limites :

- **Basse** : minimum −2 pour niveaux 1–3 (« Soit −1 (ou −2) »)
- **Haute** : max = `min(floor(niveau/4) + 2, 7)` (monstre : 10)

**Formule proposée :**

```
min(max(floor(([vitality_creature]-10)/2), -2), floor([level_creature]/4)+2, 7)
```

Même logique pour chaque modificateur (vitality, wisdom, strength, intelligence, chance, agility).

**Correction** : `modifier_wisdom_creature` a une typo actuelle `floor([wisdom]-10)/2)` → utiliser `[wisdom_creature]` et parenthèses correctes.

### 9.2 Bonus de maîtrise (mastery_bonus_creature)

PDF : **1 + floor(niveau/4)**, max 6.

**Formule proposée :**

```
min(1+floor([level_creature]/4), 6)
```

### 9.3 Jets de sauvegarde (save_*_creature)

PDF : **1d20 + mod. carac.** ou **1d20 + mod. carac. + bonus maîtrise** si maîtrise.

**Formule proposée** (bonus ajouté au d20, sans le d20) :

- **Sans maîtrise** : `[modifier_vitality_creature]`
- **Avec maîtrise** : `[modifier_vitality_creature] + [mastery_bonus_creature]`

La formule actuelle inclut le modificateur, le bonus de maîtrise multiplié par le palier profil, l’équipement, puis les couches éphémères sort et consommable. La partie permanente est bornée à ±15 (monstre ±20).

---

*Document généré à partir de la comparaison entre private/game, Equipements et forgemagie.pdf et CaractéristiquesAvecLien avec Dofus.pdf.*