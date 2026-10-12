# Commandes projet

Source unique (CLI et UI super_admin sur le récapitulatif). Les blocs `yaml` sont lus par `App\Console\CommandGuide`.  
`ui: true` = listé sur `/admin/recap` (liens vers les pages existantes, pas un second lanceur). `ui: false` = local / bootstrap, jamais servi par l’UI.
`admin:` = chemin interne `/admin/...` de la page thématique.

Entrée officielle serveur : `php artisan project:dev`.  
Liste brute : `php artisan list` · aide : `php artisan <cmd> -h`.

Confirmations (IDE Helper, apt, refresh, permissions) :

- `-y` / `--yes` : accepter (écrase les modèles pour `ide-helper:models`).
- `--no` : refuser (écrit `_ide_helper_models.php`, annule apt / refresh / permissions).
- `-n` / `--no-interaction` : réservé par Symfony. Sans `-y`, IDE Helper n’écrase pas les modèles (équivalent du défaut `[no]`).

---

## `project:prepare`

```yaml
signature: project:prepare
domain: development
ui: false
cron: false
```

Rebuild CSS, vide les caches applicatifs et les vues, régénère les index Atomic et la doc, migrations (`setup --db`), puis pipeline IDE Helper + `optimize`. Le `db:seed` associé est en **création seule** : les lignes déjà en base ne sont pas réécrites (la BDD est la source de vérité). Pour réappliquer les fichiers JSON, utiliser `project:seed --overwrite`.

```bash
php artisan project:prepare
php artisan project:prepare --clear
php artisan project:prepare -y
php artisan project:prepare --no
```

`--clear` : artefacts de tests (PHPUnit, coverage) avant la préparation. `-y` : écrase les PHPDoc des modèles. `--no` : écrit `_ide_helper_models.php`. Interdit en production.

---

## `project:dev`

```yaml
signature: project:dev
domain: development
ui: false
cron: false
```

`project:prepare` puis serveur Laravel (8000) + Vite. Option `--queue` : `queue:listen` sur les files dédiées (`notifications`, `backup`, `scrapping`, `ia`, `rules-downloads`, `maintenance`, `privacy`, `media`, puis `default`). `--no-prepare` : serveurs seuls. `--watch` : watch CSS à la place de Vite. `--clear`, `-y` et `--no` : transmis à prepare.

```bash
php artisan project:dev
php artisan project:dev --queue
php artisan project:dev --no-prepare
php artisan project:dev -y
```

Interdit en production. `composer run dev` lance `php artisan project:dev --queue` (même entrée, file incluse). `--no-prepare` pour les serveurs seuls. `composer run dev:network` reste le helper 0.0.0.0.

---

## `project:deps`

```yaml
signature: project:deps
domain: software
ui: true
cron: false
admin: /admin/project-update
```

Met à jour Composer et pnpm, puis le pipeline IDE / `optimize`. `--with-system` : apt via `setup --update` avant. Cibles : `--composer`, `--pnpm`, `--apt`. Défaut / `--all` : composer + pnpm + optimize. `-y` / `--no` : confirmations apt et IDE Helper.

```bash
php artisan project:deps
php artisan project:deps --with-system
php artisan project:deps --composer
php artisan project:deps -y
```

Interdit en production. L’admin `/admin/project-update` enfile un job (hors prod, super_admin + mot de passe).

---

## `project:review`

```yaml
signature: project:review
domain: tests
ui: true
cron: false
admin: /admin/project-review
```

Rapport Markdown (tests, Pint, PHPStan, ESLint, audit Composer, doc). Profil : `tests`, `quality`, `security`, `docs`, `all`. Ou flags `--pint`, `--tests`, `--test-back`, `--test-front`, `--phpstan`, `--eslint`, `--security`, `--docs`, `--all`.

```bash
php artisan project:review
php artisan project:review --test-back --phpstan
php artisan project:review tests
```

Interdit en production. Rapport sous `storage/app/dev-reports/`. Admin : `/admin/project-review`.

---

## `project:data`

```yaml
signature: project:data
domain: data
ui: true
cron: true
admin: /admin/content/dofusdb
```

Synchronise le catalogue DofusDB (types / races) et les fiches déjà en base avec `auto_update=true`. N’importe pas de nouvelles fiches : pour ça, `scrapping:run` ou `project:init`.

