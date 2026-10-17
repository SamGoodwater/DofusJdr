# Database — IA

> Schéma MySQL et seeders.

## Fichiers pivots

- `database/migrations/`
- `database/seeders/`
- `database/seeders/data/` — JSON versionnés (items, panoplies, consommables, 19 classes, 19 passifs, 24 sorts de classe, 19 invocations, 28 monstres JDR, 5 PNJ Incarnam) + PHP `playable-breeds.php` (présentation des 19 classes) et `playable-specializations/*.php` (11 fiches)
- `npcs.official_id` / `auto_update` : upsert seeder `jdr:npc:incarnam:…`, `auto_update = false`.
- Favoris user : table `user_favorites` (`user_id`, `entity_type`, `entity_id`)
- Jobs console admin : table `project_console_jobs` (domaine, %, sortie filtrée)
- Réglages IA métier : table `ia_generation_settings` (JSON `payload`, surcharge de `resources/ia/generation.json`)
- Astuces écran de chargement : table `loading_tips` (`body`, `url` nullable, `featured`, `is_active`, `duration_seconds` 2–30 défaut 8) — admin `/admin/loading-tips` (pas Contenu)
- Colonnes JSON/TEXT MySQL : pas de `DEFAULT` SQL (erreur 1101) ; défaut via `$attributes` Eloquent (`users.notification_channels`, `creatures.res_fixe_*`, registres de types `show_in_catalog` / `allow_scrap`).
- Classes : visuels `breeds.symbol_full` / `symbol_bw` / `logo_male` / `logo_female` / `image_full_male` / `image_full_female` (alias `image`/`icon`) via `BreedImagePaths`.
- `conditions.canonical_condition_id` : FK self nullable, jeton Dofus → état JDR `playable`. `conditions.description` : `longText`, sans max de validation.

## Liens

- Entités : [../../features/entities/_ai.md](../../features/entities/_ai.md)
- Scrapping : [../../features/scrapping/_ai.md](../../features/scrapping/_ai.md)
