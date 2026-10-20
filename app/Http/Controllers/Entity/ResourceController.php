<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Concerns\RedirectsAfterEntityCreate;
use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreResourceRequest;
use App\Http\Requests\Entity\UpdateResourceRecipeRequest;
use App\Http\Requests\Entity\UpdateResourceRequest;
use App\Http\Resources\Entity\ResourceResource;
use App\Models\Entity\Campaign;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Scenario;
use App\Models\Entity\Shop;
use App\Models\Type\ResourceType;
use App\Models\User;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use App\Support\Entity\EntityUpdateRedirect;
use App\Support\Entity\ObjectEffectEditOptions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class ResourceController extends Controller
{
    use RedirectsAfterEntityCreate;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Resource::class);

        $user = request()->user();

        $query = Resource::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'resourceType']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtres
        if (request()->has('level') && request()->level !== '') {
            $query->where('level', request()->level);
        }

        if (request()->has('resource_type_id') && request()->resource_type_id !== '') {
            $query->where('resource_type_id', request()->resource_type_id);
        }

        if (request()->has('rarity') && request()->rarity !== '') {
            $query->where('rarity', (int) request()->rarity);
        }

        if (request()->has('state') && request()->state !== '') {
            $query->where('state', (string) request()->state);
        }
        if (request()->has('read_level') && request()->read_level !== '') {
            $query->where('read_level', (int) request()->read_level);
        }
        if (request()->has('write_level') && request()->write_level !== '') {
            $query->where('write_level', (int) request()->write_level);
        }

        if (request()->has('auto_update') && request()->auto_update !== '') {
            $query->where('auto_update', (int) request()->auto_update);
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'level', 'rarity', 'price', 'weight', 'state', 'read_level', 'write_level', 'auto_update', 'dofusdb_id', 'created_at'], true)) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $resources = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/resource/Index', [
            'resources' => ResourceResource::collection($resources),
            'filters' => request()->only(['search', 'level', 'resource_type_id', 'rarity', 'state', 'read_level', 'write_level', 'auto_update']),
            'resourceTypes' => ResourceType::query()
                ->orderBy('name')
                ->get(['id', 'name', 'dofusdb_type_id', 'show_in_catalog']),
            'can' => [
                'create' => $user ? $user->can('create', Resource::class) : false,
                'updateAny' => $user ? $user->can('updateAny', Resource::class) : false,
                'deleteAny' => $user ? $user->can('deleteAny', Resource::class) : false,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Resource::class);

        return Inertia::render('Pages/entity/resource/Edit', [
            'resource' => null,
            'resourceTypes' => ResourceType::query()->select('id', 'name')->orderBy('name')->get(),
            'availableShops' => Shop::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableScenarios' => Scenario::query()->select('id', 'name', 'description')->orderBy('name')->get(),
            'availableCampaigns' => Campaign::query()->select('id', 'name', 'description')->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceRequest $request): RedirectResponse|JsonResponse
    {
        $this->authorize('create', Resource::class);

        $data = $request->validated();
        $data['created_by'] = $request->user()->id;

        // Valeurs par défaut / normalisation (éviter d'insérer explicitement NULL sur des colonnes NOT NULL)
        $data['rarity'] = array_key_exists('rarity', $data) && $data['rarity'] !== null ? (int) $data['rarity'] : 0;

        // Valeurs par défaut d'accès/état
        $data['state'] = $data['state'] ?? \App\Models\Entity\Resource::STATE_DRAFT;
        $data['read_level'] = array_key_exists('read_level', $data) && $data['read_level'] !== null ? (int) $data['read_level'] : User::ROLE_GUEST;
        $data['write_level'] = array_key_exists('write_level', $data) && $data['write_level'] !== null ? (int) $data['write_level'] : User::ROLE_GAME_MASTER;
        if ((int) $data['write_level'] < (int) $data['read_level']) {
            $data['write_level'] = (int) $data['read_level'];
        }

        if (array_key_exists('auto_update', $data)) {
            $data['auto_update'] = (bool) $data['auto_update'];
        } else {
            $data['auto_update'] = false;
        }

        $resource = Resource::create($data);

        return $this->redirectAfterEntityStore(
            $request,
            $resource,
            'entities.resources.edit',
            'entities.resources.index',
            'Ressource créée avec succès.',
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $this->authorize('view', $resource);

        $resource->load($this->viewerRelations());

        return Inertia::render('Pages/entity/resource/Show', [
            'resource' => new ResourceResource($resource),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        $this->authorize('update', $resource);

        $resource->load([
            'createdBy',
            'resourceType',
            'recipeIngredients',
            'consumables',
            'items',
            'creatures',
            'scenarios',
            'campaigns',
            'shops',
            'objectEffects',
        ]);

        $availableResourcesForRecipe = [];

        return Inertia::render('Pages/entity/resource/Edit', array_merge([
            'resource' => new ResourceResource($resource),
            'resourceTypes' => ResourceType::query()->select('id', 'name')->orderBy('name')->get(),
            'availableResourcesForRecipe' => $availableResourcesForRecipe,
            'availableShops' => Shop::query()->select('id', 'name', 'description')->orderBy('name')->limit(200)->get(),
            'availableScenarios' => Scenario::query()->select('id', 'name', 'description')->orderBy('name')->limit(200)->get(),
            'availableCampaigns' => Campaign::query()->select('id', 'name', 'description')->orderBy('name')->limit(200)->get(),
        ], ObjectEffectEditOptions::inertiaPropsFor($resource)));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        $this->authorize('update', $resource);

        $data = $request->validated();

        if (array_key_exists('rarity', $data) && $data['rarity'] === null) {
            $data['rarity'] = 0;
        }

        if (array_key_exists('read_level', $data)) {
            $data['read_level'] = (int) $data['read_level'];
        }
        if (array_key_exists('write_level', $data)) {
            $data['write_level'] = (int) $data['write_level'];
        }
        if (array_key_exists('read_level', $data) && array_key_exists('write_level', $data) && (int) $data['write_level'] < (int) $data['read_level']) {
            $data['write_level'] = (int) $data['read_level'];
        }

        if (array_key_exists('auto_update', $data)) {
            $data['auto_update'] = (bool) $data['auto_update'];
        }

        $resource->update($data);

        return EntityUpdateRedirect::respond(
            $request,
            'Ressource mise à jour avec succès.',
            fn () => back(),
            editRedirect: fn () => redirect()->route('entities.resources.edit', $resource),
            indexRedirect: fn () => redirect()->route('entities.resources.index'),
            showRedirect: fn () => redirect()->route('entities.resources.show', $resource),
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(Request $request, Resource $resource, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($resource, $actor);

        return redirect()
            ->route('entities.resources.index')
            ->with('success', 'Ressource placée en corbeille.');
    }

    /**
     * Met à jour la recette de la ressource (ingrédients = autres ressources + quantités).
     */
    public function updateRecipe(UpdateResourceRecipeRequest $request, Resource $resource): RedirectResponse
    {
        $recipe = $request->validated()['recipe'] ?? [];
        $syncData = [];
        foreach ($recipe as $ingredientResourceId => $pivotData) {
            $syncData[(int) $ingredientResourceId] = ['quantity' => (string) ($pivotData['quantity'] ?? 1)];
        }
        $resource->recipeIngredients()->sync($syncData);

        return back()->with('success', 'Recette de la ressource mise à jour.');
    }

    /**
     * Met à jour la relation many-to-many Resource <-> Item (pivot: quantity).
     */
    public function updateItems(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['items'] ?? []);
        $resource->items()->sync($sync);

        return back()->with('success', 'Objets liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Consumable (pivot: quantity).
     */
    public function updateConsumables(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'consumables' => ['nullable', 'array'],
            'consumables.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['consumables'] ?? []);
        $resource->consumables()->sync($sync);

        return back()->with('success', 'Consommables liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Creature (pivot: quantity).
     */
    public function updateCreatures(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'creatures' => ['nullable', 'array'],
            'creatures.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);

        $sync = $this->buildPivotSyncData($validated['creatures'] ?? []);
        $resource->creatures()->sync($sync);

        return back()->with('success', 'Créatures liées mises à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Shop (pivot: quantity, price, comment).
     */
    public function updateShops(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'shops' => ['nullable', 'array'],
            'shops.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'shops.*.price' => ['nullable', 'numeric', 'min:0'],
            'shops.*.comment' => ['nullable', 'string', 'max:255'],
        ]);

        $sync = [];
        foreach (($validated['shops'] ?? []) as $shopId => $pivot) {
            $shopId = (int) $shopId;
            $sync[$shopId] = [
                'quantity' => (int) ($pivot['quantity'] ?? 0),
                'price' => (int) ($pivot['price'] ?? 0),
                'comment' => $pivot['comment'] ?? null,
            ];
        }

        $resource->shops()->sync($sync);

        return back()->with('success', 'hotels de vente liées mises à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Scenario (sans pivot).
     */
    public function updateScenarios(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'scenarios' => ['nullable', 'array'],
            'scenarios.*' => ['integer', 'exists:scenarios,id'],
        ]);

        $resource->scenarios()->sync($validated['scenarios'] ?? []);

        return back()->with('success', 'Scénarios liés mis à jour.');
    }

    /**
     * Met à jour la relation Resource <-> Campaign (sans pivot).
     */
    public function updateCampaigns(Request $request, Resource $resource): RedirectResponse
    {
        $this->authorize('update', $resource);

        $validated = $request->validate([
            'campaigns' => ['nullable', 'array'],
            'campaigns.*' => ['integer', 'exists:campaigns,id'],
        ]);

        $resource->campaigns()->sync($validated['campaigns'] ?? []);

        return back()->with('success', 'Campagnes liées mises à jour.');
    }

    /**
     * Construit un payload compatible sync() pour des pivots type quantity.
     *
     * @param  array  $data  Format: { id: { quantity: X } }
     * @return array<int, array{quantity:int}>
     */
    private function buildPivotSyncData(array $data): array
    {
        $sync = [];

        foreach ($data as $id => $pivot) {
            $id = (int) $id;
            $sync[$id] = [
                'quantity' => (int) ($pivot['quantity'] ?? 0),
            ];
        }

        return $sync;
    }

    /**
     * Télécharge un PDF pour un ou plusieurs resources.
     *
     * @param  resource|null  $resource  La resource unique (si une seule)
     * @return Response
     */
    public function downloadPdf(?Resource $resource = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Resource::class);
                $resources = Resource::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($resources, 'resource');
                $filename = 'resources-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $resource) {
            abort(404);
        }

        $this->authorize('view', $resource);

        $pdf = PdfService::generateForEntity($resource, 'resource');
        $filename = 'resource-'.$resource->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Relations de lecture : liaisons filtrées par `visibleToUser` du visiteur.
     *
     * L’édition charge ces relations sans ce filtre pour ne pas masquer un
     * brouillon déjà lié (un sync suivant le détacherait).
     *
     * @return array<string, mixed>
     *
     * @example $resource->load($this->viewerRelations());
     */
    private function viewerRelations(): array
    {
        $user = request()->user();

        return [
            'createdBy',
            'resourceType',
            'recipeIngredients' => fn ($q) => $q->visibleToUser($user),
            'consumables' => fn ($q) => $q->visibleToUser($user),
            'items' => fn ($q) => $q->visibleToUser($user),
            'creatures' => fn ($q) => $q->visibleToUser($user),
            'scenarios' => fn ($q) => $q->visibleToUser($user),
            'campaigns' => fn ($q) => $q->visibleToUser($user),
            'shops' => fn ($q) => $q->visibleToUser($user),
        ];
    }
}