```bash
php artisan project:data sync
php artisan project:data sync --entity=monster
php artisan project:data sync --type=all
php artisan project:data sync --type=all --entity=monster
php artisan project:data sync --dry-run --noimage
```

Sans `--type` / `--races` : sync des entités. Avec catalogue seul : pas de sync entités tant que `--entity` n’est pas passé. Cron : clé `project_data_sync`. Atelier : `/admin/content/dofusdb`.

---

## `project:init`

```yaml
signature: project:init
domain: bootstrap
ui: false
cron: false
```

Pipeline d’installation : migrations, seeders, import règles (`pages:import-rules-toc`), capacités, types DofusDB, scrapping. `--fresh`, `--skip-scrapping`, `--skip-types`, `--verify`, `--deps`. `--overwrite` : réapplique les fichiers de seed sur les lignes déjà en base. `-y` / `--no` : transmis à `project:deps` (IDE Helper, apt).

```bash
php artisan project:init
php artisan project:init --skip-scrapping --skip-types --verify
php artisan project:init --overwrite
php artisan project:init --deps -y
```

Interdit en production.

---

## `project:seed`

```yaml
signature: project:seed
domain: bootstrap
ui: false
cron: false
```

Données locales sans DofusDB : `project:init --skip-scrapping --skip-types`. Par défaut, création seule (ne touche pas une ligne déjà présente). `--overwrite` **réécrit** les lignes existantes depuis les JSON/PHP : confirmation obligatoire ; en CI ajouter `-y`. Avant un overwrite : `scrapping:seeders:export --characteristics`.

```bash
php artisan project:seed
php artisan project:seed --fresh
php artisan project:seed --overwrite
```

---

## `project:refresh`

```yaml
signature: project:refresh
domain: bootstrap
ui: false
cron: false
```

Grand ménage local puis `project:init --fresh`. `--fast` : sans types ni scrapping. `--hard` : wipe vendor/node (`setup --refresh`) avant. `--overwrite` : transmis à `project:init`. `-y` : comme `--force` (pas de confirmation). `--no` : annule.

```bash
php artisan project:refresh --fast --force
php artisan project:refresh --fast -y
php artisan project:refresh --overwrite
php artisan project:refresh --no
```

Destructif. Interdit en production.

---

## `project:init:verify`

```yaml
signature: project:init:verify
domain: bootstrap
ui: false
cron: false
```

Contrôle le socle après init/seed (pages, types, caractéristiques, mappings). `--with-rules`, `--json`.

```bash
php artisan project:init:verify --with-rules
```

---

## `project:clear`

```yaml
signature: project:clear
domain: cleanup
ui: true
cron: true
admin: /admin/project-clear
```

Caches et artefacts. `--safe` : caches Laravel + rapports review + cache PHPStan (preset cron / prod). `--all` : en local, nettoyage large (CSS généré, queue, debugbar) ; en prod, identique à `--safe`. Flags granulaires : `--cache`, `--config`, `--route`, `--view`, `--test`, `--reviews`, `--logs`, `--phpstan-cache`, `--backups`, `--kill`, `--css`, `--queue`, `--debugbar`, `--schedule`, `--event`, `--optimize`.

```bash
php artisan project:clear --safe
php artisan project:clear --cache
php artisan project:clear --all
```

Admin : `/admin/project-clear` (super_admin + mot de passe). Cron : clé `project_clear_safe` → `project:clear --safe`.

---

## `project:clear-orphan-files`

```yaml
signature: project:clear-orphan-files
domain: cleanup
ui: true
cron: true
admin: /admin/orphan-files
```

Fichiers publics MediaLibrary sans ligne `media`. Dry-run par défaut. `--delete` pour supprimer. `--queue` pour un job suivi.

```bash
php artisan project:clear-orphan-files
php artisan project:clear-orphan-files --delete --queue
```

Admin : `/admin/orphan-files`. Cron : `media_clear_orphan_files` (off par défaut).

---

## `project:backup`

```yaml
signature: project:backup
domain: backup
ui: true
cron: true
admin: /admin/backup
```

Archive ZIP unique (`{prefix}_YYYY-MM-DD_HH-mm-ss_xxxx.zip`) : dump BDD + `storage/app` + `private/game` + manifeste v2 (checksums). Purge selon rétention. Hors production : réécrit aussi les fichiers de seed depuis la base. Options : `--no-database`, `--no-storage`, `--no-game`, `--no-seeder-data`, `--prune-only`, `--dry-run`.

