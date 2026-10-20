# Frontend — IA

> Vue 3 JavaScript + Inertia + Atomic Design.

## Attention

- Pas de TypeScript généralisé.
- `vue-i18n` est documenté historiquement mais non branché côté `resources/js`.
- State principal : props Inertia (`@inertiajs/vue3` 3), composables, Pinia 4 (peer `@vue/devtools-api`).
- Tableaux : TanStack Table 9 (`useTable` + `tableFeatures` dans `TanStackTable.vue`). Chargement Minimal/Line : `EntityViewSkeleton` (plusieurs cartes image+texte, pas un seul bloc). Vue colonnes : `TanStackTableSkeletonBody`.
- CMS sections : hors tableaux `md:w-2/3` centrées ; `*_table` en `w-full` ; overflow-x si dépassement (`SectionRenderer`, `PageRenderer` / `EntitySectionsRenderer`). Lazy / fetch : `SectionContentSkeleton` calqué sur le template. HTML différé (3 premières sections) via `api.cms.sections.content` ; PATCH sans placeholder `content_deferred`. Bibliothèques `entity_table` : bouton Créer (`CreateEntityModal`) si `can.create`.
- Sélection vue minimale : checkbox haut-gauche visible seulement carte **déployée** (ou déjà cochée).
- `Container fluid` : `w-full min-w-0` (évite le shrink-to-fit dans un parent flex).
- Footer desktop (`Layouts/Footer.vue`) : 2 lignes, logo centré ; padding via le layout, pas la molécule.
- Chrome responsive : mobile `&lt; md` = dock bas (Menu / Recherche / Compte branchés) + pas de header ; tablette `md–lg` = header + hamburger ; desktop `≥ lg` = sidebar fixe. Constantes `LAYOUT_*` dans `viewport-breakpoints.js` (padding bas dock, FAB, cookies). `useGlobalSearchUi` ouvre la recherche depuis le dock.
- Tableaux : filtres / presets en Drawer `side="end"` sous conteneur étroit ; racine `@container` ; modes de vue icon-only en conteneur étroit.
- Dashboard `/admin/content` : MJ+ (password.confirm) ; cartes Atelier DofusDB / IA / compile / prix seulement si `contentPipeline` (admin+). Camemberts pour tous les MJ+. Modal **Sources** : deux onglets (DofusDB algo + IA). Après écriture, tableau avant/après avec choix par cellule ; Enregistrer / Rétablir.
- Astuces de chargement : `/admin/loading-tips` (nav Admin, pas Contenu).
- Formules de dés : bande min/moy/max + **Valeur** / icône ghost Lancer + historique session ; reconnu si dé, tranche ou opérateur (`50-17`). `diceParser.js`, `DiceFormulaStrip`.
- Recherche globale / filtres tableau : pastilles d’état via jetons `--color-state-*` (`getEntityStateChipClass`). Brut = error, jouable = success, brouillon = umber, auto = indigo, archivé = info.
- `vite.config.js` `manualChunks` : uniquement `node_modules` (`vendor` / `cally` / `utils`). Ne pas extraire `Main.vue` ni `Utils/Formatters` : cycle de chunks au boot.
- Écran de chargement (`SiteLoadingOverlay`) : astuces bas centrées (`loadingTips` via `shareOnce`, table `loading_tips`, admin `/admin/loading-tips`). Tirage pondéré `pickLoadingTip` (featured ×3) ; `duration_seconds` (2–30, défaut 8) ; lien optionnel nouvel onglet.

## Édition d’entité (pattern)

- Header compact : `EntityEditHeader` (`Molecules/entity/shared/`) — état, options,
  annuler, supprimer, enregistrer ; `formRef` → `EntityEditForm` exposé.
- Grille : `layout-profile="sheet"` (2 cols, `span: 2` ; `spell` = alias). Capacité
  garde `capability`. Conteneurs : `EntityEditContainer` + `EntityEditGrid`.
- Pages `entity/*/Edit.vue` : header + sheet + dock masqué ; managers en
  `#after-sections`. Sous-éditeurs gardent leur propre enregistrement.
- Modal globale : `EntitySurfaceHost` + `useEntitySurfaceStore` (affichage / édition,
  pile, remplacement affichage→édition). Embed : `EntityEditEmbedBridge` (provide
  dans le Teleport) + `useEntityEditEmbed` → `redirect_after_update=stay` ; PATCH
  via `EntityUpdateRedirect` (`stay` → `back()`, sans navigation vers show/edit).

## Descendre

- [inertia/_ai.md](inertia/_ai.md)
- [atomic-design/_ai.md](atomic-design/_ai.md)
- [entity-views/_ai.md](entity-views/_ai.md)
