# Permissions & Auth — carte IA (degré 1a)

> Authentification par session (Laravel, + OAuth GitHub/Discord/Steam) et autorisation à plusieurs niveaux : rôle utilisateur entier 0→5, middlewares de zone, policies (matrice rôle×état + `read_level`/`write_level`). Le front ne fait que projeter les droits calculés côté serveur.

## Quand lire ce nœud

- Gérer les rôles, l'inscription/login, l'OAuth, la confirmation de mot de passe.
- Restreindre une route ou une action (middleware, policy, ability).
- Comprendre comment les droits arrivent au front.

## Concepts clés

- **Rôles** : entier `users.role` 0→5 = guest, user, player, game_master, admin, super_admin (`app/Models/User.php`, constantes `ROLES`). Invité = `user === null` → niveau 0. `verifyRole()`, `isAdmin()` (≥4), `isGameMaster()` (≥3), `isSuperAdmin()` (=5). Détail : [README](./README.md#roles).
- **Super admin** : `isSuperAdmin()` inclut le compte système ; `isInteractiveSuperAdmin()` = humain (utilisé pour le bypass web). Côté Inertia, `is_super_admin` = interactif. Ne pas confondre.
- **Auth** : session guard `web` ; contrôleurs `app/Http/Controllers/Auth/*` ; routes `routes/auth.php`. Sanctum présent mais **non branché** sur `User` (pas de tokens) → auth réelle = session.
- **OAuth** : `OAuthController` + modèle `OAuthAccount` (providers github/discord/steam), flux redirect/callback/link/transfer. Détail : [README](./README.md#oauth).
- **Middlewares** : `role:` (`CheckRole`), `admin.area`, `content.area`, `password.confirm` (`RequirePasswordWithInactivity`, 423 si JSON). Conversion IA HTTP + page `/admin/content/ia-generation` : `role:admin` + `password.confirm`. Détail : [README](./README.md#middlewares).
- **Policies** : `BaseEntityPolicy` (admin → auteur → matrice affichage → state + read/write_level) ; trait `AdminMutationsOnly` pour les types admin-only (monstre, PNJ, boutique, capacité, condition, trait, spécia, sort, panoplie, breed) ; surcharges locales (Spell auteur, Panoply hybride, Breed view) ; `UserPolicy`. Ability **`publish`** (relecteur) : alignée sur `updateAny` / trait admin — admin si restreint, MJ sinon (objet, campagne, scénario, conso, ressource). Les Form Requests d’entités délèguent `can('create')` / `can('update')` ; passer à `playable` (HTTP état, bulk, formulaire) exige `publish`. `read_level` / `write_level` : mutation admin only (`AccessLevelMutationGuard`, Form Requests + `PATCH …/bulk` + rétablissement d’instantané `EntityUpdateDiffService`). Un writer automatique (futur job IA) n’a le droit que de `state = auto` (`EntityStateGate::assertAutomatedWriterMaySet`). Registres de types (`TypeRegistryPolicy`) : `view` / `create` / `update` / `updateAny` / `delete` / `publish` → MJ+ (`isGameMaster`). `CreaturePolicy::viewResolvedStats` suit le `view` du monstre/PNJ lié (pas un bypass public). Payload panoplie d’un équipement (`ItemPanoplyPayload`) : mêmes règles `view` / `visibleToUser` sur le set et ses pièces. Sorts/équipements/traits nested d’un monstre ou PNJ : `visibleToUser` à l’eager-load (`resolved-stats` aussi pour les objets). Boutique / panoplies / scénarios / campagnes d’un PNJ, et PNJ d’une boutique : `visibleToUser` sur fiche / catalogue. Classe et spécialisation d’un PNJ filtrées sur fiche / catalogue / PDF. Recettes (ingrédients ressources) des objets, consommables et ressources : `visibleToUser` sur catalogue / fiche lecture. Capacité / ressource lecture : liaisons nested `visibleToUser`. `GET /api/effects/for-entity` et `/api/object-effects` : `view` sur la fiche parente. Détail : [README](./README.md#policies).
- **Projection front** : `EntityPermissionService` calcule les droits (cache 10 min) → partagés via `HandleInertiaRequests` → composable `usePermissions` (`can`, `canAccess`). `contentManagement` / `effectsAdmin` = `users`/`manageContent` (MJ+) ; `contentPipeline` / `adminPanel` = `users`/`manageAny` (admin+). Zone Contenu (`content.area` + `password.confirm`) : MJ+ pour référentiels jeu ; pipeline (Import DofusDB, mappings, IA) reste `role:admin`. Zone Admin (`admin.area`) : admin+ (astuces chargement, users, ops…). `password.confirm` ne change jamais le rôle. Le CRUD formulaire des fiches suit la policy `update` ; publier `playable` suit `publish`. Les droits **par ligne** viennent du champ `can` des Resources.

## Fichiers pivots

- `app/Models/User.php`, `app/Models/OAuthAccount.php`.
- `app/Http/Controllers/Auth/*`, `app/Http/Controllers/Auth/OAuthController.php`, `app/Support/OAuthConfig.php`.
- `app/Http/Middleware/CheckRole.php`, `EnsureAdminAreaAccess.php`, `EnsureContentManagementAccess.php`, `RequirePasswordWithInactivity.php`, `HandleInertiaRequests.php`.
- `app/Policies/Entity/BaseEntityPolicy.php`, `app/Policies/Entity/Concerns/AdminMutationsOnly.php`, `app/Policies/UserPolicy.php`.
- `app/Support/Entity/AccessLevelMutationGuard.php`, `app/Http/Requests/Concerns/RestrictsAccessLevelMutation.php`, `app/Services/Entity/EntityUpdateDiffService.php` — `read_level` / `write_level` admin only (formulaires, bulk, rétablissement d’instantané).
- `app/Support/EntityPermissions/EntityPermissionService.php`, `app/Services/EntityDisplay/EntityDisplayVisibilityService.php` (`constrainQueryToViewer` pour listes et pièces nested d’une panoplie ; colonnes `state`/`read_level`/`write_level`/`created_by` qualifiées par table, pour rester valides après un JOIN). Nested classes : `BreedController` / `BreedTableController` / `PdfService` / `PageController::renderLinkedEntityPage` appliquent `visibleToUser` aux sorts/capacités/traits/PNJ liés ; sections CMS via `HasLeveledSections::visibleSectionsEagerLoadConstraint`. Nested spécialisations : `SpecializationController` / `SpecializationTableController` / `PdfService` / même page CMS appliquent `visibleToUser` aux sorts/capacités/traits/objets/PNJ liés et le même filtre sections. Nested PNJ : `NpcController` / `NpcTableController` / `PdfService` appliquent `visibleToUser` à la classe, la spécialisation, les traits, la boutique, les panoplies, scénarios et campagnes.
- `config/entity-permissions.php`, `config/access-permissions.php`, `config/auth.php`.
- `resources/js/Composables/permissions/usePermissions.js`, `Composables/auth/useProtectedAdminAction.js`, `Pages/Molecules/action/ConfirmPasswordModal.vue`.

## Descendre

- [README humain](./README.md) — rôles, auth, OAuth, middlewares, policies, configs, flux Inertia.
- Droits par entité : [../entities/_ai.md](../entities/_ai.md).
- Doc existante (L2) : `docs/features/permissions/README.md`, `ENTITY_VISIBILITY_PHASE_A.md`.
