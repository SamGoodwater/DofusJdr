<?php

namespace App\Services;

use App\Http\Resources\Entity\BreedResource;
use App\Http\Resources\Entity\SpecializationResource;
use App\Models\Entity\Breed;
use App\Models\Entity\Specialization;
use App\Models\Page;
use App\Models\Section;
use App\Models\User;
use App\Support\BreedImagePaths;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

/**
 * Service pour la gestion des pages et sections.
 *
 * Centralise la logique métier liée aux pages :
 * - Récupération des pages du menu
 * - Construction de l'arborescence du menu
 * - Vérification des permissions de visualisation
 * - Récupération des sections affichables
 *
 * @example
 * $menuPages = PageService::getMenuPages($user);
 * $menuTree = PageService::buildMenuTree($menuPages);
 */
class PageService
{
    /**
     * Durée du cache pour les pages du menu (en secondes).
     */
    private const CACHE_TTL = 3600; // 1 heure

    private const CACHE_KEY_PAGES_SELECT = 'pages_select_list';

    /**
     * Liste légère des pages (id, title, slug) pour les selects — tableaux uniquement (L13 cache).
     *
     * @return array<int, array{id: int, title: string, slug: string}>
     */
    public static function getPagesSelectList(): array
    {
        $cached = Cache::get(self::CACHE_KEY_PAGES_SELECT);

        if (is_array($cached)) {
            return $cached;
        }

        if ($cached !== null) {
            Cache::forget(self::CACHE_KEY_PAGES_SELECT);
        }

        $list = Page::query()
            ->select('id', 'title', 'slug')
            ->orderBy('title')
            ->get()
            ->map(fn (Page $page) => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
            ])
            ->values()
            ->all();

        Cache::put(self::CACHE_KEY_PAGES_SELECT, $list, self::CACHE_TTL);

