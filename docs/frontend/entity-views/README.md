# Entity views

Les vues d'entités standardisent l'affichage des fiches JDR.

| Vue | Usage | Composant |
| --- | --- | --- |
| `minimal` | carte / grille | `*ViewMinimal.vue` |
| `line` | ligne dense de table | `*LineRow.vue` |
| `text` | inline + overlay | `*ViewText.vue` |
| `full` | détail page ou modal | `*ViewFull.vue` |
| `edit` | édition | `EntityEditHeader` + `EntityEditForm` (`sheet`) |

Ne pas créer `ViewLarge` ni `ViewCompact`. Utiliser `resolveEntityViewComponent(type, 'full')`.

## Édition (pattern sheet)

Pages `entity/*/Edit.vue` : header sticky (`EntityEditHeader`) + formulaire
`layout-profile="sheet"` (2 colonnes ; `spell` = alias). Toolbar et dock masqués ;
état dans le header. Sous-éditeurs (relations, effets, traits) dans
`#after-sections` via `EntityEditContainer :span="2"`. Capacité : même header,
grille `capability`. Créature : hors `EntityEditForm`.

## Sorts (usage unifié)

`SpellUsageBlock` factorise méta PA/PO + résolution + chips d’effets pour Minimal,
Line et le bandeau Full. Props `parts` : `meta` | `effects` | `all` (méta sous le
titre, effets en pleine largeur). Full : section Utilisation via la même méta ;
bandeau chips au-dessus du journal ; empty states structurés vs texte libre.
Notes de règles (oubliables à table) sous la méta et, en Full, aussi sous les
effets : `spellTypeRuleNotes.js`. Consommables et équipements : `EntityRuleNotes`
+ `consumableRuleNotes.js` / `itemRuleNotes.js` (cumul de buff, parchemin de
sortilège, respec de caractéristique, monture, soin hors combat, durée jusqu’au repos long).

**Édition sort** (`SpellEditFormContent` / `SpellDegreesEditor`) : identité +
métadonnées en colonnes ; propriétés mécaniques redondantes dans un panneau
replié ; degrés en pleine largeur avec portée `x-y`, effets adaptatifs
(`SpellDegreeEffectRow`), chips porteurs et menu Options/DofusDB.

## Parcours d’ouverture

```
minimal compact → survol : overlay déployé → double-clic / quick-view : modal full → agrandir : page full
```

