<?php

namespace App\Http\Controllers\Entity;

use App\Http\Controllers\Controller;
use App\Http\Requests\Entity\StoreCreatureRequest;
use App\Http\Requests\Entity\UpdateCreatureCreatureTraitsRequest;
use App\Http\Requests\Entity\UpdateCreatureRequest;
use App\Http\Resources\Entity\CreatureResource;
use App\Http\Resources\Entity\CreatureTraitResource;
use App\Models\Entity\Consumable;
use App\Models\Entity\Creature;
use App\Models\Entity\CreatureTrait;
use App\Models\Entity\Item;
use App\Models\Entity\Resource;
use App\Models\Entity\Spell;
use App\Models\User;
use App\Services\Creature\CreatureComposableCharacteristicsPersister;
use App\Services\Creature\Runtime\CreatureRuntimeStatsService;
use App\Services\Entity\EntityDeletionService;
use App\Services\PdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

class CreatureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Creature::class);

        $query = Creature::query()
            ->visibleToUser(request()->user())
            ->with(['createdBy', 'npc', 'monster']);

        // Recherche
        if (request()->has('search') && request()->search) {
            $search = request()->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Tri
        $sortColumn = request()->get('sort', 'id');
        $sortOrder = request()->get('order', 'desc');

        if (in_array($sortColumn, ['id', 'name', 'created_at'])) {
            $query->orderBy($sortColumn, $sortOrder);
        } else {
            $query->latest();
        }

        $creatures = $query->paginate(20)->withQueryString();

        return Inertia::render('Pages/entity/creature/Index', [
            'creatures' => CreatureResource::collection($creatures),
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreatureRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Creature $creature)
    {
        //
    }

    /**
     * Stats runtime : variables fusionnées (créature + objets), formules évaluées, décomposition pour tooltips / API.
     * Autorisé si le monstre / PNJ lié est `view` pour le visiteur (fiches jouables sans compte).
     * Les objets agrégés sont limités à ceux `visibleToUser` (un équipement brouillon n’entre pas dans les totaux publics).
     *
     * Query : entity=monster|class|npc (défaut monster) — aligné sur les surcharges characteristic_creature.
     */
    public function resolvedStats(Request $request, Creature $creature, CreatureRuntimeStatsService $runtimeStats): JsonResponse
    {
        $this->authorize('viewResolvedStats', $creature);

        $entity = (string) $request->query('entity', 'monster');
        $creature->load([
            'items' => fn ($q) => $q->visibleToUser($request->user()),
        ]);

        return response()->json($runtimeStats->resolve($creature, $entity));
    }

    public function edit(Creature $creature)
    {
        $this->authorize('update', $creature);

        $creature->load([
            'createdBy',
            'items',
            'resources',
            'consumables',
            'spells',
            'creatureTraits',
        ]);

        // Charger toutes les entités disponibles pour la recherche
        $availableItems = Item::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableResources = \App\Models\Entity\Resource::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableConsumables = Consumable::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableSpells = Spell::select('id', 'name', 'description', 'level')
            ->orderBy('name')
            ->get();

        $availableCreatureTraits = CreatureTraitResource::collection(
            CreatureTrait::query()->orderBy('name')->limit(5000)->get()
        )->toArray(request());

        return Inertia::render('Pages/entity/creature/Edit', [
            'creature' => new CreatureResource($creature),
            'availableItems' => $availableItems,
            'availableResources' => $availableResources,
            'availableConsumables' => $availableConsumables,
            'availableSpells' => $availableSpells,
            'availableCreatureTraits' => $availableCreatureTraits,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateCreatureRequest $request,
        Creature $creature,
        CreatureComposableCharacteristicsPersister $composablePersister,
    ) {
        $this->authorize('update', $creature);

        $validated = $request->validated();
        $composableKeys = array_flip(CreatureComposableCharacteristicsPersister::storableKeys());
        $identityPayload = array_diff_key($validated, $composableKeys);
        if ($identityPayload !== []) {
            $creature->update($identityPayload);
        }
        $composablePersister->apply($creature, $composablePersister->extractPayload($validated));

        return back()->with('success', 'Créature mise à jour avec succès.');
    }

    /**
     * Place la créature en corbeille (soft delete).
     */
    public function delete(Request $request, Creature $creature, EntityDeletionService $deletionService): RedirectResponse
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 401);

        $deletionService->softDelete($creature, $actor);

        return redirect()->route('entities.creatures.index')
            ->with('success', 'Créature placée en corbeille.');
    }

    public function updateCreatureTraits(UpdateCreatureCreatureTraitsRequest $request, Creature $creature)
    {
        $creature->creatureTraits()->sync($request->validatedCreatureTraitIds());

        return redirect()->back()
            ->with('success', 'Traits de la créature mis à jour.');
    }

    /**
     * Update the items of a creature (avec quantités).
     */
    public function updateItems(Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);

        $request->validate([
            'items' => 'array',
        ]);

        $syncData = [];
        foreach ($request->items as $itemId => $pivotData) {
            $itemId = (int) $itemId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$itemId] = ['quantity' => (int) $pivotData['quantity']];
            }
        }

        if (! empty($syncData)) {
            $itemIds = array_keys($syncData);
            $existingItems = Item::whereIn('id', $itemIds)->pluck('id')->toArray();
            $invalidIds = array_diff($itemIds, $existingItems);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['items' => 'Certains objets n\'existent pas.'])
                    ->withInput();
            }
        }

        $creature->items()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Objets de la créature mis à jour avec succès.');
    }

    /**
     * Update the resources of a creature (avec quantités).
     */
    public function updateResources(Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);

        $request->validate([
            'resources' => 'array',
        ]);

        $syncData = [];
        foreach ($request->resources as $resourceId => $pivotData) {
            $resourceId = (int) $resourceId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$resourceId] = ['quantity' => (int) $pivotData['quantity']];
            }
        }

        if (! empty($syncData)) {
            $resourceIds = array_keys($syncData);
            $existingResources = \App\Models\Entity\Resource::whereIn('id', $resourceIds)->pluck('id')->toArray();
            $invalidIds = array_diff($resourceIds, $existingResources);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['resources' => 'Certaines ressources n\'existent pas.'])
                    ->withInput();
            }
        }

        $creature->resources()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Ressources de la créature mises à jour avec succès.');
    }

    /**
     * Update the consumables of a creature (avec quantités).
     */
    public function updateConsumables(Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);

        $request->validate([
            'consumables' => 'array',
        ]);

        $syncData = [];
        foreach ($request->consumables as $consumableId => $pivotData) {
            $consumableId = (int) $consumableId; // S'assurer que l'ID est un entier
            if (is_array($pivotData) && isset($pivotData['quantity']) && $pivotData['quantity'] > 0) {
                $syncData[$consumableId] = ['quantity' => (int) $pivotData['quantity']];
            }
        }

        if (! empty($syncData)) {
            $consumableIds = array_keys($syncData);
            $existingConsumables = Consumable::whereIn('id', $consumableIds)->pluck('id')->toArray();
            $invalidIds = array_diff($consumableIds, $existingConsumables);

            if (! empty($invalidIds)) {
                return redirect()->back()
                    ->withErrors(['consumables' => 'Certains consommables n\'existent pas.'])
                    ->withInput();
            }
        }

        $creature->consumables()->sync($syncData);

        return redirect()->back()
            ->with('success', 'Consommables de la créature mis à jour avec succès.');
    }

    /**
     * Update the spells of a creature.
     */
    public function updateSpells(Request $request, Creature $creature)
    {
        $this->authorize('update', $creature);

        $request->validate([
            'spells' => 'array',
            'spells.*' => 'exists:spells,id',
        ]);

        $creature->spells()->sync($request->spells);

        return redirect()->back()
            ->with('success', 'Sorts de la créature mis à jour avec succès.');
    }

    /**
     * Télécharge un PDF pour un ou plusieurs creatures.
     *
     * @param  Creature|null  $creature  La creature unique (si une seule)
     * @return Response
     */
    public function downloadPdf(?Creature $creature = null)
    {
        $ids = request()->get('ids');

        if (! empty($ids)) {
            if (is_string($ids)) {
                $ids = explode(',', $ids);
            }

            if (is_array($ids) && count($ids) > 0) {
                $this->authorize('viewAny', Creature::class);
                $creatures = Creature::query()
                    ->visibleToUser(request()->user())
                    ->whereIn('id', $ids)
                    ->get();

                $pdf = PdfService::generateForEntities($creatures, 'creature');
                $filename = 'creatures-'.now()->format('Y-m-d-His').'.pdf';

                return $pdf->download($filename);
            }
        }

        if (! $creature) {
            abort(404);
        }

        $this->authorize('view', $creature);

        $pdf = PdfService::generateForEntity($creature, 'creature');
        $filename = 'creature-'.$creature->id.'-'.now()->format('Y-m-d-His').'.pdf';

        return $pdf->download($filename);
    }
}