```bash
php artisan project:backup
php artisan project:backup --no-storage --no-game
php artisan project:backup --no-seeder-data
php artisan project:backup --prune-only --dry-run
php artisan project:backup:list
php artisan project:backup:delete {name} --yes
php artisan project:backup:restore {name} --yes
```

Admin : `/admin/backup` (lancer, lister, supprimer, restaurer). Cron : `project_backup`. Restauration guidée : [docs/operations/README.md — Sauvegardes](../../docs/operations/README.md#sauvegardes-projectbackup).

---

## `project:backup:list`

```yaml
signature: project:backup:list
domain: backup
ui: false
cron: false
```

Liste les archives (ZIP v2 et legacy) avec format, composants, intégrité, taille.

---

## `project:backup:delete`

```yaml
signature: project:backup:delete
domain: backup
ui: false
cron: false
```

Supprime une archive par nom canonique. Confirmation : `-y` / `--yes`.

---

## `project:backup:restore`

```yaml
signature: project:backup:restore
domain: backup
ui: false
cron: false
```

Restaure une archive ZIP v2 (vérif checksums, sauvegarde de secours, maintenance). Confirmation : `--yes`. Option `--no-safety-backup`, `--write-status` (suivi fichier pour l’UI).

---

## `project:schedule:sync`

```yaml
signature: project:schedule:sync
domain: backup
ui: false
cron: false
```

Ajoute en base les tâches du catalogue manquantes, sans écraser les réglages. Après déploiement : `php artisan project:schedule:sync`. Planning : `/admin/project-schedule` (commande Artisan + lien vers la page thématique).

---

## `project:super-admin`

```yaml
signature: project:super-admin
domain: bootstrap
ui: false
cron: false
```

Crée le premier super_admin humain si aucun n’existe (hors flux `project:init`).

---

## `project:fix-permissions`

```yaml
signature: project:fix-permissions
domain: development
ui: false
cron: false
```

`chown`/`chmod` du dépôt pour un utilisateur système. Interdit en production. `-y` : continuer si l’utilisateur cible n’est pas le compte courant. `--no` : annuler.

```bash
php artisan project:fix-permissions nom_utilisateur
php artisan project:fix-permissions nom_utilisateur -y
```

---

## `scrapping:run`

```yaml
signature: scrapping:run
domain: data
ui: false
cron: true
```

Import DofusDB (nouvelles fiches ou remplacement selon `--update-mode`). Le cron ressources autorisées appelle cette commande.

```bash
php artisan scrapping:run --entity=monster --limit=50
```

Qualité effets : `scrapping:effects:quality-gate`, `scrapping:effects:pipeline`. Socle types : `scrapping:setup`, `scrapping:types:seed`, `scrapping:races:seed`.

---

## `spells:sync-elements`

```yaml
signature: spells:sync-elements
domain: data
ui: false
cron: false
```

Recalcule `spells.element` (masque 7 bits) à partir des sous-effets : `dofus_element_id`, `params.element`, slug `characteristic` (ex. `air`). `--dry-run` pour compter sans écrire.

```bash
php artisan spells:sync-elements --dry-run
php artisan spells:sync-elements
```

---

## `breeds:sync-images`

```yaml
signature: breeds:sync-images
domain: data
ui: false
cron: false
```

Aligne `symbol_*`, `logo_*`, `image_full_*` (et les alias `image` / `icon`) sur `storage/app/public/images/breeds/{slug}/`. `--dry-run` pour compter sans écrire. Resync le menu Bibliothèques sauf `--skip-pages`.

```bash
php artisan breeds:sync-images --dry-run
php artisan breeds:sync-images
```

---

## `game-terms:rewrite-dissipable`

```yaml
signature: game-terms:rewrite-dissipable
domain: data
ui: false
cron: false
```

Remplace l’adjectif Dofus « désenvoûtable » par « dissipable » dans les textes déjà en base (sorts, objets, capacités, CMS…). Les prochains scraps le font via `pickLang`. `--dry-run` pour compter sans écrire.

```bash
php artisan game-terms:rewrite-dissipable --dry-run
php artisan game-terms:rewrite-dissipable
```

---

## `pages:import-rules-toc`

```yaml
signature: pages:import-rules-toc
domain: data
ui: false
cron: false
```

Importe `private/game/rules/TABLE_DES_MATIERES.md` vers les pages CMS. Appelé par `project:init` / `project:seed`. Le chapitre 5 (équilibrage des entités) est placé dans **Pour les MJ** (`read_level` MJ) ; la page **Ressources** reste dans Règles.

```bash
php artisan pages:import-rules-toc --dry-run
php artisan pages:import-rules-toc --force-content --compile-downloads
```

---

## `pages:repair-krefs`

```yaml
signature: pages:repair-krefs
domain: data
ui: false
cron: false
```

Convertit les shortcodes `[[kref:…]]` encore stockés en clair dans les sections, et reconstruit les sections règles dont le HTML a été cassé par un shortcode imbriqué (libellé qui contient un autre `[[kref:]]`).

```bash
php artisan pages:repair-krefs --dry-run
php artisan pages:repair-krefs
```

---

## `ia:equipment-grid`

```yaml
signature: ia:equipment-grid
domain: data
ui: true
cron: false
admin: /admin/content/ia-generation
```

Rapport de couverture de la grille d’équipements JDR (niveau × slot × voie). Sans LLM : un représentant Dofus par case quand il existe (nom / icône inchangés), les trous restent vides. `--write` crée des objets `draft` techniques (`official_id` `ia-grid:…`, jamais `playable`, interdit en production). Relancer `--write` est idempotent.

```bash
php artisan ia:equipment-grid
php artisan ia:equipment-grid --slot=ring --voie=terre --level=8
php artisan ia:equipment-grid --write --limit=20
php artisan ia:equipment-grid --json=storage/logs/equipment-grid.json
```

---

## `ia:convert`

```yaml
signature: ia:convert
domain: data
ui: false
cron: false
```

Conversion IA générique d’une fiche (sort `effect`, rencontre, PNJ kit `NpcKitCatalog`, objet unique, consommable `effect`), persistée en `auto` (`auto_update=false`). 1 paquet = 1 requête Anthropic. Admin only si `--user` est fourni. Une fiche **jouable ou archivée** exige `--force` (sinon rien n’est écrit).

```bash
php artisan ia:convert spell --id=12 --brief="effet lisible à table"
php artisan ia:convert npc --id=88 --user=1
php artisan ia:convert encounter --id=12 --brief="chef Bouftou niveau 10"
php artisan ia:convert item --id=44
php artisan ia:convert consumable --id=8
```

Les fiches **jouables** d’étalons (Ganymède, Piou Vert, etc.) exigent `--force` : sans ça, rien n’est écrit. Ne pas les convertir « pour tester ».

---

## `ia:convert-encounter`

```yaml
signature: ia:convert-encounter
domain: data
ui: false
cron: false
```

Alias de `ia:convert encounter` : un monstre + 2–3 sorts-créature, persistés en `auto` (`auto_update=false`). 1 paquet = 1 requête Anthropic. Admin only si `--user` est fourni. `--force` pour une fiche jouable / archivée.

```bash
php artisan ia:convert-encounter --id=12 --brief="chef Bouftou niveau 10"
php artisan ia:convert-encounter --official-id=jdr:bestiary:piou-vert --user=1 --force
```

---

## `ia:npc-kit-catalog`

```yaml
signature: ia:npc-kit-catalog
domain: data
ui: false
cron: false
```

Pré-filtre compact PNJ (équipement playable, classes/spés hors archive, sorts playable groupés par classe, gabarit 5.1.2). Sans LLM. Few-shot : `official_id` `jdr:npc:incarnam:%`.

```bash
php artisan ia:npc-kit-catalog --level=4 --voie=terre --breed=Iop --role=guard
php artisan ia:npc-kit-catalog --json=storage/logs/npc-kit-catalog.json
```

---

## `ia:creation-guides`

```yaml
signature: ia:creation-guides
domain: data
ui: false
cron: false
```

Dump les fiches de bonne pratique de l’atelier MJ Création (philosophie, points, limites, conseils, exemples) en texte prêt pour un prompt de conversion. Sans LLM. Source : `resources/ia/creation-guides/` (même contenu que les pages `/pages/creation-*`).

```bash
php artisan ia:creation-guides
php artisan ia:creation-guides spell
php artisan ia:creation-guides --json=storage/logs/creation-guides.json
```

---

## `items:seeder-export`

```yaml
signature: items:seeder-export
domain: data
ui: false
cron: false
```

Base → seeder : écrit les équipements dans `database/seeders/data/entities/items/` (un fichier JSON par item, `effect` / `bonus` en objet éditable). Par défaut, seuls les items `auto`. `--versioned` : états retenus + items déjà présents dans les JSON (utilisé par `project:backup`). Réservé au développement : la commande écrit dans le dépôt. `image` n’est jamais exporté (URL liée à l’environnement). Pas de page admin dédiée (CLI ; backup UI = `project:backup`).

```bash
php artisan items:seeder-export
php artisan items:seeder-export --versioned
php artisan items:seeder-export --state=auto --state=draft --prune
php artisan items:seeder-export --all --prune
php artisan items:seeder-export --id=1958 --id=882
```

`--prune` supprime les fichiers qui ne correspondent plus à la sélection. L’export est déterministe : relancer sans changement en base ne produit aucun diff Git.

---

## `items:seeder-import`

```yaml
signature: items:seeder-import
domain: data
ui: false
cron: false
```

Seeder → base : rejoue les fichiers d’équipements versionnés (CLI uniquement, pas de bouton admin). Upsert sur `dofusdb_id` (`official_id` à défaut), résolution du type via `item_type_dofus_id`, synchronisation des panoplies et de la recette (références introuvables ignorées). Les ressources déjà liées aux items `playable` passent en `playable` (plancher 1 kama). `ConsumableSeeder` rejoue l’échelle de soins hors combat (`healing-out-of-combat.json`), les parchemins de caractéristique (`characteristic-respec-scrolls.json`) et les utilitaires (`utility-playable.json`). `ClassBreedSeeder` pose les 19 classes. `CapabilitySeeder` pose les 19 passifs de classe (`class-passives.json`) et les lie via `breed_capability`. `MonsterSeeder` pose les 19 invocations de classe (`class-summons.json`) et le bestiaire JDR (`incarnam.json` + `amakna.json`, 28 fiches) avant `SpellSeeder`. `SpellSeeder` rejoue les 24 sorts de classe des 19 classes (`*-level-1.json` + `*-progression.json`) et lie `invoquer` aux fiches. Idempotent. Même code que `ItemSeeder`, donc `project:seed` et `project:init` rejouent ces items.

```bash
php artisan items:seeder-import
php artisan items:seeder-import --dry-run
```

---

## `rules:compile-downloads`

```yaml
signature: rules:compile-downloads
domain: data
ui: true
cron: false
admin: /admin/content
```

Compile le livre de règles Markdown et L’Essentiel (seed CMS) en PDF et ODT. **Livre joueur** (ch. 1–4) et **L’Essentiel** → `storage/app/public/downloads/generated/`. **Atelier MJ** (ch. 5, `read_level` MJ) → disque privé `storage/app/private/downloads/generated/` (pas d’URL `/storage/…`). Hors livres : Sources / Contenu, changelog. Lancé après `project:init` / `project:seed`, via `pages:import-rules-toc --compile-downloads`, ou depuis le bouton de la gestion du contenu (admin+ ; file `rules-downloads`, worker ponctuel).

```bash
php artisan rules:compile-downloads
php artisan rules:compile-downloads --pdf
php artisan rules:compile-downloads --dry-run
```

---

## `entities:recalculate-prices`

```yaml
signature: entities:recalculate-prices
domain: data
ui: true
cron: false
admin: /admin/content
```

Réécrit `price_calculated` (formule) et vide `price_custom` pour tous les équipements, ou pour les consommables **non jouables**. Les fiches consommable `playable` gardent leur barème JDR. Les ressources (prix Dofus) ne sont pas concernées. Lancé aussi depuis les cartes Équipements / Consommables de la gestion du contenu.

```bash
php artisan entities:recalculate-prices items
php artisan entities:recalculate-prices consumables
```

---

## `media:clean-thumbnails`

```yaml
signature: media:clean-thumbnails
domain: cleanup
ui: false
cron: true
```

Supprime les vignettes Media Library trop anciennes. Cron : `media_clean_thumbnails`.

---

## `privacy:process-deletion-requests`

```yaml
signature: privacy:process-deletion-requests
domain: cleanup
ui: false
cron: true
```

Traite les demandes RGPD de suppression. Cron quotidien.