- L’overlay (`EntityMinimalCard`) ne décale pas la grille. En popover (`displayMode="extended"`), une seule carte (slot expanded, hauteur du contenu) : pas de coquille compacte ni de chrome tooltip autour (`OverlayTrigger` `chromeless`). Même overlay pour un kref d’entité dans un texte de section (`ReferenceInlineNodeView` → `loadKrefEntityMinimalOverlay`). En mode `hover`, la carte reste déployée tant qu’un tooltip **ou un menu Dropdown** issu d’elle est ouvert (panneaux téléportés hors de la carte : changement d’état, actions « ⋮ »). Les tooltips et popovers d’entités n’ont **qu’une** boîte : `OverlayTrigger` n’ajoute `tooltip-floating-surface` que si le panneau n’a pas déjà de chrome (`chromeless`, contenu sort / chips, ou `panelClass` Popover).
- Les bibliothèques (TanStack) s’ouvrent en **vue minimale**. Le mode ligne reste disponible ; le mode colonnes déjà choisi est conservé.
- **Créer** (Index et pages CMS `entity_table`) : bouton si droit `create` → modal courte (`CreateEntityModal`) avec nom / description / quelques clés ; onglet Conversion IA pour les admins (types convertibles). Ensuite la fiche s’ouvre en **Modifier**.
- En carte minimale **déployée**, la description des consommables, ressources et équipements n’est plus coupée.
- Le menu d’options de la carte minimale n’a plus de fond : les icônes restent nues à droite du titre.
- **Tous** les `*ViewMinimal` (hors `language`) passent par `EntityMinimalCard` : double-clic → modal, whitelist d’actions commune, pin flottant.
- Sorts et capacités (Minimal / Line) : le glass reste celui du thème ; l’élément se lit en halo (`getElementGlassSurfaceStyle`, lueur in/out + filet 20 % via `entity-element-ring`, `--element-border-color` / dégradé si plusieurs primaires). Sans élément, bordure thème.
- Shell commun : `useEntityMinimalShell` + `EntityMinimalTitle`.
- PNJ : mêmes densités de caractéristiques que les monstres (créature liée). Carte minimale déployée : description, langues, traits, compétences, sorts et équipements. Catalogue Index en pagination serveur.
- La **page** n’est pas l’entrée principale : **Agrandir** depuis la modal, ou Ctrl+clic.
- En `line` : même logique → modal full (`EntityLineRowActions`).
- Badges type / rareté / niveau (ressources, équipements, consommables) : infobulle via `EntityFieldTooltip`. Hors tableau `characteristics` (type, rareté) : description courte (`FIELD_HELPERS`). Caractéristiques BDD (poids, prix, stats) : helper + limites min/max figées.
- **Prix (kamas)** : l’infobulle de la fiche Full (et le helper descripteur) explique la formule. Ressource = prix Dofus. Consommable = somme de la recette. Équipement = bonus × prix unitaire + 150 × niveau + 200 × rareté. Un bouton « actualiser » (droits `update`) réécrit le calculé et vide l’ajustement manuel. L’édition item/consommable utilise `ItemPriceEditSection`.
- Actions du titre : autant de raccourcis que la largeur restante (titre → bord) ; le reste dans le dropdown `EntityActions`. Mesure : `useHorizontalOverflowCount` + `measureFlexRowLeftoverPx`. Sorts : même ligne que le titre.
- **Monstres — équipements** : s’il y en a (`creature.items`), liste identique aux sorts (`EntityViewTextLink` + aperçu `ItemViewMinimal`) en Minimal / Line / Full. Section Full masquée si la liste est vide.
- **Panoplies** : pas d’illustration DofusDB. La vignette reprend les images des pièces (jusqu’à 4), sinon les initiales du nom. Les équipements s’affichent en vue texte (icône + nom, clic → `ItemViewMinimal`) en Line / Minimal / Full. Les bonus de set (`{ "2": { force: 1 }, "3": {…} }`) s’affichent **palier par palier** (`PanoplyBonusTiers`) : une ligne (badge du nombre de pièces + chips) en Minimal et Line ; en Full, une colonne par palier (chiffre en haut, caractéristiques icon + nom en dessous). En **Minimal déployé**, une ligne **Total** sous les paliers additionne pièces + tous les paliers du set (chips à la suite, sans badge de pièces). Les paliers vides ou à 0 sont omis. Un équipement du set affiche la panoplie via `ItemPanoplyMark` : icône seule en vue minimal compacte (tooltip = nom + pièces en vue texte + bonus) ; icône + nom en minimal déployé et Line (même tooltip) ; en Full, nom, liste des pièces et bonus en colonnes. Page Modifier : équipements et bonus de set en premier (cartes du formulaire) ; nom, description et droits en bas. Recherche d’équipements via le catalogue (`EntityPickerCore`) ; chaque pièce a un bouton retirer ; les bonus se saisissent comme les effets d’équipement (caractéristique + valeur, par nombre de pièces).
- **Carte minimale** : le survol déploie **cette** carte seulement (`group/entity-minimal` sur `EntityMinimalCard`, plus de `group` anonyme sur la cellule du tableau).
- **Afficher** (toutes les entités à vues) : en Minimal, Line et listes Index / CMS, le bouton ouvre la **modal full** (`quick-view`). La **page** Show s’ouvre depuis la modal (**Agrandir**) ou par Ctrl+clic. `useEntityMinimalShell` et les Index traitent `view` comme `quick-view`. Preset `minimalLine` : pas d’action `view`.
- **Éditer** : le raccourci des options (Minimal / Line / tableau) ouvre la **page Modifier** (`edit`). La sélection de lignes du tableau (cases toujours visibles en colonnes et Line ; en vue minimale seulement sur la carte **déployée**, ou si déjà cochée) sert à l’export CSV et au PDF. Les raccourcis du tableau ignorent la saisie (recherche, filtres).
- **Favoris** : persistés en BDD (`user_favorites`) pour les comptes connectés. Accès header
  (cœur) → modal sans changer de page ; page `/favoris`. Invité·e : message pour se connecter.
  Icône cœur plein/vide dans les menus d’options. Liste en vue Minimal ; recherche via
  `EntitySearchHitCard` (aperçu Minimal au survol, clic → modal full). Favoris en tête des résultats.

Presets (`ENTITY_ACTION_CONTEXT_PRESETS` dans `entity-actions-config.js`) :

| Preset | Ordre (extrait) |
| --- | --- |
| `minimalLine` | state → pin → quick-view → view-dofusdb → refresh → favorite → copy-link → edit |
| `modalDetail` | state → favorite → copy-link → view (agrandir) → view-dofusdb → edit → refresh → delete |
| `pageDetail` | state → favorite → copy-link → view-dofusdb → edit → refresh → delete |
| `editPage` | view (afficher) → view-dofusdb → refresh → state → favorite → copy-link → delete |

## Référence DofusDB

L’action `view-dofusdb` (libellé **Ouvrir sur DofusDB**, icône `/images/logos/dofus.png`) apparaît dans les options de **toutes** les vues (minimal, ligne, colonnes, modal, page) dès que l’entité a un `dofusdb_id`. Sur la page Modifier, le menu **Options** est dans la barre du haut (ancre `EntityEditOptionsAnchor`, téléporté par `EntityEditForm`) : Afficher, DofusDB, Sources, état, favoris, lien, suppression. Le clic ouvre le store Pinia
`dofusDbReference` ; le panneau `DofusDbReferencePanel` (monté dans `Main`) affiche le deep-link
et un bouton `window.open` (pas d’iframe).