        return $list;
    }

    /**
     * Récupère les pages à afficher dans le menu.
     *
     * Filtre les pages selon :
     * - État : publiées uniquement
     * - Menu : in_menu = true
     * - Visibilité : selon le rôle de l'utilisateur
     * - Ordre : triées par menu_order
     *
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Collection<Page> Collection de pages
     */
    public static function getMenuPages(?User $user = null): Collection
    {
        $cacheKey = 'menu_pages_'.($user?->id ?? 'guest');
        $cached = Cache::get($cacheKey);

        if (is_array($cached)) {
            $ids = $cached;
        } else {
            if ($cached !== null) {
                Cache::forget($cacheKey);
            }

            $ids = Page::forMenu($user)->pluck('id')->all();
            Cache::put($cacheKey, $ids, self::CACHE_TTL);
        }

        if ($ids === []) {
            return new Collection;
        }

        return Page::query()
            ->whereIn('id', $ids)
            ->with(['parent', 'children'])
            ->get()
            ->sortBy(fn (Page $page) => array_search($page->id, $ids, true))
            ->values();
    }

    /**
     * Construit l'arborescence du menu à partir d'une collection de pages.
     *
     * Organise les pages en structure hiérarchique (parent/children) pour l'affichage
     * dans le menu de navigation. Les pages sont triées par `menu_order`.
     *
     * **Structure retournée :**
     * ```php
     * [
     *   ['id' => 1, 'title' => 'Page 1', 'url' => '/pages/page-1', 'children' => [...]],
     *   ['id' => 2, 'title' => 'Page 2', 'url' => '/pages/page-2', 'children' => []],
     * ]
     * ```
     *
     * @param  Collection<Page>  $pages  Collection de pages (doit contenir parent/children chargés)
     * @return array<int, array<string, mixed>> Arborescence du menu (pages racines avec enfants imbriqués)
     *
     * @example
     * $pages = PageService::getMenuPages($user);
     * $menuTree = PageService::buildMenuTree($pages);
     * // Utilisé pour afficher le menu hiérarchique dans le frontend
     */
    public static function buildMenuTree(Collection $pages): array
    {
        // Séparer les pages racines (sans parent) et les enfants
        $roots = $pages->filter(fn ($page) => $page->parent_id === null);
        $children = $pages->filter(fn ($page) => $page->parent_id !== null);
        $breedMenuIcons = self::prefetchBreedMenuIcons($pages);

        // Construire l'arborescence récursivement
        return $roots->map(function ($page) use ($children, $breedMenuIcons) {
            return self::buildMenuItem($page, $children, $breedMenuIcons);
        })->values()->toArray();
    }

    /**
     * Construit un item de menu avec ses enfants.
     *
     * @param  Page  $page  Page à transformer en item de menu
     * @param  Collection<Page>  $allChildren  Toutes les pages enfants disponibles
     * @param  array<int, array{icon: string|null, hover: string|null}>  $breedMenuIcons
     * @return array<string, mixed> Item de menu avec structure
     */
    private static function buildMenuItem(Page $page, Collection $allChildren, array $breedMenuIcons = []): array
    {
        $menuIcon = self::resolveMenuIconForPage($page, $breedMenuIcons);

        $item = [
            'id' => $page->id,
            'title' => $page->title,
            'slug' => $page->slug,
            'url' => route('pages.show', $page->slug, false),
            'order' => $page->menu_order,
            'menu_group' => $page->menu_group,
            'entity_key' => $page->entity_key,
            'icon' => $page->icon,
            'menu_icon' => $menuIcon['icon'],
            'menu_icon_hover' => $menuIcon['hover'],
            'page_css_classes' => $page->page_css_classes,
            'title_css_classes' => $page->title_css_classes,
            'menu_item_css_classes' => $page->menu_item_css_classes,
            'children' => [],
        ];

        // Trouver les enfants de cette page
        $pageChildren = $allChildren->filter(fn ($child) => $child->parent_id === $page->id);

        if ($pageChildren->isNotEmpty()) {
            // Construire récursivement les enfants
            $item['children'] = $pageChildren
                ->sortBy('menu_order')
                ->map(fn ($child) => self::buildMenuItem($child, $allChildren, $breedMenuIcons))
                ->values()
                ->toArray();
        }

        $item['menu_collapsible'] = self::isMenuCollapsible($page, $pageChildren->isNotEmpty());

        return $item;
    }

    /**
     * Précharge les icônes menu des classes liées (symbol-full ; colorisation CSS au hover).
     *
     * @param  Collection<Page>  $pages
     * @return array<int, array{icon: string|null, hover: string|null}>
     */
    private static function prefetchBreedMenuIcons(Collection $pages): array
    {
        $breedIds = $pages
            ->map(function (Page $page): int {
                $linked = $page->settings['linked_entity'] ?? null;
                if (! is_array($linked) || ($linked['type'] ?? '') !== 'breed') {
                    return 0;
                }

                return (int) ($linked['id'] ?? 0);
            })
            ->filter(fn (int $id): bool => $id > 0)
            ->unique()
            ->values()
            ->all();

        if ($breedIds === []) {
            return [];
        }

        $map = [];
        Breed::query()
            ->whereIn('id', $breedIds)
            ->with(['media' => fn ($query) => $query->whereIn('collection_name', ['icons', 'images'])])
            ->get(['id', 'icon', 'image', 'symbol_bw', 'symbol_full'])
            ->each(function (Breed $breed) use (&$map): void {
                $map[(int) $breed->id] = [
                    'icon' => self::resolveBreedMenuIcon($breed),
                    'hover' => BreedImagePaths::menuIconHover($breed),
                ];
            });

        return $map;
    }

    /**
     * @param  array<int, array{icon: string|null, hover: string|null}|string|null>  $breedMenuIcons
     * @return array{icon: string|null, hover: string|null}
     */
    private static function resolveMenuIconForPage(Page $page, array $breedMenuIcons): array
    {
        $empty = ['icon' => null, 'hover' => null];
        $linked = $page->settings['linked_entity'] ?? null;
        if (! is_array($linked)) {
            return $empty;
        }

        $type = (string) ($linked['type'] ?? '');
        if ($type !== 'breed') {
            return $empty;
        }

        $breedId = (int) ($linked['id'] ?? 0);
        if ($breedId < 1) {
            return $empty;
        }

        $entry = $breedMenuIcons[$breedId] ?? null;
        if (is_array($entry)) {
            return [
                'icon' => $entry['icon'] ?? null,
                'hover' => $entry['hover'] ?? null,
            ];
        }
        if (is_string($entry) && $entry !== '') {
            return ['icon' => $entry, 'hover' => null];
        }

        return $empty;
    }

    /**
     * Icône menu d'une classe : symbol-full, sinon icon, sinon symbol-bw, puis image / Spatie.
     */
    public static function resolveBreedMenuIconForSync(Breed $breed): ?string
    {
        return self::resolveBreedMenuIcon($breed);
    }

    private static function resolveBreedMenuIcon(Breed $breed): ?string
    {
        $fromLocal = BreedImagePaths::menuIcon($breed);
        if ($fromLocal !== null) {
            return self::normalizeMenuIconUrl($fromLocal);
        }

        foreach (['icons', 'images'] as $collection) {
            $media = $breed->getFirstMedia($collection);
            if ($media === null) {
                continue;
            }

            $url = $media->hasGeneratedConversion('thumb')
                ? $media->getUrl('thumb')
                : $media->getUrl();

            $normalized = self::normalizeMenuIconUrl($url);
            if ($normalized !== null) {
                return $normalized;
            }
        }

        return null;
    }

    /**
     * Exclut Font Awesome et chaînes vides ; renvoie un chemin relatif /storage/… si possible.
     */
    private static function normalizeMenuIconUrl(?string $icon): ?string
    {
        $sanitized = self::sanitizeMenuIcon($icon);
        if ($sanitized === null) {
            return null;
        }

        if (str_starts_with($sanitized, 'http://') || str_starts_with($sanitized, 'https://')) {
            $path = parse_url($sanitized, PHP_URL_PATH);
            if (is_string($path) && $path !== '') {
                return $path;
            }
        }

        return $sanitized;
    }

    /**
     * Exclut Font Awesome et chaînes vides des icônes menu image.
     */
    private static function sanitizeMenuIcon(?string $icon): ?string
    {
        if ($icon === null) {
            return null;
        }

        $trimmed = trim($icon);
        if ($trimmed === '' || str_starts_with($trimmed, 'fa-')) {
            return null;
        }

        return $trimmed;
    }

    /**
     * Vérifie si une page peut être vue par un utilisateur.
     *
     * @param  Page  $page  Page à vérifier
     * @param  User|null  $user  Utilisateur (null pour invité)
     * @return bool True si la page peut être vue
     */
    public static function canViewPage(Page $page, ?User $user = null): bool
    {
        return $page->canBeViewedBy($user);
    }

    /**
     * Récupère les sections affichables d'une page.
     *
     * Filtre les sections selon :
     * - État : publiées uniquement
     * - Visibilité : selon le rôle de l'utilisateur
     * - Ordre : triées par order
     *
     * @param  Page  $page  Page dont on veut les sections
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Collection<Section> Collection de sections
     */
    public static function getPublishedSections(Page $page, ?User $user = null): Collection
    {
        return SectionService::getDisplayableSections($page, $user);
    }

    /**
     * Invalide le cache des pages du menu.
     *
     * **Quand l'appeler :**
     * - Après création d'une page
     * - Après mise à jour d'une page (titre, slug, in_menu, parent_id, menu_order, etc.)
     * - Après suppression/restauration d'une page
     * - Après modification de la visibilité ou de l'état d'une page
     *
     * **Gestion du cache :**
     * - Le cache est séparé par utilisateur (chaque utilisateur a son propre cache)
     * - Si `$user` est null, invalide pour TOUS les utilisateurs (utilise `Cache::flush()`)
     * - Toujours invalide le cache des invités
     * - OPTIMISATION : Invalide aussi le cache de la liste des pages (select)
     *
     * @param  User|null  $user  Utilisateur spécifique (null pour tous les utilisateurs)
     *
     * @example
     * // Après modification d'une page
     * $page->update(['title' => 'Nouveau titre']);
     * PageService::clearMenuCache(); // Invalide pour tous
     *
     * // Après modification pour un utilisateur spécifique
     * PageService::clearMenuCache($user); // Invalide seulement pour cet utilisateur
     */
    public static function clearMenuCache(?User $user = null): void
    {
        if ($user) {
            Cache::forget('menu_pages_'.$user->id);
        } else {
            // Invalider pour tous les utilisateurs (pattern matching)
            // Note: Cache::flush() vide TOUT le cache, pas seulement les pages
            // Pour une meilleure performance, on pourrait utiliser un système de tags si disponible
            Cache::flush();
        }

        // Toujours invalider pour les invités
        Cache::forget('menu_pages_guest');

        // OPTIMISATION : Invalider le cache de la liste des pages (utilisé dans les selects)
        Cache::forget('pages_select_list');
    }

    /**
     * Récupère une page par son slug avec ses sections affichables.
     *
     * @param  string  $slug  Slug de la page
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return Page|null Page trouvée ou null
     */
    public static function getPageBySlug(string $slug, ?User $user = null): ?Page
    {
        $page = Page::where('slug', $slug)->first();

        if (! $page || ! self::canViewPage($page, $user)) {
            return null;
        }

        // Charger les sections affichables via SectionService
        $sections = SectionService::getDisplayableSections($page, $user);
        $page->setRelation('sections', $sections);

        return $page;
    }

    /**
     * Vérifie si une page peut être affichée dans le menu.
     *
     * @param  Page  $page  Page à vérifier
     * @param  User|null  $user  Utilisateur connecté (null pour invité)
     * @return bool True si la page peut être dans le menu
     */
    public static function canBeInMenu(Page $page, ?User $user = null): bool
    {
        return $page->isPlayable()
            && $page->in_menu
            && $page->isReadableFor($user);
    }

    /**
     * Indique si la page parente affiche ses sous-pages dans un collapse du menu Aside.
     *
     * @param  Page  $page  Page candidate
     * @param  bool|null  $hasMenuChildren  Présence d'enfants menu (évite une requête si déjà connu)
     */
    public static function isMenuCollapsible(Page $page, ?bool $hasMenuChildren = null): bool
    {
        $settings = is_array($page->settings) ? $page->settings : [];
        if (array_key_exists('menu_collapsible', $settings)) {
            return filter_var($settings['menu_collapsible'], FILTER_VALIDATE_BOOLEAN);
        }

        if ($hasMenuChildren !== null) {
            return $hasMenuChildren;
        }

        if ($page->relationLoaded('children')) {
            return $page->children->contains(fn (Page $child) => self::canBeInMenu($child, null));
        }

        return $page->children()
            ->where('in_menu', true)
            ->where('state', Page::STATE_PLAYABLE)
            ->exists();
    }

    /**
     * Index automatique des sous-pages pour une page parente (cartes CMS).
     *
     * Les catalogues Classes / Spécialisations n’affichent pas cet index : le tableau
     * d’entités sur la page suffit ; les fiches restent accessibles via le menu déplié.
     *
     * @return list<array<string, mixed>>
     */
    public static function buildMenuChildIndex(Page $page, ?User $user = null): array
    {
        if (! self::isMenuCollapsible($page)) {
            return [];
        }

        $slug = (string) ($page->slug ?? '');
        if (in_array($slug, ['bibliotheque-breed', 'bibliotheque-specialization'], true)) {
            return [];
        }

        $children = ($page->relationLoaded('children') ? $page->children : $page->children()->get())
            ->filter(fn (Page $child) => self::canBeInMenu($child, $user))
            ->sortBy('menu_order')
            ->values();

        if ($children->isEmpty()) {
            return [];
        }

        $breedIds = [];
        $specIds = [];
        foreach ($children as $child) {
            $linked = $child->settings['linked_entity'] ?? null;
            if (! is_array($linked)) {
                continue;
            }
            $type = (string) ($linked['type'] ?? '');
            $id = (int) ($linked['id'] ?? 0);
            if ($id < 1) {
                continue;
            }
            if ($type === 'breed') {
                $breedIds[] = $id;
            } elseif ($type === 'specialization') {
                $specIds[] = $id;
            }
        }

        $breeds = $breedIds !== []
            ? Breed::query()->whereIn('id', array_unique($breedIds))->get()->keyBy('id')
            : collect();
        $specializations = $specIds !== []
            ? Specialization::query()->whereIn('id', array_unique($specIds))->get()->keyBy('id')
            : collect();

        $resolver = app(BibliothequeEntityPageService::class);

        return $children->map(function (Page $child) use ($user, $resolver, $breeds, $specializations) {
            $menuIcons = self::resolveMenuIconForPage($child, []);
            $base = [
                'id' => $child->id,
                'title' => $child->title,
                'slug' => $child->slug,
                'url' => route('pages.show', $child->slug, false),
                'icon' => $child->icon,
                'entity_key' => $child->entity_key,
                'menu_icon' => $menuIcons['icon'],
                'menu_icon_hover' => $menuIcons['hover'],
            ];

            $linked = $child->settings['linked_entity'] ?? null;
            if (! is_array($linked)) {
                return array_merge($base, ['kind' => 'page']);
            }

            $type = (string) ($linked['type'] ?? '');
            $entityId = (int) ($linked['id'] ?? 0);
            if ($entityId < 1 || ! in_array($type, ['breed', 'specialization'], true)) {
                return array_merge($base, ['kind' => 'page']);
            }

            $entity = $type === 'breed'
                ? $breeds->get($entityId)
                : $specializations->get($entityId);

            if ($entity === null) {
                $entity = $resolver->resolveLinkedEntity($linked);
            }

            if ($entity === null || ! Gate::forUser($user)->allows('view', $entity)) {
                return array_merge($base, ['kind' => 'page']);
            }

            if ($entity instanceof Breed) {
                return array_merge($base, [
                    'kind' => 'breed',
                    'entity' => (new BreedResource($entity))->resolve(request()),
                    'menu_icon' => BreedImagePaths::menuIcon($entity) ?? $base['menu_icon'],
                    'menu_icon_hover' => BreedImagePaths::menuIconHover($entity),
                ]);
            }

            /** @var Specialization $entity */
            return array_merge($base, [
                'kind' => 'specialization',
                'entity' => (new SpecializationResource($entity))->resolve(request()),
            ]);
        })->values()->all();
    }
}
