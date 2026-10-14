# CMS Pages & Sections — carte IA (degré 1a)

> CMS maison : des **pages** hiérarchiques (arbre `parent_id`, menu dynamique) composées de **sections** typées (texte Tiptap, image, galerie, vidéo, tableaux d'entités, documents légaux, chartes de caractéristiques). Références inline « kref » vers entités/pages/caractéristiques.

## Quand lire ce nœud

- Travailler sur les pages publiques/règles, le menu, les sections de contenu.
- Ajouter/modifier un type de section (template), l'éditeur riche, ou les références kref.

## Concepts clés

- **Page** : `slug` unique, `state`, `read_level`/`write_level`, `parent_id`, `in_menu`/`menu_order`/`menu_group`, `settings` JSON (dont `linked_entity`). Code : `app/Models/Page.php`. Slugs critiques : `accueil`, `cgu`. Sous-pages bibliothèque (`LinkedEntityShow`) : liaisons de la fiche filtrées `visibleToUser` comme sur show/table/PDF ; sections CMS liées classe/spé filtrées `displayable()` (pas les brouillons / `read_level` hors portée).
- **Section** : appartient à une page, `template` (enum `SectionType`), `data`/`settings`, `order`, médias Spatie. Upload : pas de SVG (`StoreFileRequest` / `FileService::getAllowedUploadExtensions`). Code : `app/Models/Section.php`, `app/Enums/SectionType.php`.
- **12 templates** : `text`, `image`, `gallery`, `video`, `entity_table`, `legal_markdown`, `characteristic_norms`, `characteristic_norms_catalog`, `characteristic_reference_table`, `equipment_bonus_table`, `forgemagie_rune_table`, `download_catalog`. Détail : [README](./README.md#templates-de-sections).
- **`entity_table` (Bibliothèques)** : lecture en pagination serveur via `api.tables.*` (`SectionEntityTableRead`) — ne plus compter sur `settings.limit` (plafond legacy 50). Bouton **Créer** si `can.create` : `CreateEntityModal` (formulaire court, onglet Conversion IA admin) puis vue Modifier.
- **Rendu front** : `PageRenderer` → `SectionLazyGate` (lazy) → `SectionRenderer` → template via registry auto-discovery. Largeur : cadre `w-full min-w-0` ; sections hors tableaux `w-full md:w-2/3` centrées ; templates `*_table` en `w-full` ; `overflow-x-auto` si le contenu dépasse. Chargement : `SectionContentSkeleton` (texte / média / galerie / tableau / cartes `entity_table`). Détail : [README](./README.md#rendu-frontend).
- **kref** : références inline `@` (Tiptap) sérialisées en `<span class="kref">`. Lecture sans TipTap : `RichTextReadonlyView` hydrate la puce (`KrefInlineChip`). Entité catalogue : overlay `*ViewMinimal` ; pages/sections : popover snippet ; carac. : `CharacteristicInfoPanel` (même fiche joueur que les bibliothèques, via API card). Formule JSON tableau (`{"characteristic":"level","1":"0",…}`) : paliers lisibles (`1–2` → valeur), pas le JSON. Import : shortcode imbriqué aplati (`KrefShortcodeReplacer::flattenNestedShortcodes`) avant HTML, sinon un `|` casse les tableaux. Réparation : `pages:repair-krefs`. Détail : [README](./README.md#references-kref).
- **Barèmes CMS** : templates `characteristic_norms` / `characteristic_norms_catalog` → `CharacteristicGmPanel` ; `spell_reference_scales` → `CharacteristicScalesTool` (présélection sort possible). Catalogue normes : `group=all` accepté.
- **HTML différé** : `pages.show` n’embarque le HTML (`data.content`) que des 3 premières sections ; le reste via `api.cms.sections.content`. Un PATCH ne doit jamais persister `content_deferred` ni `content: null` (placeholder) : `SectionService` + `sanitizeSectionUpdatePayload`. L’édition attend le fetch.
- **Menu dynamique** : `GET /pages/menu` (JSON) → `useDynamicMenu` → `DynamicMenu.vue`. Groupes dans `config/nav_menu.php` (L'Essentiel, Règles, Bibliothèques, Pour les MJ, Informations) ; un groupe sans enfants visibles n’apparaît pas. Bibliothèques = classes, spés, sorts, capacités, monstres, **PNJ**, équipements… Classes / Spécialisations (`settings.menu_collapsible`) se déplient : une sous-page par fiche hors archive (`pages:sync-bibliotheque-entities`, `BibliothequeEntityPageService`). Jouable → `read_level` de la fiche ; brouillon/brut/auto → `read_level` MJ+ (absentes du menu invité). L’Essentiel = aide-mémoire (`database/seeders/data/essential-pages.php`), aligné sur `private/game/rules`. Page `essentiels-differences-dd` : écarts avec D&D 5e.
- **Atelier Création** (`/pages/creation`, MJ) : une page d’aide par type d’entité. Sorts, monstres, équipements, conso, capacités, traits, ressources : 5 blocs (philosophie, points, limites, conseils, exemples) dans `resources/ia/creation-guides/` (CMS + prompt conversion, `ia:creation-guides`). Les autres types restent dans `database/seeders/data/creation-pages.php`. Anciens slugs `contribution-creatures|objets|sorts` → 301.
- **Page « Les métiers »** (`les-metiers`, Bibliothèques) : page documentaire sans entité, seedée depuis `database/seeders/data/jobs-page.php`. Icônes dans `storage/app/public/images/jobs/`, tableau des runes en section `forgemagie_rune_table`.
- **Chapitre 5 règles** (`regles-5-*`) : équilibrage des entités. Import TOC → `menu_group=Pour les MJ`, `read_level` MJ. PDF dédié `mj-pdf` (page `ressources-mj`, disque privé). Hors PDF joueur et hors `/storage/…`.
- **Changelog** : page Informations `changelog` (menu **Journal**). Fichiers `storage/app/public/changelog/` (`intro.md`, `X.Y.Z.md`, `roadmap.md`), pas la base. Lecture `/changelog/feed/{semver}` + frise `/changelog/roadmap`. Édition admin : `GET|PUT /changelog/sources`. Hors livre.
- **Page « Ressources »** (`ressources-de-jeu`) : racine du menu Règles (groupes `regles`, `essentiel`, `fiches`, `identite`). Compilation `rules:compile-downloads` (PDF joueur ch. 1–4 + Essentiel + PDF MJ ch. 5). L’Essentiel (page `essentiels-bien-demarrer`) expose aussi le groupe `essentiel`.
- **Sécurité contenu** : Mews\Purifier (profil `section_text`) sur le HTML de section.

## Fichiers pivots

- `app/Models/Page.php`, `app/Models/Section.php`, `app/Enums/SectionType.php`.
- `app/Http/Controllers/PageController.php`, `SectionController.php` ; API `app/Http/Controllers/Api/Cms*.php` (×3).
- `app/Services/PageService.php` (menu + cache), `app/Services/SectionService.php` (save + Purifier + kref).
- `app/Policies/PagePolicy.php`, `SectionPolicy.php` ; resources `PageResource`/`SectionResource`.
- `resources/ia/creation-guides/`, `app/Services/GenerativeAi/CreationGuideCatalog.php` — canevas conversion (7 types).
- `database/seeders/data/creation-pages.php`, `database/seeders/CreationPagesSeeder.php` — atelier MJ Création.
- `app/Support/Cms/CreationPageLegacyRedirects.php` — 301 des anciens slugs `contribution-*`.
- `resources/js/Pages/Organismes/section/PageRenderer.vue` (+ `SectionRenderer`, `SectionLazyGate`, `templates/index.js`, `composables/useTemplateRegistry.js`).
- `resources/js/Pages/Molecules/data-input/RichTextEditorField.vue` + `resources/js/Composables/richText/*` (kref).

## Descendre

- [README humain](./README.md) — modèles, templates, kref, menu, routes détaillés.
- Entités référencées par kref : [../entities/_ai.md](../entities/_ai.md).
- Doc existante (L2) : `docs/features/cms/README.md`, `ENTITY_SECTIONS.md`, `docs/features/cms/README.md`.