L’action `refresh` (**Sources**, une icône) ouvre `EntitySourceModal` à **deux onglets** dès que les deux sont possibles :

- **Conversion DofusDB** : pipeline algo (`POST /api/entities/{type}/{id}/dofusdb-refresh`). Cases **contenu** et **Récupérer l’image** (indépendantes). Scrapable + droit `update`.
- **Conversion IA** : `POST /api/entities/{type}/{id}/ia-convert`, admin + `password.confirm`.

Après écriture, le tableau avant/après (`EntityUpdateDiffView`) laisse **choisir cellule par cellule** (ou toute la colonne via l’en-tête Avant / Après). **Enregistrer** envoie les champs à rétablir (`POST …/update-diff/apply`) ; **Rétablir** ou fermer le modal annule toute la conversion (`restore`). Surfaces : page, modal, carte minimale, menu tableau.

## Caractéristiques

Densités `icon` / `labeled` / `spacious` sur `CharacteristicsCard` — voir
[COMPUTED_VALUES.md](../../features/characteristics/COMPUTED_VALUES.md).
Applicable aux **monstres** et **PNJ** (créature liée) ; sorts/objets gardent leurs effets dédiés.

## Tableau objets

La vue **Line** (`ItemLineRow`) reste courte : description sur 2 lignes, **bonus** en icônes (`items.bonus`, repli sur `effect`), recette sur la fiche Full.

La vue **Colonnes** (`item-descriptors.js`) :

| Par défaut | Masqué (sélecteur de colonnes) |
| --- | --- |
| Image, nom, niveau (sm+), type (sm+), rareté (sm+), bonus (md+) | Description, résumé, prix, version Dofus |
| État (sm+) **si** `updateAny` | Métadonnées (id, dates, DofusDB…) |

Les en-têtes affichent `helper` / `general.tooltip` au survol (`TanStackTableHeader.vue`).

## Tableaux d’entités (filtres, tri, recherche)

Index server-side (`items`, `monsters`, `spells`, `resources`, `consumables`, `conditions`) : changer un filtre relance la requête (debounce) ; Réinitialiser vide `filters` (puis réapplique les défauts déclarés, s’il y en a) et revient à la page 1. Les multi-sélections partent en `filters[key][]` ; les plages (niveau, PA, stats créature) en `filters[key][min]` / `[max]`. Le **niveau** est un slider dual (`RangeDualCore`) dont les bornes viennent de tout le catalogue, pas de la page courante. L’**état** se coche en pastilles à point à droite du titre « Filtres » (défaut Jouable ; catalogue des états : hors Brut). Les types et les autres listes se choisissent dans un menu select. PA, PO, PM et les booléens liés (ligne de vue, portée modifiable) sont dans les **filtres avancés**, avec l’icône et la couleur de la caractéristique (`icons/caracteristics/`, `filterCharacteristicMeta`). Il n’y a plus d’input texte sur les propriétés : la barre de recherche suffit. Les pastilles de liste (`filter.ui.layout: "chips"`) restent possibles si un filtre les demande. Le tri envoie `sorts[i][field]` avec l’alias SQL (`item_type` → `item_type_id`, `creature_level` via sous-requête). Les champs de saisie des filtres (recherche dans un multi) ne sont pas réinitialisés à chaque refetch : les défauts ne s’appliquent que si le filtre n’a pas encore de valeur, et les `initialFilterValues` ne remplacent une clé que si son contenu a vraiment changé.

Le catalogue **objets** coche par défaut les types utiles en jeu (amulette, armes, cape, dofus, trophée, etc.). Apparats, costumes et autres cosmétiques restent dans le filtre Type, décochés tant qu’on ne les demande pas.

Le catalogue **ressources** coche par défaut les types métier (bois, minerai, plante, cuir, runes de forgemagie, etc.). Quêtes, souvenirs, zones et essences de donjon restent dans le filtre, décochés.

Le catalogue **consommables** coche par défaut potions, nourritures, boissons, parchemins, pierres d’âme, objets utilisables, etc. Certificats, coffres, fées d’artifice et types d’événement restent dans le filtre, décochés.

Le catalogue **états** coche par défaut Brouillon, Auto, Jouable et Archivé. Brut (import Dofus) reste dans le filtre, décoché. Les effets mécaniques (ne pas être déplacé, invulnérable, etc.) apparaissent en chips sur les fiches et en colonne Effets.

Index client (`npcs`, etc.) : le filtre/tri/recherche portent sur le dataset déjà chargé ; la recherche cible le nom (et les colonnes `searchable`) même si le payload n’a pas de `cells` pré-générées.

### Sort depuis un monstre

Le payload tableau / fiche d’un monstre embarque les sorts liés **visibles du viewer**
(`visibleToUser`) avec `effect_usages_chips` (pas l’arbre `effects`). Un clic ouvre
`SpellViewMinimal` en `extended` : effets + menu d’options à droite du titre
(raccourcis selon la largeur, overflow dans le dropdown). Si les chips manquent, un fetch
`api.tables.spells?whitelist[]=` complète la fiche.
